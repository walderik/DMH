<?php

include_once 'header.php';

$person = $current_person;



if (!$person->isRegistered($current_larp) && !$person->isReserve($current_larp)) {
    header('Location: index.php'); // personen är inte anmäld
    exit;
}

$registration = Registration::loadByIds($person->Id, $current_larp->Id);


include 'navigation.php';

?>
	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-file"></i>
			Anmälan för <?php echo $person->Name;?>
		</div>
   		<div class='itemcontainer'>
		    <?php 
		    if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
		    ?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Ansvarig vuxen</div>
				<?php if (!empty($registration->GuardianId)) echo $registration->getGuardian()->Name; else echo showStatusIcon(false); ?>
    			</div>
		    <?php 
		    }
		    ?>

		    <?php 
		    $minors = $person->getGuardianFor($current_larp);
		    if (!empty($minors)) {
		        echo "<div class='itemcontainer'>";
		        echo "<div class='itemname'>Ansvarig vuxen för</div>";
		        $minor_str_arr = array();
		        foreach ($minors as $minor) {
		            $minor_str_arr[] = "<a href='view_registration.php?id=".$minor->Id."'>".$minor->Name."</a>";
		        }
		        echo implode(", ", $minor_str_arr);
		        echo "</div>";
		    }
		    ?>

			<?php if (TypeOfFood::isInUse($current_larp)) { ?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Typ av mat</div>
				<?php echo TypeOfFood::loadById($registration->TypeOfFoodId)->Name;?>
    			</div>
			<?php } ?>

			<?php if (isset($registration->FoodChoice)) { ?>
	   	   		<div class='itemcontainer'>
               	<div class='itemname'>Matalternativ</div>
				<?php echo $registration->FoodChoice; ?>
    			</div>
			<?php } ?>

	   		<div class='itemcontainer'>
           	<div class='itemname'>NPC önskemål</div>
			<?php echo $registration->NPCDesire;?>
			</div>

			<?php if (HousingRequest::isInUse($current_larp)) { ?>
	   	   		<div class='itemcontainer'>
               	<div class='itemname'>Önskat boende</div>
				<?php 
				    $housingrequest = $registration->getHousingRequest();
				    if(!empty($housingrequest)) echo $housingrequest->Name;
			    ?>
    			</div>
			<?php } ?>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Boendehänsyn</div>
			<?php echo nl2br(htmlspecialchars($registration->LarpHousingComment)); ?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Typ av tält</div>
			<?php echo nl2br(htmlspecialchars($registration->TentType)); ?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Storlek på tält</div>
			<?php echo nl2br(htmlspecialchars($registration->TentSize)); ?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Vilka ska bo i tältet</div>
			<?php echo nl2br(htmlspecialchars($registration->TentHousing)); ?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Önskad placering</div>
			<?php echo nl2br(htmlspecialchars($registration->TentPlace)); ?>
			</div>

			<?php if (OfficialType::isInUse($current_larp)) { ?>
	   	   		<div class='itemcontainer'>
               	<div class='itemname'>Funktionärsönskemål</div>
				<?php echo commaStringFromArrayObject($registration->getOfficialTypes());?>
    			</div>
			<?php } ?>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Anmälda karaktärer</div>
			<?php 
			$roles = $person->getRolesAtLarp($current_larp);
			foreach($roles as $role) {
			    echo $role->getViewLink();
			    if ($role->isMain($current_larp)) echo " (Huvudkaraktär)";
			    echo "<br>";
			}
			
			 ?>
			</div>
		</div>	
	</div>


</body>
</html>
