<?php

require 'header.php';

if ($current_person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAge) {
    header('Location: index.php?error=too_young_for_larp');
    exit;
}

include 'navigation.php';
?>

	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-person"></i>
		Ange ansvarig vuxen för <?php echo $current_person->Name;?><br>till <?php echo $current_larp->Name;?>
	</div>

	<form action="logic/input_guardian_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="insert"> 
		<input type="hidden" id="LARPId" name="LARPId" value="<?php echo $current_larp->Id ?>">
		<input type="hidden" id="PersonId" name="PersonId" value="<?php echo $current_person->Id ?>">

			<div class='itemcontainer'>
	       	<div class='itemname'><label for="Name">Ansvarig vuxen</label> <font style="color:red">*</font></div>
	       	Eftersom <?php echo $current_person->Name; ?> bara är <?php  echo $current_person->getAgeAtLarp($current_larp); ?> år på lajvet behövs en ansvarig vuxen. 
    			Den ansvarige måste vara tillfrågad och accepera ansvaret.<br>
    			Skriv in namn eller personnummer på den ansvarige. Personnummer anges på formen ÅÅÅÅMMDD-NNNN.
    			Om den ansvarige inte går att hitta kommer inte din anmälan att kunna godkännas förrän det är löst.<br>
			<input type="text" id="GuardianInfo" name="GuardianInfo" size="100" maxlength="25" >
			</div>

		    
			 <div class='center'><input type="submit" class='button-18' value="Spara"></div>

		</form>
	</div>

</body>
</html>