<?php
include_once 'header.php';


    $campaign = Campaign::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $campaign = Campaign::loadById($_GET['id']);
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $campaign;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($campaign->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $campaign->Id;
                break;
            case "action":
                if (is_null($campaign->Id)) {
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
    	<h1><?php echo default_value('action');?> lajv <a href="campaign_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
    	<form action="campaign_admin.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
    		<table>
    			<tr>
    				<td><label for="Name">Namn</label></td>
    				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($campaign->Name); ?>" size="100" maxlength="250" required></td>
    			</tr>
    			<tr>
    				<td><label for="StartDate">Förkortning</label></td>
    				<td><input type="text" id="Abbreviation"
    					name="Abbreviation" value="<?php echo htmlspecialchars($campaign->Abbreviation); ?>" size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for="Icon">Ikon</label></td>
    				<td><input type="text" id="Icon"
    					name="Icon" value="<?php echo $campaign->Icon; ?>" size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for="Homepage">Hemsida</label></td>
    				<td><input type="text" id="Homepage" name="Homepage" value="<?php echo htmlspecialchars($campaign->Homepage); ?>" size="100" maxlength="250" required></td>
    			</tr>
    			<tr>
    				<td><label for="Email">Epost</label></td>
    				<td><input type="text" id="Email"
    					name="Email" value="<?php echo $campaign->Email; ?>" size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for="Bankaccount">Bankkoto</label></td>
    				<td><input type="text" id="Bankaccount"
    					name="Bankaccount" value="<?php echo htmlspecialchars($campaign->Bankaccount); ?>"  size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for="Bankaccount">Swishnummer</label></td>
    				<td><input type="text" id="SwishNumber"
    					name="SwishNumber" value="<?php echo htmlspecialchars($campaign->SwishNumber); ?>"  size="50"></td>
    			</tr>
    			<tr>
    				<td><label for="MinimumAge">Minsta ålder</label></td>
    				<td><input type="number" id="MinimumAge"
    					name="MinimumAge" value="<?php echo $campaign->MinimumAge; ?>" size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for="MinimumAgeWithoutGuardian">Minsta ålder utan ansvarig vuxen</label></td>
    				<td><input type="number" id="MinimumAgeWithoutGuardian"
    					name="MinimumAgeWithoutGuardian" value="<?php echo $campaign->MinimumAgeWithoutGuardian; ?>" size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for="Currency">Lajv-valuta</label></td>
    				<td><input type="text" id="Currency"
    					name="Currency" value="<?php echo $campaign->Currency; ?>" size="50"></td>
    			</tr>
    			<tr>
    				<td><label for=ShowGroupMemberHousingInormation>Fråga om boende för gruppmedlemmar</label><br>I anmälningsformuläret för deltagare finns frågor om bodent. Man kan välja att inte visa dem för de som har en huvudkaraktär som är med i en grupp.</td>
    				<td>
						<input type="radio" id="ShowGroupMemberHousingInormation_yes" name="ShowGroupMemberHousingInormation" value="1" <?php if ($campaign->showGroupMemberHousingInormation()) echo 'checked="checked"'?>> 
            			<label for="ShowGroupMemberHousingInormation_yes">Ja</label><br> 
            			<input type="radio" id="ShowGroupMemberHousingInormation_no" name="ShowGroupMemberHousingInormation" value="0" <?php if (!$campaign->showGroupMemberHousingInormation()) echo 'checked="checked"'?>> 
            			<label for="ShowGroupMemberHousingInormation_no">Nej</label>
					</td>
    			</tr>
    		</table>
    
    		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
    	</form>
    	</div>
    </body>

</html>