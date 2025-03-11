<?php 

session_start();
session_unset();

$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
include_once $root . '/includes/all_includes.php';
include_once 'includes/error_handling.php';


?>


<!DOCTYPE html>
<html>
	<head>
	<style>
	
	.center {
  display: block;
  margin-left: auto;
  margin-right: auto;

}
	
	</style>
		<meta charset="utf-8">
		<title>Berghems vänners Omnes Mundi</title>
		<link href="css/loginpage.css" rel="stylesheet" type="text/css">
		<link rel="icon" type="image/x-icon" href="../images/bv.ico">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.6.0/css/all.css">
	</head>
	<body>
	<img  class="center" src="images/omnes_mundi_header.jpg">

	  <?php 
	      echo '<div class="error">Omnes Mundi är tillfälligt stängt för underhåll.</div>';
	  ?>

	</body>
</html>