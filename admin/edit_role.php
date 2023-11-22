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
if (empty($role)) {
    header('Location: index.php'); // Karaktären finns inte
    exit;
}


if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // Karaktären är inte anmäld
    exit;
}

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);


if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

include 'navigation.php';
?>

	<div class="content">

		<h1><?php echo $role->Name;?></h1>
		<form action="logic/edit_role_save.php" method="post">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $role->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		
		<table>
 			<tr><td valign="top" class="header">Namn&nbsp;<font style="color:red">*</font></td>
 			<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($role->Name); ?>" size="100" maxlength="250" required></td></tr>
			<tr><td valign="top" class="header">Spelas av</td><td><?php echo $role->getPerson()->Name; ?></td></tr>

			<tr><td valign="top" class="header">Grupp</td>
			<td><?php selectionByArray('Group', Group::getAllRegistered($current_larp), false, false, $role->GroupId); ?></td></tr>

			<tr><td valign="top" class="header">Huvudkaraktär</td><td><?php echo ja_nej($larp_role->IsMainRole);?></td></tr>

			<tr><td valign="top" class="header">Yrke&nbsp;<font style="color:red">*</font></td>
			<td><input type="text" id="Profession" name="Profession" value="<?php echo htmlspecialchars($role->Profession); ?>"  size="100" maxlength="250" required></td></tr>

			<tr><td valign="top" class="header">Beskrivning&nbsp;<font style="color:red">*</font></td>
			<td><textarea id="Description" name="Description" rows="4" cols="100" maxlength="15000" required><?php echo htmlspecialchars($role->Description); ?></textarea></td></tr>
			<tr><td valign="top" class="header">Beskrivning för gruppen</td>
			<td><textarea id="DescriptionForGroup" name="DescriptionForGroup" rows="4" cols="100" maxlength="15000"><?php echo htmlspecialchars($role->DescriptionForGroup); ?></textarea></td></tr>
			<tr><td valign="top" class="header">Beskrivning för andra</td>
			<td><textarea id="DescriptionForOthers" name="DescriptionForOthers" rows="4" cols="100" maxlength="400"><?php echo htmlspecialchars($role->DescriptionForOthers); ?></textarea></td></tr>


			<tr><td valign="top" class="header">Myslajvare&nbsp;<font style="color:red">*</font></td>
			<td>
				<input type="radio" id="NoIntrigue_yes" name="NoIntrigue" value="1" <?php if ($role->isMysLajvare()) echo 'checked="checked"'?>> 
    			<label for="NoIntrigue_yes">Ja</label><br> 
    			<input type="radio" id="NoIntrigue_no" name="NoIntrigue" value="0" <?php if (!$role->isMysLajvare()) echo 'checked="checked"'?>> 
    			<label for="NoIntrigue_no">Nej</label>
			</td></tr>
			<?php if (LarperType::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Typ av lajvare&nbsp;<font style="color:red">*</font></td>
			<td><?php LarperType::selectionDropdown($current_larp, false, false, $role->LarperTypeId); ?></td></tr>
			<tr><td valign="top" class="header">Kommentar till typ av lajvare</td>
			<td><input type="text" id="TypeOfLarperComment" value="<?php echo htmlspecialchars($role->TypeOfLarperComment); ?>" name="TypeOfLarperComment"  size="100" maxlength="250"></td></tr>
			<?php }?>




			<tr><td valign="top" class="header">Tidigare lajv</td>
			<td><textarea id="PreviousLarps" name="PreviousLarps" rows="8" cols="100" maxlength="15000"><?php echo htmlspecialchars($role->PreviousLarps); ?></textarea></td></tr>

			<tr><td valign="top" class="header">Varför befinner sig<br>karaktären på platsen?&nbsp;<font style="color:red">*</font></td>
			<td><textarea id="ReasonForBeingInSlowRiver" name="ReasonForBeingInSlowRiver" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->ReasonForBeingInSlowRiver); ?></textarea></td></tr>



			<?php if (Religion::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Religion</td><td>
			<?php Religion::selectionDropdown($current_larp, false, false, $role->ReligionId); ?>
			</td></tr>
			<tr><td valign="top" class="header">Religion förklaring</td><td><input type="text" id="Religion" name="Religion" value="<?php echo htmlspecialchars($role->Religion); ?>"  size="100" maxlength="250"></td></tr>
			<?php }?>

			<?php if (Council::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Byrådet</td><td>
			<?php Council::selectionDropdown($current_larp, false, false, $role->CouncilId); ?>
			</td></tr>
			<tr><td valign="top" class="header">Byrådet förklaring</td><td><input type="text" id="Council" name="Council" value="<?php echo htmlspecialchars($role->Council); ?>"  size="100" maxlength="250"></td></tr>
			<?php }?>
			
			<?php if (Guard::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Markvakt</td><td>
			<?php Guard::selectionDropdown($current_larp, false, false, $role->GuardId); ?>
			</td></tr>
			<?php }?>			






			<tr><td valign="top" class="header">Religion</td>
			<td><input type="text" id="Religion" name="Religion" value="<?php echo htmlspecialchars($role->Religion); ?>"  size="100" maxlength="250"></td></tr>

			<tr><td valign="top" class="header">Mörk hemlighet&nbsp;<font style="color:red">*</font></td>
			<td><textarea id="DarkSecret" name="DarkSecret" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->DarkSecret); ?> </textarea></td></tr>

			<tr><td valign="top" class="header">Mörk hemlighet - intrig idéer&nbsp;<font style="color:red">*</font></td>
			<td><input type="text" id="DarkSecretIntrigueIdeas" name="DarkSecretIntrigueIdeas" value="<?php echo htmlspecialchars($role->DarkSecretIntrigueIdeas); ?>"  size="100" maxlength="250"></td></tr>

			<?php if (IntrigueType::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Intrigtyper</td>
			<td><?php selectionByArray('IntrigueType' , IntrigueType::allActive($current_larp), true, false, $role->getSelectedIntrigueTypeIds());?></td></tr>
			<?php }?>
			
			<tr><td valign="top" class="header">Intrigidéer</td>
			<td><textarea id="IntrigueSuggestions" name="IntrigueSuggestions" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->IntrigueSuggestions); ?></textarea></td></tr>

			<tr><td valign="top" class="header">Saker karaktären inte vill spela på</td>
			<td><input type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo htmlspecialchars($role->NotAcceptableIntrigues); ?>"  size="100" maxlength="250"></td></tr>

			<tr><td valign="top" class="header">Relationer med andra</td>
			<td><textarea id="CharactersWithRelations" name="CharactersWithRelations" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->CharactersWithRelations); ?></textarea></td></tr>

			<tr><td valign="top" class="header">Annan information</td>
			<td><textarea id="OtherInformation" name="OtherInformation" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->OtherInformation); ?></textarea></td></tr>

			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Rikedom&nbsp;<font style="color:red">*</font></td>
			<td><?php Wealth::selectionDropdown($current_larp, false,false, $role->WealthId); ?></td></tr>
			<?php } ?>
			
			<tr><td valign="top" class="header">Var är karaktären född?&nbsp;<font style="color:red">*</font></td>
			<td><input type="text" id="Birthplace" name="Birthplace" value="<?php echo htmlspecialchars($role->Birthplace); ?>"  size="100" maxlength="250"></td></tr>

			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
			<tr><td valign="top" class="header">Var bor karaktären?&nbsp;<font style="color:red">*</font></td>
			<td><?php
			PlaceOfResidence::selectionDropdown($current_larp, false, false, $role->PlaceOfResidenceId);
            ?></td></tr>
            <?php } ?>
           
			<tr><td valign="top" class="header">Död&nbsp;<font style="color:red">*</font></td>
			<td>
				<input type="radio" id="IsDead_yes" name="IsDead" value="1" <?php if ($role->IsDead == 1) echo 'checked="checked"'?>> 
    			<label for="IsDead_yes">Ja</label><br> 
    			<input type="radio" id="IsDead_no" name="IsDead" value="0" <?php if ($role->IsDead == 0) echo 'checked="checked"'?>> 
    			<label for="IsDead_no">Nej</label>
			</td></tr>


		</table>		
			<input type="submit" value="Spara">

			</form>

	</div>


</body>
</html>
