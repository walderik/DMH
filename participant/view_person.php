<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $PersonId = $_GET['id'];
    }
    else {
        header('Location: index.php');
    }
}

$current_person = Person::loadById($PersonId);

if (Person::loadById($current_person->Id)->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din person
}

if (!$current_person->isRegistered($current_larp)) {
    header('Location: index.php'); //Rollen är inte anmäld
}

$registration = Registration::loadByIds($current_person->Id, $current_larp->Id);



function ja_nej($val) {
    if ($val == 0) return "Nej";
    if ($val == 1) return "Ja";
}

?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
	       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>

	<div class="content">
		<h1><?php echo $current_person->Name;?></h1>
		<table>
			<tr><td valign="top" class="header">Huvudkaraktär</td><td><?php echo ja_nej($larp_role->IsMainRole);?></td></tr>
			<tr><td valign="top" class="header">NPC</td><td><?php echo ja_nej($current_role->IsNPC);?></td></tr>
			<tr><td valign="top" class="header">Yrke</td><td><?php echo $current_role->Profession;?></td></tr>
			<tr><td valign="top" class="header">Tidigare lajv</td><td><?php echo $current_role->PreviousLarps;?></td></tr>
			<tr><td valign="top" class="header">ReasonForBeingInSlowRiver</td><td><?php echo $current_role->ReasonForBeingInSlowRiver;?></td></tr>
			<tr><td valign="top" class="header">Religion</td><td><?php echo $current_role->Religion;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet</td><td><?php echo $current_role->DarkSecret;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet intrigideer</td><td><?php echo $current_role->DarkSecretIntrigueIdeas;?></td></tr>
			<tr><td valign="top" class="header">Intrigideer</td><td><?php echo $current_role->IntrigueSuggestions;?></td></tr>
			<tr><td valign="top" class="header">NotAcceptableIntrigues</td><td><?php echo $current_role->NotAcceptableIntrigues;?></td></tr>
			<tr><td valign="top" class="header">CharactersWithRelations</td><td><?php echo $current_role->CharactersWithRelations;?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $current_role->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Rikedom</td><td><?php echo Wealth::loadById($current_role->WealthId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Var bor karaktären?</td><td><?php echo PlaceOfResidence::loadById($current_role->PlaceOfResidenceId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Birthplace</td><td><?php echo $current_role->Birthplace;?></td></tr>

		</table>		
		

	</div>


</body>
</html>
