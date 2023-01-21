<?php 

session_start();
session_unset();

$error_merrage = "";
$message_message;

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['error'])) {
        $error_code = $_GET['error'];
        $error_merrage = getErrorText($error_code);
    }
    if (isset($_GET['message'])) {
        $message_code = $_GET['message'];
        $message_message = getMessageText($message_code);
    }
}

function getMessageText($code) {
    $output = "";
    
    switch ($code) {
        case "user_created":
            $output = "Kontot har skapats. Du kan logga in nu.";
            break;
        default:
            $output = "Okänt meddelande: ". $code;
    }    
    return $output;
}

function getErrorText($code) {
    $output = "";
    
    switch ($code) {
        case "stmtfailed":
            $output = "Kopplingen till databasen misslyckades. Kontakta administratören.";
            break;
        case "userNotFound":
            $output = "Använaren saknas";
            break;
        case "accountNotActivated":
            $output = "Kontot är inte aktiverat";
            break;
        case "emptyInput":
            $output = "Fyll i alla fält";
            break;
        case "invalidEmail":
            $output = "Ogiltig epostadress";
            break;
        case "passwordNotMatch":
            $output = "Lösenorden är inte lika";
            break;
        case "invalidPasswordLength":
            $output = "Lösenordet måste vara minst 5 och max 20 tecken.";
            break;
        case "userExists":
            $output = "Användaren finns redan";
            break;
        case "noSubmit":
            $output = "Försök igen";
            break;
        case "no_person":
            $output = "Du måste registrera en deltagare först";
            break;
        default:
            $output = "Okänt fel: ". $code;
    }
    return $output;
}


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
	  <div class="error"><?php echo $error_message; ?></div>
	  <div class="message"><?php echo $message_message; ?></div>
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