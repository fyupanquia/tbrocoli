<?php
/**
* 
*/
class TemplateController
{
	
	function __construct()
	{
		
	}

	function html(){
		return "<!DOCTYPE HTML>
				<html>
					<head>
						:head
					</head>
					<body class='landing' >

						<div>
							<!-- Page Wrapper -->
							<div id='page-wrapper'>

								<!-- Header -->
								<header id='header' class='alt'>
									:_header
								</header>

								:body

								<!-- Footer -->
								<footer id='footer'>
									:footer
								<footer>
							</div>

							<!-- Scripts -->
							<div class='scripts'>
								:scripts
								<script>
									$(document).ready(function(){
										:personal-script
									})
								</script>
							</div>
						</div>
						<div id='global-popup' class='popup invisible' >
							<div class='sub-content-popup' >
								:popup
							</div>
						</div>

					</body>
				</html>";
	}
	
	function render($obj){
		$html = "";

		if( is_object($obj) ){

			$head    = $this->head();
			$header  = $this->header();
			$footer  = $this->footer();
			$body    = $obj->body;
			$popup   = (isset($obj->popup)) ? $obj->popup : null;
			$scripts  = $this->scripts();
			$personalScript = (isset($obj->script)) ? $obj->script : "";

			$html    = $this->html();

			$html = str_replace(":head", $head, $html);
			$html = str_replace(":_header", $header, $html);
			$html = str_replace(":footer", $footer, $html);
			$html = str_replace(":body", $body, $html);
			$html = str_replace(":popup", $popup, $html);
			$html = str_replace(":personal-script", $personalScript, $html);
			$html = str_replace(":scripts", $scripts, $html);
		}

		return $html;
	}
	function header(){

		$user = Session::user();

		$profile = "" ; $menu = "<ul>";

		if($user){

			$avatar = ( empty($user->avatar) ) ? "/imgs/default/user.png" : "/imgs/users/$user->id/$user->avatar";
			
			$profile = "<div class='content-float-profile' >
						<img src='$avatar' />
						<label>$user->fullname</label>
						</div>";

			$menu .= "<li><a href='/'>Inicio</a></li>
					 <li><a href='/profile'>Mi Perfil</a></li>
					 <li><a href='/networks'>Redes Sociales</a></li>
					 <li><a href='/password'>Cambiar Contraseña</a></li>
					 <li>
						 <form method='POST' action='/services' >
						 	<input name='service' type='hidden' value='User' />
						 	<input name='method' type='hidden' value='logout' />
						 	<button type='submit'>Cerrar Sesión</button>
						 </form>
					 </li>";
		}else{
			$menu .= "<li><a href='/'>Inicio</a></li>
					 <li><a href='/signup'>Registrar</a></li>
					 <li><a href='/login'>Iniciar Sesión</a></li>";
		}
		$menu .= "</ul>";

		return "<h1><a href='index.html'>🔥Team Brocoli❤🔥</a></h1>
				<nav id='nav'>
					<ul>
						<li class='special'>
							<a href='#menu' class='menuToggle'><span>Menu</span></a>
							<div id='menu'>
								$profile
								$menu
							</div>
						</li>
					</ul>
				</nav>";
	}
	function head(){
		return "<title>Team Brocoli</title>
				<meta charset='utf-8' />
				<meta name='viewport' content='width=device-width, initial-scale=1' />
				<!--[if lte IE 8]><script src='/js/ie/html5shiv.js'></script><![endif]-->
				<link rel='stylesheet' href='/css/main.css' />
				<link rel='stylesheet' href='/css/app.css' />
				<link rel='stylesheet' href='/css/flags.css' />
				<!--[if lte IE 8]><link rel='stylesheet' href='/css/ie8.css' /><![endif]-->
				<!--[if lte IE 9]><link rel='stylesheet' href='/css/ie9.css' /><![endif]-->
				<link rel='icon' href='/imgs/ico/favicon.ico' type='image/x-icon'/>";
	}
	function footer(){
		return "<ul class='icons'>
					<!--li><a href='#' class='icon fa-twitter'><span class='label'>Twitter</span></a></li-->
					<li><a href='https://www.facebook.com/groups/1755585974720950/'  target='_blank' class='icon fa-facebook'><span class='label'>Facebook</span></a></li>
					<!--li><a href='#' class='icon fa-instagram'><span class='label'>Instagram</span></a></li-->
					<!--li><a href='#' class='icon fa-dribbble'><span class='label'>Dribbble</span></a></li-->
					<!--li><a href='#' class='icon fa-envelope-o'><span class='label'>Email</span></a></li-->
				</ul>
				<ul class='copyright'>
					<li>&copy; 🔥Team Brocoli❤🔥</li><li>Design: <a href='https://www.facebook.com/fyupanquia0' target='_blank' >CMB</a></li>
				</ul>";
	}
	function scripts(){
		return "<script src='/js/jquery.min.js'></script>
				<script src='/js/jquery.scrollex.min.js'></script>
				<script src='/js/jquery.scrolly.min.js'></script>
				<script src='/js/skel.min.js'></script>
				<script src='/js/util.js'></script>
				<!--[if lte IE 8]><script src='/js/ie/respond.min.js'></script><![endif]-->
				<script src='/js/main.js'></script>
				<script src='/js/app.js'></script>";
	}
}