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

$group = Group::loadById($GroupId); 


$isRegistered = $group->isRegistered($current_larp);

if ($isRegistered) {
    $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    
    $persons = Person::getGroupMembers($group, $current_larp);
    $main_characters_in_group = Role::getAllMainRolesInGroup($group, $current_larp);
    $non_main_characters_in_group = Role::getAllNonMainRolesInGroup($group, $current_larp);
    $allUnregisteredRoles = Role::getAllUnregisteredRolesInGroup($group, $current_larp);
    $NPCs = Role::getAllNPCsInGroup($group, $current_larp);
    $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);
} else {
    $intrigues = array();
}


function print_role(Role $role, Group $group, $isRegistered) {
    global $current_larp;
    echo "<tr>";
    echo "<td>";
    if ($role->hasImage()) {
        echo "<img width='30' src='../includes/display_image.php?id=$role->ImageId'/>\n";
    }
    echo "</td>";
    
    echo "<td>";
	echo $role->getViewLink();
    echo "</td>";
    echo "<td>";
    echo $role->getEditLinkPen(true);
    if ($role->isPC($current_larp)) {
        echo " <a href='logic/remove_group_member.php?groupID=$group->Id&roleID=$role->Id' onclick='return confirm(\"Är du säker på att du vill ta bort karaktären från gruppen?\");'><i class='fa-solid fa-trash-can' title='Ta bort ur gruppen'></i></a>";
    } elseif ($role->mayDelete()) {
        echo " <a href='logic/delete_npc.php?roleID=$role->Id' onclick='return confirm(\"Är du säker på att du vill radera NPCn?\");'><i class='fa-solid fa-trash-can' title='Radera'></i></a>";
        
    }
    
    echo "</td>";
    echo "<td>$role->Profession</td>";

    if ($role->isPC($current_larp)) {
    $person = $role->getPerson();
        echo "<td>" . $person->getViewLink() . "</td>";
            
        echo "<td>";
        if ($isRegistered && $person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
            echo "Ansvarig vuxen är ";
            $registration = Registration::loadByIds($role->PersonId, $current_larp->Id);
            if (!empty($registration->GuardianId)) { 
                $guardian = $registration->getGuardian();   
                echo $guardian->getViewLink();
            } else echo showStatusIcon(false);
            
        }
        

        echo "</td>";
    } 

    echo "</tr>";
}

include 'navigation.php';
include 'aktor_navigation.php';
?>
	<div class="content">
		<h1>
			<?php echo $group->Name;?>&nbsp;
			<?php if ($group->IsDead ==1) echo "<i class='fa-solid fa-skull-crossbones' title='Död'></i>"?>
			<?php 
				if ($isRegistered) {	
					echo $group->getEditLinkPen(true);
				}
			?>
		</h1>	
		<?php if ($isRegistered) {?>	
		<a href='group_sheet.php?id=<?php echo $group->Id;?>' target='_blank'>
		<i class='fa-solid fa-file-pdf' title='Gruppblad för <?php echo $group->Name;?>'></i>Gruppblad för <?php echo $group->Name;?></a> &nbsp;

		<a href='group_sheet.php?id=<?php echo $group->Id;?>&all_info=<?php echo date_format(new Datetime(),"suv") ?>' target='_blank'>
		<i class='fa-solid fa-file-pdf' title='All info om <?php echo $group->Name;?>'></i>All info om <?php echo $group->Name;?></a> &nbsp;
		<br><br>
		<?php }?>
		<?php 
		if ($group->isApproved()) {
		  echo "<strong>Godkänd</strong>";
		  if (!empty($group->ApprovedByPersonId) && !empty($group->ApprovedDate)) {
		      $approver = Person::loadById($group->ApprovedByPersonId);
		      echo " av $approver->Name, ".substr($group->ApprovedDate,0, 10); 
		  }
		  $editButton = "Ta bort godkännandet";
		}
		else {
		    echo "<strong>Ej godkänd</strong>";
		    $editButton = "Godkänn";
		}		
?>
        <form action="logic/toggle_approve_role.php" method="post"><input type="hidden" id="groupId" name="groupId" value="<?php echo $group->Id;?>"><input type="submit" value="<?php echo $editButton;?>"></form>
		<br>
        <?php 
        if ($isRegistered) {
        if ($larp_group->UserMayEdit  == 1) {
                echo "Gruppledaren får ändra gruppen " . showStatusIcon(false);
                $editButton = "Ta bort tillåtelsen att ändra";
            }
            else {
                
                $editButton = "Tillåt gruppledaren att ändra gruppen";
            }
                  
                ?>
            <form action="logic/toggle_user_may_edit_group.php" method="post"><input type="hidden" id="groupId" name="groupId" value="<?php echo $group->Id;?>"><input type="submit" value="<?php echo $editButton;?>"></form>
		<?php }?>
		<div>
		<table>
			<tr><td valign="top" class="header">Gruppansvarig</td><td>
		<?php 
		$groupleader = $group->getPerson();
		
		if (!is_null($groupleader)) {
		    $groupleader_registration = $groupleader->getRegistration($current_larp);
		     if ($isRegistered) {
			    if (isset($groupleader_registration) && !$groupleader_registration->isNotComing()) {
					echo $groupleader->getViewLink();
			    } elseif (!isset($groupleader_registration)) {
			        $reserveregistration = Reserve_Registration::loadByIds($groupleader->Id, $current_larp->Id);
    			    if (isset($reserveregistration)) echo "<s>$groupleader->Name</s> (på reservlistan)";
    			    else echo "<s>$groupleader->Name</s> (inte anmäld)";
			    } else echo "<s>$groupleader->Name</s> (avbokad)";
			 } else {
			    echo $groupleader->Name;
			}
			echo contactEmailIcon($groupleader);
		}
		?>
			</td>
					<?php 
					if ($group->hasImage()) {
            		    echo "<td rowspan='20' valign='top'>";
            		    echo "<img width='300' src='../includes/display_image.php?id=$group->ImageId'/>\n";
            		    echo "<br><a href='../common/logic/rotate_image.php?id=$group->ImageId'><i class='fa-solid fa-rotate-right' title='Rotra bild'></i></a>";
            		    echo " <a href='logic/delete_image.php?id=$group->Id&type=group'><i class='fa-solid fa-trash' title='Tab ort bild'></i></a>\n";
            		    echo " <a href='upload_image.php?id=$group->Id&type=group'><i class='fa-solid fa-image-portrait' title='Byt bild'></i></a> \n";
            		    echo "</td>";
					} else {
					    echo "<a href='upload_image.php?id=$group->Id&type=group'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
					}
            		?>
			
			</tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo $group->Description;?></td></tr>
			<tr><td valign="top" class="header">Beskrivning för andra</td><td><?php echo $group->DescriptionForOthers;?></td></tr>
			<tr><td valign="top" class="header">Vänner</td><td><?php echo $group->Friends;?></td></tr>
			<tr><td valign="top" class="header">Fiender</td><td><?php echo $group->Enemies;?></td></tr>
			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Rikedom</td>
			<td><?php $wealth=$group->getWealth(); if (!empty($walth)) echo $wealth->Name;?></td></tr>
			<?php } ?>
			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Var bor gruppen?</td>
			<td><?php $por=$group->getPlaceOfResidence(); if (!empty($por)) echo $por->Name;?></td></tr>
			<?php } ?>
			<?php if (GroupType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av grupp</td>
			<td><?php $gt=$group->getGroupType(); if (!empty($gt)) echo $gt->Name; ?></td></tr>
			<?php }?>
			<?php if (ShipType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av skepp</td>
			<td><?php $st=$group->getShipType(); if (!empty($st)) echo $st->Name; ?></td></tr>
			<?php }?>
			<?php if ($current_larp->getCampaign()->is_me()) { ?>
			<tr><td valign="top" class="header">Färg</td><td><?php echo $group->Colour; ?></td></tr>
			<?php }?>

			<?php if (!empty($larp_group)) { ?>
			<?php if (IntrigueType::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($larp_group->getIntrigueTypes());?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo nl2br(htmlspecialchars($larp_group->IntrigueIdeas));?></td></tr>
			<?php } ?>

			<?php if ($isRegistered) {?>
			<tr><td valign="top" class="header">Intrig</td><td><?php echo ja_nej($larp_group->WantIntrigue);?></td></tr>

			<tr><td valign="top" class="header">Kvarvarande intriger</td><td><?php echo $larp_group->RemainingIntrigues; ?></td></tr>
			<tr><td valign="top" class="header">Vad har hänt sedan senaste lajvet?</td><td><?php echo $larp_group->WhatHappenedSinceLastLarp; ?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $group->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Uppskattat<br>antal medlemmar</td><td><?php echo $larp_group->ApproximateNumberOfMembers;?></td></tr>
			<?php if (HousingRequest::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Önskat boende</td><td><?php echo HousingRequest::loadById($larp_group->HousingRequestId)->Name;?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Typ av tält</td><td><?php echo nl2br(htmlspecialchars($larp_group->TentType)); ?></td></tr>
			<tr><td valign="top" class="header">Storlek på tält</td><td><?php echo nl2br(htmlspecialchars($larp_group->TentSize)); ?></td></tr>
			<tr><td valign="top" class="header">Vilka ska bo i tältet</td><td><?php echo nl2br(htmlspecialchars($larp_group->TentHousing)); ?></td></tr>
			<tr><td valign="top" class="header">Önskad placering</td><td><?php echo nl2br(htmlspecialchars($larp_group->TentPlace)); ?></td></tr>
			<tr><td valign="top" class="header">Eldplats</td><td><?php echo ja_nej($larp_group->NeedFireplace);?></td></tr>
			<?php }?>
			<tr><td valign="top" class="header">Synlighet</td><td><?php echo Group::VISIBILITY_TYPES[$group->Visibility]?></td></tr>
			<tr><td valign="top" class="header">Död/Ej i spel</td><td><?php echo ja_nej($group->IsDead);?></td></tr>
		</table>		
		</div>
		<?php if ($isRegistered) { ?>
		<h2>Anmälda medlemmar 
		<?php 
		
		$personIdArr = array();
		foreach($persons as $person) {
		    $personIdArr[] = $person->Id;
		}
		$ikon = contactSeveralEmailIcon("", $personIdArr, "Medlem i $group->Name", "Meddelande till alla medlemmar i $group->Name i $current_larp->Name");
		
		echo "$ikon &nbsp; &nbsp;";
		
		
		
		?>
		</h2>
		<?php 
        echo "<div>";
		echo "<table>";
		foreach($main_characters_in_group as $group_member) {
		    print_role($group_member, $group, true);
		}
		echo "</table>";
		if (!empty($non_main_characters_in_group)) {
		    echo "<h3>Sidokaraktärer</h3>";
		    
		    echo "<table>";
		    foreach($non_main_characters_in_group as $group_member) {
		        print_role($group_member, $group, false);
		    }
		    echo "</table>";
		    
		}
		
		
		echo "</div>";
		?>
		<?php
		if(!empty($allUnregisteredRoles)) {
			echo "<h2>Icke anmälda medlemmar</h2>";
			echo "<div>";
			echo "<table>";
			foreach($allUnregisteredRoles as $role) {
				print_role($role, $group, false);
			}
			echo "</table>";
			echo "</div>";
		}
		?>
		<?php
		if(!empty($NPCs)) {
			echo "<h2>NPC'er</h2>";
			echo "<div>";
			echo "<table>";
			foreach($NPCs as $role) {
				print_role($role, $group, false);
			}
			echo "</table>";
			echo "</div>";
		}
		?>
		
		<h2>Intrig <a href='edit_group_intrigue.php?id=<?php echo $group->Id ?>'><i class='fa-solid fa-pen'></i></a></h2>
		<div>
		<?php echo $larp_group->Intrigue; ?>		<?php 
		if (!empty($larp_group->WhatHappened) || !empty($larp_group->WhatHappendToOthers) || !empty($larp_group->WhatHappensAfterLarp)) {
		    echo "<h3>Vad hände med/för $group->Name ?</h3>";
		    echo  nl2br(htmlspecialchars($larp_group->WhatHappened));
		    echo "<h3>Vad hände med/för andra?</h3>";
		    echo  nl2br(htmlspecialchars($larp_group->WhatHappendToOthers));
		    echo "<h3>Vad händer fram till nästa lajv?</h3>";
		    echo  nl2br(htmlspecialchars($larp_group->WhatHappensAfterLarp));
		}
		    ?>
		
		<?php 
		if (!empty($intrigues)) {
		    echo "<table class='data'>";
		    echo "<tr><th>Intrig</th><th>Intrigtext</th><th></th></tr>";
	        foreach ($intrigues as $intrigue) {
	           echo "<tr>";
	           echo "<td><a href='view_intrigue.php?Id=$intrigue->Id'>Intrigspår: $intrigue->Number. $intrigue->Name</a>";
	           if (!$intrigue->isActive()) echo " (inte aktuell)";
	           echo "</td>";
	           $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $group);
	           echo "<td>";
	           if ($intrigue->isActive()) {
	               if (!empty($intrigue->CommonText)) echo nl2br(htmlspecialchars($intrigue->CommonText))."<br><br>";
	               echo nl2br($intrigueActor->IntrigueText);
	               if (!empty($intrigueActor->OffInfo)) {
	                   echo "<br><br><strong>Off-information:</strong><br>".nl2br($intrigueActor->OffInfo);
	               }
	               if (!empty($intrigueActor->WhatHappened)) {
	                   echo "<br><br><strong>Vad hände:</strong><br>".nl2br(htmlspecialchars($intrigueActor->WhatHappened));
	               }
	               
	               echo "</td>";
	               echo "<td><a href='actor_intrigue_form.php?IntrigueActorId=$intrigueActor->Id&name=$group->Name'><i class='fa-solid fa-pen'></i></a></td>";
	               echo "</tr>";
	           }
	           else {
	               if (!empty($intrigue->CommonText)) echo "<s>".nl2br(htmlspecialchars($intrigue->CommonText))."</s><br><br>";
	               echo "<s>$intrigueActor->IntrigueText</s>";
	               echo "</td>";
	               echo "</tr>";
	           }
	        }
	        echo "</table>";
	       echo "<br>";
		}
		
		$known_groups = $group->getAllKnownGroups($current_larp);
		$known_roles = $group->getAllKnownRoles($current_larp);
		$known_props = $group->getAllKnownProps($current_larp);
		$known_pdfs = $group->getAllKnownPdfs($current_larp);
		
		$checkin_letters = $group->getAllCheckinLetters($current_larp);
		$checkin_telegrams = $group->getAllCheckinTelegrams($current_larp);
		$checkin_props = $group->getAllCheckinProps($current_larp);
		
		
		
		echo "<h3>Känner till</h3>";
		echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		$temp=0;
		$cols=5;
		foreach ($known_groups as $known_group) {
		    echo "<li style='display:table-cell; width:19%;'>";
		    echo "<div class='name'>$known_group->Name</div>";
		    echo "<div>Grupp</div>";
		    if ($known_group->hasImage()) {
		        echo "<img src='../includes/display_image.php?id=$known_group->ImageId'/>\n";
		    }
		    echo "</li>";
		    
		    $temp++;
		    if($temp==$cols)
		    {
		        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		        $temp=0;
		    }
		}
		foreach ($known_roles as $known_role) {
		    echo "<li style='display:table-cell; width:19%;'>";
		    echo "<div class='name'>$known_role->Name</div>";
		    $role_group = $known_role->getGroup();
		    if (!empty($role_group)) {
		        echo "<div>$role_group->Name</div>";
		    }
		    
		    if ($known_role->isPC($current_larp) && !$known_role->isRegistered($current_larp)) echo "Inte anmäld";
		    elseif ($known_role->isNPC($current_larp)) {
		        $assignment = NPC_assignment::getAssignment($known_role, $current_larp);
		        if (!empty($assignment)) {
		            $person = $assignment->getPerson();
		            if (!empty($person)) echo "<div>NPC - Spelas av ".$person->getViewLink()."</div>";
		            else echo "<div>NPC - Spelare inte tilldelad ännu</div>";
		        } else {
		            echo "NPC - Spelas inte";
		        }
		    }
		    
		    if ($known_role->hasImage()) {
		        echo "<img src='../includes/display_image.php?id=$known_role->ImageId'/>\n";
		    }
		    echo "</li>";
		    $temp++;
		    if($temp==$cols)
		    {
		        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		        $temp=0;
		    }
		}
		
		foreach ($known_props as $known_prop) {
		    $prop = $known_prop->getIntrigueProp()->getProp();
		    echo "<li style='display:table-cell; width:19%;'>\n";
		    echo "<div class='name'>$prop->Name</div>\n";
		    if ($prop->hasImage()) {
		        echo "<td>";
		        echo "<img width='100' src='../includes/display_image.php?id=$prop->ImageId'/>\n";
		    }
		    echo "</li>\n";
		    $temp++;
		    if($temp==$cols)
		    {
		        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		        $temp=0;
		    }
		}
		echo "</ul>";

		foreach ($known_pdfs as $known_pdf) {
		    $intrigue_pdf = $known_pdf->getIntriguePDF();
		    echo "<a href='view_intrigue_pdf.php?id=$intrigue_pdf->Id' target='_blank'>$intrigue_pdf->Filename</a>";
		    echo "<br>";
		}
		
		
		
		if (!empty($checkin_letters) || !empty($checkin_telegrams) || !empty($checkin_props)) {
		    echo "<h3>Ska ha vid incheckning</h3>";
		    foreach ($checkin_letters as $checkin_letter) {
		        $letter = $checkin_letter->getIntrigueLetter()->getLetter();
		        echo "Brev från: $letter->Signature till: $letter->Recipient, ".mb_strimwidth(str_replace('\n', '<br>', $letter->Message), 0, 50, '...');
		        echo "<br>";
		    }
		    foreach ($checkin_telegrams as $checkin_telegram) {
		        $telegram=$checkin_telegram->getIntrigueTelegram()->getTelegram();
		        echo "Telegram från: $telegram->Sender till: $telegram->Reciever, ".mb_strimwidth(str_replace('\n', '<br>', $telegram->Message), 0, 50, '...');
		        echo "<br>";
		    }
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		    $temp=0;
		    $cols=5;
		    foreach ($checkin_props as $checkin_prop) {
		        $prop=$checkin_prop->getIntrigueProp()->getProp();
		        echo "<li style='display:table-cell; width:19%;'>\n";
		        echo "<div class='name'>$prop->Name</div>\n";
		        if ($prop->hasImage()) {
		            echo "<td>";
		            echo "<img width='100' src='../includes/display_image.php?id=$prop->ImageId'/>\n";
		        }
		        echo "</li>\n";
		        $temp++;
		        if($temp==$cols)
		        {
		            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		            $temp=0;
		        }
		    }
		    echo "</ul>";
		}
		
		
        ?>
		
		</div>
		
		<?php 
		if ($current_larp->hasRumours()) {
		
		?>
		
		<h2>Rykten</h2>
		<div>
		<h3>Rykten som <?php echo $group->Name ?> känner till <a href='rumour_for.php?GroupId=<?php echo $group->Id ?>'><i class='fa-solid fa-plus' title='Tilldela rykten till <?php echo $group->Name ?>'></i></a></h3>
		<?php 
		$rumours = Rumour::allKnownByGroup($current_larp, $group);
		foreach($rumours as $rumour) {
		    echo "$rumour->Text <a href='rumour_form.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a><br>";
		}
		?>	
		
		<h3>Rykten som handlar om <?php echo $group->Name ?></h3>
		<?php 
		$rumours = Rumour::allConcernedByGroup($current_larp, $group);
		foreach($rumours as $rumour) {
		    echo "$rumour->Text <a href='rumour_form.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a><br>";
		}
		?>
		
		</div>
		<?php 
		}
		?>
		
				<h2>Handel</h2>
		<div>
		<?php 
		$currency = $current_larp->getCampaign()->Currency;
		$titledeeds = Titledeed::getAllForGroup($group);
		foreach ($titledeeds as $titledeed) {
		    echo "<a href='view_titledeed.php?id=$titledeed->Id'>$titledeed->Name</a>";
		    if (!$titledeed->isGeneric()) {
		        $numberOfOwners = $titledeed->numberOfOwners();
		        if ($numberOfOwners > 1) echo " 1/$numberOfOwners";
		    }
		    if ($titledeed->isInUse()) echo ", <a href='resource_titledeed_form.php?Id=$titledeed->Id'>Resultat ".$titledeed->calculateResult()." $currency</a>";
		    else echo ", ej i spel";
		    echo "<br>";
		    $produces_normally = $titledeed->ProducesNormally();
		    if (!empty($produces_normally)) echo "Tillgångar: ". commaStringFromArrayObject($produces_normally) . "<br>\n";
		    $requires_normally = $titledeed->RequiresNormally();
		    if (!empty($requires_normally)) echo "Behöver: " . commaStringFromArrayObject($requires_normally)."<br>\n";
		    echo "<br>";
		    
		}
		echo "Pengar vid lajvets start $larp_group->StartingMoney $currency";
		
		
		
		
		
		
		?>
		<?php } ?>
		 </div>
		
		
		
		<h2>Anteckningar (visas inte för deltagarna)</h2>
		<div>
		<?php echo $group->OrganizerNotes; ?>
		</div>
		
		
		<?php include 'print_group_history.php';?>	

	</div>


</body>
</html>
