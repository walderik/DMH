<?php
include_once 'header.php';

if (!AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId) && !isset($_SESSION['admin'])) {
    exit;
}

?>
   
    <?php
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
    	<form action="larp_admin.php" method="post">
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
    				<td><textarea id="ContentDescription" name="Description" rows="4" cols="150" maxlength="60000" ><?php echo htmlspecialchars($larp->Description); ?></textarea></td>
    			</tr>
    			<tr>
    				<td><label for="Name">Innehåll</label><br>Om det står något i det här fältet kommer alla deltagare vid anmälan behöva bocka i att de förstår vilken typ av lajv det är de anmäler sig till. Beskriv gärna kortfattat miljö (tex medeltid, vilda västern) och stämning (tex familjevänligt, tungt förtrycksspel).</td>
    				<td><textarea id="ContentDescription" name="ContentDescription" rows="4" cols="150" maxlength="60000" ><?php echo htmlspecialchars($larp->ContentDescription); ?></textarea></td>
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
    				<td><label for="MaxParticipants">Max antal deltagare</label></td>
    				<td><input type="number" id="MaxParticipants" name="MaxParticipants" value="<?php echo $larp->MaxParticipants; ?>" size="100" maxlength="250" ></td>
    			</tr>
    			<tr>
    				<td><label for="LatestRegistrationDate">Sista anmälningsdag</label></td>
    				<td><input type="date" id="LatestRegistrationDate"
    					name="LatestRegistrationDate" value="<?php echo $larp->LatestRegistrationDate; ?>" size="50" required></td>
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
    				<td><label for="PaymentReferencePrefix">Prefix för betalningsreferens</label></td>
    				<td><input type="text" id="PaymentReferencePrefix"
    					name="PaymentReferencePrefix" value="<?php echo htmlspecialchars($larp->PaymentReferencePrefix); ?>" size="15" maxlength="10"></td>
    			</tr>
    			<tr>
    				<td><label for=NetDays>Antal dagar för betalning</label></td>
    				<td><input type="number" id="NetDays"
    					name="NetDays" value="<?php echo $larp->NetDays; ?>" size="15" maxlength="10" required></td>
    			</tr>
    			<tr>
    				<td><label for="StartDate">Absolut sista dag för betalning</label><br>Om antal dagar för betalning ger ett senare datum än detta så sätts detta som sista betal-datum för deltagaren.</td>
    				<td><input type="date" id="LastPaymentDate"
    					name="LastPaymentDate" value="<?php echo $larp->LastPaymentDate; ?>" size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for=HasTelegrams>Telegram</label><br>Ska stöd för telegram visas i systemet?</td>
    				<td>
						<input type="radio" id="HasTelegrams_yes" name="HasTelegrams" value="1" <?php if ($larp->hasTelegrams()) echo 'checked="checked"'?>> 
            			<label for="HasTelegrams_yes">Ja</label><br> 
            			<input type="radio" id="HasTelegrams_no" name="HasTelegrams" value="0" <?php if (!$larp->hasTelegrams()) echo 'checked="checked"'?>> 
            			<label for="HasTelegrams_no">Nej</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for=HasLetters>Brev</label><br>Ska stöd för brev visas i systemet?</td>
    				<td>
						<input type="radio" id="HasLetters_yes" name="HasLetters" value="1" <?php if ($larp->hasLetters()) echo 'checked="checked"'?>> 
            			<label for="HasLetters_yes">Ja</label><br> 
            			<input type="radio" id="HasLetters_no" name="HasLetters" value="0" <?php if (!$larp->hasLetters()) echo 'checked="checked"'?>> 
            			<label for="HasLetters_no">Nej</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for=HasRumours>Rykten</label><br>Ska stöd för rykten visas i systemet?</td>
    				<td>
						<input type="radio" id="HasRumours_yes" name="HasRumours" value="1" <?php if ($larp->hasRumours()) echo 'checked="checked"'?>> 
            			<label for="HasRumours_yes">Ja</label><br> 
            			<input type="radio" id="HasRumours_no" name="HasRumours" value="0" <?php if (!$larp->hasRumours()) echo 'checked="checked"'?>> 
            			<label for="HasRumours_no">Nej</label>
					</td>
    			</tr>
    		</table>
    
    		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
    	</form>
    	</div>
    </body>

</html>