<?php
// include_once '../includes/all_includes.php';


// // We need to use sessions, so you should always start sessions using the below code.
// session_start();
// // If the user is not logged in redirect to the login page...
// if (!isset($_SESSION['loggedin'])) {
//     header('Location: /index.html');
//     exit;
// }

require '../includes/init.php';


//Ifthe user isnt admin it may not see these pages
if (!isset($_SESSION['admin'])) {
    header('Location: /participant/index.php');
    exit;
}



// // If the user has not chosen a larp, and is not on the choose larp page or the larp admin pages
// $url = $_SERVER['REQUEST_URI'];  

// if (!isset($_SESSION['larp']) && strpos($url, "choose_larp.php") == false && strpos($url, "larp_admin.php") == false && strpos($url, "larp_form.php") == false) {
//     header('Location: /participant/choose_larp.php');
//     exit;
// }


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="../includes/style.css" rel="stylesheet" type="text/css">
		<script src="https://kit.fontawesome.com/30d6e99205.js" crossorigin="anonymous"></script>
	</head>
	<body class="loggedin">

 