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

$current_group = Group::loadById($GroupId); 

if (!$current_user->isMember($current_group) && !$current_user->isGroupLeader($current_group)) {
    header('Location: index.php?error=no_member'); //Inte medlem i gruppen
    exit;
}

if (!$current_group->isRegistered($current_larp)) {
    header('Location: index.php?error=not_registered'); //Gruppen är inte anmäld
    exit;
}

$larp_group = LARP_Group::loadByIds($current_group->Id, $current_larp->Id);


$main_characters_in_group = Role::getAllMainRolesInGroup($current_group, $current_larp);
$non_main_characters_in_group = Role::getAllNonMainRolesInGroup($current_group, $current_larp);

function print_role(Role $role, Group $group) {
    global $current_user, $current_larp;
    
    echo "<li>\n";
    echo "<div class='name'>$role->Name";
    if ($current_user->isGroupLeader($group)) {
        echo " <a href='logic/remove_group_member.php?groupID=<?php echo $group->Id; ?>&roleID=<?php echo $role->Id; ?>' onclick=\"return confirm('Är du säker på att du vill ta bort karaktären från gruppen?');\">";
        echo "<i class='fa-solid fa-trash-can'></i>";
        echo "</a>";
    }
    echo "</div>\n";
    echo "Yrke: ".$role->Profession . "<br>";
    if ($role->isMain($current_larp)==0) {
        echo "Sidokaraktär<br>";
    }
    echo "Spelas av ".$role->getPerson()->Name."<br>";
    
    if ($role->getPerson()->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
        echo "Ansvarig vuxen är " . $role->getRegistration($current_larp)->getGuardian()->Name;
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


	<div class="content">
		<h1><?php echo $current_group->Name;?> 
		<a href='group_sheet.php?id=<?php echo $current_group->Id;?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Gruppblad'></i></a>
		</h1>
		<div>
		<table>
			<tr><td valign="top" class="header">Gruppansvarig</td><td><?php echo Person::loadById($current_group->PersonId)->Name;?></td>
			<?php 
			if ($current_group->hasImage()) {
    		    
			    $image = Image::loadById($current_group->ImageId);
    		    echo "<td rowspan='20' valign='top'>";
    		    echo "<img width='300' src='../includes/display_image.php?id=$current_group->ImageId'/>\n";
    		    echo "</td>";
    		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
    		}
    		?>
			
			</tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo nl2br(htmlspecialchars($current_group->Description));?></td></tr>
			<tr><td valign="top" class="header">Beskrivning för andra</td><td><?php echo nl2br(htmlspecialchars($current_group->DescriptionForOthers));?></td></tr>
			<tr><td valign="top" class="header">Vänner</td><td><?php echo nl2br(htmlspecialchars($current_group->Friends));?></td></tr>
			<tr><td valign="top" class="header">Fiender</td><td><?php echo nl2br(htmlspecialchars($current_group->Enemies));?></td></tr>
			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Rikedom</td><td><?php echo $current_group->getWealth()->Name; ?></td></tr>
			<?php }?>
			<?php if (PlaceOfResidence::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Var bor gruppen?</td><td><?php echo $current_group->getPlaceOfResidence()->Name; ?></td></tr>
			<?php }?>


			<?php if (GroupType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av grupp</td><td><?php echo $current_group->getGroupType()->Name; ?></td></tr>
			<?php }?>
			<?php if (ShipType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av skepp</td><td><?php echo $current_group->getShipType()->Name; ?></td></tr>
			<?php }?>
			<?php if (Colour::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Färg</td><td><?php echo $current_group->getColour()->Name; ?></td></tr>
			<?php }?>

			<tr><td valign="top" class="header">Intrig</td><td><?php echo ja_nej($larp_group->WantIntrigue); ?></td></tr>
			<?php if (IntrigueType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($current_group->getIntrigueTypes()); ?></td></tr>
			<?php } ?>
			<?php if ($current_user->isGroupLeader($current_group)) { ?>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo nl2br(htmlspecialchars($current_group->IntrigueIdeas));?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Kvarvarande intriger</td><td><?php echo nl2br(htmlspecialchars($larp_group->RemainingIntrigues)); ?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo nl2br(htmlspecialchars($current_group->OtherInformation));?></td></tr>
			<tr><td valign="top" class="header">Antal medlemmar</td><td><?php echo $larp_group->ApproximateNumberOfMembers;?></td></tr>
			<tr><td valign="top" class="header">Önskat boende</td><td><?php echo HousingRequest::loadById($larp_group->HousingRequestId)->Name;?></td></tr>


			<tr><td valign="top" class="header">Typ av tält</td><td><?php echo nl2br(htmlspecialchars($larp_group->TentType)); ?></td></tr>
			<tr><td valign="top" class="header">Storlek på tält</td><td><?php echo nl2br(htmlspecialchars($larp_group->TentSize)); ?></td></tr>
			<tr><td valign="top" class="header">Vilka ska bo i tältet</td><td><?php echo nl2br(htmlspecialchars($larp_group->TentHousing)); ?></td></tr>
			<tr><td valign="top" class="header">Önskad placering</td><td><?php echo nl2br(htmlspecialchars($larp_group->TentPlace)); ?></td></tr>

			<tr><td valign="top" class="header">Eldplats</td><td><?php echo ja_nej($larp_group->NeedFireplace);?></td></tr>
		</table>		
		
		
		<h2>Anmälda medlemmar</h2>

		<?php 

		
		echo "<div class='container' style ='box-shadow: none; margin: 0px; padding: 0px;'>\n";
		if (empty($main_characters_in_group) && empty($non_main_characters_in_group)) {
		    echo "Inga anmälda i gruppen än.";
		}
		else {
		    echo "<ul class='image-gallery'>\n";
		    foreach ($main_characters_in_group as $role) {
		        print_role($role, $current_group);
		    }
		    echo "</ul>\n";
		    if (!empty($non_main_characters_in_group)) {
    		    echo "<h3>Sidokaraktärer</h3>";
    		    echo "<ul class='image-gallery'>\n";
    		    foreach ($non_main_characters_in_group as $role) {
    		        print_role($role, $current_group);
    		    }
    		    echo "</ul>\n";
		    }
		}
		
		echo "</DIV>\n";
		
		
		?>
		</div>    

		<h2>Intrig</h2>
		<div>

			<?php 
			if ($current_larp->isIntriguesReleased()) {
			    echo "<p>".nl2br($larp_group->Intrigue) ."</p>"; 
			    
			    
			    $intrigues = Intrigue::getAllIntriguesForGroup($current_group->Id, $current_larp->Id);
			    $intrigue_numbers = array();
		        foreach ($intrigues as $intrigue) {
		            if ($intrigue->isActive()) {
		                $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $current_group);
		                if (!empty($intrigue->CommonText)) echo "<p>".nl2br(htmlspecialchars($intrigue->CommonText))."</p>";
		                if (!empty($intrigueActor->IntrigueText)) echo "<p>".nl2br($intrigueActor->IntrigueText). "</p>";
		                if (!empty($intrigueActor->OffInfo)) {
		                    echo "<p><strong>Off-information:</strong><br><i>".nl2br($intrigueActor->OffInfo)."</i></p>";
		                }
		                
		                if (!empty($intrigueActor->IntrigueText) || !empty($intrigue->CommonText) || !empty($intrigueActor->OffInfo)) $intrigue_numbers[] = $intrigue->Number;
		            }
		        }
		        if (!empty($intrigue_numbers)) {
		            echo "<p>Intrignummer " . implode(', ', $intrigue_numbers).". De kan behövas om du behöver hjälp av arrangörerna med en intrig under lajvet.</p>";
                }
                
                $known_groups = $current_group->getAllKnownGroups($current_larp);
                $known_roles = $current_group->getAllKnownRoles($current_larp);
                $known_npcgroups = $current_group->getAllKnownNPCGroups($current_larp);
                $known_npcs = $current_group->getAllKnownNPCs($current_larp);
                $known_props = $current_group->getAllKnownProps($current_larp);
                $known_pdfs = $current_group->getAllKnownPdfs($current_larp);
                
                $checkin_letters = $current_group->getAllCheckinLetters($current_larp);
                $checkin_telegrams = $current_group->getAllCheckinTelegrams($current_larp);
                $checkin_props = $current_group->getAllCheckinProps($current_larp);
                
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
		        $rumours = Rumour::allKnownByGroup($current_larp, $current_group);
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
			
			
					<?php 
		$previous_larps = $current_group->getPreviousLarps();
		if (isset($previous_larps) && count($previous_larps) > 0) {
		    
		    echo "<h2>Historik</h2>";
		    foreach ($previous_larps as $prevoius_larp) {
		        $previous_larp_group = LARP_Group::loadByIds($current_group->Id, $prevoius_larp->Id);
		        echo "<div class='border'>";
		        echo "<h3>$prevoius_larp->Name</h3>";
		        if (!empty($previous_larp_group->Intrigue)) {
		            echo "<strong>Intrig</strong><br>";
		            echo "<p>".nl2br($previous_larp_group->Intrigue)."</p>";
		        }
		        
		        $intrigues = Intrigue::getAllIntriguesForGroup($current_group->Id, $prevoius_larp->Id);
		        foreach($intrigues as $intrigue) {
		            $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $current_group);
		            if ($intrigue->isActive() && !empty($intrigueActor->IntrigueText)) {
		                echo "<p><strong>Intrig</strong><br>".nl2br($intrigueActor->IntrigueText)."</p>";
		                
		                echo "<p><strong>Vad hände med det?</strong><br>";
		                if (!empty($intrigueActor->WhatHappened)) echo nl2br($intrigueActor->WhatHappened);
		                else echo "Inget rapporterat";
		                echo "</p>";
		            }
		        }
		        
		        echo "<br><strong>Vad hände för $current_group->Name?</strong><br>";
		        if (isset($previous_larp_group->WhatHappened) && $previous_larp_group->WhatHappened != "")
		            echo nl2br(htmlspecialchars($previous_larp_group->WhatHappened));
		            else echo "Inget rapporterat";
	            echo "<br><strong>Vad hände för andra?</strong><br>";
	            if (isset($previous_larp_group->WhatHappendToOthers) && $previous_larp_group->WhatHappendToOthers != "")
	                echo nl2br(htmlspecialchars($previous_larp_group->WhatHappendToOthers));
	                else echo "Inget rapporterat";
	            echo "</div>";
		                
		    }
		}
			    
			
			
		?>
			
			
			
		</div>

	</div>


</body>
</html>
