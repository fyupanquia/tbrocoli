<?php
/**
* 
*/
class Country extends Entity 
{
	static $table = "countries";

	function __construct()
	{
		# code...
	}

	
	static function all($arg = null, $arg2 = null){
		return parent::all(self::$table,$arg);
	}

	static function where($arg1=null, $arg2 = null, $arg3 = null){
		return parent::where(self::$table,$arg1,$arg2);
	}
	
}