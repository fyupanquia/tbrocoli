<?php
/**
* 
*/
class File
{
	
	function __construct()
	{
		
	}

	static function render($path,$data){
		ob_start();   	 
	    if(is_array($data) )extract($data, EXTR_OVERWRITE);
	    
	    include_once $path;
	    $contenido = ob_get_contents();
	    ob_get_clean();
	    return $contenido;
	}


	static function upload($arr){
	/**
	 * Upload a file
	 *
	 * This method is used to retrieve the account corresponding
	 * to a given login. <b>Note:</b> it is not required that
	 * the user be currently logged in.
	 *
	 * @access public
	 * @param string $arr ["folder"=>"./imgs/","max_size"=>30000,"types"=>["image/jpeg","image/png","image/gif"],"file"=>$_FILES[?],"filename"=>"filename"]
	 * @return array ["message"=>?,"succces"=>bool,"data"=>?]
	 */

		$rsp = ["message"=>"No se logrò procesar la subida del archivo.","success"=>false,"data"=>null];

		if( is_array($arr) ){

			if( isset($arr["types"]) &&  isset($arr["file"]) && isset($arr["types"])){


				$types 			= $arr["types"];
		    	$max_size 		= ( isset($arr["max_size"]) ) ? $arr["max_size"] : 1000000;
				$upload_folder  = ( isset($arr["folder"]) ) ? $arr["folder"] : "";
				$file 			= $arr["file"];
				$extension 		= strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
				$filename       = ( isset($arr["filename"]) ) ? trim($arr["filename"].".".$extension) : trim($file['name']) ;

				$typefile       = $file['type'];
				$sizefile 		= $file['size'];
				$tmpfile	    = $file['tmp_name'];
				$path 			= $upload_folder . $filename;
				
				$data 		 	= $file;
				$data["ext"] 	= $extension;

				if (!file_exists($upload_folder))  mkdir($upload_folder,0777,true);
				
				if(file_exists($path)) unlink($path);

				if($sizefile<$max_size){
					if(in_array($typefile, $types)){

						if (!move_uploaded_file($tmpfile, $path))
							$rsp["message"] = "No fue posible subir este archivo .Inténtelo otra vez por favor."; 
						else{
							$rsp = ["success"=>true,"data"=>$data,"message"=>"Archivo cargado correctamente."];
						}
					}else{
						$rsp["message"] = "Sólo se aceptan archivos de tipo (png,jpg,gif)"; 
					}
				}else{
					$rsp["message"] = "El archivo debe pesar menor o igual a ". ($max_size/1000000) ." MB"; 
				}


			}

		}

		return $rsp;
	}
}