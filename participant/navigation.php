<!DOCTYPE html>
<html>
<head>

	<link href="../css/navigation_participant.css" rel="stylesheet" type="text/css">

	<?php 
	include '../common/navigation_beginning.php';
	$persons = $current_user->getPersons();
	if (!empty($persons)) {
	    foreach ($persons as $person) {	
	        $houses = $person->housesOf();
	        if (!empty($houses)) {
	            foreach ($houses as $persons_house) echo "<a href='view_house.php?id=$persons_house->Id'>$persons_house->Name</a>";
	        }
	    }
	}
    ?>

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
	  <a href="help.php"><i class="fa-solid fa-circle-info"></i> Hjälp</a>
	  
	<?php include '../common/navigation_site_part_selector.php';?>  
	<?php include '../common/navigation_end.php';?> 

