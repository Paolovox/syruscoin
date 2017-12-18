<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Transaction;
use Session;
use Carbon\Carbon;


use Request;
use be\kunstmaan\multichain\MultichainClient;
use be\kunstmaan\multichain\MultichainHelper;

class UserController extends Controller {


	/**
	 * Instantiate a new UserController instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->multichain = new MultichainClient( env('MULTICHAIN_IP_PORT'), env('MULTICHAIN_USERNAME', 'multichainrpc'),  env('MULTICHAIN_PASSWORD'), 3);
		$this->helper = new MultichainHelper($this->multichain);

	}


	//register new user
	public function register(){

		$data = Request::all();

		if(!isset($data['username']) || !isset($data['password'])){
			die(json_encode(array('status' => 500)));
		}

		$username = $data['username'];
		$password = sha1($data['password']);

		//controllo se l'utente già esiste
		if($this->userExists($username)){
			die(json_encode(array('status' => 501, 'description' => 'Utente già registrato')));
		}

		//create new wallet for user
		$wallet = $this->multichain->getNewAddress();
		//set permissions [active, send, receive ]
		$this->multichain->grantCustom($wallet, 'activate,send,receive');

		//insert user nello stream USERS e USERS_ADDRESS
		$this->multichain->publish("users", $username,  bin2hex(json_encode($password)));
		$this->multichain->publish("users_address", $username,  bin2hex(json_encode($wallet)));

		//send init syruscoin
		$this->multichain->sendFromAddress($this->getAdminAddress(), $wallet,  doubleval(env('INIT_COIN')));

		die(json_encode(array('status' => 200, 'description' => 'Registrazione completata')));
	}


	//login
	public function login(){
		$data = Request::all();

		$username = $data['username'];
		$password = sha1($data['password']);

		if($this->getUserCredentials($username) === $password){

			$token = md5(uniqid($username.$password , true));

			Request::session()->put($token, array($username,  Carbon::now() ));
			Request::session()->save();

			die(json_encode(array('token' => $token)));
		}

		die(json_encode(array('status' => 500)));

	}


	//get general info by token
	public function getInfo(){
		$data = Request::all();

		if($this->checkToken($data)){
			$data_token = Request::session()->get($data['token']);

			$username = $data_token[0];
			$wallet_address = $this->getWalletAddressByUsername($username);
			$balance = $this->getBalanceByAddress($wallet_address);
			$transazioni = $this->getListTransactionsByAddress($wallet_address);

			return json_encode(array(
				'info' => 1,
				'name' => $username,
				'wallet' => $wallet_address,
				'balance' => $balance,
				'token' => $data['token']
				)
			);

		}else{
			return json_encode(array('status' => 500, 'description' => 'token scaduto'));
		};

	}


	//send coin to address
	public function sendTo(){

		$data = Request::all();


		if($this->checkToken($data)){

			$data_token = Request::session()->get($data['token']);


			$username = $data_token[0];
			$wallet_address = $this->getWalletAddressByUsername($username);
			$balance = $this->getBalanceByAddress($wallet_address);

			if(empty($data['coins']) || empty($data['address'])){
				return json_encode(array("status" => 500, "description" => "Inserire username e coins"));
			}

			$username_destination = $data['address'];
			$coins = $data['coins'];

			if(!$this->userExists($username_destination)){
				return json_encode(array("status" => 500, "description" => "L'utente non esiste"));
			}


			if(doubleval($balance) < doubleval($coins)){
				return json_encode(array('status' => 500, 'description' => 'I tuoi coins non coprono il prezzo della transazione'));
			}

			if(doubleval($balance) > 0 && doubleval($coins) > 0 && doubleval($balance) > doubleval($coins)){
				$wallet_address_destination = $this->getWalletAddressByUsername($username_destination);
				$transaction = $this->multichain->sendFromAddress($wallet_address, $wallet_address_destination , doubleval($coins));
				$this->multichain->publish("transactions", Carbon::now()->toDateTimeString() , bin2hex(json_encode($transaction)));
				return json_encode(array('status' => 200, 'description' => 'Transazione Effettuata', 'txid' => $transaction ));
			}else{
				return json_encode(array('status' => 500, 'description' => 'Transazione Non Effettuata'));
			}

		}else{
			return json_encode(array('status' => 500, 'description' => 'token scaduto'));
		};


	}

	//controllo se l'utente esiste
	private function userExists($userName){
		$userRecords = $this->multichain->setDebug(true)->listStreamKeyItems("users", $userName, true, 1, -1, true);
		return count($userRecords)>0;
	}


	//get admin address
	private function getAdminAddress(){
		$permissionsInfo = $this->multichain->listPermissions("admin");
		foreach ($permissionsInfo as $permissionItem) {
			$validationInfo = $this->multichain->validateAddress($permissionItem['address']);
			if ($validationInfo['ismine']) {
				return $permissionItem['address'];
			}
		}
		return -1;
	}

	//username e password
	private function getUserCredentials($userName)
	{
			$userRecords = $this->multichain->setDebug(true)->listStreamKeyItems('users', $userName, true, 1, -1, true);

			if(count($userRecords)>0){
					$contentHex = $userRecords[0]['data'];
					$contentArr = json_decode(hex2bin($contentHex), true);
					return $contentArr;
			}
			return false;
	}


	//get wallet address by Username
	private function getWalletAddressByUsername($username){

		if($this->userExists($username)){

			$userRecords = $this->multichain->setDebug(true)->listStreamKeyItems('users_address', $username, true, 1, -1, true);
			if(count($userRecords)>0){
					$contentHex = $userRecords[0]['data'];
					$contentArr = json_decode(hex2bin($contentHex), true);
					return $contentArr;
			}
		}

		return false;
	}


	//ritorna i coin by address
	private function getBalanceByAddress($address){
		$balance = $this->multichain->setDebug(true)->getAddressBalances($address);
		if($balance && count($balance) > 0){
			return $balance[0]['qty'];
		};
		return false;

	}

	//ritorna ultime 10 transazioni by address
	private function getListTransactionsByAddress($address){
		$transactions = $this->multichain->setDebug(true)->listAddressTransactions($address);
		return $transactions;
	}


	//controlla l'esistenza del token e la scadenza
	private function checkToken($data){

		if(!isset($data['token']) || !Request::session()->has($data['token'])){
			return false;
		}

		$token_data = Request::session()->get($data['token']);
		$token_time = Carbon::parse($token_data[1]);
		$diff = Carbon::now()->diffInMinutes($token_time,false);

		if( intval($diff) < -60 ){
			Request::session()->forget($data['token']);
			Request::session()->save();
			return false;
		};

		return true;
	}




	private function listAddresses(){
		return $this->multichain->listAllAddresses();
	}


	public function randomTransactions(){

		$addresses = $this->listAddresses();
		dump($addresses);
		$n_addressess = count($addresses);

		$from_address = rand(0,$n_addressess-1);
		$to_address = rand(0,$n_addressess-1);

		$from_address = $addresses[$from_address]['address'];
		$to_address = $addresses[$to_address]['address'];

		dump($from_address);
		dump($to_address);

		if($from_address == $to_address) return false;

		$coin = $this->getCoinByAddress($from_address,"syruscoin");
		dump("coins address from = ".$coin);
		if(intval($coin) > 0){
			$randomCoin = rand(0, 30) / 10;

			dump("random coins = ".$randomCoin);
			if($coin > doubleval($randomCoin) && doubleval($randomCoin) > 0){
				$hash = $this->multichain->sendAssetFrom($from_address, $to_address, "syruscoin", intval($randomCoin));
				//save transaction
				$transaction = new Transaction();
				$transaction->address_from = $from_address;
				$transaction->address_to = $to_address;
				$transaction->hash = $hash;
				$transaction->asset = "syruscoin";
				$transaction->qty = intval($randomCoin);
				$transaction->save();

				dump($hash);
			}
		}
	}

	public function getLastTransactions(){

		$output = array();

		$transactions_keys = $this->multichain->setDebug(true)->listStreamKeys('transactions', '*', false, 10, -10, true);
		if($transactions_keys){
			foreach ($transactions_keys as $tran => $value) {
				$transaction_key = $value['key'];
				$transazione = $this->multichain->setDebug(true)->listStreamKeyItems('transactions', $transaction_key, true, 1, -1, true);
				$contentHex = $transazione[0]['data'];
				$contentArr = json_decode(hex2bin($contentHex), true);

				$transazione_data = $this->multichain->setDebug(true)->getWalletTransaction($contentArr, false, true);

				// dump($transazione_data);

				$output[$transazione_data['txid']] = array(
					'coins' => $transazione_data['vout'][0]['amount'],
					'address' => $transazione_data['vout'][0]['addresses'][0],
					'time' => Carbon::createFromTimestamp($transazione_data['timereceived'])->toDateTimeString()
				);
			}
		}

		return response()->json($output);
	}



		public function transaction()
		{
			return view('pages.transaction');
		}

}
