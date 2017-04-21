<?php

/**
* 
*/
class UserNetworkController
{
	private $user = null;
	function __construct()
	{
		$this->user = Session::user();
	}
	function save(){

		if($this->user){

			$idnetwork = post("idnetwork");
			$iduser    = $this->user->id;
			$url       = post("url");
			$main      = post("main");
			$id        = post("id"); // id UserNetwork
			$network   = null;		// if is update , it would be a object

			if( !empty($id) ) $network   = BDPDO::fetchObj("SELECT * FROM ".UserNetwork::TABLE." WHERE id=:id AND iduser=:iduser",compact("id","iduser")); // query if exists usernetwork
			
				if( (Network::where(["id"=>$idnetwork])) || $network ){

					if( strlen($url)>=1 && strlen($url)<=50 ){
						
						if($main=="1") BDPDO::update(UserNetwork::TABLE,["main"=>"0"],["iduser"=>$this->user->id]);

						if($id){

								BDPDO::update(UserNetwork::TABLE,compact("url","main"),compact("id"));

						}else{

								BDPDO::insert(UserNetwork::TABLE,compact("idnetwork","url","main","iduser"));

						}

						Session::flash(["UserNetworkController-crud", 
										json_encode(["message"=>"Red social guardada correctamente.","type"=>"success"]) ]);
					}else
						Session::flash(["UserNetworkController-crud", 
								json_encode(["message"=>"Ingrese correctamente la url de su red social.","type"=>"warning"]) ]);

				}else
					Session::flash(["UserNetworkController-crud", 
								json_encode(["message"=>"Seleccione una red social.","type"=>"warning"]) ]);



			header('Location: /networks');
			exit;
		}
		header('Location: /');
		exit;
	}

	function delete(){
		if($this->user){
			$id        = post("id"); // id UserNetwork
			$iduser    = $this->user->id;

			if( !empty($id) ) $network   = BDPDO::fetchObj("SELECT * FROM ".UserNetwork::TABLE." WHERE id=:id AND iduser=:iduser",compact("id","iduser")); // query if exists 

			if($network){
				Session::flash(["UserNetworkController-crud", 
								json_encode(["message"=>"Datos eliminados correctamente.","type"=>"success"]) ]);

				BDPDO::delete(UserNetwork::TABLE,compact("id"));
			}else
				Session::flash(["UserNetworkController-crud", 
								json_encode(["message"=>"Ha surgido un error. Inténtelo más tarde.","type"=>"error"]) ]);

			header('Location: /networks');
			exit;
		}

		header('Location: /');
		exit;
	}
}