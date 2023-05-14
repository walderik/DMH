<?php
include_once 'header.php';

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
    
    include 'navigation_subpage.php';
    
    ?>
    <div class="content"> 
    	<h1><?php echo default_value('action');?> lajv <a href="larp_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
    	<form action="larp_admin.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
     		<input type="hidden" id="Campaign" name="Campaign" value="<?php echo $larp->CampaignId?>">
    		<table>
    			<tr>
    				<td><label for="Name">Namn</label></td>
    				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($larp->Name); ?>" size="100" maxlength="250" required></td>
    			</tr>
    			<tr>
    				<td><label for="StartDate">Startdatum</label></td>
    				<td><input type="datetime-local" id="StartDate"
    					name="StartDate" value="<?php echo $larp->StartDate; ?>" size="50" required></td>
    			</tr>
    			<tr>
    				<td><label for="EndDate">Slutdatum</label></td>
    				<td><input type="datetime-local" id="EndDate"
    					name="EndDate" value="<?php echo $larp->EndDate; ?>" size="50" required></td>
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
    				<td><label for="StartTimeLARPTime">Start lajvtid</label></td>
    				<td><input type="datetime-local" id="StartTimeLARPTime"
    					name="StartTimeLARPTime" value="<?php echo $larp->StartTimeLARPTime; ?>"  size="50"></td>
    			</tr>
    			<tr>
    				<td><label for="EndTimeLARPTime">Slut lajvtid</label></td>
    				<td><input type="datetime-local" id="EndTimeLARPTime"
    					name="EndTimeLARPTime" value="<?php echo $larp->EndTimeLARPTime; ?>" size="50"></td>
    			</tr>
    			<tr>
    				<td><label for="PaymentReferencePrefix">Prefix för betalningsreferens</label></td>
    				<td><input type="text" id="PaymentReferencePrefix"
    					name="PaymentReferencePrefix" value="<?php echo htmlspecialchars($larp->PaymentReferencePrefix); ?>" size="15" maxlength="10" required></td>
    			</tr>
    			<tr>
    				<td><label for=NetDays>Antal dagar för betalning</label></td>
    				<td><input type="number" id="NetDays"
    					name="NetDays" value="<?php echo $larp->NetDays; ?>" size="15" maxlength="10" required></td>
    			</tr>
    		</table>
    
    		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
    	</form>
    	</div>
    </body>

</html>