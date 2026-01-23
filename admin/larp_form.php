<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'update') {
        $larp=LARP::loadById($_POST['Id']);
        $larp->setValuesByArray($_POST);
        $larp->update();
    }
    if (isset($larp)) header('Location: larp_form.php?operation=update&id='.$larp->Id);
    else header('Location: index.php');
    exit;
}


    $larp = LARP::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $larp = LARP::loadById($_GET['id']);
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $larp;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($larp->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $larp->Id;
                break;
            case "action":
                if (is_null($larp->Id)) {
                    $output = "Lägg till";
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
    	<h1><?php echo default_value('action');?> lajv</h1>
    	<form action="larp_form.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
     		<input type="hidden" id="CampaignId" name="CampaignId" value="<?php echo $larp->CampaignId?>">
    		<table>
    			<tr>
    				<td><label for="Name">Namn</label></td>
    				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($larp->Name); ?>" size="100" maxlength="250" required></td>
    			</tr>
    			<tr>
    				<td><label for="Name">Beskrivning</label><br>Om det står något i det här fältet visas det när man väljer bland kommande lajv. Beskriv gärna kortfattat miljö (tex medeltid, vilda västern) och stämning (tex familjevänligt, tungt förtrycksspel).</td>
    				<td><textarea id="ContentDescription" name="Description" rows="4" style='width:80%;' maxlength="60000" ><?php echo htmlspecialchars($larp->Description); ?></textarea></td>
    			</tr>
    			<tr>
    				<td><label for="Name">Innehåll</label><br>Om det står något i det här fältet kommer alla deltagare vid anmälan behöva bocka i att de förstår vilken typ av lajv det är de anmäler sig till. Beskriv gärna kortfattat miljö (tex medeltid, vilda västern) och stämning (tex familjevänligt, tungt förtrycksspel).</td>
    				<td><textarea id="ContentDescription" name="ContentDescription" rows="4" style='width:80%;' maxlength="60000" ><?php echo htmlspecialchars($larp->ContentDescription); ?></textarea></td>
    			</tr>
    			<tr>
    				<td><label for="StartDate">Startdatum</label></td>
    				<td><input type="datetime-local" id="StartDate"
    					name="StartDate" value="<?php echo formatDateTimeForInput($larp->StartDate); ?>" size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for="EndDate">Slutdatum</label></td>
    				<td><input type="datetime-local" id="EndDate"
    					name="EndDate" value="<?php echo formatDateTimeForInput($larp->EndDate); ?>" size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for="MaxParticipants">Max antal deltagare</label><br>Funktionärer räknas inte in i antal deltagare</td>
    				<td><input type="number" id="MaxParticipants" name="MaxParticipants" value="<?php echo $larp->MaxParticipants; ?>" size="100" maxlength="250"  required></td>
    			</tr>
    			<tr>
    				<td><label for="LatestRegistrationDate">Sista anmälningsdag</label></td>
    				<td><input type="date" id="LatestRegistrationDate"
    					name="LatestRegistrationDate" value="<?php echo $larp->LatestRegistrationDate; ?>" size="50" required></td>
    			</tr>
    			
				<tr>
    				<td><label for="NoRoles">Antal karaktärer som deltagarna får anmäla till lajvet</label><br>Arrangörer kan lägga till fler karaktärer efteråt.</td>
    				<td>
    					<select id="NoRoles" name="NoRoles">
    					<?php 
    					for ($i=0; $i<=(count(LARP::NO_ROLES)-1); $i++) {
    					    echo "<option value='$i' ";
    					    if ($larp->NoRoles==$i) echo "selected";
    					    echo ">";
    					    echo LARP::NO_ROLES[$i];
    					    echo "</option>";
    					}
    					?>
    					</select>
    				</td>
    			</tr>
    			
    			
    			<tr>
    				<td><label for="StartTimeLARPTime">Start lajvtid</label><br>Om lajvet har ett datum och tid inlajv så fyller man i det här.</td>
    				<td><input type="datetime-local" id="StartTimeLARPTime"
    					name="StartTimeLARPTime" value="<?php echo formatDateTimeForInput($larp->StartTimeLARPTime); ?>"  size="50"></td>
    			</tr>
    			<tr>
    				<td><label for="EndTimeLARPTime">Slut lajvtid</label></td>
    				<td><input type="datetime-local" id="EndTimeLARPTime"
    					name="EndTimeLARPTime" value="<?php echo formatDateTimeForInput($larp->EndTimeLARPTime); ?>" size="50"></td>
    			</tr>
    			<tr>
    				<td><label for="PaymentReferencePrefix">Prefix för betalningsreferens</label><br>Max 4 tecken</td>
    				<td><input type="text" id="PaymentReferencePrefix"
    					name="PaymentReferencePrefix" value="<?php echo htmlspecialchars($larp->PaymentReferencePrefix); ?>" size="15" maxlength="4"></td>
    			</tr>
    			<tr>
    				<td><label for=NetDays>Antal dagar för betalning</label></td>
    				<td><input type="number" id="NetDays"
    					name="NetDays" value="<?php echo $larp->NetDays; ?>" size="15" maxlength="10" required></td>
    			</tr>
    			<tr>
    				<td><label for="LastPaymentDate">Absolut sista dag för betalning</label><br>Om antal dagar för betalning ger ett senare datum än detta så sätts detta som sista betal-datum för deltagaren.</td>
    				<td><input type="date" id="LastPaymentDate"
    					name="LastPaymentDate" value="<?php echo $larp->LastPaymentDate; ?>" size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for=ChooseParticipationDates>Välja dagar?</label><br>Ska deltagarna få markera i anmälan vilka dagar de kommer att närvara?</td>
    				<td>
						<input type="radio" id="ChooseParticipationDates_yes" name="ChooseParticipationDates" value="1" <?php if ($larp->chooseParticipationDates()) echo 'checked="checked"'?>> 
            			<label for="ChooseParticipationDates_yes">Ja</label><br> 
            			<input type="radio" id="ChooseParticipationDates_no" name="ChooseParticipationDates" value="0" <?php if (!$larp->chooseParticipationDates()) echo 'checked="checked"'?>> 
            			<label for="ChooseParticipationDates_no">Nej</label>
					</td>
    			</tr>
     			<tr>
    				<?php $hasRegistration = $larp->hasRegistrations(); ?>
    				<td><label for='VisibleToParticipants'>Ska lajvet synas i Omnes Mundis kalendarium?</label>
    				     
    				     <?php if ($hasRegistration) echo "<br><span style='font-style: italic;'>(Lajvet har anmälningar och kan därför inte göras osynligt.)</span>"?>
    				</td>
    				<td>
						<input type="radio" id="VisibleToParticipants_yes" name="VisibleToParticipants" value="1" <?php if ($larp->isVisibleToParticipants()) echo 'checked="checked"'?> <?php if ($hasRegistration) echo " disabled ";?>> 
            			<label for="VisibleToParticipants_yes">Ja</label><br> 
            			<input type="radio" id="VisibleToParticipants_no" name="VisibleToParticipants" value="0" <?php if (!$larp->isVisibleToParticipants()) echo 'checked="checked"'?> <?php if ($hasRegistration) echo " disabled ";?>> 
            			<label for="VisibleToParticipants_no">Nej</label>
					</td>
    			</tr>
	    		</table>
    			<h2>Anmälan</h2>Inställningar för anmälningsformulär
	    		<table>
	   			<tr>
    				<td><label for=HasPhotograph>Har fotograf</label><br>Deltagarna kommer att få frågan vid anmälan om de godkänner att vara med på bild.</td>
    				<td>
						<input type="radio" id="HasPhotograph_yes" name="HasPhotograph" value="1" <?php if ($larp->hasPhotograph()) echo 'checked="checked"'?>> 
            			<label for="HasPhotograph_yes">Ja</label><br> 
            			<input type="radio" id="HasPhotograph_no" name="HasPhotograph" value="0" <?php if (!$larp->hasPhotograph()) echo 'checked="checked"'?>> 
            			<label for="HasPhotograph_no">Nej</label>
					</td>
    			</tr>
	   			<tr>
    				<td><label for=HasTentQuestions>Tältfrågor</label><br>Ska det finns frågor kring tält i anmälan för deltagare?</td>
    				<td>
						<input type="radio" id="HasTentQuestions_yes" name="HasTentQuestions" value="1" <?php if ($larp->hasTentQuestions()) echo 'checked="checked"'?>> 
            			<label for="HasTentQuestions_yes">Ja</label><br> 
            			<input type="radio" id="HasTentQuestions_no" name="HasTentQuestions" value="0" <?php if (!$larp->hasTentQuestions()) echo 'checked="checked"'?>> 
            			<label for="HasTentQuestions_no">Nej</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for=MinimumAgeNPC>Minsta ålder för att vara NPC</label><br>Om man är tillräckligt gammal kommer frågna om NPC att synas vid anmälan.<br>Oavsett kan en arrangör sätta någon till NPC senare.</td>
    				<td><input type="number" id="MinimumAgeNPC"
    					name="MinimumAgeNPC" value="<?php echo $larp->MinimumAgeNPC; ?>" size="15" maxlength="10" required></td>
    			</tr>
    			<tr>
    				<td><label for=MinimumAgeOfficial>Minsta ålder för att vara funktionär</label><br>Om man är tillräckligt gammal kommer frågna om funktionär att synas vid anmälan.<br>Oavsett kan en arrangör sätta någon till funktionär senare.</td>
    				<td><input type="number" id="MinimumAgeOfficial"
    					name="MinimumAgeOfficial" value="<?php echo $larp->MinimumAgeOfficial; ?>" size="15" maxlength="10" required></td>
    			</tr>
	   			<tr>
    				<td><label for=OfficialsMustShowCriminalRecord>Krav på belastningsregisterutdrag för funktionärer</label><br>Deltagarna kommer vid anmälan få informaiton om att funktionrer måste ha med sig belastningsregisterutdrag till lajvet.</td>
    				<td>
						<input type="radio" id="OfficialsMustShowCriminalRecord_yes" name="OfficialsMustShowCriminalRecord" value="1" <?php if ($larp->hasOfficialsMustShowCriminalRecord()) echo 'checked="checked"'?>> 
            			<label for=OfficialsMustShowCriminalRecord_yes">Ja</label><br> 
            			<input type="radio" id="OfficialsMustShowCriminalRecord_no" name="OfficialsMustShowCriminalRecord" value="0" <?php if (!$larp->hasOfficialsMustShowCriminalRecord()) echo 'checked="checked"'?>> 
            			<label for="OfficialsMustShowCriminalRecord_no">Nej</label>
					</td>
    			</tr>
    			
    			
    		</table>
			<h2>Speltekniker</h2>Vilka speltekniker ska det finnas stöd för i systemet för detta lajv?
			<table class="smalldata">
    			<tr>
    				<td><label for=HasTelegrams>Telegram</label></td>
    				<td>
						<input type="radio" id="HasTelegrams_yes" name="HasTelegrams" value="1" <?php if ($larp->hasTelegrams()) echo 'checked="checked"'?>> 
            			<label for="HasTelegrams_yes">Ja</label><br> 
            			<input type="radio" id="HasTelegrams_no" name="HasTelegrams" value="0" <?php if (!$larp->hasTelegrams()) echo 'checked="checked"'?>> 
            			<label for="HasTelegrams_no">Nej</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for=HasLetters>Brev</label></td>
    				<td>
						<input type="radio" id="HasLetters_yes" name="HasLetters" value="1" <?php if ($larp->hasLetters()) echo 'checked="checked"'?>> 
            			<label for="HasLetters_yes">Ja</label><br> 
            			<input type="radio" id="HasLetters_no" name="HasLetters" value="0" <?php if (!$larp->hasLetters()) echo 'checked="checked"'?>> 
            			<label for="HasLetters_no">Nej</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for=HasRumours>Rykten</label></td>
    				<td>
						<input type="radio" id="HasRumours_yes" name="HasRumours" value="1" <?php if ($larp->hasRumours()) echo 'checked="checked"'?>> 
            			<label for="HasRumours_yes">Ja</label><br> 
            			<input type="radio" id="HasRumours_no" name="HasRumours" value="0" <?php if (!$larp->hasRumours()) echo 'checked="checked"'?>> 
            			<label for="HasRumours_no">Nej</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for=HasAlchemy>Alkemi</label></td>
    				<td>
						<input type="radio" id="HasAlchemy_yes" name="HasAlchemy" value="1" <?php if ($larp->hasAlchemy()) echo 'checked="checked"'?>> 
            			<label for="HasAlchemy_yes">Ja</label><br> 
            			<input type="radio" id="HasAlchemy_no" name="HasAlchemy" value="0" <?php if (!$larp->hasAlchemy()) echo 'checked="checked"'?>> 
            			<label for="HasAlchemy_no">Nej</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for="LastDayAlchemySupplier">Sista dag för lövjeristinformaiton</label><br>Sista dagen som lövjeristerna får lägga upp vilka ingredienser de tar med sig.</td>
    				<td><input type="date" id="LastDayAlchemySupplier"
    					name="LastDayAlchemySupplier" value="<?php echo $larp->LastDayAlchemySupplier; ?>" size="50"></td>
    			</tr>
    			<tr>
    				<td><label for="LastDayAlchemySupplier">Sista dag för alkemistinformaiton</label><br>Sista dagen som alkemisterna får lägga upp och önska recept.<br>De kan även önska efter lajvet för att kunna lägga in sådant de har lärt sig under lajvet.</td>
    				<td><input type="date" id="LastDayAlchemy"
    					name="LastDayAlchemy" value="<?php echo $larp->LastDayAlchemy; ?>" size="50"></td>
    			</tr>
    			<tr>
    				<td><label for=HasMagic>Magi</label></td>
    				<td>
						<input type="radio" id="HasMagic_yes" name="HasMagic" value="1" <?php if ($larp->hasMagic()) echo 'checked="checked"'?>> 
            			<label for="HasMagic_yes">Ja</label><br> 
            			<input type="radio" id="HasMagic_no" name="HasMagic" value="0" <?php if (!$larp->hasMagic()) echo 'checked="checked"'?>> 
            			<label for="HasMagic_no">Nej</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for=HasVisions>Syner</label></td>
    				<td>
						<input type="radio" id="HasVisions_yes" name="HasVisions" value="1" <?php if ($larp->hasVisions()) echo 'checked="checked"'?>> 
            			<label for="HasVisions_yes">Ja</label><br> 
            			<input type="radio" id="HasVisions_no" name="HasVisions" value="0" <?php if (!$larp->hasVisions()) echo 'checked="checked"'?>> 
            			<label for="HasVisions_no">Nej</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for=HasCommerce>Handel</label></td>
    				<td>
						<input type="radio" id="HasCommerce_yes" name="HasCommerce" value="1" <?php if ($larp->hasCommerce()) echo 'checked="checked"'?>> 
            			<label for="HasCommerce_yes">Ja</label><br> 
            			<input type="radio" id="HasCommerce_no" name="HasCommerce" value="0" <?php if (!$larp->hasCommerce()) echo 'checked="checked"'?>> 
            			<label for="HasCommerce_no">Nej</label>
					</td>
    			</tr>
    		</table>
    		<h2>Utvärdering</h2>
    		<?php 
    		$isEvaluationStarted = false;
    		if ($larp->useInternalEvaluation()) {
    		    $question_result = EvaluationNumberQuestion::get("larp_q1", $larp);
    		    if ($question_result->number_of_responders > 0) $isEvaluationStarted = true;
    		} else $isEvaluationStarted = $larp->isEvaluationOpen();
	
    		
    		?>
    		<table>
    			<tr>
    				<td><label for="EvaluationOpenDate">När ska utvärderingen öppnas?</label><br>Utvärderingen kan inte öppna förrän lajvet är slut.<br>När lajvet är slut kan deltagarna se när utvärderingen kommer att öppna om den inte redan är öppen.<br>Om inget datum sätts öppnar det när lajvet slutar.</td>
    				<td><input type="date" id="EvaluationOpenDate"
    					name="EvaluationOpenDate" value="<?php echo $larp->EvaluationOpenDate; ?>" size="50" 
    					<?php if ($isEvaluationStarted) echo "disabled=disabled";?> ></td>
    			</tr>
    			
    			<tr>
    				<td><label for='EvaluationType'>Typ av utvärdering</label><br>Ska Omnes Mundis inbygda utvärdering användas eller en extern?<br><a href='../participant/evaluation.php?ViewOnly=1' target='_blank'>Frågorna i den inbygda utvärderingen.</a></td>
    				<td>
						<input type="radio" id="Internal" name="EvaluationType" value="1" <?php if ($larp->useInternalEvaluation()) echo 'checked="checked"'?> onclick='toggleEnableLink(false)' <?php if ($isEvaluationStarted) echo "disabled=disabled";?> > 
            			<label for="Internal">Inbygd</label><br> 
            			<input type="radio" id="External" name="EvaluationType" value="0" <?php if (!$larp->useInternalEvaluation()) echo 'checked="checked"'?> onclick='toggleEnableLink(true)' <?php if ($isEvaluationStarted) echo "disabled=disabled";?> > 
            			<label for="External">Extern</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for="EvaluationLink">Länk till extern utvärdering</label></td>
    				<td><input type="url" id="EvaluationLink" name="EvaluationLink" value="<?php echo htmlspecialchars($larp->EvaluationLink); ?>" size="100" maxlength="250" required <?php if ($isEvaluationStarted) echo "disabled=disabled";?> ></td>
    			</tr>
    		
    		</table>
    
    		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
    	</form>
    	</div>
    </body>

</html>
    <script>
    function toggleEnableLink(enable) {
        if (enable) document.getElementById('EvaluationLink').disabled = false;
        else {
            linkField = document.getElementById('EvaluationLink');
            linkField.disabled = true;
            linkField.value = "";
        }
    }

    <?php if (!$isEvaluationStarted) echo "toggleEnableLink(!".$larp->useInternalEvaluation().");"; ?>
    </script>
