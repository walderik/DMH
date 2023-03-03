
<?php

include_once 'header_subpage.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $PersonId = $_GET['id'];
    }
    else {
        header('Location: index.php');
    }
}

$person = Person::loadById($PersonId);

if (Person::loadById($person->Id)->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din person
}

if (!$person->isRegistered($current_larp)) {
    header('Location: index.php'); //Rollen är inte anmäld
}

$registration = Registration::loadByIds($person->Id, $current_larp->Id);

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}



?>


	<div class="content">
		<h1><?php echo $person->Name;?></h1>
		<form action="logic/edit_person_save.php" method="post">
    		<input type="hidden" id="PersonId" name="PersonId" value="<?php echo $person->Id; ?>">
    		<input type="hidden" id="RegistrationId" name="RegistrationId" value="<?php echo $registration->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
    		
 			<tr><td valign="top" class="header">Namn</td>
 			<td><input type="text" id="Name" name="Name" value="<?php echo $person->Name; ?>" size="100" maxlength="250" required></td></tr>
    		
			<tr><td valign="top" class="header">Personnummer</td>
			<td><input type="text" id="SocialSecurityNumber" value="<?php echo $person->SocialSecurityNumber; ?>"
					name="SocialSecurityNumber" pattern="\d{8}-\d{4}|\d{8}-x{4}|\d{12}|\d{8}x{4}"  placeholder="ÅÅÅÅMMDD-NNNN" size="20" maxlength="13" required>
	</td></tr>
			<tr><td valign="top" class="header">Email</td>
			<td><input type="Email" id="email" name="Email" value="<?php echo $person->Email; ?>"  size="100" maxlength="250" required></td></tr>
			<tr><td valign="top" class="header">Mobilnummer</td>
			<td><input type="text" id="PhoneNumber" name="PhoneNumber" value="<?php echo $person->PhoneNumber; ?>"  size="100" maxlength="250"></td></tr>
			<tr><td valign="top" class="header">Närmaste anhörig</td>
			<td><textarea id="EmergencyContact" name="EmergencyContact" rows="4" cols="100" required><?php echo $person->EmergencyContact; ?></textarea></td></tr>
		    <?php 
		    if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
		    ?>
			<tr><td valign="top" class="header">Ansvarig vuxen</td>
			<td><input type="text" id="Guardian" value="<?php echo $registration->Guardian; ?>" name="Guardian"  size="100" maxlength="250"></td></tr>
		    
		    <?php 
		    }
		    ?>

			<tr><td valign="top" class="header">Typ av lajvare</td>
			<td><?php LarperType::selectionDropdown(false, true, $person->LarperTypeId); ?></td></tr>
			<tr><td valign="top" class="header">Kommentar till typ av lajvare</td>
			<td><input type="text" id="TypeOfLarperComment" value="<?php echo $person->TypeOfLarperComment; ?>" name="TypeOfLarperComment"  size="100" maxlength="250"></td></tr>


			<tr><td valign="top" class="header">Erfarenhet</td>
			<td><?php Experience::selectionDropdown(false, true, $person->ExperienceId); ?></td></tr>
			<tr><td valign="top" class="header">Intriger du inte vill spela på</td>
			<td><input type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo $person->NotAcceptableIntrigues; ?>" size="100" maxlength="250" ></td></tr>

			<tr><td valign="top" class="header">Typ av mat</td>
			<td><?php TypeOfFood::selectionDropdown(false, true, $person->TypeOfFoodId); ?></td></tr>
			<tr><td valign="top" class="header">Vanliga allergier</td>
			<td><?php NormalAllergyType::selectionDropdown(true, false, $person->getSelectedNormalAllergyTypeIds()); ?></td></tr>

			<tr><td valign="top" class="header">Andra allergier</td>
			<td><textarea id="FoodAllergiesOther" name="FoodAllergiesOther" rows="4" cols="100"><?php echo $person->FoodAllergiesOther; ?></textarea></td></tr>

			<tr><td valign="top" class="header">NPC önskemål</td>
			<td><input type="text" id="NPCDesire" name="NPCDesire" size="100" maxlength="250" value="<?php echo $registration->NPCDesire;?>"></td></tr>
			<tr><td valign="top" class="header">Husförvaltare</td>
			<td><?php selectionDropdownByArray('House', House::all(), false, false, $person->HouseId); ?></td></tr>
			<tr><td valign="top" class="header">Önskat boende</td>
			<td><?php HousingRequest::selectionDropdown(false,true,$registration->HousingRequestId);?></td></tr>


			<tr><td valign="top" class="header">Annan information</td>
			<td><textarea id="OtherInformation" name="OtherInformation" rows="4" cols="100"><?php echo $person->OtherInformation; ?></textarea></td></tr>
			<tr><td valign="top" class="header">Medlem</td><td><?php echo ja_nej($person->isMember($current_larp->StartDate))?></td></tr>
			<tr><td valign="top" class="header">Anmäld</td><td><?php echo $registration->RegisteredAt;?></td></tr>
			<tr><td valign="top" class="header">Godkänd</td><td><?php if (isset($registration->Approved)) { echo $registration->Approved; } else { echo "Nej"; }?></td></tr>
			<tr><td valign="top" class="header">Funktionär</td><td><?php echo ja_nej($registration->IsOfficial)?></td></tr>

			<tr><td valign="top" class="header">Betalningsreferens</td><td><?php echo $registration->PaymentReference;?></td></tr>
			<tr><td valign="top" class="header">Belopp att betala</td><td><?php echo $registration->AmountToPay;?></td></tr>
			<tr><td valign="top" class="header">Belopp betalat</td><td><?php echo $registration->AmountPayed;?></td></tr>
			<tr><td valign="top" class="header">Betalat datum</td><td><?php echo $registration->Payed;?></td></tr>
			<?php 
			if ($registration->NotComing) {
			?>
			<tr><td valign="top" class="header">Avbokad</td><td><?php echo ja_nej($registration->NotComing);?></td></tr>
			<tr><td valign="top" class="header">Återbetalning</td><td><?php echo $registration->ToBeRefunded;?></td></tr>
			<tr><td valign="top" class="header">Återbetalningsdatum</td><td><?php echo $registration->RefundDate;?></td></tr>
			<?php  }?>
		</table>		
			<input type="submit" value="Spara">

			</form>


	</div>


</body>
</html>
