<?php
/**
* 
*/
class Session
{
	
	function __construct()
	{
		# code...
	}

	static function id($id = null){

		if($id) $_SESSION["team_broccoli_session_id"] = $id ;
		else if($id === false) unset($_SESSION["team_broccoli_session_id"]);
		else if($id === null ) 
			return isset($_SESSION["team_broccoli_session_id"]) ? $_SESSION["team_broccoli_session_id"] : null;

	}

	static function user($user = null){
		
		if($user) $_SESSION["user-session"] = json_encode($user);
		else if($user === false) unset($_SESSION["user-session"]);
		else if($user === null ) 
		return isset($_SESSION["user-session"]) ? json_decode($_SESSION["user-session"]) : null;

	}

	static function users($users = null){
		
		if($users) $_SESSION["users-session"] = json_encode($users);
		else if($users === false) unset($_SESSION["users-session"]);
		else if($users === null ) 
		return isset($_SESSION["users-session"]) ? json_decode($_SESSION["users-session"]) : null;

	}


	static function paginator($name,$data = null){
		
		if($data) $_SESSION["paginator-".$name."-session"] = json_encode($data);
		else if($data === false) unset($_SESSION["paginator-".$name."-session"]);
		else if($data === null ) 
		return isset($_SESSION["paginator-".$name."-session"]) ? json_decode($_SESSION["paginator-".$name."-session"]) : null;

	}

	static function flash($arg = null){
		$r = null;

		if(is_array($arg)){
			if( count($arg) == 2){
				self::clearFlash();

				$_SESSION[ $arg[0] ] = $arg[1];
				$_SESSION["flash"] = $arg[0];
			}
		}else if(is_string($arg)){
			$r = isset($_SESSION[ $arg ]) ? $_SESSION[ $arg ] : null;

			self::clearFlash();
		}
		
		return $r;
	}

	static function clearFlash(){
		if( isset($_SESSION["flash"]) ) unset( $_SESSION[ $_SESSION["flash"] ] );
	}
}