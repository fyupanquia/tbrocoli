<?php
/**
* 
*/
class User extends Entity
{
	static $table = "users";
	const  TABLE  = "users";
	static $maxEdad = 70;
	static $minEdad = 10;

	function __construct()
	{

	}
	
	static function all(){
		return parent::all(self::$table);
	}

	static function where($arg1=null,$arg2 = null){
		return parent::where(self::$table,$arg1,$arg2);
	}

}