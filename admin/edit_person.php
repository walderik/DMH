<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $PersonId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$person = Person::loadById($PersonId);

if (!$person->isRegistered($current_larp)) {
    header('Location: index.php'); // Karaktären är inte anmäld
    exit;
}

$registration = Registration::loadByIds($person->Id, $current_larp->Id);

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

$roles = $person->getRolesAtLarp($current_larp);
$userMayEdit = false;
foreach($roles as $role) {
    if ($role->userMayEdit($current_larp)) $userMayEdit = true;
}

include 'navigation.php';

?>


	<div class="content">
		<h1><?php echo $person->Name;?></h1>
		<form action="logic/edit_person_save.php" method="post">
    		<input type="hidden" id="PersonId" name="PersonId" value="<?php echo $person->Id; ?>">
    		<input type="hidden" id="RegistrationId" name="RegistrationId" value="<?php echo $registration->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
    		
 			<tr><td valign="top" class="header">Namn&nbsp;<font style="color:red">*</font></td>
 			<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($person->Name); ?>" size="100" maxlength="250" required></td></tr>
    		
			<tr><td valign="top" class="header">Personnummer&nbsp;<font style="color:red">*</font></td>
				<td><input type="text" id="SocialSecurityNumber" value="<?php echo $person->SocialSecurityNumber; ?>"
					name="SocialSecurityNumber" pattern="\d{8}-\d{4}|\d{8}-x{4}|\d{12}|\d{8}x{4}"  placeholder="ÅÅÅÅMMDD-NNNN" size="20" maxlength="13" required>
			</td></tr>
			<tr><td valign="top" class="header">Email&nbsp;<font style="color:red">*</font></td>
			<td><input type="Email" id="email" name="Email" value="<?php echo htmlspecialchars($person->Email); ?>"  size="100" maxlength="250" required>
			<?php  echo contactEmailIcon($person->Name,$person->Email); ?>
			</td></tr>
			<tr><td valign="top" class="header">Mobilnummer</td>
			<td><input type="text" id="PhoneNumber" name="PhoneNumber" value="<?php echo htmlspecialchars($person->PhoneNumber); ?>"  size="100" maxlength="250"></td></tr>
			<tr><td valign="top" class="header">Närmaste anhörig&nbsp;<font style="color:red">*</font></td>
			<td><textarea id="EmergencyContact" name="EmergencyContact" rows="4" cols="100" maxlength="60000" required><?php echo $person->EmergencyContact; ?></textarea></td></tr>
		    <?php 
		    if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
		    ?>
			<tr><td valign="top" class="header">Ansvarig vuxen&nbsp;<font style="color:red">*</font></td>
			<td><input type="text" id="GuardianInfo" name="GuardianInfo" placeholder="Ange namn eller personnummer på den som är ansvarig vuxen" 
			value="<?php if (!empty($registration->GuardianId)) echo htmlspecialchars($registration->getGuardian()->Name);?>" size="100" maxlength="250"></td></tr>
		    
		    <?php 
		    }
		    ?>

			<tr><td valign="top" class="header">Erfarenhet&nbsp;<font style="color:red">*</font></td>
			<td><?php Experience::selectionDropdown(false, true, $person->ExperienceId); ?></td></tr>
			<tr><td valign="top" class="header">Intriger du inte vill spela på</td>
			<td><input type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo htmlspecialchars($person->NotAcceptableIntrigues); ?>" size="100" maxlength="250" ></td></tr>

			<?php 
		    echo "<tr><td valign='top' class='header'>Närvarande&nbsp;<font style='color:red'>*</font></td>";
		    echo "<td>";
		    
		    
		    $formatter = new IntlDateFormatter(
		        'sv-SE',
		        IntlDateFormatter::FULL,
		        IntlDateFormatter::FULL,
		        'Europe/Stockholm',
		        IntlDateFormatter::GREGORIAN,
		        'EEEE d MMMM'
		        );
		    
		    $begin = new DateTime(substr($current_larp->StartDate,0,10));
		    $end   = new DateTime(substr($current_larp->EndDate,0,10));
		    $daysNotAttendingArr = explode(", ",$registration->LarpPartNotAttending);
		    for($i = $begin; $i <= $end; $i->modify('+1 day')){
		        $datestr = $i->format("Y-m-d");
		        $checkedStr = "checked='checked'";
		        if (in_array($datestr, $daysNotAttendingArr)) $checkedStr = "";
		        echo "<input type='checkbox' id='day$datestr' name='ChooseParticipationDates[]' value='$datestr' $checkedStr>";
		        echo "<label for='day$datestr'> ".$formatter->format($i)."</label><br>";
		    }
		    echo "</td>";
			?>



			<?php if (TypeOfFood::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av mat&nbsp;<font style="color:red">*</font></td>
			<td><?php TypeOfFood::selectionDropdown($current_larp, false, true, $registration->TypeOfFoodId); ?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Matalternativ</td><td><input type="text" id="FoodChoice" name="FoodChoice" value="<?php echo htmlspecialchars($registration->FoodChoice); ?>" size="100" maxlength="250"></td></tr>
			   
			<tr><td valign="top" class="header">Vanliga allergier</td>
			<td><?php NormalAllergyType::selectionDropdown(true, false, $person->getSelectedNormalAllergyTypeIds()); ?></td></tr>

			<tr><td valign="top" class="header">Andra allergier</td>
			<td><textarea id="FoodAllergiesOther" name="FoodAllergiesOther" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($person->FoodAllergiesOther); ?></textarea></td></tr>

			<tr><td valign="top" class="header">NPC önskemål</td>
			<td><input type="text" id="NPCDesire" name="NPCDesire" size="100" maxlength="250" value="<?php echo htmlspecialchars($registration->NPCDesire);?>"></td></tr>
			
			<?php if (HousingRequest::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Önskat boende&nbsp;<font style="color:red">*</font></td>
			<td><?php HousingRequest::selectionDropdown($current_larp, false,true,$registration->HousingRequestId);?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Typ av tält</td>
			<td><input class="input_field" type="text" id="TentType" name="TentType"  maxlength="200" value="<?php echo htmlspecialchars($registration->TentType); ?>"></td></tr>
			<tr><td valign="top" class="header">Storlek på tält</td>
			<td><input class="input_field" type="text" id="TentSize" name="TentSize"  maxlength="200" value="<?php echo htmlspecialchars($registration->TentSize); ?>"></td></tr>
			<tr><td valign="top" class="header">Vilka ska bo i tältet</td>
			<td><textarea class="input_field" id="TentHousing" name="TentHousing" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($registration->TentHousing); ?></textarea></td></tr>
			<tr><td valign="top" class="header">Önskad placering</td>
			<td><input class="input_field" type="text" id="TentPlace" name="TentPlace"  maxlength="200" value="<?php echo htmlspecialchars($registration->TentPlace); ?>"></td></tr>
			<tr><td valign="top" class="header">Boendehänsyn, generell</td>
			<td><input class="input_field" type="text" id="HousingComment" name="HousingComment" value="<?php echo htmlspecialchars($person->HousingComment); ?>" size="100" maxlength="200" ></td></tr>
			<tr><td valign="top" class="header">Boendehänsyn för lajvet</td>
			<td><input class="input_field" type="text" id="LarpHousingComment" name="LarpHousingComment" value="<?php echo htmlspecialchars($registration->LarpHousingComment); ?>" size="100" maxlength="200" ></td></tr>

			<tr><td valign="top" class="header">Hälsa</td>
			<td>
			<textarea id="HealthComment" name="HealthComment" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($person->HealthComment); ?></textarea>
			</td></tr>



			<tr><td valign="top" class="header">Annan information</td>
			<td><textarea id="OtherInformation" name="OtherInformation" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($person->OtherInformation); ?></textarea></td></tr>

			<tr><td valign="top" class="header">Får visa namn</td>
			<td>
    			<input type="radio" id="HasPermissionShowName_yes" name="HasPermissionShowName" value="1" <?php if ($person->HasPermissionShowName()) echo 'checked="checked"'?> required> 
    			<label for="HasPermissionShowName_yes">Ja</label><br> 
    			<input type="radio" id="HasPermissionShowName_no" name="HasPermissionShowName" value="0" <?php if (!$person->HasPermissionShowName()) echo 'checked="checked"'?>> 
    			<label for="HasPermissionShowName_no">Nej</label>
			</td></tr>


			<tr><td valign="top" class="header">Medlem</td><td><?php echo ja_nej($registration->isMember())?></td></tr>
			<tr><td valign="top" class="header">Anmäld</td><td><?php echo $registration->RegisteredAt;?></td></tr>
			<tr><td valign="top" class="header">Godkända karaktärer</td><td><?php echo ja_nej($person->isApprovedCharacters($current_larp))?></td></tr>
			<tr><td valign="top" class="header">Funktionär</td><td><?php echo ja_nej($registration->IsOfficial)?></td></tr>
			<?php if (OfficialType::isInUse($current_larp)) { ?>
			<tr><td valign="top" class="header">Typ av funktionär</td><td><?php OfficialType::selectionDropdown($current_larp, true,false,$registration->getSelectedOfficialTypeIds());?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Betalningsreferens</td><td><?php echo $registration->PaymentReference;?></td></tr>
			<tr><td valign="top" class="header">Belopp att betala</td><td><?php echo $registration->AmountToPay;?></td></tr>
			<tr><td valign="top" class="header">Belopp betalat</td><td><?php echo $registration->AmountPayed;?></td></tr>
			<tr><td valign="top" class="header">Betalat datum</td><td><?php echo $registration->Payed;?></td></tr>
			<?php 
			if ($registration->isNotComing()) {
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
