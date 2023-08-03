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
    if (isset($role->ImageId) && !is_null($role->ImageId)) {
        $image = Image::loadById($role->ImageId);
        if (!is_null($image)) {
            
            echo "<img src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
            if (!empty($image->Photographer) && $image->Photographer!="") {
                echo "<div class='photographer'>Fotograf $image->Photographer</div>\n";
            }
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
    		    echo "<td rowspan='20' valign='top'><img width='300' src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/></td>";
    		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
    		}
    		?>
			
			</tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo $current_group->Description;?></td></tr>
			<tr><td valign="top" class="header">Beskrivning för andra</td><td><?php echo $current_group->DescriptionForOthers;?></td></tr>
			<tr><td valign="top" class="header">Vänner</td><td><?php echo $current_group->Friends;?></td></tr>
			<tr><td valign="top" class="header">Fiender</td><td><?php echo $current_group->Enemies;?></td></tr>
			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Rikedom</td><td><?php echo $current_group->getWealth()->Name; ?></td></tr>
			<?php }?>
			<?php if (PlaceOfResidence::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Var bor gruppen?</td><td><?php echo $current_group->getPlaceOfResidence()->Name; ?></td></tr>
			<?php }?>
			<tr><td valign="top" class="header">Intrig</td><td><?php echo ja_nej($larp_group->WantIntrigue); ?></td></tr>
			<?php if (IntrigueType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($current_group->getIntrigueTypes()); ?></td></tr>
			<?php } ?>
			<?php if ($current_user->isGroupLeader($current_group)) { ?>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo $current_group->IntrigueIdeas;?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Kvarvarande intriger</td><td><?php echo $larp_group->RemainingIntrigues; ?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $current_group->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Antal medlemmar</td><td><?php echo $larp_group->ApproximateNumberOfMembers;?></td></tr>
			<tr><td valign="top" class="header">Önskat boende</td><td><?php echo HousingRequest::loadById($larp_group->HousingRequestId)->Name;?></td></tr>
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
			if ($current_larp->DisplayIntrigues == 0) {
			    echo "<p>".nl2br($larp_group->Intrigue) ."</p>"; 
			    
			    
			    $known_actors = array();
			    $known_npcs = array();
			    $known_npcgroups = array();
			    $known_props = array();
			    $checkin_letters = array();
			    $checkin_telegrams = array();
			    $checkin_props = array();
			    $intrigues = Intrigue::getAllIntriguesForGroup($current_group->Id, $current_larp->Id);
			    $intrigue_numbers = array();
		        foreach ($intrigues as $intrigue) {
		            if ($intrigue->isActive()) {
		                $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $current_group);
		                echo "<p>".nl2br($intrigueActor->IntrigueText). "</p>";
		                if (!empty($intrigueActor->OffInfo)) {
		                    echo "<p><strong>Off-information:</strong><br><i>".nl2br($intrigueActor->OffInfo)."</i></p>";
		                }
		                
		                $known_actors = array_merge($known_actors, $intrigueActor->getAllKnownActors());
		                $known_npcs = array_merge($known_npcs, $intrigueActor->getAllKnownNPCs());
		                $known_npcgroups = array_merge($known_npcgroups, $intrigueActor->getAllKnownNPCGroups());
		                $known_props = array_merge($known_props, $intrigueActor->getAllKnownProps());
		                $checkin_letters = array_merge($checkin_letters, $intrigueActor->getAllCheckinLetters());
		                $checkin_telegrams = array_merge($checkin_telegrams, $intrigueActor->getAllCheckinTelegrams());
		                $checkin_props = array_merge($checkin_props, $intrigueActor->getAllCheckinProps());
		                $intrigue_numbers[] = $intrigue->Number;
		            }
		        }
		        if (!empty($intrigue_numbers)) {
		            echo "<p>Intrignummer " . implode(', ', $intrigue_numbers).". De kan behövas om du behöver hjälp av arrangörerna med en intrig under lajvet.</p>";
                }
		        if (!empty($known_actors) || !empty($known_npcs) || !empty($known_props) || !empty($known_npcgroups)) {
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
		        echo "<h2>Rykten</h2>";
		        $rumours = Rumour::allKnownByGroup($current_larp, $current_group);
		        foreach($rumours as $rumour) {
		            echo $rumour->Text;
		            echo "<br>";
		        }
		        
			}

			else {
			    echo "Intrigerna är inte klara än.";
			}
			?>
			
			
			
			
			
		</div>

	</div>


</body>
</html>
