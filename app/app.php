<?php
// global settings
error_reporting(E_ERROR);
//error_reporting(E_ALL ^ E_NOTICE);
// SET TIMEZONE
date_default_timezone_set('America/Lima');
session_start();


spl_autoload_register(function($class){
        require_once($_SERVER['DOCUMENT_ROOT']."/app/entities/".$class.".php");
});

// IMPORTANT VAR 
if(! Session::id()  ) Session::id( session_id() ) ; // global SESSIONID

function vd($str){
	echo "<pre>";
	var_dump($str);
	echo "</pre>";
}

function we($str = null){
	echo $str;
	exit();
}

function post($index){
	$str = "";

	if(isset($_POST[$index])){
		if( is_array($_POST[$index]) ){

			$new = [];
			foreach ($_POST[$index] as $key => $value) {
				$new[$key] = trim(AntiXSS::setFilter( $value , "white", "everything"));
			}

			$str = $new;

		}else
			$str = trim( AntiXSS::setFilter( $_POST[$index] , "white", "everything") );
		
	}

	
	return $str;		
}


function postInteger($index){
	$str = "";

	if(isset($_POST[$index])){
		if( is_array($_POST[$index]) ){

			$new = [];
			foreach ($_POST[$index] as $key => $value) {
				$new[$key] = intval( trim( strip_tags(AntiXSS::setFilter( $value , "white", "everything") ) ) );
			}

			$str = $new;

		}else
			$str = intval( trim( strip_tags( AntiXSS::setFilter( $_POST[$index] , "white", "everything") ) ) );
		
	}

	
	return $str;		
}


function get($index){

	 return trim( AntiXSS::setFilter( $_GET[$index] , "white", "everything") );

}


function rd($path = ""){

	if(!empty($path)){
		header('Location: '.$path);
		exit;
	}

}

function domain(){
	return (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" ;
}