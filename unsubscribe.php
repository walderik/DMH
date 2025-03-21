<?php




session_start();
session_unset();

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root . '/includes/all_includes.php';

include_once $root . '/includes/error_handling.php';


if (isset($_GET['code']) && isset($_GET['personId'])) {
    $code = $_GET['code'];
    $personId = $_GET['personId'];
    $person = Person::loadById($personId);
    if (empty($person) || $person->getUnsubscribeCode() != $code) {
        header("location: index.php?error=userNotFound");
        exit();
    }

    $person->IsSubscribed = 0;
    $person->update();
}


?>


<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Avstå från utskick</title>
		<link href="css/loginpage.css" rel="stylesheet" type="text/css">
		<link rel="icon" type="image/x-icon" href="../images/bv.ico">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	
	<body>
	  <h1>Avstå från utskick (unsubscribe)</h1>
      	  <div class="login-register">
    	  <div class="sign">
        	  Du har nu avstått från utskick till <?php echo $person->Name?><br><br>
        	  Alla meddelanden som skickas till dig kommer fortfarande att gå att läsa i Omnes Mundi på respektive lajv.<br>
        	  Om du vill ha utskick igen till din epost kan du ändra det inne i Omnes Mundi genom att redigera dina personuppgifter.
    	</div>
    	</div>
	</body>
</html>