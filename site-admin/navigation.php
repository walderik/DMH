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

function changePart() {
  	var part_selector = document.getElementById("part");
	if (part_selector.value.length != 0) {
		window.location.href = part_selector.value;
	}
}
</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <script src="https://kit.fontawesome.com/30d6e99205.js" crossorigin="anonymous"></script>
    <link href="../css/navigation_site_admin.css" rel="stylesheet" type="text/css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<link href="../css/gallery.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="../images/bv.ico">
	<title>Omnes Mundi</title>
	
</head>
<body>
<div class="topnav"  id="myTopnav">
    <div id="left">
    	<a href="#home" target="_blank" style="padding: 11px 5px 11px 5px;" class="always_show">
    	<img src="../images/bv.ico" width="30" height="30"/></a>
    	<a href="../participant/choose_larp.php" id="larp_name" class="logo always_show">Omnes Mundi</a>
    </div>
    <div id="right">

	  
	  <div id="placeholder" class="dropdown">&nbsp;<br>&nbsp;
	    <button class="dropbtn">   
	    </button>
	  </div> 


	  <a href="index.php"><i class="fa-solid fa-house"></i> Hem</a>
     <select name='part' id='part' onchange="changePart()">
      	<option value='../participant/'>Deltagare</option>
      	<?php if (AccessControl::hasAccessLarp($current_user, $current_larp)) {?>
     	<option value='../admin/'>Arrangör</option>
     	<?php }?>
    	<option value=''  selected='selected'>OM Admin</option>
	 </select>
	  <!-- <a href="help.php"><i class="fa-solid fa-circle-info"></i> Hjälp</a> -->
    	<div class="dropdown">
    		<button class="dropbtn"><i class="fa-solid fa-user" title="<?php echo $current_user->Name?>"></i> <?php echo $current_user->Name?></button>
		    <div class="dropdown-content">
		    	<a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut&nbsp;&nbsp;&nbsp;&nbsp;</a>
    		</div>
    	
    	</div>
	  <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
	  </div>
    
    </div>

