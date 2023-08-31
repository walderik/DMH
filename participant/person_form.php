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
            $person = Person::loadById($_GET['id']);           
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

	<div class="content">
		<h1>Registrering av deltagare</h1>
		<form action="logic/person_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php echo $person->Id; ?>">
    		<input type="hidden" id="UserId" name="UserId" value="<?php echo $person->UserId; ?>">


			<p>

			Vi behöver veta en del saker om dig som person som är skilt från de karaktärer du spelar.</p>
			<h2>Personuppgifter</h2>
			
			<div class="question">
				<label for="Name">För och efternamn</label>&nbsp;<font style="color:red">*</font>
				<br> <input class="input_field" type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($person->Name); ?>" size="100" maxlength="50" required>
			</div>
			<div class="question">
				<label for="Email">E-post</label>&nbsp;<font style="color:red">*</font><br>
				<div class="explanation">E-post är det sättet arrangörerna skickar ut information till deltagare, dvs allmänna utskick, intrig och boendeinformation. Det är också det sättet vi kommer att kontakta dig om vi har några frågor kring din anmälan. Se till att det är en epost som du läser regelbundet.</div>
				<input  class="input_field" type="Email" id="email" name="Email" value="<?php echo htmlspecialchars($person->Email); ?>"  size="100" maxlength="100" required>
			</div>
			<div class="question">
				<label for="SocialSecurityNumber">Personnummer</label>&nbsp;<font style="color:red">*</font><br> 
				<div class="explanation">Nummret ska vara ÅÅÅÅMMDD-NNNN.<br />
				Om du saknar personnummer kommer du att behöva hjälp av Berghems Vänner för att kunna bli medlem. Skriv då in 0000 som de fyra sista så länge.<br />
				Personnumret kommer att kontrolleras mot medlemsregistret eftersom medlemsskap krävs för att få delta på lajvet. </div>
				<?php 
				if ($person->isNeverRegistered()) {
				    echo "<input type='text' id='SocialSecurityNumber' value='$person->SocialSecurityNumber;'".
				    "name='SocialSecurityNumber' pattern='\d{8}-\d{4}|\d{12}'  placeholder='ÅÅÅÅMMDD-NNNN' size='20' maxlength='13' required>";
				} else {
				    echo "$person->SocialSecurityNumber";
				    
				    
				}?>
				
			</div>
			<div class="question">
				<label for="PhoneNumber">Mobilnummer</label>
				<br> <input class="input_field" type="text" id="PhoneNumber" name="PhoneNumber" value="<?php echo htmlspecialchars($person->PhoneNumber); ?>"  size="100" maxlength="100">
			</div>
			<div class="question">
				<label for="EmergencyContact">Närmaste anhörig</label>&nbsp;<font style="color:red">*</font>
				<br> 
				<div class="explanation">Namn, funktion och mobilnummer till närmast anhöriga. Används enbart i nödfall, exempelvis vid olycka. T ex Greta, Mamma, 08-12345678. <br />
				Det bör vara någon som inte är med på lajvet.</div>
				<textarea class="input_field" id="EmergencyContact" name="EmergencyContact" rows="4" cols="100" maxlength="60000" required><?php echo htmlspecialchars($person->EmergencyContact); ?></textarea>
			</div>
			
			
			<h2>Husförvaltare</h2>
			<div class="question">
				<label for="House">Är du husförvaltare?</label><br>
				<div class="explanation">I så fall välj ditt hus</div>
				<?php selectionByArray('House', House::all(), false, false, $person->HouseId); ?>
			</div>

			
			<h2>Lajvrelaterat</h2>
			
			<div class="question">
				<label for="ExperiencesId">Hur erfaren lajvare är du?</label>&nbsp;<font style="color:red">*</font><br>
       			<div class="explanation"><?php Experience::helpBox(); ?></div>
                <?php Experience::selectionDropdown(false, true, $person->ExperienceId); ?>
            </div>
			<div class="question">
				<label for="NotAcceptableIntrigues">Vilken typ av intriger vill du absolut inte spela på?</label>
				<br> 
				<div class="explanation">Eftersom vi inte vill att någon ska må dåligt är det bra att veta vilka begränsningar du som person har vad det gäller intriger.</div>
				<input class="input_field" type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo htmlspecialchars($person->NotAcceptableIntrigues); ?>" size="100" maxlength="200" >
			</div>

			<h2>Hälsa</h2>

			<div class="question">
				<label for="NormalAllergyType">Vanliga allergier?</label>
				<br> 
				
				<div class="explanation">Har du någon eller några av de vanligaste mat-allergierna?<br>
				<?php NormalAllergyType::helpBox(); ?></div>
				<?php NormalAllergyType::selectionDropdown(true, false, $person->getSelectedNormalAllergyTypeIds()); ?>
			</div>
			
			<div class="question">
				<label for="FoodAllergiesOther">Annat kring matallergier eller annan specialkost? </label><br>
				<div class="explanation">Om du har allergier eller specialkost som inte täcks av den ovanstående frågan vill vi att du skriver om det här.<br>
				Om du inte har något, skriv inget.</div>
				<textarea class="input_field" id="FoodAllergiesOther" name="FoodAllergiesOther" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($person->FoodAllergiesOther); ?></textarea>
			</div>

			<div class="question">
				<label for="HousingComment">Boendehänsyn</label><br>
				<div class="explanation">Har du några speciella saker vi behöver ta hänsyn till när vi planerar boendet?<br>
				Om du inte har något, skriv inget.</div>
				<input class="input_field" type="text" id="HousingComment" name="HousingComment" value="<?php echo htmlspecialchars($person->HousingComment); ?>" size="100" maxlength="200" >
			</div>

			<div class="question">
				<label for="HealthComment">Fysisk och mental hälsa</label><br>
				<div class="explanation">Är det något som vore bra om våra sjukvårdare/trygghetsvärdar vet om dig för att lättare kunna göra ett bra jobb?<br>
				Om du inte har något, skriv inget.</div>
				<textarea class="input_field" id="HealthComment" name="HealthComment" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($person->HealthComment); ?></textarea>
			</div>


			<h2>Övrigt</h2>
			<div class="question">
				<label for="OtherInformation">Övrig information</label><br>
				<div class="explanation">Är det något annat kring din off-person arrangörerna bör veta? Tex bra kunskaper som sjukvårdare.<br><br>
				Om du inte har något, skriv inget.</div>
				<textarea class="input_field" id="OtherInformation" name="OtherInformation" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($person->OtherInformation); ?></textarea>
			
			 
			</div>
			

			<h2>Personuppgifter</h2>

			<div class="question">
				<label for="HasPermissionShowName">Visa namn</label> <font style="color:red">*</font><br>
       			<div class="explanation">Tillåter du att vi visar ditt namn i olika sammanhang? Exempelvis i listan med karaktärer och när boendet presenteras.
			</div>

    			<input type="radio" id="HasPermissionShowName_yes" name="HasPermissionShowName" value="1" <?php if ($person->hasPermissionShowName()) echo 'checked="checked"'?> required> 
    			<label for="HasPermissionShowName_yes">Ja</label><br> 
    			<input type="radio" id="HasPermissionShowName_no" name="HasPermissionShowName" value="0" <?php if (!$person->hasPermissionShowName()) echo 'checked="checked"'?>> 
    			<label for="HasPermissionShowName_no">Nej</label>

								
           </div>


			<div class="question">
			<label for="PUL">GDPR</label> <font style="color:red">*</font><br>
			<div class="explanation">Härmed samtycker jag till att föreningen Berghems
			Vänner får hantera och lagra mina uppgifter - såsom namn/
			e-postadress/telefonnummer/hälsouppgifter/annat. Detta för att kunna
			arrangera lajvet. <br>Den rättsliga grunden för personuppgiftsbehandlingen är att du ger ditt samtycke.&nbsp;</div>
			
			
			
			
			<input type="checkbox" id="PUL" name="PUL" value="Ja" required>
  			<label for="PUL">Jag samtycker</label>
			</div>

			  <input type="submit" value="<?php default_value('action'); ?>">
		</form>
	</div>

</body>
</html>