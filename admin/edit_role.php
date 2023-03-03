<?php

include_once 'header_subpage.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RoleId = $_GET['id'];
    }
    else {
        header('Location: index.php');
    }
}

$role = Role::loadById($RoleId);


if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); //Rollen är inte anmäld
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


?>

	<div class="content">

		<h1><?php echo $role->Name;?></h1>
		<form action="logic/edit_role_save.php" method="post">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $role->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		
		<table>
 			<tr><td valign="top" class="header">Namn</td>
 			<td><input type="text" id="Name" name="Name" value="<?php echo $role->Name; ?>" size="100" maxlength="250" required></td></tr>
			<tr><td valign="top" class="header">Spelas av</td><td><?php echo $role->getPerson()->Name; ?></td></tr>

			<tr><td valign="top" class="header">Grupp</td>
			<td><?php selectionDropdownByArray('Group', Group::getRegistered($current_larp), false, false, $role->GroupId); ?></td></tr>

			<tr><td valign="top" class="header">Huvudkaraktär</td><td><?php echo ja_nej($larp_role->IsMainRole);?></td></tr>

			<tr><td valign="top" class="header">NPC</td><td><?php echo ja_nej($role->IsNPC);?></td></tr>

			<tr><td valign="top" class="header">Yrke</td>
			<td><input type="text" id="Profession" name="Profession" value="<?php echo $role->Profession; ?>"  size="100" maxlength="250" required></td></tr>

			<tr><td valign="top" class="header">Beskrivning</td>
			<td><textarea id="Description" name="Description" rows="4" cols="100" required><?php echo $role->Description; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Tidigare lajv</td>
			<td><textarea id="PreviousLarps" name="PreviousLarps" rows="8" cols="100"><?php echo $role->PreviousLarps; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Varför befinner sig<br>karaktären i Slow River?</td>
			<td><textarea id="ReasonForBeingInSlowRiver" name="ReasonForBeingInSlowRiver" rows="4" cols="100" required><?php echo $role->ReasonForBeingInSlowRiver; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Religion</td>
			<td><input type="text" id="Religion" name="Religion" value="<?php echo $role->Religion; ?>"  size="100" maxlength="250"></td></tr>

			<tr><td valign="top" class="header">Mörk hemlighet</td>
			<td><textarea id="DarkSecret" name="DarkSecret" rows="4" cols="100" required><?php echo $role->DarkSecret; ?> </textarea></td></tr>

			<tr><td valign="top" class="header">Mörk hemlighet - intrig idéer</td>
			<td><input type="text" id="DarkSecretIntrigueIdeas" name="DarkSecretIntrigueIdeas" value="<?php echo $role->DarkSecretIntrigueIdeas; ?>"  size="100" maxlength="250" required></td></tr>

			<tr><td valign="top" class="header">Intrigtyper</td>
			<td><?php selectionDropdownByArray('IntrigueType' , IntrigueType::allActive(), true, false, $larp_role->getSelectedIntrigueTypeIds());?></td></tr>

			<tr><td valign="top" class="header">Intrigidéer</td>
			<td><textarea id="IntrigueSuggestions" name="IntrigueSuggestions" rows="4" cols="100"><?php echo $role->IntrigueSuggestions; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Saker karaktären inte vill spela på</td>
			<td><input type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo $role->NotAcceptableIntrigues; ?>"  size="100" maxlength="250"></td></tr>

			<tr><td valign="top" class="header">Relationer med andra</td>
			<td><textarea id="CharactersWithRelations" name="CharactersWithRelations" rows="4" cols="100"><?php echo $role->CharactersWithRelations; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Annan information</td>
			<td><textarea id="OtherInformation" name="OtherInformation" rows="4" cols="100"><?php echo $role->OtherInformation; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Rikedom</td>
			<td><?php Wealth::selectionDropdown(false,true, $role->WealthId); ?></td></tr>

			<tr><td valign="top" class="header">Var är karaktären född?</td>
			<td><input type="text" id="Birthplace" name="Birthplace" value="<?php echo $role->Birthplace; ?>"  size="100" maxlength="250" required></td></tr>

			<tr><td valign="top" class="header">Var bor karaktären?</td>
			<td><?php
            PlaceOfResidence::selectionDropdown(false, true, $role->PlaceOfResidenceId);
            ?></td></tr>


		</table>		
			<input type="submit" value="Spara">

			</form>

	</div>


</body>
</html>
