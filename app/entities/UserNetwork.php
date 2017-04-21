<?php
/**
* 
*/
class UserNetwork extends Entity
{
	const  TABLE  = "user_networks";
	
	function __construct()
	{
		# code...
	}
	static function all(){
		return parent::all(self::TABLE);
	}

	static function where($arg1=null,$arg2 = null){
		return parent::where(self::TABLE,$arg1,$arg2);
	}
}