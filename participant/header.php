<?php
include_once '../includes/all_includes.php';


// We need to use sessions, so you should always start sessions using the below code.
session_start();
// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['loggedin'])) {
    header('Location: /index.html');
    exit;
}

// If the user has not chosen a larp, and is not on the choose larp page
$url = $_SERVER['REQUEST_URI'];  

if (!isset($_SESSION['larp']) && strpos($url, "choose_larp.php") == false) {
    header('Location: choose_larp.php');
    exit;
}


?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Home Page</title>
		<link href="../includes/style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">

 