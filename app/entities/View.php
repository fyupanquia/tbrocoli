<?php
/**
* 
*/
class View
{
	
	function __construct($_pdo)
	{
		
	}

	static function render($nameview,$data = null){
		$contenido = "";

		$route = $_SERVER['DOCUMENT_ROOT']."/resources/views/$nameview.phtml";

		if(file_exists($route)){
			ob_start();   	 
		    if(is_array($data) )extract($data, EXTR_OVERWRITE);
		    
		    include_once $route;
		    $contenido = ob_get_contents();
		    ob_get_clean();
		}
		return $contenido;
	}
}