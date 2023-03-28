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

$larp_group = LARP_Group::loadByIds($current_group->Id, $current_larp->Id);

$group_members = Role::getRegisteredRolesInGroup($current_group, $current_larp);


include 'navigation_subpage.php';
?>


	<div class="content">
		<h1><?php echo $current_group->Name;?>&nbsp;
		<?php if ($current_group->IsDead ==1) echo "<i class='fa-solid fa-skull-crossbones' title='Död'></i>"?>
		<a href='edit_group.php?id=<?php echo $current_group->Id;?>'><i class='fa-solid fa-pen'></i></a></h1>
		<table>
			<tr><td valign="top" class="header">Gruppansvarig</td><td><a href ="view_person.php?id=<?php echo $current_group->PersonId;?>"><?php echo $current_group->getPerson()->Name;?></a></td></tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo $current_group->Description;?></td></tr>
			<tr><td valign="top" class="header">Beskrivning för andra</td><td><?php echo $current_group->DescriptionForOthers;?></td></tr>
			<tr><td valign="top" class="header">Vänner</td><td><?php echo $current_group->Friends;?></td></tr>
			<tr><td valign="top" class="header">Fiender</td><td><?php echo $current_group->Enemies;?></td></tr>
			<tr><td valign="top" class="header">Rikedom</td><td><?php echo Wealth::loadById($current_group->WealthId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Var bor gruppen?</td><td><?php echo PlaceOfResidence::loadById($current_group->PlaceOfResidenceId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Intrig</td><td><?php echo ja_nej($larp_group->WantIntrigue);?></td></tr>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($larp_group->getIntrigueTypes());?></td></tr>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo $current_group->IntrigueIdeas;?></td></tr>
			<tr><td valign="top" class="header">Kvarvarande intriger</td><td><?php echo $larp_group->RemainingIntrigues; ?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $current_group->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Antal medlemmar</td><td><?php echo $larp_group->ApproximateNumberOfMembers;?></td></tr>
			<tr><td valign="top" class="header">Önskat boende</td><td><?php echo HousingRequest::loadById($larp_group->HousingRequestId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Eldplats</td><td><?php echo ja_nej($larp_group->NeedFireplace);?></td></tr>
			<tr><td valign="top" class="header">Död/Ej i spel</td><td><?php echo ja_nej($current_group->IsDead);?></td></tr>
		</table>		
		
		
		<h2>Anmälda medlemmar</h2>
		<?php 

		foreach($group_members as $group_member) {

		    echo "<a href ='view_role.php?id=" . $group_member->Id ."'>" . 
		    $group_member->Name . "</a>";
		    if ($group_member->hasImage()) {
		        
		        $image = Image::loadById($group_member->ImageId);
		        echo " <img width=30 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>";
		        if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    }
		    
		    echo "- " . 
            $group_member->Profession . ". Spelas av " . 
            "<a href ='view_person.php?id=" . $group_member->getPerson()->Id . "'>" .
            $group_member->getPerson()->Name . "</a>";


            if ($group_member->getPerson()->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
                echo ", ansvarig vuxen är ";
                if (!empty($registration->GuardianId)) echo $registration->getGuardian()->Name; else echo showStatusIcon(false);

 		    }

		    
		    echo "</td></tr>";
		    echo " <a href='logic/remove_group_member.php?groupID=$current_group->Id&roleID=$group_member->Id".
		    	"onclick='return confirm(\"Är du säker på att du vill ta bort karaktären från gruppen?\");'><i class='fa-solid fa-trash-can'></i></a>";

            echo "<br>"; 
		}
		?>
		<h2>Intrig</h2>
		<?php echo $larp_group->Intrigue; ?>
		

	</div>


</body>
</html>
