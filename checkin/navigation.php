<!DOCTYPE html>
<html>
<head>
    <link href="../css/navigation_admin.css" rel="stylesheet" type="text/css">
	<link href='../css/participant_style.css' rel='stylesheet' type='text/css'>	

	<?php

	include '../common/navigation_beginning.php';
	
	?>

  <?php 
      $campaign = $current_larp->getCampaign();
      
      ?>	
	


	  <div class="dropdown">
	    <button class="dropbtn">Incheckning 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
            <a href= "../checkin/search_person.php">Sök person</a>
            <a href="../checkin/not_checked_in.php">Inte incheckade</a>
	    </div>
	  </div> 
	  <div class="dropdown">
	    <button class="dropbtn">Utcheckning 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
            <a href="../checkin/search_person.php">Sök person</a>
            <a href="../checkin/search_vehicle.php">Sök fordon</a>
            <a href="../checkin/not_checked_out.php">Inte utcheckade</a>
	    </div>
	  </div> 
	  
	<?php include '../common/navigation_site_part_selector.php';?>  
	<?php include '../common/navigation_end.php';?>   

