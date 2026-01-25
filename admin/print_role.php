<?php 
$isReserve = Reserve_LARP_Role::isReserve($role->Id, $current_larp->Id);

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);


if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
}

$person = $role->getPerson();

$isRegistered = $role->isRegistered($current_larp);
//$subdivisions = Subdivision::allForRole($role, $current_larp);
$reserve_larp_role = Reserve_LARP_Role::loadByIds($role->Id, $current_larp->Id);
if (isset($reserve_larp_role)) $isReserve = true;
else $isReserve = false;
$campaign = $current_larp->getCampaign();
?>
<table>
<?php 
if (!is_null($person) && $person->isNotComing($current_larp)) {
    echo "<tr><td></td><td><strong>Karaktären kommer inte på lajvet</strong></td></tr>";
}

if ($role->isMysLajvare()) {
    echo "<tr><td></td><td><strong>Bakgrundslajvare</strong></td></tr>";
}?>
		
	<tr>
		<td valign="top" class="header">Spelas av</td>
		<td>
		<?php 
		if (is_null($person)) {
		    $assignment = NPC_assignment::getAssignment($role, $current_larp);
		    if (isset($assignment)) {
		        if ($assignment->IsAssigned()) {
		            $person = $assignment->getPerson();
		            echo $person->getViewLink()." ";
		            echo contactEmailIcon($person)."<br>".$assignment->Time;
		        } else {
		            "Ska spelas, inte tilldelad";
		        }
		    } else echo "NPC";
		}
		elseif ($isRegistered) { 
		     echo $person->getViewLink()." ";
		     echo contactEmailIcon($person).", ".$person->getExperience()->Name;
		} 
		else {
		    echo $person->getViewLink()." ";
		    echo contactEmailIcon($person)." ";
	        echo " <form action='logic/turn_into_npc.php' method='post'";
	        echo " onsubmit='return confirm(\"Är du säker på att du ska ta bort karaktären från $person->Name? Glöm i så fall inte att kommunicera det till $person->Name.\")'";
	        echo ">";
	        echo "<input type='hidden' id='RoleId' name='RoleId' value='$role->Id'>";
	        echo "<button type='submit'>Gör om till NPC</button>";
	        echo "</form>";
		}
		?>
		</td>
		<?php 
		if ($role->hasImage()) {
		    
		    $image = Image::loadById($role->ImageId);
		    echo "<td rowspan='20' valign='top'>";
		    echo "<img width='300' src='../includes/display_image.php?id=$role->ImageId'/>\n";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    echo "<br><a href='../common/logic/rotate_image.php?id=$role->ImageId'><i class='fa-solid fa-rotate-right' title='Rotra bild'></i></a>"; 
		    echo " <a href='logic/delete_image.php?id=$role->Id&type=role'><i class='fa-solid fa-trash' title='Tab ort bild'></i></a>\n";
		    echo " <a href='upload_image.php?id=$role->Id&type=role'><i class='fa-solid fa-image-portrait' title='Byt bild'></i></a> \n";
		    echo "</td>";
		} else {
		    echo "<a href='upload_image.php?id=$role->Id&type=role'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";		    
		}
		?>
			
			</tr>
			<?php if ($role->isNPC($current_larp)) {
			    echo "<tr><td valign='top' class='header'>Skapad av</td><td>".$role->getCreator()->getViewLink()."</td></tr>";
			    
			}?>
		<?php if (isset($group)) {?>
			<tr><td valign="top" class="header">Grupp</td><td><?php echo $group->getViewLink() ?></td></tr>
		<?php }?>
		
		<?php if (!empty($subdivisions)) { 
		  echo "<tr><td valign='top' class='header'>Medlem i</td><td>";
		  foreach ($subdivisions as $subdivision) echo $subdivision->getViewLink()."<br>";
		  echo "</td></tr>";
		    
		    
		}?>
		<?php if ($isRegistered) {?>
			<tr><td valign="top" class="header">Huvudkaraktär</td><td>
			<?php 
			if ($isReserve) echo ja_nej($reserve_larp_role->IsMainRole);
			else echo ja_nej($larp_role->IsMainRole);
			?></td></tr>
		<?php }?>

			<tr><td valign="top" class="header">Yrke</td><td><?php echo $role->Profession;?></td></tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo nl2br($role->Description);?></td></tr>
			
			<?php if ($campaign->hasGroups()) {?>
			<tr><td valign="top" class="header">Beskrivning för gruppen</td><td><?php echo nl2br($role->DescriptionForGroup);?></td></tr>
			<?php } ?>
			
			<tr><td valign="top" class="header">Beskrivning för andra</td><td><?php echo nl2br($role->DescriptionForOthers);?></td></tr>

			<?php if (Race::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Ras</td><td>
			<?php 
			$race = $role->getRace();
			if (!empty($race)) echo $race->Name;
			?>
			</td></tr>
			<tr><td valign="top" class="header">Kommentar till ras</td><td><?php echo $role->RaceComment;?></td></tr>
			<?php } ?>


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
			
			<tr><td valign="top" class="header">Varför befinner sig karaktären på platsen?</td><td><?php echo nl2br(htmlspecialchars($role->ReasonForBeingInSlowRiver));?></td></tr>
			
			<?php if (Ability::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Kunskaper</td><td><?php echo commaStringFromArrayObject($role->getAbilities());?></td></tr>
			<tr><td valign="top" class="header">Kunskaper förklaring</td><td><?php echo $role->AbilityComment;?></td></tr>
			<?php }?>

			<?php if (RoleFunction::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Funktioner</td><td><?php echo commaStringFromArrayObject($role->getRoleFunctions());?></td></tr>
			<tr><td valign="top" class="header">Funktioner förklaring</td><td><?php echo $role->RoleFunctionComment;?></td></tr>
			<?php }?>
			
			<?php if ($campaign->is_hfs()) {?>
			<tr><td valign="top" class="header">Superhjältenamn</td><td><?php echo $role->SuperHeroName;?></td></tr>
			
			<?php }?>
			

			<?php if (SuperPowerActive::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Superkraft, aktiv</td><td><?php echo commaStringFromArrayObject($role->getSuperPowerActives());?></td></tr>
			<?php }?>

			<?php if (SuperPowerPassive::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Superkraft, passiv</td><td><?php echo commaStringFromArrayObject($role->getSuperPowerPassives());?></td></tr>
			<?php }?>


			<?php 
			$magician = Magic_Magician::getForRole($role);
			$alchemist = Alchemy_Alchemist::getForRole($role);
			$alchemy_supplier = Alchemy_Supplier::getForRole($role);
			
			if (isset($magician) || isset($alchemist) || isset($alchemy_supplier)) {
			     echo "<td  valign='top' class='header'>Tilldelad förmåga</td>";
			     echo "<td>";
			     if (isset($magician)) echo "<a href='view_magician.php?id=$magician->Id'>Magiker</a>";
			     if (isset($alchemist)) echo "<a href='view_alchemist.php?id=$alchemist->Id'>Alkemist</a>";
			     if (isset($alchemy_supplier)) echo "<a href='view_alchemy_supplier.php?id=$alchemy_supplier->Id'>Lövjerist</a>";
			     echo "</td>";
			
			}
			
			
			
			?>



			<?php if (Religion::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Religion</td><td>
			<?php 
			$religion = $role->getReligion();
			if (!empty($religion)) echo $religion->Name;
			?>
			</td></tr>
			<tr><td valign="top" class="header">Religion förklaring</td><td><?php echo $role->Religion;?></td></tr>
			<?php }?>


			<?php if (Belief::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Hur troende</td><td>
			<?php 
			$belief = $role->getBelief();
			if (!empty($belief)) echo $belief->Name;
			?>
			</td></tr>
			<?php }?>

			<tr><td valign="top" class="header">Mörk hemlighet</td><td><?php echo $role->DarkSecret;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet - intrig idéer</td><td><?php echo nl2br($role->DarkSecretIntrigueIdeas); ?></td></tr>
			
			<?php if (!empty($larp_role) || !empty($reserve_larp_role)) { ?>
			<?php if (IntrigueType::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Intrigtyper</td><td>
			<?php 
			     if (!empty($larp_role)) echo commaStringFromArrayObject($larp_role->getIntrigueTypes());
			     elseif (!empty($reserve_larp_role)) echo commaStringFromArrayObject($reserve_larp_role->getIntrigueTypes());
			     ?>
			</td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Intrigidéer</td><td>
				<?php 
				    if (!empty($larp_role)) echo nl2br($larp_role->IntrigueIdeas); 
				    elseif (!empty($reserve_larp_role)) echo nl2br($reserve_larp_role->IntrigueIdeas); 
				?>
			</td></tr>	
			<?php  }?>	
			
			<tr><td valign="top" class="header">Saker karaktären inte vill spela på</td><td><?php echo $role->NotAcceptableIntrigues;?></td></tr>
			<tr><td valign="top" class="header">Relationer med andra</td><td><?php echo nl2br(htmlspecialchars($role->CharactersWithRelations));?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $role->OtherInformation;?></td></tr>
			
			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Rikedom</td><td>
			<?php 
			$wealth = $role->getWealth();
			if (!empty($wealth)) echo $wealth->Name;
			?>
			</td></tr>
			<?php }?>
			
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
			<tr><td valign="top" class="header">Död</td><td><?php echo ja_nej($role->IsDead);?></td></tr>

		</table>		
