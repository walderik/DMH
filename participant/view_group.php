<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $GroupId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}
$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));

if($isMob){
    $columns=2;
    $type="Mobile";
    //echo 'Using Mobile Device...';
}else{
    $columns=5;
    $type="Computer";
    //echo 'Using Desktop...';
}
$temp=0;


$group = Group::loadById($GroupId); 

if (!$current_person->isMemberGroup($group) && !$current_person->isGroupLeader($group)) {
    header('Location: index.php?error=no_member'); //Inte medlem i gruppen
    exit;
}

$larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);


$main_characters_in_group = Role::getAllMainRolesInGroup($group, $current_larp);
$non_main_characters_in_group = Role::getAllNonMainRolesInGroup($group, $current_larp);
$allUnregisteredRoles = Role::getAllUnregisteredRolesInGroup($group, $current_larp);
$NPCs_in_group = Role::getAllNPCsInGroup($group);

function print_role(Role $role, Group $group, $isComing) {
    global $current_person, $current_larp, $type;
    $isNPC = false;
    if (is_null($role->PersonId)) $isNPC = true; 
    
    if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
    else echo "<li style='display:table-cell; width:49%;'>\n";
    
    echo "<div class='name'>";
    if ($isNPC) {
        echo $role->getViewLink();
    } else echo $role->Name;
    if ($current_person->isGroupLeader($group)) {
        echo " <a href='logic/remove_group_member.php?groupID=".$group->Id."&roleID=".$role->Id."' onclick=\"return confirm('Är du säker på att du vill ta bort karaktären från gruppen?');\">";
        echo "<i class='fa-solid fa-trash-can'></i>";
        echo "</a>";
    }
    echo "</div>\n";
    echo "Yrke: ".$role->Profession . "<br>";
    if ($role->isMain($current_larp)==0) {
        echo "Sidokaraktär<br>";
    }

    echo "Spelas av ";

    if ($isNPC) echo "NPC";
    else {
        $person =  $role->getPerson();
        $person->Name;
    }
    echo "<br>";
    
    if ($isComing && !$isNPC && $person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
        $guardian = $role->getRegistration($current_larp)->getGuardian();
        if (isset($guardian)) echo "Ansvarig vuxen är " . $guardian->Name;
        else echo "Ansvarig vuxen är inte utpekad.";
    }
    
    echo "<div class='description'>$role->DescriptionForGroup</div>\n";
    if ($role->hasImage()) {
        $image = Image::loadById($role->ImageId);
        echo "<img src='../includes/display_image.php?id=$role->ImageId'/>\n";
        if (!empty($image->Photographer)) {
            echo "<div class='photographer'>Fotograf $image->Photographer</div>\n";
        }
    }
    else {
        echo "<img src='../images/man-shape.png' />\n";
        echo "<div class='photographer'><a href='https://www.flaticon.com/free-icons/man' title='man icons'>Man icons created by Freepik - Flaticon</a></div>\n";
    }
    echo "</li>\n\n";
    
}

include 'navigation.php';
?>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-people-group"></i>
			<?php echo $group->Name;?>
			<a href='group_sheet.php?id=<?php echo $group->Id;?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Gruppblad'></i></a>
			<?php 
			if ($current_person->isGroupLeader($group) && (!$group->isRegistered($current_larp) || $group->userMayEdit($current_larp))) {
			    echo " " . $group->getEditLinkPen(false);
			}
			?>
    		
		</div>
		
		<?php 
		if ($group->hasImage()) {
		    echo "<div class='itemcontainer'>";
		    $image = Image::loadById($group->ImageId);
		    echo "<img width='300' src='../includes/display_image.php?id=$group->ImageId'/>\n";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    echo "</div>";
		}
		?>

	   <div class='itemcontainer'>
       <div class='itemname'>Gruppansvarig</div>
	   <?php echo Person::loadById($group->PersonId)->Name;?>
	   </div>

	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning</div>
	   <?php echo nl2br(htmlspecialchars($group->Description));?>
	   </div>

	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning för andra</div>
	   <?php echo nl2br(htmlspecialchars($group->DescriptionForOthers));?>
	   </div>

	   <div class='itemcontainer'>
       <div class='itemname'>Vänner</div>
	   <?php echo nl2br(htmlspecialchars($group->Friends));?>
	   </div>
			
	   <div class='itemcontainer'>
       <div class='itemname'>Fiender</div>
	   <?php echo nl2br(htmlspecialchars($group->Enemies));?>
	   </div>

		<?php if (Wealth::isInUse($current_larp)) {?>
		   <div class='itemcontainer'>
           <div class='itemname'>Rikedom</div>
    	   <?php echo $group->getWealth()->Name; ?>
    	   </div>
		<?php }?>
		
		<?php if (PlaceOfResidence::isInUse($current_larp)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Var bor gruppen?</div>
    	   <?php echo $group->getPlaceOfResidence()->Name; ?>
    	   </div>
		<?php }?>


		<?php if (GroupType::isInUse($current_larp)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Typ av grupp</div>
    	   <?php echo $group->getGroupType()->Name; ?>
    	   </div>
		<?php }?>

		<?php if (ShipType::isInUse($current_larp)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Typ av skepp</div>
    	   <?php echo $group->getShipType()->Name; ?>
    	   </div>
		<?php }?>
		
		<?php if ($current_larp->getCampaign()->is_me()) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Färg</div>
    	   <?php echo $group->Colour; ?>
    	   </div>
		<?php }?>
		<?php if (IntrigueType::isInUse($current_larp)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Intrigtyper</div>
    	   <?php echo commaStringFromArrayObject($group->getIntrigueTypes()); ?>
    	   </div>
		<?php } ?>

		<?php if ($current_person->isGroupLeader($group)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Intrigidéer</div>
    	   <?php echo nl2br(htmlspecialchars($group->IntrigueIdeas));?>
    	   </div>
		<?php } ?>

	   <div class='itemcontainer'>
       <div class='itemname'>Annan information</div>
	   <?php echo nl2br(htmlspecialchars($group->OtherInformation)); ?>
	   </div>
	   <?php  if (isset($larp_group)) { ?>
	   
    	   <div class='itemcontainer'>
           <div class='itemname'>Önskar intrig</div>
    	   <?php echo ja_nej($larp_group->WantIntrigue); ?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Kvarvarande intriger</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->RemainingIntrigues)); ?>
    	   </div>
    
   	   	   <div class='itemcontainer'>
           <div class='itemname'>Vad har hänt?</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->WhatHappenedSinceLastLarp)); ?>
    	   </div>
    
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Uppskattat antal medlemmar på lajvet</div>
    	   <?php echo $larp_group->ApproximateNumberOfMembers;?>
    	   </div>
    
	   
    	   <div class='itemcontainer'>
           <div class='itemname'>Önskat boende</div>
    	   <?php echo HousingRequest::loadById($larp_group->HousingRequestId)->Name;?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Typ av tält</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->TentType)); ?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Storlek på tält</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->TentSize)); ?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Vilka ska bo i tältet</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->TentHousing)); ?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Önskad placering</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->TentPlace)); ?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Eldplats</div>
    	   <?php echo ja_nej($larp_group->NeedFireplace);?>
    	   </div>
	   <?php  }?>

		<div class='itemcontainer'>
		<div class='itemname'>Anmälda medlemmar</div>
		
		<?php 
		echo "<div class='container' style ='box-shadow: none; margin: 0px; padding: 0px;'>\n";
		if (empty($main_characters_in_group) && empty($non_main_characters_in_group)) {
		    echo "Inga anmälda i gruppen än.";
		}
		else {
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		    foreach ($main_characters_in_group as $role) {
		        print_role($role, $group, true);
		        $temp++;
		        if($temp==$columns) {
		            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		            $temp=0;
		        }
		    }
		    $temp=0;
		    echo "</ul>\n";

		    if (!empty($non_main_characters_in_group)) {
		        echo "<div class='itemname'>Sidokarktärer</div>";
    		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
    		    foreach ($non_main_characters_in_group as $role) {
    		        print_role($role, $group, true);
    		        $temp++;
    		        if($temp==$columns) {
    		            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
    		            $temp=0;
    		        }
    		    }
    		    $temp=0;
    		    echo "</ul>\n";
		    }
		}
		
		echo "</div>\n";
		?>
		</div>
		
		<?php
		if(!empty($allUnregisteredRoles)) {
		    echo "<div class='itemcontainer'>";
		    echo "<div class='itemname'>Icke anmälda medlemmar</div>";

			$temp=0;
			echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
			foreach($allUnregisteredRoles as $role) {
			    print_role($role, $group, false);
			    $temp++;
			    if($temp==$columns) {
			        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			        $temp=0;
			    }
			}
			$temp=0;
			echo "</ul>\n";
			echo "</div>\n";
		}
		?>
		
		<div class='itemcontainer'>
		<div class='itemname'>NPC'er i gruppen</div>

		<?php 
		echo "<div class='container' style ='box-shadow: none; margin: 0px; padding: 0px;'>\n";
		if (empty($NPCs_in_group)) {
		    echo "Det finns inga NPC'er i gruppen än.";
		}
		else {
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		    foreach ($NPCs_in_group as $role) {
		        print_role($role, $group, true);
		        $temp++;
		        if($temp==$columns) {
		            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		            $temp=0;
		        }
		    }
		    $temp=0;
		    echo "</ul>\n";

		}
		
		echo "</div>\n";
		?>
		<div class='center'><a href='role_form.php?action=insert&type=npc&groupId=<?php echo $group->Id ?>'><button class='button-18'><i class='fa-solid fa-plus'></i><i class='fa-solid fa-person'></i> &nbsp;Skapa ny NPC i gruppen</button></a></div>


		
		</div>
		
		
		
		</div>
		    
		    
		    
		    
		<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-scroll"></i> Intrig
		</div>
		<div class='itemcontainer'>
			<?php 
			if ($current_larp->isIntriguesReleased()) {
			    echo "<p>".nl2br($larp_group->Intrigue) ."</p>"; 
			    
			    
			    $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);
			    $intrigue_numbers = array();
		        foreach ($intrigues as $intrigue) {
		            if ($intrigue->isActive()) {
		                $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $group);
		                if (!empty($intrigue->CommonText)) echo "<p>".nl2br(htmlspecialchars($intrigue->CommonText))."</p>";
		                if (!empty($intrigueActor->IntrigueText)) echo "<p>".nl2br($intrigueActor->IntrigueText). "</p>";
		                if (!empty($intrigueActor->OffInfo)) {
		                    echo "<p><strong>Off-information:</strong><br><i>".nl2br($intrigueActor->OffInfo)."</i></p>";
		                }
		                
		                if (!empty($intrigueActor->IntrigueText) || !empty($intrigue->CommonText) || !empty($intrigueActor->OffInfo)) {
		                    $intrigue_numbers[] = $intrigue->Number;
		                    echo "<hr>";
		                }
		            }
		        }
		        if (!empty($intrigue_numbers)) {
		            echo "<p>Intrignummer " . implode(', ', $intrigue_numbers).". De kan behövas om du behöver hjälp av arrangörerna med en intrig under lajvet.</p>";
                }
                
                $known_groups = $group->getAllKnownGroups($current_larp);
                $known_roles = $group->getAllKnownRoles($current_larp);
                $known_npcgroups = $group->getAllKnownNPCGroups($current_larp);
                $known_npcs = $group->getAllKnownNPCs($current_larp);
                $known_props = $group->getAllKnownProps($current_larp);
                $known_pdfs = $group->getAllKnownPdfs($current_larp);
                
                $checkin_letters = $group->getAllCheckinLetters($current_larp);
                $checkin_telegrams = $group->getAllCheckinTelegrams($current_larp);
                $checkin_props = $group->getAllCheckinProps($current_larp);
                
                if (!empty($known_groups) || !empty($known_roles) || !empty($known_npcs) || !empty($known_props) || !empty($known_npcgroups)) {
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
			                echo "<td>";
			                echo "<img width='100' src='../includes/display_image.php?id=$npc->ImageId'/>\n";
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

		        foreach ($known_pdfs as $known_pdf) {
		            $intrigue_pdf = $known_pdf->getIntriguePDF();
		            echo "<a href='view_intrigue_pdf.php?id=$intrigue_pdf->Id' target='_blank'>$intrigue_pdf->Filename</a>";
		            echo "<br>";
		        }
		        
		        
		        if (!empty($checkin_letters) || !empty($checkin_telegrams) || !empty($checkin_props)) {
		            echo "<h3>Ska ha vid incheckning</h3>";
		            foreach ($checkin_letters as $checkin_letter) {
		                $letter = $checkin_letter->getIntrigueLetter()->getLetter();
		                echo "Brev från: $letter->Signature till: $letter->Recipient<br>";
		            }
		            foreach ($checkin_telegrams as $checkin_telegram) {
		                $telegram=$checkin_telegram->getIntrigueTelegram()->getTelegram();
		                echo "Telegram från: $telegram->Sender till: $telegram->Reciever<br>";
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
		        $rumours = Rumour::allKnownByGroup($current_larp, $group);
		        if (!empty($rumours)) {
    		        echo "<h2>Rykten</h2>";
    		        foreach($rumours as $rumour) {
    		            echo $rumour->Text;
    		            echo "<br>";
    		        }
		        }
		        
			}

			else {
			    echo "Intrigerna är inte klara än.";
			}
			?>
			</div>
			</div>
			
			
		<?php 
		$previous_larps = $group->getPreviousLarps();
		if (isset($previous_larps) && count($previous_larps) > 0) {
		    ?>
		    
		    <div class='itemselector'>
		    <div class="header">
		    	<i class="fa-solid fa-landmark"></i> Historik
		    </div>
		    <div class='itemcontainer'>
		    
		   <?php 
		    foreach ($previous_larps as $prevoius_larp) {
		        $previous_larp_group = LARP_Group::loadByIds($group->Id, $prevoius_larp->Id);
		        echo "<div class='border'>";
		        echo "<h3>$prevoius_larp->Name</h3>";
		        if (!empty($previous_larp_group->Intrigue)) {
		            echo "<strong>Intrig</strong><br>";
		            echo "<p>".nl2br($previous_larp_group->Intrigue)."</p>";
		        }
		        
		        $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $prevoius_larp->Id);
		        foreach($intrigues as $intrigue) {
		            $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $group);
		            if ($intrigue->isActive() && !empty($intrigueActor->IntrigueText)) {
		                echo "<p><strong>Intrig</strong><br>".nl2br($intrigueActor->IntrigueText)."</p>";
		                
		                echo "<p><strong>Vad hände med det?</strong><br>";
		                if (!empty($intrigueActor->WhatHappened)) echo nl2br($intrigueActor->WhatHappened);
		                else echo "Inget rapporterat";
		                echo "</p>";
		            }
		        }
		        
		        echo "<br><strong>Vad hände för $group->Name?</strong><br>";
		        if (isset($previous_larp_group->WhatHappened) && $previous_larp_group->WhatHappened != "")
		            echo nl2br(htmlspecialchars($previous_larp_group->WhatHappened));
	            else echo "Inget rapporterat";
	            echo "<br><strong>Vad hände för andra?</strong><br>";
	            if (isset($previous_larp_group->WhatHappendToOthers) && $previous_larp_group->WhatHappendToOthers != "")
	                echo nl2br(htmlspecialchars($previous_larp_group->WhatHappendToOthers));
                else echo "Inget rapporterat";
                echo "<br><strong>Vad händer fram till nästa lajv?</strong><br>";
                if (isset($previous_larp_group->WhatHappensAfterLarp) && $previous_larp_group->WhatHappensAfterLarp != "")
                    echo nl2br(htmlspecialchars($previous_larp_group->WhatHappensAfterLarp));
                else echo "Inget rapporterat";
                echo "</div>";
		                
		    }
		}
			    
			
			
		?>
			
			
			
		</div>

	</div>


</body>
</html>
