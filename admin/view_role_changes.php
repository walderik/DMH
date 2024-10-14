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
$oldRoleCopy = $role->getOldApprovedRole();

$group = $role->getGroup();
$oldGroup = $oldRoleCopy->getGroup();

function setClass($text1, $text2, $side) {
    if ($text1 == $text2) {
        if ($side == 1) echo "class='unchangedNew'";
        else echo "class='unchangedOld'";
    } else {
        if ($side == 1) echo "class='changedNew'";
        else echo "class='changedOld'";
    }
}


include 'navigation.php';
?>

<style>
.unchangedNew {
}

.changedNew {

    background-color:#c4fed6;
}

.unchangedOld {
    color:grey;
}

.changedOld {
    background-color:#fbd3cb;
}

</style>

	<div class="content">
		<h1><?php echo $role->Name;?>&nbsp;
		<?php if ($role->IsDead ==1) echo "<i class='fa-solid fa-skull-crossbones' title='Död'></i>"?>
		</h1>
		
		<?php 
		if ($role->isApproved()) {
		  echo "<strong>Godkänd</strong>";
		  if (!empty($role->ApprovedByUserId) && !empty($role->ApprovedDate)) {
		      $approvedUser = User::loadById($role->ApprovedByUserId);
		      echo " av $approvedUser->Name, ".substr($role->ApprovedDate,0, 10); 
		  }
		  $editButton = "Ta bort godkännandet";
		}
		else {
		    echo "<strong>Ej godkänd</strong>";
		    $editButton = "Godkänn";
		}		
		?>
		<?php 
		if ($role->userMayEdit($current_larp)) {
		    echo "Spelare får ändra på karaktären och därför kan den inte godkännas.";
		} else {
		?>
        <form action="logic/toggle_approve_role.php" method="post"><input type="hidden" id="roleId" name="roleId" value="<?php echo $role->Id;?>"><input type="submit" value="<?php echo $editButton;?>"></form>
        <?php } ?>
		<br>
		
 		<div>
 		
		<table>
			<tr>
				<th></th>
				<th>Ny version av karaktären</th>
				<th>Gammal version av karaktären<br>
						  
    		  <?php if (!empty($oldRoleCopy->ApprovedByUserId) && !empty($oldRoleCopy->ApprovedDate)) {
    		      $approvedUser = User::loadById($oldRoleCopy->ApprovedByUserId);
    		      echo "Godkänd av $approvedUser->Name, ".substr($oldRoleCopy->ApprovedDate,0, 10); 
    		  }
    		  ?>
				</th>
			</tr>


		<?php if ($role->isMysLajvare() && $oldRoleCopy->isMysLajvare()) {?>
			<tr><td></td><td><strong>Bakgrundslajvare</strong></td><td><strong>Bakgrundslajvare</strong></td></tr>
		<?php } elseif (($role->isMysLajvare() || $oldRoleCopy->isMysLajvare())) { ?>
			<tr><td></td>
			<td><?php if ($role->isMysLajvare()) { ?><strong>Bakgrundslajvare</strong> <?php } ?></td>
			<td><?php if ($oldRoleCopy->isMysLajvare()) { ?><strong>Bakgrundslajvare</strong> <?php } ?></td>
			</tr>
		<?php } ?>
			<tr>
				<td valign="top" class="header">Grupp</td>
				<td  <?php setClass($role->GroupId, $oldRoleCopy->GroupId, 1); ?>><?php if (isset($group)) echo $group->getViewLink() ?></td>
				<td <?php setClass($role->GroupId, $oldRoleCopy->GroupId, 2); ?>><?php if (isset($oldGroup)) echo $oldGroup->getViewLink() ?></td>
			</tr>
			<tr>
				<td valign="top" class="header">Yrke</td>
				<td <?php setClass($role->Profession, $oldRoleCopy->Profession, 1); ?>><?php echo $role->Profession;?></td>
				<td <?php setClass($role->Profession, $oldRoleCopy->Profession, 2); ?>><?php echo $oldRoleCopy->Profession;?></td>
			</tr>
			<tr>
				<td valign="top" class="header">Beskrivning</td>
				<td <?php setClass($role->Description, $oldRoleCopy->Description, 1); ?>><?php echo nl2br($role->Description);?></td>
				<td <?php setClass($role->Description, $oldRoleCopy->Description, 2); ?>><?php echo nl2br($oldRoleCopy->Description);?></td>
			</tr>
			<tr>
				<td valign="top" class="header">Beskrivning för gruppen</td>
				<td <?php setClass($role->DescriptionForGroup, $oldRoleCopy->DescriptionForGroup, 1); ?>><?php echo nl2br($role->DescriptionForGroup);?></td>
				<td <?php setClass($role->DescriptionForGroup, $oldRoleCopy->DescriptionForGroup, 2); ?>><?php echo nl2br($oldRoleCopy->DescriptionForGroup);?></td>
			</tr>
			<tr>
				<td valign="top" class="header">Beskrivning för andra</td>
				<td <?php setClass($role->DescriptionForOthers, $oldRoleCopy->DescriptionForOthers, 1); ?>><?php echo nl2br($role->DescriptionForOthers);?></td>
				<td <?php setClass($role->DescriptionForOthers, $oldRoleCopy->DescriptionForOthers, 2); ?>><?php echo nl2br($oldRoleCopy->DescriptionForOthers);?></td>
			</tr>

			<?php if (Race::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Ras</td>
				<td <?php setClass($role->RaceId, $oldRoleCopy->RaceId, 1); ?>>
        			<?php 
        			$race = $role->getRace();
        			if (!empty($race)) echo $race->Name;
        			?>
				</td>
				<td <?php setClass($role->RaceId, $oldRoleCopy->RaceId, 2); ?>>
        			<?php 
        			$race = $oldRoleCopy->getRace();
        			if (!empty($race)) echo $race->Name;
        			?>
				</td>
			</tr>
			<tr>
				<td valign="top" class="header">Kommentar till ras</td>
				<td <?php setClass($role->RaceComment, $oldRoleCopy->RaceComment, 1); ?>><?php echo $role->RaceComment;?></td>
				<td <?php setClass($role->RaceComment, $oldRoleCopy->RaceComment, 2); ?>><?php echo $oldRoleCopy->RaceComment;?></td>
			</tr>
			<?php } ?>

		<?php if (!$role->isMysLajvare() || !$oldRoleCopy->isMysLajvare()) {?>
			<?php if (LarperType::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Typ av lajvare</td>
				<td <?php setClass($role->LarperTypeId, $oldRoleCopy->LarperTypeId, 1); ?>>
    			<?php 
    			$larpertype = $role->getLarperType();
    			if (!empty($larpertype)) echo $larpertype->Name;
    			?>
				</td>
				<td <?php setClass($role->LarperTypeId, $oldRoleCopy->LarperTypeId, 2); ?>>
    			<?php 
    			$larpertype = $oldRoleCopy->getLarperType();
    			if (!empty($larpertype)) echo $larpertype->Name;
    			?>
				</td>
			</tr>
			<tr>
				<td valign="top" class="header">Kommentar till typ av lajvare</td>
				<td <?php setClass($role->TypeOfLarperComment, $oldRoleCopy->TypeOfLarperComment, 1); ?>><?php echo $role->TypeOfLarperComment;?></td>
				<td <?php setClass($role->TypeOfLarperComment, $oldRoleCopy->TypeOfLarperComment, 2); ?>><?php echo $oldRoleCopy->TypeOfLarperComment;?></td>
			</tr>
			<?php } ?>
			
			<tr>
				<td valign="top" class="header">Varför befinner sig karaktären på platsen?</td>
				<td <?php setClass($role->ReasonForBeingInSlowRiver, $oldRoleCopy->ReasonForBeingInSlowRiver, 1); ?>><?php echo nl2br(htmlspecialchars($role->ReasonForBeingInSlowRiver));?></td>
				<td <?php setClass($role->ReasonForBeingInSlowRiver, $oldRoleCopy->ReasonForBeingInSlowRiver, 2); ?>><?php echo nl2br(htmlspecialchars($oldRoleCopy->ReasonForBeingInSlowRiver));?></td>
			</tr>
			
			<?php if (Ability::isInUse($current_larp)) {
			    $newAbilities = commaStringFromArrayObject($role->getAbilities());
			    $oldAbilities = commaStringFromArrayObject($oldRoleCopy->getAbilities());
			    ?>
			<tr>
				<td valign="top" class="header">Kunskaper</td>
				<td <?php setClass($newAbilities, $oldAbilities, 1); ?>><?php echo $newAbilities;?></td>
				<td <?php setClass($newAbilities, $oldAbilities, 2); ?>><?php echo $oldAbilities;?></td>
			</tr>
			<tr>
				<td valign="top" class="header">Kunskaper förklaring</td>
				<td <?php setClass($role->AbilityComment, $oldRoleCopy->AbilityComment, 1); ?>><?php echo $role->AbilityComment;?></td>
				<td <?php setClass($role->AbilityComment, $oldRoleCopy->AbilityComment, 2); ?>><?php echo $oldRoleCopy->AbilityComment;?></td>
			</tr>
			<?php }?>

			<?php if (RoleFunction::isInUse($current_larp)) {
			    $newFunctions = commaStringFromArrayObject($role->getRoleFunctions());
			    $oldFunctions = commaStringFromArrayObject($oldRoleCopy->getRoleFunctions());
			    ?>
			<tr>
				<td valign="top" class="header">Funktioner</td>
				<td <?php setClass($newFunctions, $oldFunctions, 1); ?>><?php echo $newFunctions;?></td>
				<td <?php setClass($newFunctions, $oldFunctions, 2); ?>><?php echo $oldFunctions;?></td>
			</tr>
			<tr>
				<td valign="top" class="header">Funktioner förklaring</td>
				<td <?php setClass($role->RoleFunctionComment, $oldRoleCopy->RoleFunctionComment, 1); ?>><?php echo $role->RoleFunctionComment;?></td>
				<td <?php setClass($role->RoleFunctionComment, $oldRoleCopy->RoleFunctionComment, 2); ?>><?php echo oldRoleCopy->RoleFunctionComment;?></td>
			</tr>
			<?php }?>

			<?php if (Religion::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Religion</td>
				<td <?php setClass($role->ReligionId, $oldRoleCopy->ReligionId, 1); ?>>
    			<?php 
    			$religion = $role->getReligion();
    			if (!empty($religion)) echo $religion->Name;
    			?>
    			</td>
				<td <?php setClass($role->ReligionId, $oldRoleCopy->ReligionId, 2); ?>>
    			<?php 
    			$religion = $oldRoleCopy->getReligion();
    			if (!empty($religion)) echo $religion->Name;
    			?>
    			</td>
			</tr>
			<tr>
				<td valign="top" class="header">Religion förklaring</td>
				<td <?php setClass($role->Religion, $oldRoleCopy->Religion, 1); ?>><?php echo $role->Religion;?></td>
				<td <?php setClass($role->Religion, $oldRoleCopy->Religion, 2); ?>><?php echo $oldRoleCopy->Religion;?></td>
			</tr>
			<?php }?>


			<?php if (Belief::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Hur troende</td>
				<td <?php setClass($role->BeliefId, $oldRoleCopy->BeliefId, 1); ?>>
    			<?php 
    			$belief = $role->getBelief();
    			if (!empty($belief)) echo $belief->Name;
    			?>
    			</td>
				<td <?php setClass($role->BeliefId, $oldRoleCopy->BeliefId, 2); ?>>
    			<?php 
    			$belief = $oldRoleCopy->getBelief();
    			if (!empty($belief)) echo $belief->Name;
    			?>
    			</td>
			</tr>
			<?php }?>

			<?php if (Council::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Byrådet</td>
				<td <?php setClass($role->CouncilId, $oldRoleCopy->CouncilId, 1); ?>>
    			<?php 
    			$council = $role->getCouncil();
    			if (!empty($council)) echo $council->Name;
    			?>
    			</td>
				<td <?php setClass($role->CouncilId, $oldRoleCopy->CouncilId, 2); ?>>
    			<?php 
    			$council = $oldRoleCopy->getCouncil();
    			if (!empty($council)) echo $council->Name;
    			?>
    			</td>
			</tr>
			<tr><td valign="top" class="header">Byrådet förklaring</td>
			<td <?php setClass($role->Council, $oldRoleCopy->Council, 1); ?>><?php echo $role->Council;?></td>
			<td <?php setClass($role->Council, $oldRoleCopy->Council, 2); ?>><?php echo $oldRoleCopy->Council;?></td>
			</tr>
			<?php }?>
			
			<?php if (Guard::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Markvakt</td><td <?php setClass($role->GuardId, $oldRoleCopy->GuardId, 1); ?>>
			<?php 
			$guard = $role->getGuard();
			if (!empty($guard)) echo $guard->Name;
			?>
			</td>
			<td <?php setClass($role->GuardId, $oldRoleCopy->GuardId, 2); ?>>
			<?php 
			$guard = $oldRoleCopy->getGuard();
			if (!empty($guard)) echo $guard->Name;
			?>
			</td>
			</tr>
			<?php }?>			
			
			<tr>
				<td valign="top" class="header">Mörk hemlighet</td>
				<td <?php setClass($role->DarkSecret, $oldRoleCopy->DarkSecret, 1); ?>><?php echo $role->DarkSecret;?></td>
				<td <?php setClass($role->DarkSecret, $oldRoleCopy->DarkSecret, 2); ?>><?php echo $oldRoleCopy->DarkSecret;?></td>
			</tr>
			<tr>
				<td valign="top" class="header">Mörk hemlighet - intrig idéer</td>
				<td <?php setClass($role->DarkSecretIntrigueIdeas, $oldRoleCopy->DarkSecretIntrigueIdeas, 1); ?>><?php echo nl2br($role->DarkSecretIntrigueIdeas); ?></td>
				<td <?php setClass($role->DarkSecretIntrigueIdeas, $oldRoleCopy->DarkSecretIntrigueIdeas, 2); ?>><?php echo nl2br($oldRoleCopy->DarkSecretIntrigueIdeas); ?></td>
			</tr>
			
			<?php if (IntrigueType::isInUse($current_larp)) {
			    $newIntrigues = commaStringFromArrayObject($role->getIntrigueTypes());
			    $oldIntrigues = commaStringFromArrayObject($oldRoleCopy->getIntrigueTypes());
			    ?>
			<tr>
				<td valign="top" class="header">Intrigtyper</td>
				<td <?php setClass($newIntrigues, $oldIntrigues, 1); ?>><?php echo $newIntrigues;?></td>
				<td <?php setClass($newIntrigues, $oldIntrigues, 2); ?>><?php echo $oldIntrigues;?></td>
			</tr>
			<?php } ?>
			
			<tr>
				<td valign="top" class="header">Intrigidéer</td>
				<td <?php setClass($role->IntrigueSuggestions, $oldRoleCopy->IntrigueSuggestions, 1); ?>><?php echo nl2br($role->IntrigueSuggestions); ?></td>
				<td <?php setClass($role->IntrigueSuggestions, $oldRoleCopy->IntrigueSuggestions, 2); ?>><?php echo nl2br($oldRoleCopy->IntrigueSuggestions); ?></td>
			</tr>
			<tr>
				<td valign="top" class="header">Saker karaktären inte vill spela på</td>
				<td <?php setClass($role->NotAcceptableIntrigues, $oldRoleCopy->NotAcceptableIntrigues, 1); ?>><?php echo $role->NotAcceptableIntrigues;?></td>
				<td <?php setClass($role->NotAcceptableIntrigues, $oldRoleCopy->NotAcceptableIntrigues, 2); ?>><?php echo $oldRoleCopy->NotAcceptableIntrigues;?></td>
			</tr>
			
			<tr>
				<td valign="top" class="header">Relationer med andra</td>
				<td <?php setClass($role->CharactersWithRelations, $oldRoleCopy->CharactersWithRelations, 1); ?>><?php echo $role->CharactersWithRelations;?></td>
				<td <?php setClass($role->CharactersWithRelations, $oldRoleCopy->CharactersWithRelations, 2); ?>><?php echo $oldRoleCopy->CharactersWithRelations;?></td>
			</tr>
			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Rikedom</td>
				<td <?php setClass($role->WealthId, $oldRoleCopy->WealthId, 1); ?>>
    			<?php 
    			$wealth = $role->getWealth();
    			if (!empty($wealth)) echo $wealth->Name;
    			?>
    			</td>
				<td <?php setClass($role->WealthId, $oldRoleCopy->WealthId, 2); ?>>
    			<?php 
    			$wealth = $oldRoleCopy->getWealth();
    			if (!empty($wealth)) echo $wealth->Name;
    			?>
    			</td>
			</tr>
			<?php }?>
			
			<tr>
				<td valign="top" class="header">Var är karaktären född?</td>
				<td <?php setClass($role->Birthplace, $oldRoleCopy->Birthplace, 1); ?>><?php echo $role->Birthplace;?></td>
				<td <?php setClass($role->Birthplace, $oldRoleCopy->Birthplace, 2); ?>><?php echo $oldRoleCopy->Birthplace;?></td>
			</tr>
			
			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Var bor karaktären?</td>
				<td <?php setClass($role->PlaceOfResidenceId, $oldRoleCopy->PlaceOfResidenceId, 1); ?>>
    			<?php 
    			$por = $role->getPlaceOfResidence();
    			if (!empty($por)) echo $por->Name;
    			?>
    			</td>
				<td <?php setClass($role->PlaceOfResidenceId, $oldRoleCopy->PlaceOfResidenceId, 2); ?>>
    			<?php 
    			$por = $oldRoleCopy->getPlaceOfResidence();
    			if (!empty($por)) echo $por->Name;
    			?>
    			</td>
			</tr>
			<?php } ?>
			
			<tr>
				<td valign="top" class="header">Annan information</td>
				<td <?php setClass($role->OtherInformation, $oldRoleCopy->OtherInformation, 1); ?>><?php echo $role->OtherInformation;?></td>
				<td <?php setClass($role->OtherInformation, $oldRoleCopy->OtherInformation, 2); ?>><?php echo $oldRoleCopy->OtherInformation;?></td>
			</tr>
			
			
		<?php }?>

		</table>		
		</div>
		
		
		<h2>Anteckningar (visas inte för deltagaren) <a href='edit_intrigue.php?id=<?php echo $role->Id ?>'><i class='fa-solid fa-pen'></i></a></h2>
		<div>
		<?php    echo nl2br(htmlspecialchars($role->OrganizerNotes)); ?>
		</div>

		<?php include 'print_role_history.php';?>		

	</div>


</body>
</html>
