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
		$this->multichain = new MultichainClient("http://127.0.0.1:6752", "multichainrpc", "76tMKwnCBg5qsGRbBVYcydzWQg4P16fDhtibW8FcZvKR", 3);
		$this->helper = new MultichainHelper($this->multichain);
	}


	//register new user
	//TODO decidere se alla creazione riceve già coim
	public function register(){
		$data = Request::all();

		$user = new User();
		$user->name = $data['name'];
		$user->password = sha1($data['password']);
		$user->email = $data['email'];

		//generate token
		$user->remember_token = md5(uniqid($user->email.$user->password , true));

		//create new wallet for user
		$wallet = $this->multichain->getNewAddress();
		$user->wallet = $wallet;

		//set permissions [active, send, receive ]
		$this->multichain->grantCustom($wallet, 'activate,send,receive');

		$user->save();

		return $user;
	}


	//get username, password
	//TODO token based auth
	public function login(){
		$data = Request::all();

		$user = User::where('email',$data['email'])
			->where('password',sha1($data['password']))->first();


		//TODO scadenza token


		if($user){
			$user->remember_token = md5(uniqid($user->email.$user->password.rand() , true));
			$user->save();

			return json_encode(array("token" => $user->remember_token));

		}else{
			return -1; //TODO autenticazione fallita
		}

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


	public function test(){
		dd('ciao');
	}



}
