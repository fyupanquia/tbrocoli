<?php
/**
* Yupanqui Allcca Frank
*/
require_once($_SERVER['DOCUMENT_ROOT'].'/app/global.php');

class BDPDO
{
	
	function __construct()
	{
		
	}

	static function connection($server = null , $dbname = null, $user = null, $password = null){
		$SERVER   = ($server) ? $server : DB_SERVER;
	    $DBNAME   = ($dbname) ? $dbname : DB_NAME;
	    $USER     = ($user) ? $user : DB_USER;
	    $PASSWORD = ($password) ? $password : DB_PASSWORD;

	    $pdo = null;

	    try {
	        $pdo = new PDO("mysql:host={$SERVER};dbname={$DBNAME}", $USER, $PASSWORD);
	    } catch (PDOException $e) {
	        die($e->getMessage());
	    }

	    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	    return $pdo;
	}

	static function isPDO($arg = null){
		return ($arg instanceof PDO);
	}

	static function basicEval($query,$fields,$pdo){		

		return ( is_array($fields) || is_null($fields) ) && self::isPDO($pdo);
	}

	static function _fetch($query,$fields = null ,&$pdo=null){
    	$stmt       = null;
	    $pdo 		= (  self::isPDO($pdo) ) ?  $pdo : self::connection() ;
	    
	    if( self::basicEval($query,$fields,$pdo)   ){
	    
	    		try {
			        $stmt = $pdo->prepare($query);
			        $stmt->execute($fields);

			    } catch (PDOException $e) {
			    	self::gc($pdo,$query);
			        die($e->getMessage());
			    }
	    }

	    self::gc($pdo,$query);	// don't put $stmt 

	    return $stmt;
	}

	/**
	* Anti Injection Method
	*
	* @return array query result
	* @param Query string database query
	* @param fields array params
	* @param pdo object PDO connection
	**/
	static function fetchArr($Query,$fields = null ,&$pdo=null) {
		$rsp  = null;
		$stmt = self::_fetch($Query,$fields,$pdo);

		if($stmt){
			$rsp = $stmt->fetch(PDO::FETCH_ASSOC);
			$rsp = ($rsp) ? $rsp : null;
		}

		self::gc($pdo,$stmt);

	    return $rsp;
	}

	/**
	* Anti Injection Method
	*
	* @return object query result
	* @param Query string database query
	* @param fields array params
	* @param pdo object PDO connection
	**/
	static function fetchObj($Query,$fields = null ,&$pdo=null) {
	    $rsp  = null;
		$stmt = self::_fetch($Query,$fields,$pdo);

		if($stmt){
			$rsp = $stmt->fetchObject();
			$rsp = ($rsp) ? $rsp : null;
		}

		self::gc($pdo,$stmt);

	    return $rsp;
	}

	/**
	* Anti Injection Method
	*
	* @return Array Arrays query result
	* @param Query string database query
	* @param fields array params
	* @param pdo object PDO connection
	**/
	static function fetchAllArr($Query,$fields = null ,&$pdo=null) {
		$rsp  = null;
		$stmt = self::_fetch($Query,$fields,$pdo);
		if($stmt){
			$rsp = [];
		    while ($arr = $stmt->fetch(PDO::FETCH_ASSOC) ) {
		        $rsp[] = $arr;
		    }
		}
		
		self::gc($pdo,$stmt);

	    return $rsp;
	}
	/**
	* Anti Injection Method
	*
	* @return Array Objects query result
	* @param Query string database query
	* @param fields array params
	* @param pdo object PDO connection
	**/
	static function fetchAllObj($Query,$fields = null ,&$pdo=null) {
		$rsp  = null;
		$stmt = self::_fetch($Query,$fields,$pdo);
		if($stmt){
			$rsp = [];
		    while ($obj = $stmt->fetchObject() ) {
		        $rsp[] = $obj;
		    }
		}
		
		self::gc($pdo,$stmt);

	    return $rsp;
	}
	
	static function parseDataFilter($data,&$pdo){
    	$values = [] ;

    	try {
    		foreach ($data as $key => $value) {
				//$names[] = (string) $key;
				
				$valor = $pdo->quote($value);
				$values[] = is_int($valor) ? $valor : "$key = :$key";
			}
    	} catch (Exception $e) {
    		self::gc($pdo,$stmt);
    	}

		return $values;
    }

	/**
	* Anti Injection Method UPDATE
	* @param $tabla string: Nombre de tabla
	* @param $data array: Columnas y valores a actualizar
	* @param $where array: Columnas y valores de filtro
	* @param pdo object PDO connection
	**/	
	
	static function update($tabla, array $data, array $where, &$pdo = null) {
    	$pdo 		= ( self::isPDO($pdo) ) ?  $pdo : self::connection() ;
		$whereArray = $setArray = array();
		$whereString = $setString = '';

		$tabla = (string) $tabla;
		$where = (array) $where;

		$rsp  = false;

		if (!empty($tabla) && !empty($data) && !empty($where)) {

			$setArray   = self::parseDataFilter($data, $pdo);
			$whereArray = self::parseDataFilter($where, $pdo);

			$setString   = implode(', ', $setArray);
			$whereString = implode(' AND ', $whereArray);
			
            $sql = "UPDATE $tabla SET $setString WHERE $whereString";
            $query = $pdo->prepare($sql);
			
			try {
				
				foreach ($data as $name => &$value) {
					$value = ($value==null) ?  "" : $value ;
					$query->bindParam( ":".$name, $value);
				}	
				foreach ($where as $name => &$value) {
					$value = ($value==null) ?  "" : $value ;
					$query->bindParam( ":".$name, $value);
				}

				$rsp = $query->execute();	
				
			} catch (PDOException $e) {
				self::gc($pdo,$query);
				die($e->getMessage());
			}   

		}

		self::gc($pdo,$query);

	    return $rsp;
    }

	/**
	* Anti Injection Method INSERT
	* @param $data array: Columnas y valores a guardar en la tabla
	* @param pdo object PDO connection
	**/	
		
	static function insert($tabla, array $data, &$pdo = null) {
    	$pdo   = ( self::isPDO($pdo) ) ?  $pdo : self::connection() ;

		$values = array();
		$query  = null;
		$tabla  = (string) $tabla;
		$data   = (array) $data;
		$return = array('success' => false, 'lastInsertId' => 0);

		if (!empty($tabla) && !empty($data)) {

			$values = self::parseDataFilter($data,$pdo);

			$valuesString = implode(', ', $values);
            
				$sql = "INSERT INTO $tabla SET $valuesString ";
				$query = $pdo->prepare($sql);

				try {
					
					foreach ($data as $name => &$value) {
						$value = ($value==null) ?  "" : $value ;
						$query->bindParam( ":".$name, $value);
					}	
					
					$return['success'] = $query->execute();
					$return['lastInsertId'] = $pdo->lastInsertId(); 

				} catch (PDOException $e) {
					self::gc($pdo,$query);
					die($e->getMessage());
					print "Error!: " . $e->getMessage() . "</br>"; 
				}   
		}

		self::gc($pdo,$query);

		return $return;
    }	


	/**
	* Anti Injection Method DELETE
	* @param $tabla string : nombre de la tabla
	* @param $data array: Columnas y valores para el where
	* @param pdo object PDO connection
	**/	
	static function delete($tabla, array $data, &$pdo = null) {

    	$pdo   = ( self::isPDO($pdo) ) ?  $pdo : self::connection();
		$names = $values = array();
		$tabla = (string) $tabla;
		$data   = (array) $data;
		$query  = null;
		$return = array('success' => false, 'lastInsertId' => 0);

		if (!empty($tabla) && !empty($data)) {
			
			$values = self::parseDataFilter($data,$pdo);

			$whereString = implode(' AND ', $values);
				
				$sql = "DELETE FROM $tabla WHERE $whereString ";
				$query = $pdo->prepare($sql);

				try {
					
					foreach ($data as $name => &$value) {
						$value = ($value==null) ?  "" : $value ;
						$query->bindParam( ":".$name, $value);
					}	
					
					$return = $query->execute();

				} catch (PDOException $e) {
					self::gc($pdo,$query);
					die($e->getMessage());
					print "Error!: " . $e->getMessage() . "</br>"; 
				}   
		}
		self::gc($pdo,$query);
		return $return;
    }		

	/** 
	* Garbage Collector
	* Connections & Statements Cleaner
	* @param $connection PDO Connection
	* @param $statement PDO statement
	**/	
    static function gc(&$connection,&$statement){
    		$connection = null;
    		$statement  = null;
    }

}