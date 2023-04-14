<?php

include_once 'header.php';


$roles = $current_larp->getAllMainRoles();

include 'navigation_subpage.php';
?>


	<div class="content">
	
		<h1>Alla karaktärer</h1>
	
		<?php foreach ($roles as $role) {
		    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
		    
		    
		    if (isset($role->GroupId)) {
		        $group=Group::loadById($role->GroupId);
		    }
		    
		    
		    ?>
		<h2>
			<?php echo $role->Name;?>&nbsp;
			<a href='edit_role.php?id=<?php echo $role->Id;?>'><i class='fa-solid fa-pen'></i></a>
			<a href='character_sheet.php?id=<?php echo $role->Id;?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad'></i></a>
		</h2>
		<table>
			<tr><td valign="top" class="header">Spelas av</td><td><a href ="view_person.php?id=<?php echo $role->PersonId;?>"><?php echo $role->getPerson()->Name; ?></a></td></tr>
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
			<tr><td valign="top" class="header">Rikedom</td><td><?php echo $role->getWealth()->Name;?></td></tr>
			<tr><td valign="top" class="header">Var är karaktären född?</td><td><?php echo $role->Birthplace;?></td></tr>
			<tr><td valign="top" class="header">Var bor karaktären?</td><td><?php echo $role->getPlaceOfResidence()->Name; ?></td></tr>

		</table>		

		
		<h3>Intrig</h3>
		<?php    echo $larp_role->Intrigue; 
        }
		?>

		

	</div>


</body>
</html>
