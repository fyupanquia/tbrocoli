<?php
/**
* 
*/
class Entity
{

	function __construct()
	{
		
	}

	static function all($table = null,$arg = null){
		
		if($table) $s = BDPDO::fetchAllObj("SELECT * FROM ". $table ." ".$arg);
		else $s = null;

		return $s;
	}

	static function where($table = null,$arg1 = null,$arg2 = null){
		$r = null;
		if($table!= null){
			$rfields = " WHERE ";
			$fields  = null;

			if(is_array($arg1)){
				$rfields .= self::rfields($arg1);
				if(is_array($arg1))  $fields   = $arg1;

			}else if(is_string($arg1)){
				$rfields .= $arg1;

				if(is_array($arg2)) $fields   = $arg2;
			}
			
			$r = BDPDO::fetchObj("SELECT * FROM ". $table ."  ".$rfields ,$fields);
		}
		return $r;
	}
	static function rfields($fields = null){
		$s = "";

		if($fields){
			foreach ($fields as $field => $value) $s .= " $field = :$field AND";
			$s = rtrim($s,"AND");
		}

		return $s;
	}
}