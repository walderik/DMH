    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.6.0/css/all.css">    
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<link href="../css/gallery.css" rel="stylesheet" type="text/css">
	
	<?php 
	if (!empty($current_larp)) {
	?>
		<link rel="icon" type="image/x-icon" href="../images/<?php echo $current_larp->getCampaign()->Icon; ?>">
		<title><?php echo $current_larp->Name;?></title>
	<?php 
	} else { 	
	?>
		<link rel="icon" type="image/x-icon" href="../images/bv.ico">
		<title>Omnes Mundi</title>
	<?php 
	}
	?>    

</head>
<body>
<div class="topnav"  id="myTopnav">
    <div id="left">
    	<a href="#home" target="_blank" style="padding: 11px 5px 11px 5px;" class="always_show">
    	<?php if (!empty($current_larp)) { ?>
    	<img src="../images/<?php echo $current_larp->getCampaign()->Icon; ?>" width="30" height="30"/></a>
    	<a href="../participant/choose_larp.php" id="larp_name" class="logo always_show"><?php echo $current_larp->Name;?></a>
    	<?php } else {?>
    	<img src="../images/bv.ico" width="30" height="30"/></a>
    	<a href="../participant/choose_larp.php" id="larp_name" class="logo always_show">Omnes Mundi</a>
    	<?php }?>
    </div>
    <div id="right">

	  
	  <div id="placeholder" class="dropdown">&nbsp;<br>&nbsp;
	    <button class="dropbtn">   
	    </button>
	  </div> 
	  <?php 
	  if ($_SESSION['navigation'] == Navigation::LARP) {
	      $location =  '../admin/';
	  } elseif ($_SESSION['navigation'] == Navigation::CAMPAIGN) {
	      $location =  '../campaign/';
	  } elseif ($_SESSION['navigation'] == Navigation::BOARD) {
	      $location =  '../board/';
	  } elseif ($_SESSION['navigation'] == Navigation::HOUSES) {
	      $location =  '../houses/';
	  } elseif ($_SESSION['navigation'] == Navigation::OM_ADMIN) {
	      $location =  '../site-admin/';
	  } else {
	      $location =  '../participant/';
	  }
	  
	  
	  ?>
	  
	  <a href="<?php echo $location; ?>index.php"><i class="fa-solid fa-house"></i> Hem</a>
	  <?php 
	  $uri = $_SERVER['REQUEST_URI'];
	  
	  if (str_contains($uri, "/participant/")) {
	      //TODO bygg mail för deltagare
	      //<a href="../participant/mail.php"><i class="fa-solid fa-envelope"></i> Epost</a>
	  } else {
        echo "<a href='../common/mail_admin.php'><i class='fa-solid fa-envelope'></i> Epost</a>";
      }
