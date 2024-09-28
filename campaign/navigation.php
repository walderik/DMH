<!DOCTYPE html>
<html>
<head>

	<link href="../css/navigation_admin.css" rel="stylesheet" type="text/css">

	<?php include '../common/navigation_beginning.php';?> 

    	<!--  Egen meny -->
	  <div class="dropdown">
	    <button class="dropbtn">Inställningar 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      	<a href="../campaign/forms.php">Anmälningsformulär</a>
            <a href="../campaign/selection_data_admin.php?type=advertismenttypes">Annonsformat</a>
            <a href="../campaign/selection_data_admin.php?type=titledeedplace">Platser för handel</a>
            <a href="../campaign/campaign_admin.php">Inställningar för kampanjen</a>

 	    </div>
	  </div>
	  
	  <div class="dropdown">
	    <button class="dropbtn">Lajv 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      	<a href="../campaign/organizers.php">Arrangörer</a>
            <a href="../campaign/larp_admin.php">Lajv i kampanjen</a>
 	    </div>
	  </div>
	  
  	  <div class="dropdown">
	    <button class="dropbtn">Ekonomi 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      	<a href="../campaign/economy.php">Kassabok</a>
	      	<a href="../campaign/economy_overview.php">Ekonomisk översikt</a>
            <a href="../campaign/bookkeeping_account_admin.php">Bokföringskonton</a>
 	    </div>
	  </div>
	  
	<?php include '../common/navigation_site_part_selector.php';?>  
	<?php include '../common/navigation_end.php';?> 

