<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $current_larp->Name;?></title>
		<link href="../css/style.css" rel="stylesheet" type="text/css">
		<link href="../css/admin_style.css" rel="stylesheet" type="text/css">
		<link rel="icon" type="image/x-icon" href="../images/<?php echo $current_larp->getCampaign()->Icon; ?>">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.6.0/css/all.css">  
		</head>
	<body class="loggedin">
	<nav id="navigation">
        <a href="<?php echo $current_larp->getCampaign()->Homepage ?>" class="logo" target="_blank">
        <img src="../images/<?php echo $current_larp->getCampaign()->Icon; ?>" width="30" height="30"/>
        </a>
<a href="../participant/choose_larp.php" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i>Hem</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>
