<?php
/**
* 
*/
class Crypt
{
	
	function __construct()
	{
		# code...
	}

	function set($str){
		return crypt($str, '$2a$09$tARm1a9A9N7q1W9T9n5LqR$');
	}
}