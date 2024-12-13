<?php
    require 'header.php'; 
    $person = Person::newWithDefault();
    $person->Email = $current_user->Email;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $person = $current_person;          
        } else {
        }
    }
    
    
    if ($operation == 'update' && $person->UserId != $current_user->Id) {
        header('Location: index.php'); //Inte din person
        exit;
    }
    
        
    function default_value($field) {
        GLOBAL $person;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($person->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;            
            case "action":
                if (is_null($person->Id)) {
                    $output = "Registrera";
                    break;
                }
                $output = "Uppdatera";
                break;
        }

        echo $output;
    }
    
    include 'navigation.php';
?>
	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-person"></i>
		<?php default_value('action'); ?> person
	</div>


	<form action="logic/person_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php echo $person->Id; ?>">
		<input type="hidden" id="UserId" name="UserId" value="<?php echo $person->UserId; ?>">


			<div class='itemcontainer'>
			Vi behöver veta en del saker om dig som person som är skilt från de karaktärer du spelar. Och som gäller generellt för alla lajv och alla kampanjer.<br>
			Du kan sjäv redigera de här uppgifterna när något ändrar sig eller om det är något du inte vill att Berghems Vänner ska veta om dig längre. Det enda du inte kan ändra är ditt personnummer. Om du behöver ändra det får du kontakta Berghems Vänner så löser vi det.<br>
			Kontrollera gärna inför varje lajv att uppgiftera fortfarande stämmer. 
			</div>
			
			<div class='subheader'>Uppgifter om dig</div>

			<div class='itemcontainer'>
	       	<div class='itemname'><label for="Name">För och efternamn</label> <font style="color:red">*</font></div>
			<input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($person->Name); ?>" size="100" maxlength="50" required>
			</div>
			
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="Email">E-post</label> <font style="color:red">*</font></div>
			E-post är det sättet arrangörerna skickar ut information till deltagare, dvs allmänna utskick, intrig och boendeinformation. Det är också det sättet vi kommer att kontakta dig om vi har några frågor kring din anmälan. Se till att det är en epost som du läser regelbundet.<br>
			<input type="Email" id="Email" name="Email" value="<?php echo htmlspecialchars($person->Email); ?>" maxlength="100" required>
			</div>
			
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="SocialSecurityNumber">Personnummer</label> <font style="color:red">*</font></div>
			Nummret ska vara ÅÅÅÅMMDD-NNNN.<br />
			Om du saknar personnummer kommer du att behöva hjälp av Berghems Vänner för att kunna bli medlem. Skriv då in 0000 som de fyra sista så länge.<br />
			Personnumret kommer att kontrolleras mot medlemsregistret eftersom medlemsskap krävs för att få delta på lajvet. <br>
			<?php 
			if ($person->isNeverRegistered()) {
			    echo "<input type='text' id='SocialSecurityNumber' value='$person->SocialSecurityNumber'".
			    "name='SocialSecurityNumber' pattern='\d{8}-\d{4}|\d{12}'  placeholder='ÅÅÅÅMMDD-NNNN' size='20' maxlength='13' required>";
			} else {
			    echo "$person->SocialSecurityNumber";
			    
			    
			}?>
			</div>


			<div class='itemcontainer'>
	       	<div class='itemname'><label for="PhoneNumber">Mobilnummer</label></div>
			<input type="text" id="PhoneNumber" name="PhoneNumber" value="<?php echo htmlspecialchars($person->PhoneNumber); ?>"  size="100" maxlength="100">
			</div>
			
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="EmergencyContact">Närmaste anhörig</label>&nbsp;<font style="color:red">*</font></div>
			Namn, funktion och mobilnummer till närmast anhöriga. Används enbart i nödfall, exempelvis vid olycka. T ex Greta, Mamma, 08-12345678. <br />
				Det bör vara någon som inte är med på lajvet.<br>
			<textarea id="EmergencyContact" name="EmergencyContact" rows="4" cols="100" maxlength="60000" required><?php echo htmlspecialchars($person->EmergencyContact); ?></textarea>
			</div>
			
			
			<div class='subheader'>Hälsa</div>

			<div class='itemcontainer'>
	       	<div class='itemname'><label for="NormalAllergyType">Vanliga allergier?</label></div>
			<?php NormalAllergyType::selectionDropdown(true, false, $person->getSelectedNormalAllergyTypeIds()); ?>
			</div>
			
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="FoodAllergiesOther">Annat kring matallergier eller annan specialkost? </label></div>
			Om du har allergier eller specialkost som inte täcks av den ovanstående frågan vill vi att du skriver om det här.<br>
				Om du inte har något, lämna fältet tomt.<br>
			<textarea id="FoodAllergiesOther" name="FoodAllergiesOther" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($person->FoodAllergiesOther); ?></textarea>
			</div>

			<div class='itemcontainer'>
	       	<div class='itemname'><label for="HousingComment">Generella boendehänsyn</label></div>
			Har du några speciella saker vi behöver ta hänsyn till när vi planerar boendet? Skriv bara saker som gäller på alla lajv.
			<br>
			Om du inte har något, lämna fältet tomt.<br>
			<input type="text" id="HousingComment" name="HousingComment" value="<?php echo htmlspecialchars($person->HousingComment); ?>" size="100" maxlength="200" >
			</div>

			<div class='itemcontainer'>
	       	<div class='itemname'><label for="HealthComment">Fysisk och mental hälsa</label></div>
			Är det något som vore bra om våra sjukvårdare/trygghetsvärdar vet om dig för att lättare kunna göra ett bra jobb?<br>
			Om du inte har något, lämna fältet tomt.<br>
			<textarea id="HealthComment" name="HealthComment" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($person->HealthComment); ?></textarea>
			</div>


			<div class='subheader'>Lajvrelaterat</div>
			
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="ExperiencesId">Hur erfaren lajvare är du?</label>&nbsp;<font style="color:red">*</font></div>
			<?php Experience::selectionDropdown(false, true, $person->ExperienceId); ?>
            </div>
            
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="NotAcceptableIntrigues">Vilken typ av intriger vill du absolut inte spela på?</label></div>
			Eftersom vi inte vill att någon ska må dåligt är det bra att veta vilka begränsningar du som person har vad det gäller intriger. 
			Vilken typ av intriger vill du absolut inte spela på oavsett kampanj eller setting? Det gäller alltså alla lajv.<br>
			<input type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo htmlspecialchars($person->NotAcceptableIntrigues); ?>" size="100" maxlength="200" >
			</div>

			<div class='subheader'>Övrigt</div>
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="OtherInformation">Övrig information</label></div>
			Är det något annat kring din off-person arrangörerna bör veta? Tex bra kunskaper som sjukvårdare.<br><br>
			Om du inte har något, lämna fältet tomt.<br>
			<textarea id="OtherInformation" name="OtherInformation" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($person->OtherInformation); ?></textarea>
			</div>
			

			<div class='subheader'>Hantering av personuppgifter</div>

			<div class='itemcontainer'>
	       	<div class='itemname'><label for="HasPermissionShowName">Visa namn</label> <font style="color:red">*</font></div>
			Tillåter du att vi visar ditt namn i olika sammanhang? Exempelvis i listan med karaktärer och när boendet presenteras.
			<br>

			<input type="radio" id="HasPermissionShowName_yes" name="HasPermissionShowName" value="1" <?php if ($person->hasPermissionShowName()) echo 'checked="checked"'?> required> 
			<label for="HasPermissionShowName_yes">Ja</label><br> 
			<input type="radio" id="HasPermissionShowName_no" name="HasPermissionShowName" value="0" <?php if (!$person->hasPermissionShowName()) echo 'checked="checked"'?>> 
			<label for="HasPermissionShowName_no">Nej</label>
          	</div>


			<div class='itemcontainer'>
	       	<div class='itemname'><label for="IsSubscribed">Ta emot epost</label> <font style="color:red">*</font></div>
			Tillåter du att vi skickar epost till dig med meddelanden från arrangörerna och information om lajvet? <br>
   			Alla meddelanden kommer att finnas inne i Omnes Mundi i vilket fall, men du behöver logga in och kontrollera om någon har skickat något till dig ifall du inte tar emot epost.
			<br>

			<input type="radio" id="IsSubscribed_yes" name="IsSubscribed" value="1" <?php if ($person->isSubscribed()) echo 'checked="checked"'?> required> 
			<label for="IsSubscribed_yes">Ja</label><br> 
			<input type="radio" id="IsSubscribed_no" name="IsSubscribed" value="0" <?php if (!$person->isSubscribed()) echo 'checked="checked"'?>> 
			<label for="IsSubscribed_no">Nej</label>
			</div>


			<div class='itemcontainer'>
	       	<div class='itemname'><label for="PUL">GDPR</label> <font style="color:red">*</font></div>
			Härmed samtycker jag till att föreningen Berghems
			Vänner får hantera och lagra mina uppgifter - såsom namn,
			e-postadress, telefonnummer, hälsouppgifter och annat. Detta för att kunna
			arrangera lajvet. <br>Den rättsliga grunden för personuppgiftsbehandlingen är att du ger ditt samtycke.<br>
			<input type="checkbox" id="PUL" name="PUL" value="Ja" required>
  			<label for="PUL">Jag samtycker</label>
			</div>

			 <div class='center'><input type="submit" class='button-18' value="<?php default_value('action'); ?>"></div>

		</form>
	</div>

</body>
</html>