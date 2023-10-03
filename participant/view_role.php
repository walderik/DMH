<?php

require 'header.php';

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
$person = $role->getPerson();

if ($person->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
if (empty($larp_role)) $larp_role = Reserve_LARP_Role::loadByIds($role->Id, $current_larp->Id);

if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
}



include 'navigation.php';
?>

	<div class="content">
		<h1><?php echo $role->Name;?>
		<a href='character_sheet.php?id=<?php echo $role->Id;?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för <?php echo $role->Name;?>'></i></a>
		</h1>
		<div>
		
		<table>
			<tr><td valign="top" class="header">Godkänd</td><td><?php echo showStatusIcon($role->isApproved())." ". ja_nej($role->isApproved()) ?></td><tr>
					<tr><td valign="top" class="header">Spelas av</td><td><?php echo $person->Name; ?></td>
		<?php 
		if ($role->hasImage()) {
		    
		    $image = Image::loadById($role->ImageId);
		    echo "<td rowspan='20' valign='top'>";
		    echo "<img width='300' src='image.php?id=$role->ImageId'/>\n";
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
		<?php if (!$role->isMysLajvare()) {?>
		
			<?php if (LarperType::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Typ av lajvare</td><td>
			<?php 
			$larpertype = $role->getLarperType();
			if (!empty($larpertype)) echo $larpertype->Name;
			?>
			</td></tr>
			<tr><td valign="top" class="header">Kommentar till typ av lajvare</td><td><?php echo $role->TypeOfLarperComment;?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Tidigare lajv</td><td><?php echo $role->PreviousLarps;?></td></tr>
			<tr><td valign="top" class="header">Varför befinner sig karaktären på platsen?</td><td><?php echo $role->ReasonForBeingInSlowRiver;?></td></tr>
			<tr><td valign="top" class="header">Religion</td><td><?php echo $role->Religion;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet</td><td><?php echo $role->DarkSecret;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet - intrig idéer</td><td><?php echo nl2br($role->DarkSecretIntrigueIdeas); ?></td></tr>
			
			<?php if (IntrigueType::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($role->getIntrigueTypes());?></td></tr>
			<?php }?>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo nl2br($role->IntrigueSuggestions); ?></td></tr>
			<tr><td valign="top" class="header">Saker karaktären inte vill spela på</td><td><?php echo $role->NotAcceptableIntrigues;?></td></tr>
			<tr><td valign="top" class="header">Relationer med andra</td><td><?php echo $role->CharactersWithRelations;?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $role->OtherInformation;?></td></tr>
			
			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Rikedom</td><td>
			<?php 
			$wealth = $role->getWealth();
			if (!empty($wealth)) echo $wealth->Name;
			?>
			</td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Var är karaktären född?</td><td><?php echo $role->Birthplace;?></td></tr>
			
			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Var bor karaktären?</td><td>
			<?php 
			$por = $role->getPlaceOfResidence();
			if (!empty($por)) echo $por->Name;
			?>
			</td></tr>
			<?php } ?>
		<?php }?>
		</table>		
		</div>
		<h2>Intrig</h2>
		<div>
			<?php if ($current_larp->isIntriguesReleased()) {
			    echo "<p>".nl2br($larp_role->Intrigue) ."</p>"; 
			    
			    $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
			    $intrigue_numbers = array();
		        foreach ($intrigues as $intrigue) {
		            if ($intrigue->isActive()) {
		                $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
		                echo "<p>".nl2br($intrigueActor->IntrigueText). "</p>";
		                if (!empty($intrigueActor->OffInfo)) {
		                    echo "<p><strong>Off-information:</strong><br><i>".nl2br($intrigueActor->OffInfo)."</i></p>";
		                }
		                if (!empty($intrigueActor->IntrigueText)) $intrigue_numbers[] = $intrigue->Number;
		            }
		        }
		        if (!empty($intrigue_numbers)) {
		            echo "<p>Intrignummer " . implode(', ', $intrigue_numbers).". De kan behövas om du behöver hjälp av arrangörerna med en intrig under lajvet.</p>";
                }
                
                $known_groups = $role->getAllKnownGroups($current_larp);
                $known_roles = $role->getAllKnownRoles($current_larp);
                
                $known_npcgroups = $role->getAllKnownNPCGroups($current_larp);
                $known_npcs = $role->getAllKnownNPCs($current_larp);
                $known_props = $role->getAllKnownProps($current_larp);
                $known_pdfs = $role->getAllKnownPdfs($current_larp);
 
                $checkin_letters = $role->getAllCheckinLetters($current_larp);
                $checkin_telegrams = $role->getAllCheckinTelegrams($current_larp);
                $checkin_props = $role->getAllCheckinProps($current_larp);
                
                
                if (!empty($known_groups) || !empty($known_roles) || !empty($known_npcs) || !empty($known_props) || !empty($known_npcgroups)) {
			        echo "<h3>Känner till</h3>";
			        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			        $temp=0;
			        $cols=5;
			        foreach ($known_groups as $known_group) {
			            echo "<li style='display:table-cell; width:19%;'>";
			            echo "<div class='name'><a href='view_known_group.php?id=$known_group->Id'>$known_group->Name</a></div>";
			            echo "<div>Grupp</div>";
			            if ($known_group->hasImage()) {
			                echo "<img src='image.php?id=$known_group->ImageId'/>\n";
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
			            echo "<div class='name'><a href='view_known_role.php?id=$known_role->Id'>$known_role->Name</a></div>";
			            $role_group = $known_role->getGroup();
			            if (!empty($role_group)) {
			                echo "<div>$role_group->Name</div>";
			            }
			            
			            if ($known_role->hasImage()) {
			                echo "<img src='image.php?id=$known_role->ImageId'/>\n";
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
			                echo "<img width='100' src='image.php?id=$npc->ImageId'/>\n";
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
			                echo "<td>";
			                echo "<img width='100' src='image.php?id=$prop->ImageId'/>\n";
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
		                    echo "<td>";
		                    echo "<img width='100' src='image.php?id=$prop->ImageId'/>\n";
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
		        
		        
		        echo "<h2>Rykten</h2>";
        		$rumours = Rumour::allKnownByRole($current_larp, $role);
        		echo "<ul style='list-style-type: disc;'>";
        		foreach($rumours as $rumour) {
        		    echo "<li style='margin-bottom:7px;margin-left:20px'>$rumour->Text\n";

        		}
        		echo "</ul>";
			}

			else {
			    echo "Intrigerna är inte klara än.";
			}
			?>
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
