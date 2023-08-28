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


$main_characters_in_group = Role::getAllMainRolesInGroup($current_group, $current_larp);
$non_main_characters_in_group = Role::getAllNonMainRolesInGroup($current_group, $current_larp);


function print_role($role, $group) {
    global $current_larp;
    echo "<tr>";
    echo "<td>";
    if ($role->hasImage()) {
        
        $image = Image::loadById($role->ImageId);
        echo " <img width=30 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>";
    }
    echo "</td>";
    
    echo "<td><a href='view_role.php?id=" . $role->Id . "'>$role->Name</a>";
    echo "</td>";
    echo "<td>";
    echo " <a href='edit_role.php?id=" . $role->Id . "'><i class='fa-solid fa-pen' title='Ändra'></i></a>";
    echo " <a href='logic/remove_group_member.php?groupID=$group->Id&roleID=$role->Id".
        "onclick='return confirm(\"Är du säker på att du vill ta bort karaktären från gruppen?\");'><i class='fa-solid fa-trash-can' title='Ta bort ur gruppen'></i></a>";
    echo "</td>";
    echo "<td>$role->Profession</td>";

    $person = $role->getPerson();
    echo "<td><a href ='view_person.php?id=$person->Id'>$person->Name</a></td>";
        
    
    echo "<td>";
    if ($role->getPerson()->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
        echo "Ansvarig vuxen är ";
        $registration = Registration::loadByIds($role->PersonId, $current_larp->Id);
        if (!empty($registration->GuardianId)) { 
            $guardian = $registration->getGuardian();   
            echo "<a href ='view_person.php?id=$guardian->Id'>$guardian->Name</a>"; 
        } else echo showStatusIcon(false);
        
    }
    
    echo "</td>";

    echo "</tr>";
}

include 'navigation.php';
?>


	<div class="content">
		<h1><?php echo $current_group->Name;?>&nbsp;
		<?php if ($current_group->IsDead ==1) echo "<i class='fa-solid fa-skull-crossbones' title='Död'></i>"?>
		<a href='edit_group.php?id=<?php echo $current_group->Id;?>'><i class='fa-solid fa-pen'></i></a> 
		</h1>		
		<a href='group_sheet.php?id=<?php echo $current_group->Id;?>' target='_blank'>
		<i class='fa-solid fa-file-pdf' title='Gruppblad för <?php echo $current_group->Name;?>'></i>Gruppblad för <?php echo $current_group->Name;?></a> &nbsp;

		<a href='group_sheet.php?id=<?php echo $current_group->Id;?>&all_info=<?php echo date_format(new Datetime(),"suv") ?>' target='_blank'>
		<i class='fa-solid fa-file-pdf' title='All info om <?php echo $current_group->Name;?>'></i>All info om <?php echo $current_group->Name;?></a> &nbsp;
		<br><br>
        <?php if ($larp_group->UserMayEdit  == 1) {
                echo "Gruppledaren får ändra gruppen " . showStatusIcon(false);
                $editButton = "Ta bort tillåtelsen att ändra";
            }
            else {
                
                $editButton = "Tillåt gruppledaren att ändra gruppen";
            }
                  
                ?>
            <form action="logic/toggle_user_may_edit_group.php" method="post"><input type="hidden" id="groupId" name="groupId" value="<?php echo $current_group->Id;?>"><input type="submit" value="<?php echo $editButton;?>"></form>
		
		<div>
		<table>
			<tr><td valign="top" class="header">Gruppansvarig</td><td><a href ="view_person.php?id=<?php echo $current_group->PersonId;?>"><?php echo $current_group->getPerson()->Name;?></a></td>
					<?php 
					if ($current_group->hasImage()) {
		    
					    $image = Image::loadById($current_group->ImageId);
            		    echo "<td rowspan='20' valign='top'><img width='300' src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>";
            		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
            		    echo "</td>";
            		}
            		?>
			
			</tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo $current_group->Description;?></td></tr>
			<tr><td valign="top" class="header">Beskrivning för andra</td><td><?php echo $current_group->DescriptionForOthers;?></td></tr>
			<tr><td valign="top" class="header">Vänner</td><td><?php echo $current_group->Friends;?></td></tr>
			<tr><td valign="top" class="header">Fiender</td><td><?php echo $current_group->Enemies;?></td></tr>
			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Rikedom</td><td><?php echo $current_group->getWealth()->Name;?></td></tr>
			<?php } ?>
			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Var bor gruppen?</td><td><?php echo $current_group->getPlaceOfResidence()->Name;?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Intrig</td><td><?php echo ja_nej($larp_group->WantIntrigue);?></td></tr>
			<?php if (IntrigueType::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($current_group->getIntrigueTypes());?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo $current_group->IntrigueIdeas;?></td></tr>
			<tr><td valign="top" class="header">Kvarvarande intriger</td><td><?php echo $larp_group->RemainingIntrigues; ?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $current_group->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Uppskattat<br>antal medlemmar</td><td><?php echo $larp_group->ApproximateNumberOfMembers;?></td></tr>
			<?php if (HousingRequest::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Önskat boende</td><td><?php echo HousingRequest::loadById($larp_group->HousingRequestId)->Name;?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Eldplats</td><td><?php echo ja_nej($larp_group->NeedFireplace);?></td></tr>
			<tr><td valign="top" class="header">Död/Ej i spel</td><td><?php echo ja_nej($current_group->IsDead);?></td></tr>
		</table>		
		</div>
		
		<h2>Anmälda medlemmar</h2>
		<?php 
        echo "<div>";
		echo "<table>";
		foreach($main_characters_in_group as $group_member) {
		    print_role($group_member, $current_group);
		}
		echo "</table>";
		if (!empty($non_main_characters_in_group)) {
		    echo "<h3>Sidokaraktärer</h3>";
		    
		    echo "<table>";
		    foreach($non_main_characters_in_group as $group_member) {
		        print_role($group_member, $current_group);
		    }
		    echo "</table>";
		    
		}
		echo "</div>";
		?>
		<h2>Intrig <a href='edit_group_intrigue.php?id=<?php echo $current_group->Id ?>'><i class='fa-solid fa-pen'></i></a></h2>
		<div>
		<?php echo $larp_group->Intrigue; ?>
		<?php 

		$known_actors = array();
		$known_npcs = array();
		$known_npcgroups = array();
		$known_props = array();
		$checkin_letters = array();
		$checkin_telegrams = array();
		$checkin_props = array();
		if (!empty($intrigues)) {
		    echo "<table class='data'>";
		    echo "<tr><th>Intrig</th><th>Intrigtext</th><th></th></tr>";
	        foreach ($intrigues as $intrigue) {
	           echo "<tr>";
	           echo "<td><a href='view_intrigue.php?Id=$intrigue->Id'>Intrig: $intrigue->Number. $intrigue->Name</a>";
	           if (!$intrigue->isActive()) echo " (inte aktuell)";
	           echo "</td>";
	           $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $current_group);
	           echo "<td>";
	           if ($intrigue->isActive()) {
	               echo "$intrigueActor->IntrigueText";
	               if (!empty($intrigueActor->OffInfo)) {
	                   echo "<br><br><strong>Off-information:</strong><br>$intrigueActor->OffInfo";
	               }
	               echo "</td>";
	               echo "<td><a href='actor_intrigue_form.php?IntrigueActorId=$intrigueActor->Id&name=$current_group->Name'><i class='fa-solid fa-pen'></i></a></td>";
	               echo "</tr>";
	               $known_actors = array_merge($known_actors, $intrigueActor->getAllKnownActors());
	               $known_npcs = array_merge($known_npcs, $intrigueActor->getAllKnownNPCs());
	               $known_props = array_merge($known_props, $intrigueActor->getAllKnownProps());
	               $known_npcgroups = array_merge($known_npcgroups, $intrigueActor->getAllKnownNPCGroups());
	               $checkin_letters = array_merge($checkin_letters, $intrigueActor->getAllCheckinLetters());
	               $checkin_telegrams = array_merge($checkin_telegrams, $intrigueActor->getAllCheckinTelegrams());
	               $checkin_props = array_merge($checkin_props, $intrigueActor->getAllCheckinProps());
	           }
	           else {
	               echo "<s>$intrigueActor->IntrigueText</s>";
	               echo "</td>";
	               echo "</tr>";
	           }
	        }
	        echo "</table>";
	       echo "<br>";
		}
		
		echo "<h3>Känner till</h3>";
		echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		$temp=0;
		$cols=5;
		foreach ($known_actors as $known_actor) {
		    $knownIntrigueActor = $known_actor->getKnownIntrigueActor();
		    
		    if (!empty($knownIntrigueActor->GroupId)) {
		        $group=$knownIntrigueActor->getGroup();
		        echo "<li style='display:table-cell; width:19%;'>";
		        echo "<div class='name'>$group->Name</div>";
		        echo "<div>Grupp</div>";
		        echo "</li>";
		        
		    } else {
		        $role = $knownIntrigueActor->getRole();
		        echo "<li style='display:table-cell; width:19%;'>";
		        echo "<div class='name'>$role->Name</div>";
		        $role_group = $role->getGroup();
		        if (!empty($role_group)) {
		            echo "<div>$role_group->Name</div>";
		        }
		        
		        if ($role->hasImage()) {
		            $image = Image::loadById($role->ImageId);
		            if (!is_null($image)) {
		                
		                echo "<img src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
		            }
		        }
		        echo "</li>";
		        
		    }
		    $temp++;
		    if($temp==$cols)
		    {
		        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		        $temp=0;
		    }
		}
		foreach ($known_npcgroups as $known_npcgroup) {
		    $npcgroup=$known_npcgroup->getIntrigueNPCGroup()->getNPCGroup();
		    echo "<li style='display:table-cell; width:19%;'>\n";
		    echo "<div class='name'>$npcgroup->Name</div>\n";
		    echo "<div>NPC-grupp</div>";
		    echo "</li>\n";
		    $temp++;
		    if($temp==$cols)
		    {
		        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		        $temp=0;
		    }
		}
		foreach ($known_npcs as $known_npc) {
		    $npc=$known_npc->getIntrigueNPC()->getNPC();
		    echo "<li style='display:table-cell; width:19%;'>\n";
		    echo "<div class='name'>$npc->Name</div>\n";
		    $npc_group = $npc->getNPCGroup();
		    if (!empty($npc_group)) {
		        echo "<div>$npc_group->Name</div>";
		    }
		    if ($npc->hasImage()) {
		        $image = Image::loadById($npc->ImageId);
		        echo "<td><img width=100 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
		    }
		    echo "</li>\n";
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
		        $image = Image::loadById($prop->ImageId);
		        echo "<td><img width=100 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
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
		            $image = Image::loadById($prop->ImageId);
		            echo "<td><img width=100 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
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
		<h2>Rykten</h2>
		<div>
		<h3>Rykten som <?php echo $current_group->Name ?> känner till</h3>
		<?php 
		$rumours = Rumour::allKnownByGroup($current_larp, $current_group);
		foreach($rumours as $rumour) {
		    echo "$rumour->Text <a href='rumour_form.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a><br>";
		}
		?>	
		
		<h3>Rykten som handlar om <?php echo $current_group->Name ?></h3>
		<?php 
		$rumours = Rumour::allConcernedByGroup($current_larp, $current_group);
		foreach($rumours as $rumour) {
		    echo "$rumour->Text <a href='rumour_form.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a><br>";
		}
		?>
		
		</div>
		
				<h2>Handel</h2>
		<div>
		<?php 
		$currency = $current_larp->getCampaign()->Currency;
		$titledeeds = Titledeed::getAllForGroup($current_group);
		foreach ($titledeeds as $titledeed) {
		    $numberOfOwners = $titledeed->numberOfOwners();
		    echo "<a href='titledeed_form.php?operation=update&id=" . $titledeed->Id . "'>$titledeed->Name</a>";
		    if ($numberOfOwners > 1) echo " 1/$numberOfOwners";
		    echo ", <a href='resource_titledeed_form.php?Id=$titledeed->Id'>Resultat ".$titledeed->calculateResult()." $currency</a>";
		    echo "<br>";
		    $produces_normally = $titledeed->ProducesNormally();
		    if (!empty($produces_normally)) echo "Producerar: ". commaStringFromArrayObject($produces_normally) . "<br>\n";
		    $requires_normally = $titledeed->RequiresNormally();
		    if (!empty($requires_normally)) echo "Behöver: " . commaStringFromArrayObject($requires_normally)."<br>\n";
		    echo "<br>";
		    
		}
		echo "Pengar vid lajvets start $larp_group->StartingMoney $currency";
		
		
		
		
		
		
		?>
		
		 </div>
		
		
		
		<h2>Anteckningar (visas inte för deltagarna)</h2>
		<div>
		<?php echo $current_group->OrganizerNotes; ?>
		</div>
		

	</div>


</body>
</html>
