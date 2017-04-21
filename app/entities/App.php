<?php

require_once( $_SERVER['DOCUMENT_ROOT'].'/app/entities/Template.php' );
/**
* 
*/
class App 
{
	
	function __construct()
	{
		# code...
	}

	function index(){
		$body = "<!-- Banner -->
					<section id='banner'>
						<div class='inner'>
						</div>
						<!--a href='#one' class='more scrolly'>Learn More</a-->
					</section>";

		$this->render( (object) compact("body") );
	}

	function signup(){

		$body = "<section id='two' class='wrapper alt style2'>
						<section class='spotlight'>
							<div class='image'><img src='imgs/banners/nike.jpg' alt='' /></div><div class='content'>
								<h2>Registro de Usuarios</h2>
								<form method='POST' action='/services' >
									<input type='hidden' name='service' value='UserController' />
									<input type='hidden' name='method' value='register' />

									<div class='field' ><input type='text' name='fullname' placeholder='Nombre Completo' required/></div>
									<div class='field' ><input type='email'     name='email' placeholder='Correo Electrónico' required/></div>
									<div class='field' ><input type='password' name='password' placeholder='Contraseña' required /></div>

									<button type='submit'>Registrar</button>
								</form>
							</div>
						</section>
					</section>";

		$this->render( (object) compact("body") );
	}

	function render($obj){

		$t = new Template;
		$html = $t->render( $obj );

		echo $html;
		exit();
	}
}