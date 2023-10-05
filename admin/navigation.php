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
    <link href="../css/navigation_admin.css" rel="stylesheet" type="text/css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<link href="../css/gallery.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="../images/<?php echo $current_larp->getCampaign()->Icon; ?>">
	<title><?php echo $current_larp->Name;?></title>
	
</head>
<body>
<div class="topnav"  id="myTopnav">
    <div id="left">
    	<a href="#home" target="_blank" style="padding: 11px 5px 11px 5px;" class="always_show">
    	<img src="../images/<?php echo $current_larp->getCampaign()->Icon; ?>" width="30" height="30"/></a>
    	<a href="../participant/choose_larp.php" id="larp_name" class="logo always_show"><?php echo $current_larp->Name;?></a>
    </div>
    <div id="right">

	  
	  <div id="placeholder" class="dropdown">&nbsp;<br>&nbsp;
	    <button class="dropbtn">   
	    </button>
	  </div> 


	  <a href="index.php"><i class="fa-solid fa-house"></i> Hem</a>
	  <div class="dropdown">
	    <button class="dropbtn">Praktiskt 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      	<a href="settings.php">Inställningar</a>
	      	<a href="mail_admin.php">E-post</a>
            <a href="registered_persons.php">Deltagare</a>
            <a href="approval.php">Godkänna</a>
            <a href="kitchen.php">Köket</a>
            <a href="officials.php">Funktionärer</a>
            <a href="housing.php">Boende</a>
            <a href="reserves.php">Reservlista</a>
            <a href="economy.php">Kassabok</a>
            <a href="refund_persons.php">Återbetalning</a>
            <a href="reports.php">Rapporter</a>
            <a href="statistics.php">Statistik</a>
            <a href="evaluation_result.php">Utvärdering</a>
		    <a href="ssn_check.php">Medlemskontroll</a> 


	    </div>
	  </div> 
	  <div class="dropdown">
	    <button class="dropbtn">Spel 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
            <a href="groups.php">Grupper</a>
            <a href="roles.php">Karaktärer</a>
            <a href="intrigue_admin.php">Intrigspår</a>
            <a href="commerce.php">Handel</a>
            <a href="timeline_admin.php">Körschema</a>
			
			<?php if ($current_larp->hasTelegrams()) {?>
            <a href="telegram_admin.php">Telegram</a>
            <?php }?>
            
			<?php if ($current_larp->hasLetters()) {?>
            <a href="letter_admin.php">Brev</a>
            <?php }?>
            
			<?php if ($current_larp->hasRumours()) {?>
            <a href="rumour_admin.php">Rykten</a>
            <?php }?>
            
            <a href="prop_admin.php">Rekvisita</a>
            <a href="npc.php">NPC'er</a>
            <a href="what_happened.php">Vad hände?</a>
	    </div>
	  </div> 
      <a href="../participant/" style="color: #99bbff"><i class="fa-solid fa-unlock"></i>Deltagare</a>

	<?php 
	 if (isset($_SESSION['admin'])) {
	 ?>	
	  <a href="../site-admin/" style="color: red"><i class="fa-solid fa-lock"></i>OM Admin</a>
    <?php }?>
	  <a href="help.php"><i class="fa-solid fa-circle-info"></i> Hjälp</a>
	  <a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut&nbsp;&nbsp;&nbsp;&nbsp;</a>
	  <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
	  </div>
    
    </div>

	  