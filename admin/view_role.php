<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RoleId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$role = Role::loadById($RoleId);


if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);


if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
}


include 'navigation_subpage.php';
?>


	<div class="content">
		<h1><?php echo $role->Name;?>&nbsp;
		<?php if ($role->IsDead ==1) echo "<i class='fa-solid fa-skull-crossbones' title='Död'></i>"?>
		
		<a href='edit_role.php?id=<?php echo $role->Id;?>'>
		<i class='fa-solid fa-pen'></i></a></h1>
            <?php if ($larp_role->UserMayEdit  == 1) {
                echo "Deltagaren får ändra karaktären " . showStatusIcon(false);
                $editButton = "Ta bort tillåtelsen att ändra";
            }
            else {
                
                $editButton = "Tillåt deltagaren att ändra karaktären";
            }
                  
                ?>
            <form action="logic/toggle_user_may_edit_role.php" method="post"><input type="hidden" id="roleId" name="roleId" value="<?php echo $role->Id;?>"><input type="submit" value="<?php echo $editButton;?>"></form>
		<div>
		<table>
			<tr><td valign="top" class="header">Spelas av</td><td><a href ="view_person.php?id=<?php echo $role->PersonId;?>"><?php echo $role->getPerson()->Name; ?></a></td>
		<?php 
		if ($role->hasImage()) {
		    
		    $image = Image::loadById($role->ImageId);
		    echo "<td rowspan='20' valign='top'><img width='300' src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    echo "</td>";
		}
		?>
			
			</tr>
		<?php if (isset($group)) {?>
			<tr><td valign="top" class="header">Grupp</td><td><a href ="view_group.php?id=<?php echo $group->Id;?>"><?php echo $group->Name; ?></a></td></tr>
		<?php }?>
			<tr><td valign="top" class="header">Huvudkaraktär</td><td><?php echo ja_nej($larp_role->IsMainRole);?></td></tr>
			<tr><td valign="top" class="header">Yrke</td><td><?php echo $role->Profession;?></td></tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo nl2br($role->Description);?></td></tr>
			<tr><td valign="top" class="header">Beskrivning för gruppen</td><td><?php echo nl2br($role->DescriptionForGroup);?></td></tr>
			<tr><td valign="top" class="header">Beskrivning för andra</td><td><?php echo nl2br($role->DescriptionForOthers);?></td></tr>
			<tr><td valign="top" class="header">Tidigare lajv</td><td><?php echo $role->PreviousLarps;?></td></tr>
			<tr><td valign="top" class="header">Varför befinner sig karaktären i Slow River?</td><td><?php echo $role->ReasonForBeingInSlowRiver;?></td></tr>
			<tr><td valign="top" class="header">Religion</td><td><?php echo $role->Religion;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet</td><td><?php echo $role->DarkSecret;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet - intrig idéer</td><td><?php echo nl2br($role->DarkSecretIntrigueIdeas); ?></td></tr>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($role->getIntrigueTypes());?></td></tr>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo nl2br($role->IntrigueSuggestions); ?></td></tr>
			<tr><td valign="top" class="header">Saker karaktären inte vill spela på</td><td><?php echo $role->NotAcceptableIntrigues;?></td></tr>
			<tr><td valign="top" class="header">Relationer med andra</td><td><?php echo $role->CharactersWithRelations;?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $role->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Rikedom</td><td><?php echo $role->getWealth()->Name; ?></td></tr>
			<tr><td valign="top" class="header">Var är karaktären född?</td><td><?php echo $role->Birthplace;?></td></tr>
			<tr><td valign="top" class="header">Var bor karaktären?</td><td><?php echo $role->getPlaceOfResidence()->Name; ?></td></tr>
			<tr><td valign="top" class="header">Död</td><td><?php echo ja_nej($role->IsDead);?></td></tr>

		</table>		
		</div>
		
		<h2>Intrig</h2>
		<div>
		<?php    echo $larp_role->Intrigue; ?>
		</div>
		<h2>Anteckningar (visas inte för deltagaren)</h2>
		<div>
		<?php    echo $role->OrganizerNotes; ?>
		</div>
		<?php 
		$previous_larps = $role->getPreviousLarps();
		if (isset($previous_larps) && count($previous_larps) > 0) {
		    
		    echo "<h2>Historik</h2>";
		    foreach ($previous_larps as $prevoius_larp) {
		        $previous_larp_role = LARP_Role::loadByIds($role->Id, $prevoius_larp->Id);
		        echo "<div class='border'>";
		        echo "<h3>$prevoius_larp->Name</h3>";
		        echo "<strong>Intrig</strong><br>";
		        echo nl2br($previous_larp_role->Intrigue);
		        echo "<br><strong>Vad hände för $role->Name?</strong><br>";
		        if (isset($previous_larp_role->WhatHappened) && $previous_larp_role->WhatHappened != "")
		            echo $previous_larp_role->WhatHappened;
		            else echo "Inget rapporterat";
	            echo "<br><strong>Vad hände för andra?</strong><br>";
	            if (isset($previous_larp_role->WhatHappendToOthers) && $previous_larp_role->WhatHappendToOthers != "")
	                echo $previous_larp_role->WhatHappendToOthers;
	                else echo "Inget rapporterat";
	            echo "</div>";
		                
		    }
		}
			    
			
			
		?>
		

	</div>


</body>
</html>
