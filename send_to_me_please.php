<?php 

session_start();
session_unset();

include_once 'includes/error_handling.php';

if (!isset($_GET['action'])) {
    header("location: ../index.php?error=noSubmit");
    exit();
}

$todo = $_GET['action'];
if ($todo=='activation') {
    $title = 'aktiveringsbrevet';
} else {
    $title = 'lösenordslänk';
}
?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Skicka om</title>
		<link href="css/loginpage.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
	  <h1>Död mans hand anmälningssystem</h1>
	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }?>
	  <?php if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }?>
	  <div class="login-register">
		<div class="login">
			<h1>Skicka om <?php echo $title; ?></h1>
			<form action="includes/resend.php" method="POST">
				<input type="hidden" id="action" name="action" value="<?php echo $todo; ?>"> 
				<label for="email">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="email" placeholder="Epost" id="email" required>
				<div class="filler"></div>
				<input type="submit" value="Skicka" name="submit">
			</form>
		</div>
	  </div>
	</body>
</html>