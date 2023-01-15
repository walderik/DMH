<?php 

session_start();
session_unset();

?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Login</title>
		<link href="css/style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
	  <div class="login-register">
		<div class="login">
			<h1>Logga in</h1>
			<form action="includes/authenticate.php" method="POST">
				<label for="email">
					<i class="fas fa-user"></i>
				</label>
				<input type="text" name="email" placeholder="Epost" id="email" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Lösenord" id="password" required>
				<input type="submit" value="Logga in" name="submit">
			</form>
		</div>
		<div class="register">
			<h1>Registrera nytt konto</h1>
			<form action="includes/register.php" method="POST" autocomplete="off">
				<label for="email">
					<i class="fas fa-envelope"></i>
				</label>
				<input type="email" name="email" placeholder="Epost" id="email" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="password" placeholder="Lösenord" id="password" required>
				<label for="password">
					<i class="fas fa-lock"></i>
				</label>
				<input type="password" name="passwordrepeat" placeholder="Repetera lösenord" id="password" required>
				<input type="submit" value="Registrera"  name="submit">
			</form>
		</div>
	  </div>
	</body>
</html>