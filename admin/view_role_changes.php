<?php

include_once 'header.php';
include '../includes/finediff.php';

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

//$group = $role->getGroup();
//$oldGroup = $oldRoleCopy->getGroup();


function echoDiff($fromtxt, $totxt) {
    $opcodes = FineDiff::getDiffOpcodes($fromtxt, $totxt, FineDiff::$wordGranularity);
    $to_text = FineDiff::renderDiffToHTMLFromOpcodes($fromtxt, $opcodes);
    
    echo "<td>$to_text</td>";
    
}

function echoNameDiff($oldthing, $newthing) {
    $oldName = "";
    $newName = "";
    
    
    if (!empty($newthing)) $newName = $newthing->Name;
    if (!empty($oldthing)) $oldName = $oldthing->Name;
    
    echoDiff($oldName, $newName);
}


include 'navigation.php';
?>

<style>
ins {
	color: green;
	background: #dfd;
	text-decoration: none
}

del {
	color: red;
	background: #fdd;
	text-decoration: none
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
        
		  <?php if (!empty($oldRoleCopy->ApprovedByUserId) && !empty($oldRoleCopy->ApprovedDate)) {
		      $approvedUser = User::loadById($oldRoleCopy->ApprovedByUserId);
		      echo "Tidigare godkänd av $approvedUser->Name, ".substr($oldRoleCopy->ApprovedDate,0, 10); 
    		  }
    		  ?>
        
		<br>
		
 		<div>
 		
		<table>

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
				<?php echoNameDiff($oldRoleCopy->getGroup(), $role->getGroup()); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Yrke</td>
				<?php echoDiff($oldRoleCopy->Profession, $role->Profession); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Beskrivning</td>
				<?php echoDiff($oldRoleCopy->Description, $role->Description); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Beskrivning för gruppen</td>
				<?php echoDiff($oldRoleCopy->DescriptionForGroup, $role->DescriptionForGroup); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Beskrivning för andra</td>
				<?php echoDiff($oldRoleCopy->DescriptionForOthers, $role->DescriptionForOthers); ?>
			</tr>

			<?php if (Race::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Ras</td>
				<?php echoNameDiff($oldRoleCopy->getRace(), $role->getRace()); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Kommentar till ras</td>

				<?php echoDiff($oldRoleCopy->RaceComment, $role->RaceComment); ?>
			</tr>
			<?php } ?>

		<?php if (!$role->isMysLajvare() || !$oldRoleCopy->isMysLajvare()) {?>
			<?php if (LarperType::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Typ av lajvare</td>
				<?php echoNameDiff($oldRoleCopy->getLarperType(), $role->getLarperType()); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Kommentar till typ av lajvare</td>
				<?php echoDiff($oldRoleCopy->TypeOfLarperComment, $role->TypeOfLarperComment); ?>
			</tr>
			<?php } ?>
			
			<tr>
				<td valign="top" class="header">Varför befinner sig karaktären på platsen?</td>
				<?php echoDiff($oldRoleCopy->ReasonForBeingInSlowRiver, $role->ReasonForBeingInSlowRiver); ?>
			</tr>
			
			<?php if (Ability::isInUse($current_larp)) {
			    $newAbilities = commaStringFromArrayObject($role->getAbilities());
			    $oldAbilities = commaStringFromArrayObject($oldRoleCopy->getAbilities());
			    ?>
			<tr>
				<td valign="top" class="header">Kunskaper</td>
				<?php echoDiff($oldAbilities, $newAbilities); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Kunskaper förklaring</td>
				<?php echoDiff($oldRoleCopy->AbilityComment, $role->AbilityComment); ?>
			</tr>
			<?php }?>

			<?php if (RoleFunction::isInUse($current_larp)) {
			    $newFunctions = commaStringFromArrayObject($role->getRoleFunctions());
			    $oldFunctions = commaStringFromArrayObject($oldRoleCopy->getRoleFunctions());
			    ?>
			<tr>
				<td valign="top" class="header">Funktioner</td>
				<?php echoDiff($oldFunctions, $newFunctions); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Funktioner förklaring</td>
				<?php echoDiff($oldRoleCopy->RoleFunctionComment, $role->RoleFunctionComment); ?>
			</tr>
			<?php }?>

			<?php if (Religion::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Religion</td>
				<?php echoNameDiff($oldRoleCopy->getReligion(), $role->getReligion()); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Religion förklaring</td>
				<?php echoDiff($oldRoleCopy->Religion, $role->Religion); ?>
			</tr>
			<?php }?>


			<?php if (Belief::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Hur troende</td>
				<?php echoNameDiff($oldRoleCopy->getBelief(), $role->getBelief()); ?>
			</tr>
			<?php }?>

			
			<tr>
				<td valign="top" class="header">Mörk hemlighet</td>
				<?php echoDiff($oldRoleCopy->DarkSecret, $role->DarkSecret); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Mörk hemlighet - intrig idéer</td>
				<?php echoDiff($oldRoleCopy->DarkSecretIntrigueIdeas, $role->DarkSecretIntrigueIdeas); ?>
			</tr>
			
			<?php if (IntrigueType::isInUse($current_larp)) {
			    $newIntrigues = commaStringFromArrayObject($role->getIntrigueTypes());
			    $oldIntrigues = commaStringFromArrayObject($oldRoleCopy->getIntrigueTypes());
			    ?>
			<tr>
				<td valign="top" class="header">Intrigtyper</td>
				<?php echoDiff($oldIntrigues, $newIntrigues); ?>
			</tr>
			<?php } ?>
			
			<tr>
				<td valign="top" class="header">Intrigidéer</td>
				<?php echoDiff($oldRoleCopy->IntrigueSuggestions, $role->IntrigueSuggestions); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Saker karaktären inte vill spela på</td>
				<?php echoDiff($oldRoleCopy->NotAcceptableIntrigues, $role->NotAcceptableIntrigues); ?>
			</tr>
			
			<tr>
				<td valign="top" class="header">Relationer med andra</td>
				<?php echoDiff($oldRoleCopy->CharactersWithRelations, $role->CharactersWithRelations); ?>
			</tr>
			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Rikedom</td>
				<?php echoNameDiff($oldRoleCopy->getWealth(), $role->getWealth()); ?>
			</tr>
			<?php }?>
			
			<tr>
				<td valign="top" class="header">Var är karaktären född?</td>
				<?php echoDiff($oldRoleCopy->Birthplace, $role->Birthplace); ?>
			</tr>
			
			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Var bor karaktären?</td>
				<?php echoNameDiff($oldRoleCopy->getPlaceOfResidence(), $role->getPlaceOfResidence()); ?>
			</tr>
			<?php } ?>
			
			<tr>
				<td valign="top" class="header">Annan information</td>
				<?php echoDiff($oldRoleCopy->OtherInformation, $role->OtherInformation); ?>
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
