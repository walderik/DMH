<!DOCTYPE html>
<html>
<head>

	<link href="../css/navigation_admin.css" rel="stylesheet" type="text/css">

	<?php include '../common/navigation_beginning.php';?> 


	<!--  Egen meny -->
	    	<!--  Egen meny -->
	    <a href="../houses/house_admin.php?type=house">Hus</a>
	    <a href="../houses/house_admin.php?type=camp">Lägerplatser</a>
	    <a href="../houses/xxx.php">Husförvaltare</a>
	
	  <div class="dropdown">
	    <button class="dropbtn">Kartor 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
		    <a href="../houses/all_houses.php">Hus</a>
		    <a href="../houses/all_housing.php">Hus & Läger</a>
 	    </div>
	  </div>

      	<a href="../common/mail_admin.php">Skickad epost</a>

	<?php include '../common/navigation_site_part_selector.php';?>  
	<?php include '../common/navigation_end.php';?> 

