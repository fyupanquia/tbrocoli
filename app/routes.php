<?php
require_once($_SERVER['DOCUMENT_ROOT']."/app/app.php");

// GET or POST parameters
$service  = isset($_REQUEST["service"]) ? $_REQUEST["service"] : null;
$method   = isset($_REQUEST["method"])  ? $_REQUEST["method"] : null;

// entities path
$service = ucwords($service);
$service = empty($service) ? "AppController"   : $service."Controller" ;
$method =  empty($method)  ? "index" : $method ;

$ruta = $_SERVER['DOCUMENT_ROOT'].'/app/controllers/'.$service.'.php';

// Check if filex exists
if( !file_exists($ruta) ){

	echo json_encode(["success"=>false,"msg"=>"Unidentified Service"]);

}else{
	require_once($ruta);
	// new object instance
	$s 					= new $service();
	$variableMetodo 	= array($s,$method);

	if(is_callable($variableMetodo))
		echo json_encode($s->$method()) ;
	else
		echo json_encode(["success"=>false,"msg"=>"Unidentified method"]) ;
}

exit();