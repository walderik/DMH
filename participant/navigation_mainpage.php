<!DOCTYPE html>
<html>
<head>
<script>
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <script src="https://kit.fontawesome.com/30d6e99205.js" crossorigin="anonymous"></script>
    <link href="../css/navigation_participant.css" rel="stylesheet" type="text/css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="../images/<?php echo $current_larp->getCampaign()->Icon; ?>">
	<title><?php echo $current_larp->Name;?></title>
	
</head>
<body>
<div class="topnav"  id="myTopnav">
    <div id="left">
    	<a href="#home" target="_blank" style="padding: 11px 5px 11px 5px;" class="always_show">
    	<img src="../images/<?php echo $current_larp->getCampaign()->Icon; ?>" width="30" height="30"/></a>
    	<a href="choose_larp.php" id="larp_name" class="logo always_show"><?php echo $current_larp->Name;?></a>
    </div>
    <div id="right">

	  
	  <div id="placeholder" class="dropdown">&nbsp;<br>&nbsp;
	    <button class="dropbtn">   
	    </button>
	  </div> 


	  <div class="dropdown">
	    <button class="dropbtn">Registrera 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      <a href="person_form.php">Deltagare</a>
	      <a href="role_form.php">Karaktär</a>
	      <a href="group_form.php">Grupp</a>
	    </div>
	  </div> 
	
  <?php if ($current_larp->mayRegister()) {?>	
	  <div class="dropdown">
	    <button class="dropbtn">Anmäl 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      <a href="select_person.php">Deltagare</a>
	      <a href="group_registration_form.php">Grupp</a>
	    </div>
	  </div> 
    <?php }?>
	<?php 
	 if (isset($_SESSION['admin'])) {
	 ?>	
	  <a href="../admin/" style="color: red"><i class="fa-solid fa-lock"></i>Admin</a>
    <?php }?>
	  <a href="help.php"><i class="fa-solid fa-circle-info"></i> Hjälp</a>
	  <a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut&nbsp;&nbsp;&nbsp;&nbsp;</a>
	  <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
	  </div>
    
    </div>

