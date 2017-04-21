<?php
require_once( $_SERVER['DOCUMENT_ROOT'].'/app/controllers/AppController.php' );
/**
* 
*/
class UserController 
{
	private $maxperlist = null;

	function __construct()
	{
		$this->maxperlist = 100;
	}

	function register(){
		$fullname = post("fullname");
		$email    = post("email");
		$password = Crypt::set(post("password"));
		$state    = "0";
		$rtn      = "";

		$user = BDPDO::fetchObj("SELECT * FROM users WHERE email= :email ", compact("email") );
		
		if(!$user){
			
			if( strlen($fullname)<=100 && strlen($fullname)>0 && preg_match("/^([A-Za-zÑñáéíóúÁÉÍÓÚ ]+)$/",$fullname)  ){

				if( strlen($email)<=100 && strlen($email)>0 && filter_var($email, FILTER_VALIDATE_EMAIL) ){
					if( strlen($password)<=100 && strlen($password)>6 ){
						$alias = "u".date('YmdHis');
						$confirmation_code = $alias.Session::id();

						$arrUser = compact("fullname","email","password","alias","state","confirmation_code") ;

						$insert = BDPDO::insert("users",$arrUser);
						if($insert["success"]){
							$this->sendMessage($arrUser);
						}
						Session::flash(["UserController-register", 
							json_encode(["message"=>"Usuario registrado correctamente, pase a confirmar su correo electrónico.","type"=>"success"]) ]);

					}else Session::flash(["UserController-register", 
							json_encode(["message"=>"Ingrese correctamente su contraseña (6-20).","type"=>"error"]) ]);

				}else Session::flash(["UserController-register", 
							json_encode(["message"=>"Ingrese correctamente su email (max 100)","type"=>"error"])  ]);

			}else Session::flash(["UserController-register",
							json_encode(["message"=>"Ingrese correctamente su nombre (max 100).","type"=>"error"]) ]);

		}else{
			Session::flash(["UserController-register", 
							json_encode(["message"=>"Este usuario ya se encuentra registrado","type"=>"warning"]) ]);
		}

		rd('/signup');	
		
	}
	
	function confirmEmail(){
		$confirmation_code = get("confirmation_code");

		if(!empty($confirmation_code)){
			$user = User::where(["confirmation_code"=>$confirmation_code,"state"=>0]);
			if($user){

				BDPDO::update(User::TABLE,["state"=>1],["id"=>$user->id]);

				Session::flash(["UserController-confirm",  json_encode(["message"=>"Su correo a sido verificado correctamente.","type"=>"success"]) ]);

				$app = new AppController;

				return $app->successConfirmation();
			}else
				rd('/');
		}else
			rd('/');
	}

	function sendMessage($arr){

		$message = View::render("user/mail",[ "fullname"=>$arr["fullname"],"web"=> domain() , "confirmation_code"=>$arr["confirmation_code"] ]);

		$email = new Email();
		$email->Subject = "BIENVENIDO AL TEAM BROCOLI";
		$email->AddAddress($arr["email"], $arr["fullname"]);
		$email->MsgHTML($message);
		$rsp = $email->Send();
		return $rsp;
	}

	function update(){
		$fullname = post("fullname");
		$alias    = post("alias");
		$cel      = post("cel");
		$phone    = post("phone");
		$edad     = postInteger("edad");
		$idcountry= postInteger("idcountry");
		$avatar   = post("avatar");
		$edad     = ($edad>=User::$minEdad && $edad<=User::$maxEdad) ? $edad : "";

		$user     = Session::user();

		if( strlen($fullname)<=100 && strlen($fullname)>0 && preg_match("/^([A-Za-zÑñáéíóúÁÉÍÓÚ ]+)$/",$fullname)  ){
				$id = $user->id;
				
				$qUser = User::where("alias = :alias AND id!= :id",compact("alias","id"));
				//$user = User::where(["alias"=>$alias]);

				if( ( strlen($alias)<=50 && strlen($alias) >=0 && preg_match("/^([A-Za-z0-9_]+)$/",$alias) ) ||  strlen($alias)==0 ){
					if($qUser==null  ){

						if( strlen($cel)<=50 && strlen($cel)>=0 ){

							if( strlen($phone)<=50 && strlen($phone)>=0 ){


								if(!empty($avatar)){
									$ext = strtolower(pathinfo($avatar, PATHINFO_EXTENSION));
									
									$avatarTemp = "./public/imgs/users/$user->id/avatar-temp.".$ext;
									$newAvatar  = "./public/imgs/users/$user->id/avatar.".$ext;

									if(file_exists($avatarTemp)){
										
											foreach (glob("./public/imgs/users/$user->id/avatar.*") as $nombre_archivo)
												unlink($nombre_archivo);

										rename($avatarTemp,$newAvatar);

											foreach (glob("./public/imgs/users/$user->id/avatar-temp.*") as $nombre_archivo)
												unlink($nombre_archivo);

										$avatar   = "avatar.".$ext;
									}
								}else $avatar = $user->avatar;

								$udp = BDPDO::update("users",compact("fullname","alias","cel","phone","edad","idcountry","avatar"),compact("id"));

								if($udp){
									Session::flash(["UserController-update", 
										json_encode(["message"=>"Datos guardados correctamente.","type"=>"success"]) ]);

									$user->fullname = $fullname;
									$user->alias    = $alias;
									$user->cel      = $cel;
									$user->phone    = $phone;
									$user->edad     = $edad;
									$user->idcountry= $idcountry;
									$user->avatar   = $avatar;

									Session::user($user);
								}
								else
									Session::flash(["UserController-update", 
										json_encode(["message"=>"No se logró guardar los datos.","type"=>"error"]) ]);

							}else
								Session::flash(["UserController-update", 
									json_encode(["message"=>"Ingrese correctamente su #teléfono (max 50).","type"=>"error"]) ]);

						}else
							Session::flash(["UserController-update", 
								json_encode(["message"=>"Ingrese correctamente su #celular (max 50).","type"=>"error"]) ]);

					}else
						Session::flash(["UserController-update", 
							json_encode(["message"=>"El alias indicado ya esta siendo usado.","type"=>"error"]) ]);

				}else
					Session::flash(["UserController-update", 
							json_encode(["message"=>"Ingrese su alias correctamente (números, letras , no espacios , no caracteres extraños , max 50).","type"=>"error"]) ]);

		}else Session::flash(["UserController-update",
						json_encode(["message"=>"Ingrese correctamente su nombre (max 100).","type"=>"error"]) ]);

		rd('/profile');
	}

	function password(){
		$password        = post("password");
		$password        = Crypt::set($password);
		$newpassword     = post("new_password");
		$confirmpassword = post("confirm_password");
		$user 			 = Session::user();

		if( $user->password ==  $password ){
			if( ( strlen($newpassword)<=100 && strlen($newpassword)>6 ) && ( strlen($confirmpassword)<=100 && strlen($confirmpassword)>6 ) ){

				if($newpassword === $confirmpassword){
					
					$id = $user->id;
					$password =  Crypt::set($newpassword);
					BDPDO::update("users",compact("password"),compact("id") );
					$user->password = $password;

					Session::user($user);


					Session::flash(["UserController-password",
								json_encode(["message"=>"Su contraseña ha sido cambiado exitosamente. $script ","type"=>"success","success"=>true]) ]);

				}else Session::flash(["UserController-password",
								json_encode(["message"=>"Confirme correctamente su nueva contraseña.","type"=>"error","success"=>false]) ]);

			}else Session::flash(["UserController-password",
						json_encode(["message"=>"Ingrese correctamente su nueva contraseña (6-20).","type"=>"error","success"=>false]) ]);

		}else Session::flash(["UserController-password",
						json_encode(["message"=>"Ingrese correctamente su contraseña actual.","type"=>"error","success"=>false]) ]);

		
		rd('/password');
	}

	function login(){

		$sUser = Session::user();

		if(!$sUser){
			$email    = post("email");
			$password = Crypt::set(post("password"));
			$state    = 0;

			$user = BDPDO::fetchObj("SELECT * FROM users WHERE email= :email AND password= :password ", compact("email","password") );
			
			if($user){

				if($user->state=="1"){
					Session::user( $user );
					rd('/');
				}else{
					Session::flash(["UserController-login", 
								json_encode(["message"=>"Confirme su correo electrónico para poder acceder.","type"=>"warning"]) ]);
				}

			}else{
				Session::flash(["UserController-login", 
								json_encode(["message"=>"Usuario y/o contraseña incorrectos.","type"=>"warning"]) ]);
			}

			rd('/login');

		}else rd('/');
		
	}

	function search(){
		$nalias	   = trim( post("nalias") );
		$nalias    = ($nalias!="") ? $nalias : null;
		$idcountry = post("idcountry");

		$sUsers     = Session::users();

		$users = [];
		foreach ($sUsers as $key => $user) {
			if( strpos($user->fullname,$nalias)!==false ||  strpos($user->auser,$nalias)!==false  || $nalias === null ){
				if( $idcountry==0 || $user->idcountry == $idcountry){
					$users[] = $user;
				}
			}
		}

		if($users) $list = $this->paginator(1,$users);
		else $list = ["list"=>"No se encontraron resultados."];
		
		return $list;
	}

	function reverse(){
		$users = Session::paginator("users");
		$users = array_reverse($users);

		$list = $this->paginator(1,$users);

		return $list;
	}

	function paginator($page = null , $users=null){

		$max = $this->maxperlist;
		
		if($users) Session::paginator("users",$users);
		else $users = Session::paginator("users");

		$page 	= ($page) ? $page : $_POST["page"];
		$since 	= ($page-1) * $max;
		$until 	= $page * $max;

		$total = count($users);
		$mod   = $total % $max;
		$pages = intval($total / $max);
		$pages = ($mod==0) ? $pages : ($pages+1) ;

		$list = View::render("user/list",["users"=>$users,"since"=>$since,"until"=>$until,"pages"=>$pages,"page"=>$page]);

		return compact("list");
	}

	function uploadAvatar(){

			$user = Session::user();
			$rsp = ["success"=>false,"message"=>null,"data"=>null];

			if($user){

				$arr = [
						"folder"=>"./public/imgs/users/$user->id/",
						"max_size"=>3000000,
						"types"=>["image/jpeg","image/png","image/gif"],  // png , jpg , gif
						"file"=>$_FILES['avatar'],
						"filename"=>"avatar-temp"
						];

				$rsp = File::upload($arr);

				if( $rsp["success"] ) $rsp["data"]["id"] = $user->id;
				
		    
			}

				
			return $rsp;
	}

	function logout(){
		Session::user( false );
		rd('/login');
	}
}