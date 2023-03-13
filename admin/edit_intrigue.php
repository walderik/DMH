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

$current_role = Role::loadById($RoleId);


if (!$current_role->isRegistered($current_larp)) {
    header('Location: index.php'); //Rollen är inte anmäld
    exit;
}

$larp_role = LARP_Role::loadByIds($current_role->Id, $current_larp->Id);


if (isset($current_role->GroupId)) {
    $group=Group::loadById($current_role->GroupId);
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

include 'navigation_subpage.php';
?>


	<div class="content">
		<h1><?php echo $current_role->Name;?>&nbsp;<a href='edit_role.php?id=<?php echo $current_role->Id;?>'><i class='fa-solid fa-pen'></i></a></h1>
		<table>
			<tr><td valign="top" class="header">Spelas av</td><td><a href ="view_person.php?id=<?php echo $current_role->PersonId;?>"><?php echo $current_role->getPerson()->Name; ?></a></td></tr>
		<?php if (isset($group)) {?>
			<tr><td valign="top" class="header">Grupp</td><td><a href ="view_group.php?id=<?php echo $group->Id;?>"><?php echo $group->Name; ?></a></td></tr>
		<?php }?>
			<tr><td valign="top" class="header">Huvudkaraktär</td><td><?php echo ja_nej($larp_role->IsMainRole);?></td></tr>
			<tr><td valign="top" class="header">NPC</td><td><?php echo ja_nej($current_role->IsNPC);?></td></tr>
			<tr><td valign="top" class="header">Yrke</td><td><?php echo $current_role->Profession;?></td></tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo nl2br($current_role->Description);?></td></tr>
			<tr><td valign="top" class="header">Tidigare lajv</td><td><?php echo $current_role->PreviousLarps;?></td></tr>
			<tr><td valign="top" class="header">Varför befinner sig karaktären i Slow River?</td><td><?php echo $current_role->ReasonForBeingInSlowRiver;?></td></tr>
			<tr><td valign="top" class="header">Religion</td><td><?php echo $current_role->Religion;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet</td><td><?php echo $current_role->DarkSecret;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet - intrig idéer</td><td><?php echo nl2br($current_role->DarkSecretIntrigueIdeas); ?></td></tr>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($larp_role->getIntrigueTypes());?></td></tr>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo nl2br($current_role->IntrigueSuggestions); ?></td></tr>
			<tr><td valign="top" class="header">Saker karaktären inte vill spela på</td><td><?php echo $current_role->NotAcceptableIntrigues;?></td></tr>
			<tr><td valign="top" class="header">Relationer med andra</td><td><?php echo $current_role->CharactersWithRelations;?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $current_role->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Rikedom</td><td><?php echo Wealth::loadById($current_role->WealthId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Var är karaktären född?</td><td><?php echo $current_role->Birthplace;?></td></tr>
			<tr><td valign="top" class="header">Var bor karaktären?</td><td><?php echo PlaceOfResidence::loadById($current_role->PlaceOfResidenceId)->Name;?></td></tr>

		</table>		

		
		<h2>Intrig</h2>
		<form action="logic/edit_intrigue_save.php" method="post">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $current_role->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<textarea id="Intrigue" name="Intrigue" rows="20" cols="150" maxlength="60000"><?php    echo $larp_role->Intrigue; ?></textarea>
		
		<input type="submit" value="Spara">

			</form>

		

	</div>


</body>
</html>
