<?php 
$isReserve = Reserve_LARP_Role::isReserve($role->Id, $current_larp->Id);

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);


if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
}

$person = $role->getPerson();


?>
<table>
<?php
if ($role->isMysLajvare()) {
    echo "<tr><td></td><td><strong>Myslajvare</strong></td></tr>";
}?>
		
			<tr>
    			<td valign="top" class="header">Spelas av</td>
    			<td>
    			<a href ="view_person.php?id=<?php echo $role->PersonId;?>"><?php echo $person->Name; ?></a>&nbsp;
    			(<?php echo $person->getAgeAtLarp($current_larp) ?> år)
			</td>
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
		
		<?php if (!$isReserve) {?>
			<tr><td valign="top" class="header">Huvudkaraktär</td><td><?php echo ja_nej($larp_role->IsMainRole);?></td></tr>
		<?php }?>

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
			<?php } ?>
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
