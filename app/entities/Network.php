<?php
/**
* 
*/
class Network extends Entity
{
	static $table = "networks";
	
	function __construct()
	{
		# code...
	}
	static function all($arg1 = null, $arg2 = null){
		return parent::all(self::$table);
	}

	static function where($arg1=null,$arg2 = null,$arg3 = null){
		return parent::where(self::$table,$arg1,$arg2);
	}
}