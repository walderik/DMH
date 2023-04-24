<?php
require 'header.php';
include_once '../includes/error_handling.php';

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Omnes Mundos</title>
		<link href="../css/style.css" rel="stylesheet" type="text/css">
		<link href="../css/admin_style.css" rel="stylesheet" type="text/css">
		<link rel="icon" type="image/x-icon" href="../images/bv.ico">
		<script src="https://kit.fontawesome.com/30d6e99205.js" crossorigin="anonymous"></script>
	</head>
	<body class="loggedin">

	<nav id="navigation">
        <a href="" class="logo" target="_blank">
        <img src="../images/bv.ico" width="30" height="30"/>
        </a>
        <a href="" class="logo">Omnes Mundos</a>
          <ul class="links">
            <li><a href="../admin/" style="color: #99bbff"><i class="fa-solid fa-unlock"></i>Arrangör</a></li>
            <li><a href="../participant/" style="color: #99bbff"><i class="fa-solid fa-unlock"></i>Deltagare</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


		<div class="content">
			<h1>Omnes Mundos admin</h1>
			<p>			    
				<a href="campaign_admin.php">Kampanjer</a><br>
			    <a href="house_admin.php">Hus i byn</a><br>
			    <a href="user_admin.php">Användare / Logins</a><br>
			    <a href="access_control_admin.php">Behörigheter</a><br>
		    </p>
		    <h2>Basdata</h2>
		    <p>	    			
    		    <a href="selection_data_admin.php?type=normalallergytypes">Vanliga allergier</a>	<br>
    		    <a href="selection_data_admin.php?type=experiences">Erfarenhet som lajvare</a>	<br>
		    </p>
		</div>
	</body>
</html>