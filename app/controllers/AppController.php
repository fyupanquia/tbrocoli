<?php

require_once( $_SERVER['DOCUMENT_ROOT'].'/app/controllers/TemplateController.php' );
require_once( $_SERVER['DOCUMENT_ROOT'].'/app/controllers/UserController.php' );
/**
* 
*/
class AppController  
{
	private $user = null;

	function __construct()
	{
		$this->user = Session::user();
	}

	function index(){
		
		$body = "<!-- Banner -->
					<section id='banner'>
						<div class='inner'>
						</div>
						<!--a href='#one' class='more scrolly'>Learn More</a-->
					</section>";

		if( $this->user ){
			$users     = BDPDO::fetchAllObj("SELECT u.id iduser,u.fullname,u.alias auser,c.id idcountry,c.name country,c.alias acountry,u.avatar,u.alias FROM users u 
											 LEFT JOIN countries c ON u.idcountry= c.id
											 WHERE u.state=1 ORDER BY u.id DESC");
			
			Session::users($users);

			$countries 	= Country::all();
			$total 		= count($users);

			$header = "	
						<section class='panel-control' >
							<div>Brocolis registrados : $total </div>
							<ul class='control-buttons'>
								<li> <button type='button' onClick='brocolis.psearch()' >BUSCAR</button> </li>
								<li> <button type='button' onClick='brocolis.reset()' >RESTAURAR</button> </li>
								<li> <button type='button' onClick='brocolis.reverse()'>INVERTIR</button> </li>
							</ul>
						</section>
						";

			$options = "";
			if($countries){
				$options = "<select name='idcountry' >
							<option value='0'>--Paìs--</option>";

				foreach ($countries as $key => $country) 
					$options .= "<option value='$country->id'>$country->name</option>";

				$options .= "</select>";
			}

			$header .= "<section id='s-users-list' class='wrapper alt style2 invisible'>
							<section class='spotlight'>
								<div class='image'><img src='imgs/banners/search.png' alt='' /></div><div class='content'>
									<h2>BUSCAR</h2>
									<form name='search-brocoli'>
										<div class='field' ><input type='text' placeholder='Nombre o Alias' name='nalias'/></div>
										<div class='field' >
											$options
										</div>
									</form>
								</div>
							</section>
						</section>";

			$uc   = new UserController();

			if($users) $list = $uc->paginator(1,$users);
			else $list = ["list"=>"No se encontraron resultados."];

			$list = $list['list'];

			$list = "<div class='content-list'> $list </div>";

			$body .= $header.$list;
		}

		$this->render( (object) compact("body") );
	}

	function profile(){

		if($this->user){
			$user      = $this->user;
			$countries = Country::all(" ORDER BY name ASC ");

			$message = ""; $class = "";
			
			$obj = Session::flash("UserController-update");

			if($obj){
				$obj = json_decode($obj);
				$message = $obj->message;
				$class   = $obj->type;
			}

			$oedad = "<select name='edad' >
						<option value='0'>--Edad--</option>";

			for ($i= User::$minEdad ; $i < User::$maxEdad ; $i++){

				if($user->edad == $i) $fielde = "selected";
				else $fielde = "";

				$oedad .= "<option value='$i' $fielde >$i</option>";
			}

			$oedad .= "</select>";

			

			if($countries){
				$ocountry = "<select name='idcountry' >
							<option value='0'>--Paìs--</option>";

				foreach ($countries as $key => $country) {
					if($user->idcountry == $country->id) $fieldc = "selected";
					else $fieldc = "";

					$ocountry .= "<option value='$country->id' $fieldc >$country->name</option>";
				}

				$ocountry .= "</select>";
			}

			$avatar = ( empty($user->avatar) ) ? "/imgs/default/user.png" : "/imgs/users/$user->id/$user->avatar";

			$body = "<section id='s-profile' class='wrapper alt style2'>
							<section class='spotlight'>
								<div class='image'><img src='imgs/banners/profile.jpg' alt='' /></div><div class='content'>
									<h2>DATOS PERSONALES</h2>
									
									<form name='profile' method='POST' action='/services' >
										<b name='message' class='$class' >$message</b>
										<input type='hidden' name='service' value='User' />
										<input type='hidden' name='method'  value='update' />

										<div class='field content-img' >
											
											<div class='profile-border' >
												<img name='preview' src='$avatar' onClick='showUpload()'>
												<div class='panel-message' onClick='showUpload()' >Cambiar imagen</div>
											</div>
											
											<input type='file' style='display: none;' name='avatar' id='avatar' onchange=\"app.upload('avatar');\">
											<script>
												function showUpload(){
													$('form[name=profile] #avatar').click();
												}
											</script>
										</div>
										<div class='field' ><input type='text' name='fullname' placeholder='Nombre Completo' maxlength='100' value='$user->fullname' required/></div>
										<div class='field' ><input type='text' name='alias' placeholder='Alias' maxlength='50' value='$user->alias' /></div>
										<div class='field' ><input type='text' name='cel' placeholder='Celular' maxlength='50' value='$user->cel' /></div>
										<!--div class='field' ><input type='text' name='phone' placeholder='Teléfono' maxlength='50' value='$user->phone' /></div-->
										<div class='field' >$oedad</div>
										<div class='field'> $ocountry</div>

										<button type='submit'>Guardar</button>
									</form>
								</div>
							</section>
						</section>";

			$this->render( (object) compact("body") );
		}else
			header('Location: /');


	}

	function successConfirmation(){

		if(!$this->user){
			
			$message = ""; $class = "";
			
			$obj = Session::flash("UserController-confirm");

			if($obj){
				$obj = json_decode($obj);
				$message = $obj->message;
				$class   = $obj->type;
			}

			$body = "<section id='s-confirm' class='wrapper alt style2'>
							<section class='spotlight'>
								<div class='image'><img src='/imgs/banners/profile.jpg' alt='' /></div>
								<div class='content'>
									<p class='$class' >$message</p>
									<a href='/login' target='_self' class='button' >INICIAR SESIÓN</a>
								<div>
							</section>
						</section>";

			$this->render( (object) compact("body") );
		}else
			header('Location: /');


	}


	function show(){

		if($this->user){
			$user = $this->user;
			$message = ""; $class = ""; $iduser = $user->id;
			
			$obj = Session::flash("UserController-update");

			if($obj){
				$obj = json_decode($obj);
				$message = $obj->message;
				$class   = $obj->type;
			}

			$country = Country::where(["id"=>$user->idcountry]);

			$rs      = BDPDO::fetchAllObj("SELECT n.name,n.url nurl,un.url unurl FROM user_networks un 
										   INNER JOIN networks n ON un.idnetwork = n.id 
										   WHERE un.iduser = :iduser ",compact("iduser"));
			$htmlNetworks = "";

			if($rs){
				foreach ($rs as $key => $rss) {
					$url = $rss->nurl.$rss->unurl;
					$htmlNetworks .= "<div class='field' ><b>".ucwords($rss->name)." :</b> <a href='$url' target='_blank' >$url</a>  </div>";
				}
			}

			$htmlCountry = ($country) ? "<i class='flag flag-".$country->alias."' alt='$country->name'></i>" : "";

			$avatar = ( empty($user->avatar) ) ? "/imgs/default/user.png" : "/imgs/users/$user->id/$user->avatar";

			$body = "<section id='s-show' class='wrapper alt style2'>
							<section class='spotlight'>
								<div class='image'><img src='/imgs/banners/profile.jpg' alt='' /></div><div class='content'>
									<h2>$user->fullname $htmlCountry </h2>
									
									<div class='field content-img' >
										<div class='profile-border' >
											<img name='preview' src='$avatar' >
										</div>
									</div>
									<div class='field' ><b >Alias : </b>$user->alias</div>
									<div class='field' ><b >Celular : </b>$user->cel</div>
									<div class='field' ><b>Edad : </b>$user->edad</div>
									$htmlNetworks
							</section>
						</section>";

			$this->render( (object) compact("body") );
		}else
			header('Location: /');


	}

	function signup(){

		if(!$this->user){
			$obj = Session::flash("UserController-register");
			$message = ""; $class = "";

			if($obj){
				$obj = json_decode($obj);
				$message = $obj->message;
				$class   = $obj->type;
			}

			$body = "<section id='two' class='wrapper alt style2'>
							<section class='spotlight'>
								<div class='image'><img src='imgs/banners/nike.jpg' alt='' /></div><div class='content'>
									<h2>REGISTRAR</h2>
									<p class='$class' >$message</p>
									<form method='POST' action='/services' >
										<input type='hidden' name='service' value='User' />
										<input type='hidden' name='method' value='register' />

										<div class='field' ><input type='text' name='fullname' placeholder='Nombre Completo' maxlength='100' required/></div>
										<div class='field' ><input type='email'     name='email' placeholder='Correo Electrónico' maxlength='80'  required/></div>
										<div class='field' ><input type='password' name='password' placeholder='Contraseña' maxlength='20' required /></div>

										<button type='submit'>Registrar</button>
									</form>
								</div>
							</section>
						</section>";

			$this->render( (object) compact("body") );
		}else
			header('Location: /');

	}

	function login(){

		if(!$this->user){

			$obj = Session::flash("UserController-login");
			$message = ""; $class = "";

			if($obj){
				$obj = json_decode($obj);
				$message = $obj->message;
				$class   = $obj->type;
			}

			$body = "<section id='two' class='wrapper alt style2'>
							<section class='spotlight'>
								<div class='image'><img src='imgs/banners/team.jpg' alt='' /></div><div class='content'>
									<h2>INICIAR SESIÓN</h2>
									<p class='$class' >$message</p>
									<form method='POST' action='/services' >
										<input type='hidden' name='service' value='User' />
										<input type='hidden' name='method' value='login' />

										<div class='field' ><input type='email'     name='email' placeholder='Correo Electrónico' maxlength='80'  required/></div>
										<div class='field' ><input type='password' name='password' placeholder='Contraseña' maxlength='20' required /></div>

										<button type='submit'>Iniciar Sesión</button>
									</form>
								</div>
							</section>
						</section>";

			$this->render( (object) compact("body") );

		}else
			header('Location: /');

	}

	function password(){

		if($this->user){

			$obj = Session::flash("UserController-password");
			$message = ""; $class = "";$success="";$popup="";$script="";

			if($obj){
				$obj = json_decode($obj);
				$message = $obj->message;
				$class   = $obj->type;
				$success   = $obj->success;
			}

			if($success){
				$popup = "<div class='body-popup'>
							<div class='confirm-action'>
								<label> Su contraseña ha sido cambiada correctamente ¿Qué desea realizar?</label>
								<form name='confirm-change-password-action' method='POST' action='/services' >
									<input name='service' value='user' type='hidden' />
									<input name='method' value='logout' type='hidden' />
									<div class='field' >  <input type='radio' id='stay' name='action-change-password' value='stay' checked> <label for='stay' >Continuar Sesión</label> </div>
									<div class='field' >  <input type='radio' id='logout' name='action-change-password' value='logout'>  <label for='logout' >Volver Iniciar Sesión</label> </div>
									<button type='button' >Aceptar</button>
								</form>

								<script>
									$('form[name=confirm-change-password-action] button').click(function(){
										var action = $('form[name=confirm-change-password-action] input[name=action-change-password]:checked').val()
										console.log(action);

										if(action=='logout'){
											$(this).parent().submit();
										}else{
											app.popup();
										}
									});
								</script>
							</div>
						</div>";

				$script = "app.popup()";
			}

			$body = "<section id='two' class='wrapper alt style2'>
							<section class='spotlight'>
								<div class='image'><img src='imgs/banners/team.jpg' alt='' /></div><div class='content'>
									<h2>CAMBIAR CONTRASEÑA</h2>
									<p class='$class' >$message</p>
									<form method='POST' action='/services' >
										<input type='hidden' name='service' value='User' />
										<input type='hidden' name='method' value='password' />

										<div class='field' ><input type='password' name='password' placeholder='Contraseña Actual' maxlength='20' required /></div>
										<div class='field' ><input type='password' name='new_password' placeholder='Nueva Contraseña' maxlength='20' required /></div>
										<div class='field' ><input type='password' name='confirm_password' placeholder='Confirmar Contraseña' maxlength='20' required /></div>

										<button type='submit'>Guardar</button>
									</form>

								</div>
							</section>
						</section>";



			$this->render( (object) compact("body","popup","script") );

		}else
			header('Location: /');

	}

	function networks(){

		if($this->user){

			$obj = Session::flash("UserNetworkController-crud");
			$message = "";$messageb = ""; $class = "";$classb = "" ;$id = $this->user->id;$forms="";$newUserNetwork="";$script="";

			if($obj){
				$obj = json_decode($obj);
				$message = $obj->message;
				$class   = $obj->type;
			}

			$myNetworks = BDPDO::fetchAllObj("SELECT n.id idnetwork,n.name,n.url urlnetwork,un.id idunetwork,un.url urlunetwork,un.main 
												FROM user_networks un
												INNER JOIN networks n ON un.idnetwork=n.id WHERE iduser=:id",compact("id"));

			$networks   = BDPDO::fetchAllObj("SELECT * FROM networks WHERE id NOT IN(SELECT idnetwork FROM user_networks WHERE iduser=:id) ",compact("id"));

			if($networks){


				$onetwork = "<select name='idnetwork' >
							<option value='0'>--Red Social--</option>";

				foreach ($networks as $key => $network) 
					$onetwork .= "<option value='$network->id'>$network->name</option>";

				$onetwork .= "</select>";

				$newUserNetwork  =  "	
										<div>
											<div class='field' > <button name='add'>Agregar</button> </div>
											<div class='usernetwork-add invisible' >
												<form name='usernetwork' method='POST' action='/services'>
													<input type='hidden' name='service' value='UserNetwork' />
													<input type='hidden' name='method' value='save' />
													<div class='field'>$onetwork</div>
													<div class='field'><input type='text' name='url' value='' placeholder='Red Social (URL)' /></div>
													<div class='field'><input type='checkbox' id='main' name='main' value='1'><label for='main' >Asignar como principal</label></div>
													<div class='field'> <button type='submit'>GUARDAR</button> </div>
												</form>
											</div>
										</div>
									";

				$script .= "	$('button[name=add]').click(function(){

								if($('.usernetwork-add').hasClass('invisible')){
									$('.usernetwork-add').removeClass('invisible');	
								}else{
									$('.usernetwork-add').addClass('invisible');
								}
								
							})";
			}

			

			if($myNetworks){

				$forms = "<b class='info fs_8' >Marca como principal la red social a la que se dirigirá al dar clic sobre tu perfil.</b>";

				foreach ($myNetworks as $key => $myNetwork) {
					$checked = ($myNetwork->main=="1") ? "checked" : "";

					$nameform       = "usernetwork-form-".$myNetwork->idunetwork;
					$nameformdelete = "usernetwork-form-delete-".$myNetwork->idunetwork ;
					$namebtndelete  = "btn-delete-".$myNetwork->idunetwork."";

					$forms .= "<div class='network-content' > ".ucwords($myNetwork->name)." : ".$myNetwork->urlnetwork."

									<div>
										<form name='$nameform' method='POST' action='/services' >
											<input type='hidden' name='service' value='UserNetwork' />
											<input type='hidden' name='method' value='save' />
											<input type='hidden' name='id' value='$myNetwork->idunetwork' />
											<div class='field'>
												<input type='text' name='url' value='$myNetwork->urlunetwork' />
											</div> 
											<div class='field'>
												<input type='checkbox' id='main".$myNetwork->idnetwork."' name='main' value='1' $checked ><label for='main".$myNetwork->idnetwork."' >Principal</label>
											</div>
											<div class='field' >
												<button type='submit' >Guardar</button>
												<button name='$namebtndelete' type='button' >Eliminar</button>
											</div>
										</form>
									</div>

									<div class='invisible' >
										<form name='$nameformdelete' method='POST' action='/services'>
										<input name='service' value='UserNetwork' />
										<input name='method' value='delete' />
										<input name='id' value='$myNetwork->idunetwork' />
										</form>
									</div>

								</div>";

					$script .= "  
									$('form[name=$nameform] button[name=$namebtndelete]').click(function(){
										if( confirm('¿ Estás seguro de eliminar estos datos ?') ){ 
											$('form[name=$nameformdelete]').submit() 
										}
									})
								";
				}

				$forms .= "<style>
							.network-content{
								border: 1px solid gray;
							    padding: .5em;
							    margin: .5em 0em;
							}
							.network-content form{
								margin:0;
							}
							</style>";

				$script .= " 
						 		$('form input[type=checkbox]').change(function() {

					         		if( $(this).is(':checked') ){
										$('input[type=checkbox]').prop('checked', false);
										$(this).prop('checked', true);
					         		}
						        });
						    ";
			}else{

				$messageb = "Usted no tiene configurado ninguna red social.";
				$classb   = "info";
				
			}


			$body = "<section id='two' class='wrapper alt style2'>
							<section class='spotlight'>
								<div class='image'><img src='imgs/banners/team.jpg' alt='' /></div><div class='content'>
									<h2>Mis redes Sociales</h2>
									<label class='$class' >$message</label>
									<label class='$classb' >$messageb</label>
									$newUserNetwork
									$forms
								</div>
							</section>
						</section>";
			

			$this->render( (object) compact("body","script") );


		}else
			header('Location: /');
	}

	function render($obj){

		$t = new TemplateController;
		$html = $t->render( $obj );

		echo $html;
		exit();
	}
}