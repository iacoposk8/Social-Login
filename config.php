<?php 
	session_start();
	//session_destroy();
	//$_SESSION = [];

	//https://console.developers.google.com/apis/credentials
	//https://developers.facebook.com/apps/
	$config = array(
		"google" => array(
			"id" => "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
			"secret" => "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
		),
		"facebook" => array(
			"id" => "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
			"secret" => "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
		),
		"email" => array(
			"send_mail" => array(
				"from_email" => "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX",
				"subject" => "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
			),
			"language" => "en",
			"messages" => array(
				"en" => array(
					"email" => "Email",
					"password" => "Password",
					"new_password" => "New password",
					"login" => "Login",
					"signin" => "Sign in",
					"resend" => "Resend the activation email",
					"forgot" => "Forgot password ",
					"send" => "Send",
					"reset_password" => "Reset password",
					"user_exists" => "Already registered user",
					"email_not_valid" => "The email address entered is invalid",
					"password_strength" => "The password must contain uppercase, lowercase letters, numbers and be at least 8 characters long",
					"success_signin" => "Thank you for registering, check your email to activate your account",
					"success_forgot" => "Password reset, check email to activate account",
					"error" => "Error sending the email, please try later",
					"message_signin" => "Thank you for registering on XXXXXXXXX. Click on the following link to confirm your registration. <br /> If you have not registered on XXXXXXXXX, ignore this email.",
					"message_forgot" => "Password reset on XXXXXXXXX. Click on the following link to change your password. <br /> If you have not requested a password change, please ignore this email.",
					"login_error" => "Incorrect email or password or user not yet enabled"
				),
				"it" => array(
					"email" => "Email",
					"password" => "Password",
					"new_password" => "Nuova password",
					"login" => "Login",
					"signin" => "Registrati",
					"resend" => "Invia nuovamente la mail di attivazione",
					"forgot" => "Password dimenticata",
					"send" => "invia",
					"reset_password" => "Reimposta password",
					"user_exists" => "Utente già registrato",
					"email_not_valid" => "L'indirizzo email inserito non è valido",
					"password_strength" => "La password deve contenere lettere maiuscole, minuscole, numeri e essere lunga almeno 8 caratteri",
					"success_signin" => "Grazie per esserti registrato, controlla l'e-mail per attivare l'account",
					"success_forgot" => "Password resettata, controlla l'e-mail per attivare l'account",
					"error" => "Errore durante l'invio della email, provare in un secondo momento",
					"message_signin" => "Grazie per esserti registrato su XXXXXXXXX. Clicca sul seguente link per confermare la registrazione.<br />Se non ti sei registrato su XXXXXXXXX ignora questa email.",
					"message_forgot" => "Password resettata su XXXXXXXXX. Clicca sul seguente link per cambiare la password.<br />Se non hai richiesto il cambio della password, ignora questa email.",
					"login_error" => "Mail o password incorretti o utente non ancora abilitato"
				)
			)
		)
	);

	$destination = "XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX";
		
	$dsn = "mysql:host=XXXXXXXXX;dbname=XXXXXXXXX";
	$user = "XXXXXXXXX";
	$passwd = "XXXXXXXXX"; 
	$pdo = new PDO($dsn, $user, $passwd);
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$actual_link = explode("?",$actual_link)[0];

	function password_strength($password){
		$uppercase = preg_match('@[A-Z]@', $password);
		$lowercase = preg_match('@[a-z]@', $password);
		$number    = preg_match('@[0-9]@', $password);
		$specialChars = preg_match('@[^\w]@', $password);
		$specialChars = true;
		
		if(!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
			return false;
		}else{
			return true;
		}
	}