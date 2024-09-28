<?php

if (!isset($_GET['code']) || !isset($_GET['email'])) {
    header("location: ../index.php?error=userNotFound");
    exit();
}

$code = $_GET['code'];


session_start();
session_unset();

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
include_once $root . '/includes/all_includes.php';

include_once $root . '/includes/error_handling.php';

$user = User::loadByEmailChangeCode($code);

if (is_null($user) || ($user->Email != $_GET['email'])) {
    header("location: ../index.php?error=userNotFound");
    exit();
}

$user->setEmailChangeCode();

?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Byt lösenord</title>
		<link href="css/loginpage.css" rel="stylesheet" type="text/css">
		<link rel="icon" type="image/x-icon" href="../images/bv.ico">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.6.10/css/all.css">
	</head>
	<body>
	  <h1>Byt lösenord på ditt konto</h1>
	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }?>
	  <?php if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }?>
	  <div class="login-register">
		<div class="register">
			<h1>Ange lösenord</h1>
			<form action="includes/password_changer.php" method="POST" autocomplete="off">
				<input type="hidden" id="the_code" name="the_code" value="<?php echo $user->EmailChangeCode ?>"> 
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Lösenord" id="password" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="passwordrepeat" placeholder="Repetera lösenord" id="password" required>
				<input type="submit" value="Byt lösenord"  name="submit">
			</form>
			</div>
		</div>
	  </div>

	</body>
</html>