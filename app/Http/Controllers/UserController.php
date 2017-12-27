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

			return json_encode(array(
				'username' => $username,
				'wallet_address' => $wallet_address,
				'balance' => $balance,
				'token' => $data['token'],
				)
			);

		}else{
			return json_encode(array('status' => 500, 'description' => 'token scaduto'));
		};

	}

	//get transazioni in entrata e uscita (in - out)
	public function getMytransactions(){
		$data = Request::all();

		if($this->checkToken($data)){
			$data_token = Request::session()->get($data['token']);
			$username = $data_token[0];
			$wallet_address = $this->getWalletAddressByUsername($username);
			if(!isset($data['page'])){
				$page = 1;
			}else{
				$page = $data['page'];
			}

			if(!isset($data['in']) && !isset($data['out']) && !isset($data['all'])){
				return json_encode(array("error" => "Specificare i parametri 'in', 'out' o 'all'",));
			}

			$output = array();
			$transazioni = $this->getListTransactionsByAddress($wallet_address, $page);
			if(count($transazioni) > 0){
				foreach ($transazioni as $transazione) {

					if(count($transazione['items']) > 0) continue; //non è una transazione di coins

					if(isset($data['in']) && !isset($data['all'])){
						if(doubleval($transazione['balance']['amount']) < 0) continue;
					}

					if(isset($data['out']) && !isset($data['all'])){
						if(doubleval($transazione['balance']['amount']) > 0) continue;
					}

					$output[Carbon::createFromTimestamp($transazione['timereceived'])->toDateTimeString()] = array(
						'txid' => $transazione['txid'],
						'coins' => $transazione['balance']['amount'],
						'address' => $transazione['addresses'][0],
						'time' => Carbon::createFromTimestamp($transazione['timereceived'])->toDateTimeString(),
						'size' => mb_strlen(hex2bin($transazione['hex']))
					);
				}
			}

			krsort($output);
			return json_encode(array(
				'transactions' => $output
				)
			);

		}else{
			return json_encode(array('status' => 500, 'description' => 'token scaduto'));
		}

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

	//get all miners
	private function getAllMiners(){
		$output = array();
		$permissionsInfo = $this->multichain->listPermissions("mine");
		foreach ($permissionsInfo as $permissionItem) {
			$validationInfo = $this->multichain->validateAddress($permissionItem['address']);
			$output[] = $permissionItem;
			// if ($validationInfo['ismine']) {
			// 	return $permissionItem['address'];
			// }
		}
		return $output;
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

	//ritorna transazioni by address e page
	private function getListTransactionsByAddress($address, $page){
		if($page <= 0){
			return false;
		}
		$transactions = $this->multichain->setDebug(true)->listAddressTransactions($address,10, ($page-1)*10 ,true);
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
		$n_addressess = count($addresses);

		$from_address = rand(0,$n_addressess-1);
		$to_address = rand(0,$n_addressess-1);

		$from_address = $addresses[$from_address]['address'];
		$to_address = $addresses[$to_address]['address'];


		if($from_address == $to_address) return false;

		$coin = $this->getBalanceByAddress($from_address);
		if(doubleval($coin) > 0){
			$randomCoin = rand(0, 30) / 10;

			if($coin > doubleval($randomCoin) && doubleval($randomCoin) > 0){
				$transaction = $this->multichain->sendFromAddress($from_address, $to_address, doubleval($randomCoin));
				$this->multichain->publish("transactions", Carbon::now()->toDateTimeString() , bin2hex(json_encode($transaction)));
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

				$output[$transazione_data['txid']] = array(
					'coins' => $transazione_data['vout'][0]['amount'],
					'address' => $transazione_data['vout'][0]['addresses'][0],
					'time' => Carbon::createFromTimestamp($transazione_data['timereceived'])->toDateTimeString(),
					'size' => mb_strlen(hex2bin($transazione_data['hex']))
				);
			}
		}

		return response()->json($output);
	}




	public function countTransactions(){
		$transactions = $this->multichain->listStreamKeysCustom('transactions');
		die( json_encode(array( 'count' => count($transactions))));
	}

	public function countMiners(){
		die( json_encode(array( 'count' => count($this->getAllMiners()))));
	}


	private function checkIfExistsTransaction($hash){
		$transactions = $this->multichain->listStreamKeysCustom('transactions');

		dd($transactions);
		
		if($transactions){
			foreach ($transactions as $tran => $value) {
				$transaction_key = $value['key'];
				$transazione = $this->multichain->setDebug(true)->listStreamKeyItems('transactions', $transaction_key, true, 1, -1, true);
				$contentHex = $transazione[0]['data'];
				$contentArr = json_decode(hex2bin($contentHex), true);
				if($contentArr == $hash) return true;
			}
		}
		return false;
	}


	private function checkIfExistsAddress($hash){
		$addresses = $this->multichain->listAllAddresses();
		if($addresses){
			foreach ($addresses as $address) {
				if($address['address'] == $hash) return true;
			}
		}
		return false;
	}


	public function getCurrentBlock(){
		$current_block = $this->multichain->setDebug(true)->getblockchaininfo();
		die(json_encode(array('current_block' => $current_block['blocks'])));
	}


	public function getCurrentDifficulty(){
		$current_block = $this->multichain->setDebug(true)->getblockchaininfo();
		die(json_encode(array('current_difficulty' => "~ ".( doubleval($current_block['difficulty']) * 100 ).'%')));
	}

	//render transaction
	public function transaction(){
		$data = Request::all();

		if(!isset($data['tx']) || empty($data['tx'])){
			return view('pages.home', $input);
		}

		//check if transaction exists
		if($this->checkIfExistsTransaction($data['tx'])){

			$transazione_data = $this->multichain->setDebug(true)->getWalletTransaction($data['tx'], false, true);

			$input = array(
				"txid" => $data['tx'],
				'coins' => $transazione_data['vout'][0]['amount'],
				'address_to' => $transazione_data['vout'][0]['addresses'][0],
				'address_from' => $transazione_data['vin'][0]['addresses'][0],
				'block' => $transazione_data['blockindex'],
				'time' => Carbon::createFromTimestamp($transazione_data['timereceived'])->toDateTimeString(),
				'state' => $transazione_data['valid'],

			);

			return view('pages.transaction', $input);

		}else{

			return view('pages.home', $input);

		}

		//se esiste prelevo tutti i parametri per la view

	}



	//render transaction
	public function search(){
		$data = Request::all();
		$input = array();

		if(!isset($data['src']) || empty($data['src'])){
			return view('pages.home', $input);
		}

		//check if transaction exists
		if($this->checkIfExistsTransaction($data['src'])){


			return view('pages.transaction', $input);
		}elseif($this->checkIfExistsAddress($data['src'])) { //che if address exists
			return view('pages.address', $input);
		}else{
			return view('pages.home', $input);
		}

	}

}
