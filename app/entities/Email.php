<?php

	//Incluimos la clase de PHPMailer
	require_once( $_SERVER['DOCUMENT_ROOT'].'/libraries/PHPMailer/class.phpmailer.php');
	require_once( $_SERVER['DOCUMENT_ROOT'].'/libraries/PHPMailer/class.smtp.php');

/**
* 
*/
class Email
{
	private $mailer  = null;
	public  $Subject = null;

	function __construct()
	{
		
		$this->mailer 					= new PHPMailer();  //Creamos una instancia en lugar usar mail()
		$this->mailer->SMTPKeepAlive 	= true;
		$this->mailer->IsSMTP(); 							// telling the class to use SMTP
		$this->mailer->Host       		= "smtp.gmail.com"; // SMTP server
		//$correo->SMTPDebug  = 2;                    	  // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only
		$this->mailer->SMTPAuth   = true;                  // enable SMTP authentication
		$this->mailer->Host       = "smtp.gmail.com"; // sets the SMTP server
		$this->mailer->Port       = 587;                    // set the SMTP port for the GMAIL server
		$this->mailer->Username   = "cmbteambroccoli@gmail.com"; // SMTP account username
		$this->mailer->Password   = "marcela18";        // SMTP account password
		 
		//Usamos el SetFrom para decirle al script quien envia el correo
		$this->mailer->SetFrom("cmbteambroccoli@gmail.com","TEAM BROCCOLI");
		 
		//Usamos el AddReplyTo para decirle al script a quien tiene que responder el correo
		//$this->mailer->AddReplyTo("cmbteambroccoli@gmail.com","TEAM BROCCOLI");
		 
		//Usamos el AddAddress para agregar un destinatario
		//$this->mailer->AddAddress("cmbteambroccoli@gmail.com","TEAM BROCCOLI" );

		$this->Subject = "Default Subject";
	}

	function AddAttachment($path = null){
		//Si deseamos agregar un archivo adjunto utilizamos AddAttachment
		//$this->mailer->AddAttachment("images/phpmailer.gif");	

		if(!empty($path)) $this->mailer->AddAttachment($path);	
		
	}

	function AddAddress($email, $fullname){

		if( !empty($email) ){
			//Usamos el AddAddress para agregar un destinatario
			$this->mailer->AddAddress($email, $fullname);
		}

	}

	function MsgHTML($msg = null){
		/*
		 * Si deseamos enviar un correo con formato HTML utilizaremos MsgHTML:
		 * $mailer->MsgHTML("<strong>Mi Mensaje en HTML</strong>");
		 * Si deseamos enviarlo en texto plano, haremos lo siguiente:
		 * $mailer->IsHTML(false);
		 * $mailer->Body = "Mi mensaje en Texto Plano";
		 */
		$this->mailer->MsgHTML($msg);
	}

	function Send(){

		//Ponemos el asunto del mensaje
		$this->mailer->Subject = $this->Subject ;

		//Enviamos el correo
		$rsp = $this->mailer->Send() ;

		$this->mailer->SmtpClose();
		return $rsp;
	}
}