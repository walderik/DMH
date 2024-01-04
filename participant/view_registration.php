<?php

include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $PersonId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$person = Person::loadById($PersonId);



if (!$person->isRegistered($current_larp) && !$person->isReserve($current_larp) || $person->UserId != $current_user->Id) {
    header('Location: index.php'); // personen är inte anmäld, eller det här är inte din person
    exit;
}

$registration = Registration::loadByIds($person->Id, $current_larp->Id);


include 'navigation.php';

?>

	<div class="content">
		<h1><?php echo $person->Name;?></h1>
		<div>
		<table>
		    <?php 
		    if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
		    ?>
			<tr><td valign="top" class="header">Ansvarig vuxen</td><td>
			
			<?php if (!empty($registration->GuardianId)) echo $registration->getGuardian()->Name; else echo showStatusIcon(false); ?>
			
			</td></tr>
		    
		    <?php 
		    }
		    ?>

		    <?php 
		    $minors = $person->getGuardianFor($current_larp);
		    if (!empty($minors)) {
    			echo "<tr><td valign='top' class='header'>Ansvarig för</td><td>";
    			$minor_str_arr = array();
    			foreach ($minors as $minor) {
    			     $minor_str_arr[] = "<a href='view_registration.php?id=".$minor->Id."'>".$minor->Name."</a>";
    			}
    			echo implode(", ", $minor_str_arr);
    			echo "</td></tr>";
		    }
		    ?>



			<?php if (TypeOfFood::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av mat</td><td><?php echo TypeOfFood::loadById($registration->TypeOfFoodId)->Name;?></td></tr>
			<?php } ?>
			<?php if (isset($registration->FoodChoice)) { ?>
			    <tr><td valign="top" class="header">Matalternativ</td><td><?php echo $registration->FoodChoice; ?></td></tr>
			    
			<?php } ?>
			<tr><td valign="top" class="header">NPC önskemål</td><td><?php echo $registration->NPCDesire;?></td></tr>
			
			<?php if (HousingRequest::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Önskat boende</td><td><?php echo HousingRequest::loadById($registration->HousingRequestId)->Name;?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Boendehänsyn</td><td><?php echo nl2br(htmlspecialchars($registration->LarpHousingComment)); ?></td></tr>
			<tr><td valign="top" class="header">Typ av tält</td><td><?php echo nl2br(htmlspecialchars($registration->TentType)); ?></td></tr>
			<tr><td valign="top" class="header">Storlek på tält</td><td><?php echo nl2br(htmlspecialchars($registration->TentSize)); ?></td></tr>
			<tr><td valign="top" class="header">Vilka ska bo i tältet</td><td><?php echo nl2br(htmlspecialchars($registration->TentHousing)); ?></td></tr>
			<tr><td valign="top" class="header">Önskad placering</td><td><?php echo nl2br(htmlspecialchars($registration->TentPlace)); ?></td></tr>
			
			<?php if (OfficialType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Funktionärsönskemål</td><td><?php echo commaStringFromArrayObject($registration->getOfficialTypes());?></td></tr>
			<?php } ?>
		</table>	
		</div>	
	</div>


</body>
</html>
