<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Transaction;

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
			die(json_encode(array('status' => 501)));
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

		die(json_encode(array('status' => 200)));
	}


	//login
	public function login(){
		$data = Request::all();

		$username = $data['username'];
		$password = sha1($data['password']);

		if($this->getUserCredentials($username) === $password){

			$token = md5(uniqid($username.$password , true));
			Request::session()->put($token, array($username, date("Y-m-d H:i:s")));
			die(json_encode(array('status' => 200)));
		}

		die(json_encode(array('status' => 500)));

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


	//get coint qty by token
	public function getCoinQty(){

		$data = Request::all();
		$user = User::isValid($data['token']);

		if($user){
			$wallet = $user->wallet;

			$asset = $this->multichain->getCoinQty($wallet,"syruscoin");
			if($asset){
				return $asset['total'][0]['qty'];
			}else{
				return "asset non valido";
			}
		};

		return -1; //TODO autenticazione fallita
	}

	//send syruscoin to address
	public function sentTo(){

		$data = Request::all();
		$user = User::isValid($data['token']);

		if($user){
			$wallet_destination = $data['address'];
			$qty = $data['qty'];

			$asset = $this->multichain->getCoinQty($user->wallet,"syruscoin");

			if($asset){
				if( intval($asset['total'][0]['qty'] ) <= 0 ) return "coin <= 0" ;
				$result = $this->multichain->sendAssetFrom($user->wallet, $wallet_destination, "syruscoin", intval($qty));


				//save transaction
				$transaction = new Transaction();
				$transaction->address_from = $user->wallet;
				$transaction->address_to = $wallet_destination;
				$transaction->hash = $result;
				$transaction->asset = "syruscoin";
				$transaction->qty = intval($qty);
				$transaction->save();

				return $result;
			}
			else{
				return "asset non valido";
			}
		}else{
			return -1; //TODO autenticazione fallita
		}


	}



	private function listAddresses(){
		return $this->multichain->listAllAddresses();
	}

	private function getCoinByAddress($address, $asset){
		$coin = $this->multichain->getCoinQty($address,$asset);
		if($coin){
			return $coin['total'][0]['qty'];
		}
		return -1;
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
		$transactions = Transaction::whereNull('deleted_at')->orderBy('created_at', 'desc')->get();
		$output = array();
		foreach ($transactions as $tran) {
			$tran->delete();
			$output[] = $tran->toArray();
		}
		return response()->json($output);
	}


		public function test(){
		//	print_r($this->multichain->getInfo());
			dd($this->getCoinAllAddresses());
			dd('ciao');
		}


		public function transaction()
		{
			return view('pages.transaction');
		}

}
