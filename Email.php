<?php
	require_once("config.php");
	
	//create tables if don't exists
	$stmt = $pdo->prepare('DESCRIBE `auth_users`');
	$stmt->execute();
	$count_table = $stmt->fetchAll();
	if(!count($count_table)){  
		$pdo->exec("CREATE TABLE `auth_users` (
		  `id` int(11) NOT NULL,
		  `email` varchar(255) NOT NULL,
		  `password` varchar(255) NOT NULL,
		  `password_pending` varchar(255) NOT NULL,
		  `active` int(1) NOT NULL,
		  `code` varchar(255) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;
		ALTER TABLE `auth_users` ADD PRIMARY KEY (`id`);
		ALTER TABLE `auth_users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
	}

	function access($row, $type){
		global $destination;
		$_SESSION['email'] = $row["email"];
	    $_SESSION['firstname'] = explode("@", $row["email"])[0];
	    $_SESSION['lastname'] = "";
		$_SESSION['type'] = $type;

	    die('<script>window.location.replace("'.$destination.'");</script>');
	}

	function send_mail($type, $code){
		global $config, $actual_link;
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
		$headers .= 'From: <'.$config["email"]["send_mail"]["from_email"].'>' . "\r\n";

		if($type == "signin")
			$mex = $config["email"]["messages"][$config["email"]["language"]]["message_signin"];
		if($type == "forgot")
			$mex = $config["email"]["messages"][$config["email"]["language"]]["message_forgot"];

		if(mail($_POST['email'], $config["email"]["send_mail"]["subject"], $mex . '<br /><a href="'.$actual_link."?code=".$code.'&email='.$_POST['email'].'">'.$actual_link."?code=".$code.'&email='.$_POST['email'].'</a>', $headers)){
			if($type == "signin")
				$success = $config["email"]["messages"][$config["email"]["language"]]["success_signin"];
			if($type == "forgot")
				$success = $config["email"]["messages"][$config["email"]["language"]]["success_forgot"];
		} else {
			$error = $config["email"]["messages"][$config["email"]["language"]]["error"];
		}

		return array("success" => $success, "error" => $error);
	}

	if(isset($_POST["login"])){
		$stmt = $pdo->prepare("SELECT * FROM auth_users WHERE email = :email AND password = :password AND active = 1");
		$stmt->execute([':email' => $_POST['email'], ':password' => md5(md5($_POST['password']))]);
		$row = $stmt->fetchAll();
		if(!count($row)){
			$error = $config["email"]["messages"][$config["email"]["language"]]["login_error"];
		} else {
			access($row[0], "login");
		}
	}

	if(isset($_POST["resend"])){
		$stmt = $pdo->prepare("SELECT * FROM auth_users WHERE email = :email");
		$stmt->execute([':email' => $_POST['email']]);
		$row = $stmt->fetchAll();
		
		$sm = send_mail("signin", $row[0]["code"]);
		$success = $sm["success"];
		$error = $sm["error"];
	}
	
	if(isset($_POST["signin"]) || isset($_POST["forgot"])){
		if(isset($_POST["signin"])){
			$stmt = $pdo->prepare("SELECT * FROM auth_users WHERE email = :email");
			$stmt->execute([':email' => $_POST['email']]);
			$rows = $stmt->fetchAll();
		}
		
		$error = "";
		$success = "";

		if(isset($_POST["signin"])){
			if(count($rows))
				$error .= $config["email"]["messages"][$config["email"]["language"]]["user_exists"] . "<br />";
		}
		if(!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
			$error .= $config["email"]["messages"][$config["email"]["language"]]["email_not_valid"] . "<br />";
		if(!password_strength($_POST["password"]))
			$error .= $config["email"]["messages"][$config["email"]["language"]]["password_strength"] . "<br />";

		if($error == ""){
			$code = md5(rand(000000,999999));
			if(isset($_POST["signin"])){
				$stmt= $pdo->prepare("INSERT INTO auth_users (email, password_pending, code, active) VALUES (:email, :password, :code, 0)");
			}
			if(isset($_POST["forgot"])){
				$stmt= $pdo->prepare("UPDATE auth_users SET password_pending = :password, code = :code WHERE email = :email");
			}
			$stmt->execute([':email' => $_POST['email'], ':password' => md5(md5($_POST['password'])), ':code' => $code]);

			if(isset($_POST["signin"]))
				$sm = send_mail("signin", $code);
			if(isset($_POST["forgot"]))
				$sm = send_mail("forgot", $code);

			$success = $sm["success"];
			$error = $sm["error"];
		}
	}
	
	if(isset($_GET["code"]) && isset($_GET["email"])){
		$stmt = $pdo->prepare("SELECT * FROM auth_users WHERE email = :email AND code = :code");
		$stmt->execute([':email' => $_GET['email'], ':code' => $_GET['code']]);
		$row = $stmt->fetchAll()[0];

		if(count($row)){		
			$stmt= $pdo->prepare("UPDATE auth_users SET password = password_pending, active = 1 WHERE email = :email");
			$stmt->execute([':email' => $_GET['email']]);
			access($row, "active");
		}
	}
?>

<html>
	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="style.css" rel="stylesheet" />
	</head>
	<body>
		<div id="mail_form">
			<?php
				if($error != "")
					echo '<div class="dialog error">'.$error.'</div>';
				else if($success != "")
					echo '<div class="dialog success">'.$success.'</div>';
			?>
			<form method="post">
				<label><?php echo $config["email"]["messages"][$config["email"]["language"]]["email"]; ?></label>
				<input type="text" name="email" placeholder="Email" class="field" />
				<?php if(!isset($_GET["resend"])){ ?>
					<?php 
					if(isset($_GET["forgot"]))
						$label_pwd = $config["email"]["messages"][$config["email"]["language"]]["new_password"];
					else
						$label_pwd = $config["email"]["messages"][$config["email"]["language"]]["password"];
					?>
					<label><?php echo $label_pwd; ?></label>
					<input type="password" name="password" placeholder="Password" class="field" />
				<?php } if(!isset($_GET["forgot"]) && !isset($_GET["resend"])){ ?>
					<input type="submit" name="login" value="<?php echo $config["email"]["messages"][$config["email"]["language"]]["login"] ?>" class="button" />
					<input type="submit" name="signin" value="<?php echo $config["email"]["messages"][$config["email"]["language"]]["signin"] ?>" class="button" />
				<?php } if(isset($_GET["forgot"])) { ?>
					<input type="submit" name="forgot" value="<?php echo $config["email"]["messages"][$config["email"]["language"]]["reset_password"] ?>" class="button" />
				<?php } if(isset($_GET["resend"])) { ?>
					<input type="submit" name="resend" value="<?php echo $config["email"]["messages"][$config["email"]["language"]]["send"] ?>" class="button" />
				<?php } ?>
			</form>
			<a href="?forgot=1"><?php echo $config["email"]["messages"][$config["email"]["language"]]["forgot"]; ?></a><br />
			<a href="?resend=1"><?php echo $config["email"]["messages"][$config["email"]["language"]]["resend"]; ?></a>
		</div>
	</body>
</html>