<!DOCTYPE html>
<html>
<head>

	<link href="../css/navigation_participant.css" rel="stylesheet" type="text/css">
	<link href='../css/participant_style.css' rel='stylesheet' type='text/css'>
	
	<?php
	
	include '../common/navigation_beginning.php';
	
	if (isset($current_person)) {
	    $houses = $current_person->housesOf();

        if (!empty($houses)) {
            foreach ($houses as $persons_house) echo "<a href='../participant/view_house.php?id=$persons_house->Id'>$persons_house->Name</a>";
        }
	}
	?>

  <?php if (isset($current_larp)) {?>	

	  <div class="dropdown">
	    <button class="dropbtn">Registrera 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      <a href="../participant/person_form.php">Deltagare</a>
	      <a href="../participant/role_form.php">Karaktär</a>
	      <a href="../participant/group_form.php">Grupp</a>
	    </div>
	  </div> 
    <?php }?>
	
  <?php if (isset($current_larp) && $current_larp->mayRegister()) {?>	
	  <div class="dropdown">
	    <button class="dropbtn">Anmäl 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      <a href="../participant/person_registration_form.php">Deltagare</a>
	      <a href="../participant/group_registration_form.php">Grupp</a>
	    </div>
	  </div> 
    <?php }?>
	  <a href="../participant/help.php"><i class="fa-solid fa-circle-info"></i> Hjälp</a>
	  
	<?php include '../common/navigation_site_part_selector.php';?>  
	<?php include '../common/navigation_end.php';?> 

