<?php

include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "update";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'insert') {
        $group = Group::newWithDefault();
        if (isset($_GET['hidden'])) $group->IsVisibleToParticipants = 0;
        $larp_group = LARP_Group::newWithDefault();
        $persons_in_group = array();
        $persons_in_group[] = $current_person;
        
        
    } elseif ($operation == 'update') {
        $GroupId = $_GET['id'];
        $group = Group::loadById($GroupId);
        if (!$group->isRegistered($current_larp)) {
            header('Location: index.php'); //Gruppen är inte anmäld
            exit;
        }
        
        $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
        $persons_in_group = Person::getPersonsInGroupAtLarp($group, $current_larp);
        $group_leader = $group->getPerson();
        if (!is_null($group_leader) && !existsInArray($group_leader, $persons_in_group)) {
            $persons_in_group[] = $group_leader;
        }
    } else {
    }
}

$main_characters_in_group = Role::getAllMainRolesInGroup($group, $current_larp);
$non_main_characters_in_group = Role::getAllNonMainRolesInGroup($group, $current_larp);

$intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);


if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}


function existsInArray($entry, $array) {
    foreach ($array as $compare) {
        if ($compare->Id == $entry->Id) {
           return true;
        }
    }
    return false;
}

function print_role($group_member) {
    global $current_larp;
    
    echo $group_member->getViewLink();
    echo " - " . $group_member->Profession;
    $person = $group_member->getPerson();
    if (!is_null($person)) {
        echo " spelas av " . $person->getViewLink();
        
        if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
            
            echo ", ansvarig vuxen är ";
            $registration = Registration::loadByIds($person->Id, $current_larp->Id);
            if (!empty($registration->GuardianId)) {
                $registration->getGuardian()->Name;
            }
            
        }
    } else echo " NPC";
    echo "<br>";   
}

function default_value($field) {
    GLOBAL $group;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($group->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "action":
            if (is_null($group->Id)) {
                $output = "Skapa";
                break;
            }
            $output = "Ändra";
            break;
    }
    
    echo $output;
}



include 'navigation.php';
?>

	<div class="content">
		<h1>
    		<?php 
    		if ($operation == 'update') {
    		    echo "Ändra $group->Name";
    		} else {
    		    echo "Skapa en grupp";
    		}    
    		 ?>
		 </h1>
    	<?php 
    	if (!is_null($group->Id) && empty($main_characters_in_group) && empty($non_main_characters_in_group) && (empty($intrigues))) {
    	
    	?>
        <form action="logic/remove_group_from_larp.php" method="post"><input type="hidden" id="groupId" name="groupId" value="<?php echo $group->Id;?>"><input type="submit" value="Ta bort gruppen från lajvet"></form>
    	<?php } ?>
		<form action="logic/edit_group_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="GroupId" name="GroupId" value="<?php echo $group->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr><td valign="top" class="header">Namn&nbsp;<font style="color:red">*</font></td>
			<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($group->Name); ?>" required></td></tr>

			<tr><td valign="top" class="header">Gruppansvarig&nbsp;<font style="color:red">*</font></td>
			<td>
			<?php selectionByArray('Person', $persons_in_group, false, false, $group->PersonId);?>
			</td></tr>



			<tr><td valign="top" class="header">Beskrivning&nbsp;<font style="color:red">*</font></td>
			<td><textarea id="Description" name="Description" rows="4" cols="50" maxlength="60000" required><?php echo htmlspecialchars($group->Description); ?></textarea></td></tr>

			<tr><td valign="top" class="header">Beskrivning för andra</td>
			<td><textarea id="DescriptionForOthers" name="DescriptionForOthers" rows="4" cols="50" maxlength="1000"><?php echo htmlspecialchars($group->DescriptionForOthers); ?></textarea></td></tr>

			<tr><td valign="top" class="header">Vänner</td>
			<td><textarea id="Friends" name="Friends" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->Friends); ?></textarea></td></tr>

			<tr><td valign="top" class="header">Fiender</td>
			<td><textarea id="Enemies" name="Enemies" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->Enemies); ?></textarea></td></tr>

			<?php if (Wealth::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Rikedom&nbsp;<font style="color:red">*</font></td>
			<td><?php Wealth::selectionDropdown($current_larp, false, true, $group->WealthId);?></td></tr>
			<?php } ?>

			<?php if (PlaceOfResidence::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Var bor gruppen?&nbsp;<font style="color:red">*</font></td>
			<td><?php PlaceOfResidence::selectionDropdown($current_larp, false, true, $group->PlaceOfResidenceId);?></td></tr>
			<?php } ?>

			<?php if (GroupType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av grupp&nbsp;<font style="color:red">*</font></td>
			<td><?php GroupType::selectionDropdown($current_larp, false, true, $group->GroupTypeId);?></td></tr>
			<?php } ?>
			<?php if (ShipType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av skepp&nbsp;<font style="color:red">*</font></td>
			<td><?php ShipType::selectionDropdown($current_larp, false, true, $group->ShipTypeId);?></td></tr>
			<?php } ?>
			<?php if ($current_larp->getCampaign()->is_me()) { ?>
			<tr><td valign="top" class="header">Färg&nbsp;<font style="color:red">*</font></td>
			<td><input type="text" id="Colour" name="Colour" maxlength="250" value="<?php echo htmlspecialchars($group->Colour); ?>"></td></tr>
			<?php } ?>



			<tr><td valign="top" class="header">Intrig&nbsp;<font style="color:red">*</font></td>
			<td>
				<input type="radio" id="WantIntrigue_yes" name="WantIntrigue" value="1" <?php if ($larp_group->WantIntrigue == 1) echo 'checked="checked"'?>> 
    			<label for="WantIntrigue_yes">Ja</label><br> 
    			<input type="radio" id="WantIntrigue_no" name="WantIntrigue" value="0" <?php if ($larp_group->WantIntrigue == 0) echo 'checked="checked"'?>> 
    			<label for="WantIntrigue_no">Nej</label>
			</td></tr>

			<?php if (IntrigueType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Intrigtyper</td>
			<td><?php IntrigueType::selectionDropdownGroup($current_larp, true, false, $group->getSelectedIntrigueTypeIds()); ?></td></tr>
			<?php } ?>

			<tr><td valign="top" class="header">Intrigidéer</td>
			<td><textarea id="IntrigueIdeas" name="IntrigueIdeas" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->IntrigueIdeas); ?></textarea></td></tr>


			<tr><td valign="top" class="header">Synlig för deltagare&nbsp;<font style="color:red">*</font><br>En grupp som inte är synlig för deltagare är mest lämlig för NPC'er.</td>
			<td>
				<input type="radio" id="IsVisibleToParticipants_yes" name="IsVisibleToParticipants" value="1" <?php if ($group->isVisibleToParticipants()) echo 'checked="checked"'?>> 
    			<label for="IsVisibleToParticipants_yes">Ja</label><br> 
    			<input type="radio" id="IsVisibleToParticipants_no" name="IsVisibleToParticipants" value="0" <?php if (!$group->isVisibleToParticipants()) echo 'checked="checked"'?>> 
    			<label for="IsVisibleToParticipants_no">Nej</label>
			</td></tr>

			<tr><td valign="top" class="header">Död/Ej i spel&nbsp;<font style="color:red">*</font></td>
			<td>
				<input type="radio" id="IsDead_yes" name="IsDead" value="1" <?php if ($group->IsDead == 1) echo 'checked="checked"'?>> 
    			<label for="IsDead_yes">Ja</label><br> 
    			<input type="radio" id="IsDead_no" name="IsDead" value="0" <?php if ($group->IsDead == 0) echo 'checked="checked"'?>> 
    			<label for="IsDead_no">Nej</label>
			</td></tr>

			<tr><td colspan="2"><h2>Anmälningsinformation</h2></td></tr>

			<tr><td valign="top" class="header">Kvarvarande intriger</td>
			<td><textarea id="RemainingIntrigues" name="RemainingIntrigues" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($larp_group->RemainingIntrigues); ?></textarea></td></tr>

			<tr><td valign="top" class="header">Annan information</td>
			<td><textarea id="OtherInformation" name="OtherInformation" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->OtherInformation); ?></textarea></td></tr>

			<tr><td valign="top" class="header">Antal medlemmar&nbsp;<font style="color:red">*</font></td>
			<td><input type="text" id="ApproximateNumberOfMembers" name="ApproximateNumberOfMembers" value="<?php echo htmlspecialchars($larp_group->ApproximateNumberOfMembers); ?>" required></td></tr>

			<?php if (HousingRequest::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Önskat boende&nbsp;<font style="color:red">*</font></td>
			<td><?php HousingRequest::selectionDropdown($current_larp, false,true, $larp_group->HousingRequestId);?></td></tr>
			<?php } ?>

			<tr><td valign="top" class="header">Eldplats&nbsp;<font style="color:red">*</font></td>
			<td>
				<input type="radio" id="NeedFireplace_yes" name="NeedFireplace" value="1" <?php if ($larp_group->NeedFireplace == 1) echo 'checked="checked"'?>> 
    			<label for="NeedFireplace_yes">Ja</label><br> 
    			<input type="radio" id="NeedFireplace_no" name="NeedFireplace" value="0" <?php if ($larp_group->NeedFireplace == 0) echo 'checked="checked"'?>> 
    			<label for="NeedFireplace_no">Nej</label>
			</td></tr>
			
			<tr><td valign="top" class="header">Typ av tält</td>
			<td><input class="input_field" type="text" id="TentType" name="TentType"  maxlength="200" value="<?php echo htmlspecialchars($larp_group->TentType); ?>"></td></tr>
			<tr><td valign="top" class="header">Storlek på tält</td>
			<td><input class="input_field" type="text" id="TentSize" name="TentSize"  maxlength="200" value="<?php echo htmlspecialchars($larp_group->TentSize); ?>"></td></tr>
			<tr><td valign="top" class="header">Vilka ska bo i tältet</td>
			<td><textarea class="input_field" id="TentHousing" name="TentHousing" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($larp_group->TentHousing); ?></textarea></td></tr>
			<tr><td valign="top" class="header">Önskad placering</td>
			<td><input class="input_field" type="text" id="TentPlace" name="TentPlace"  maxlength="200" value="<?php echo htmlspecialchars($larp_group->TentPlace); ?>"></td></tr>

			
		</table>		
			<input type="submit" value="<?php default_value('action'); ?>">

			</form>
		
		
		<h2>Anmälda medlemmar</h2>
		<?php 
		foreach($main_characters_in_group as $group_member) {
		    print_role($group_member);
		}
		if (!empty($non_main_characters_in_group)) {
		    echo "<h3>Sidokaraktärer</h3>";
		    
		    foreach($non_main_characters_in_group as $group_member) {
		        print_role($group_member);
		    }
		}
		

         ?>
		<h2>Intrig</h2>

	    <?php  echo $larp_group->Intrigue; ?>
		

	</div>


</body>
</html>
