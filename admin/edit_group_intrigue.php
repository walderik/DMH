<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $GroupId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$current_group = Group::loadById($GroupId);


if (!$current_group->isRegistered($current_larp)) {
    header('Location: index.php'); //Gruppen är inte anmäld
    exit;
}


if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

$larp_group = LARP_Group::loadByIds($current_group->Id, $current_larp->Id);

$main_characters_in_group = Role::getAllMainRolesInGroup($current_group, $current_larp);
$non_main_characters_in_group = Role::getAllNonMainRolesInGroup($current_group, $current_larp);

function print_role($group_member) {
    global $current_larp;
    
    
    echo "<a href ='view_role.php?id=" . $group_member->Id ."'>" .
        $group_member->Name . "</a> - " .
        $group_member->Profession . " spelas av " .
        "<a href ='view_person.php?id=" . $group_member->getPerson()->Id . "'>" .
        $group_member->getPerson()->Name . "</a>";
        
        
        if ($group_member->getPerson()->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
            echo ", ansvarig vuxen är ";
            if (!empty($registration->GuardianId)) {
                $group_member->getRegistration($current_larp)->getGuardian()->Name;
            }
            
        }
        echo "<br>";
        
}


include 'navigation.php';
?>


	<div class="content">
		<h1><?php echo $current_group->Name;?>&nbsp;<a href='edit_group.php?id=<?php echo $current_group->Id;?>'><i class='fa-solid fa-pen'></i></a></h1>
		<table>
			<tr><td valign="top" class="header">Gruppansvarig</td><td><a href ="view_person.php?id=<?php echo $current_group->PersonId;?>"><?php echo $current_group->getPerson()->Name;?></a></td></tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo $current_group->Description;?></td></tr>
			<tr><td valign="top" class="header">Beskrivning för andra</td><td><?php echo $current_group->DescriptionForOthers;?></td></tr>
			<tr><td valign="top" class="header">Vänner</td><td><?php echo $current_group->Friends;?></td></tr>
			<tr><td valign="top" class="header">Fiender</td><td><?php echo $current_group->Enemies;?></td></tr>
			
			<?php if (Wealth::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Rikedom</td><td><?php echo $current_group->getWealth()->Name; ?></td></tr>
			<?php }?>
			<?php if (PlaceOfResidence::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Var bor gruppen?</td><td><?php echo $current_group->getPlaceOfResidence()->Name;?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Intrig</td><td><?php echo ja_nej($larp_group->WantIntrigue);?></td></tr>
			<?php if (IntrigueType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($current_group->getIntrigueTypes());?></td></tr>
			<?php }?>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo $current_group->IntrigueIdeas;?></td></tr>
			<tr><td valign="top" class="header">Kvarvarande intriger</td><td><?php echo $larp_group->RemainingIntrigues; ?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $current_group->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Antal medlemmar</td><td><?php echo $larp_group->ApproximateNumberOfMembers;?></td></tr>
			<?php if (HousingRequest::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Önskat boende</td><td><?php echo HousingRequest::loadById($larp_group->HousingRequestId)->Name;?></td></tr>
			<?php }?>
			<tr><td valign="top" class="header">Eldplats</td><td><?php echo ja_nej($larp_group->NeedFireplace);?></td></tr>
		</table>		
		
		
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
		<?php 
		$intrigues = Intrigue::getAllIntriguesForGroup($current_group->Id, $current_larp->Id);
		if (!empty($intrigues)) {
		    echo "<table class='data'>";
		    echo "<tr><th>Intrig</th><th>Intrigtext</th></tr>";
	        foreach ($intrigues as $intrigue) {
	           echo "<tr>";
	           echo "<td><a href='view_intrigue.php?Id=$intrigue->Id'>Intrig: $intrigue->Number. $intrigue->Name</a></td>";
	           $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $current_group);
	           echo "<td>$intrigueActor->IntrigueText</td>";
	           echo "</tr>";
	       }
	       echo "</table>";
	       echo "<br>";
		}
	    ?>
		
		<form action="logic/edit_group_intrigue_save.php" method="post">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $current_group->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<textarea id="Intrigue" name="Intrigue" rows="20" cols="150" maxlength="60000"><?php    echo htmlspecialchars($larp_group->Intrigue); ?></textarea><br>
		
		<input type="submit" value="Spara">

			</form>

		<h2>Anteckningar (Visas inte för deltagarna, men tänk på att en deltagare kan bli arrangör.)</h2>
		<form action="logic/edit_group_intrigue_save.php" method="post">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $current_group->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<textarea id="OrganizerNotes" name="OrganizerNotes" rows="20" cols="150" maxlength="60000"><?php    echo htmlspecialchars($current_group->OrganizerNotes); ?></textarea><br>
		
		<input type="submit" value="Spara">

			</form>



	</div>


</body>
</html>
