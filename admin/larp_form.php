<?php
include_once '../includes/db.inc.php';
require '../models/LARP.php';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Lägg till / Redigera lajv</title>
</head>

    <body>
    
    <?php
    $larp = LARP::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = $_GET['operation'];
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
    
    
    ?>
	<form action="larp_admin.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo $larp->Name; ?>" required></td>
			</tr>
			<tr>
				<td><label for="Abbreviation">Förkortning</label></td>
				<td><input type="text" id="Abbreviation" name="Abbreviation" value="<?php echo $larp->Abbreviation; ?>" required></td>
			</tr>
			<tr>
				<td><label for="TagLine">Tag line</label></td>
				<td><input type="text" id="TagLine" name="TagLine" value="<?php echo $larp->TagLine; ?>"></td>
			</tr>
			<tr>
				<td><label for="StartDate">Startdatum</label></td>
				<td><input type="datetime-local" id="StartDate"
					name="StartDate" value="<?php echo $larp->StartDate; ?>" required></td>
			</tr>
			<tr>
				<td><label for="EndDate">Slutdatum</label></td>
				<td><input type="datetime-local" id="EndDate"
					name="EndDate" value="<?php echo $larp->EndDate; ?>" required></td>
			</tr>
			<tr>
				<td><label for="MaxParticipants">Max antal deltagare</label></td>
				<td><input type="number" id="MaxParticipants" name="MaxParticipants" value="<?php echo $larp->MaxParticipants; ?>"></td>
			</tr>
			<tr>
				<td><label for="LatestRegistrationDate">Sista anmälningsdag</label></td>
				<td><input type="date-local" id="LatestRegistrationDate"
					name="LatestRegistrationDate" value="<?php echo $larp->LatestRegistrationDate; ?>" required></td>
			</tr>
			<tr>
				<td><label for="StartTimeLARPTime">Start lajvtid</label></td>
				<td><input type="datetime-local" id="StartTimeLARPTime"
					name="StartTimeLARPTime" value="<?php echo $larp->StartTimeLARPTime; ?>" ></td>
			</tr>
			<tr>
				<td><label for="EndTimeLARPTime">Slut lajvtid</label></td>
				<td><input type="datetime-local" id="EndTimeLARPTime"
					name="EndTimeLARPTime" value="<?php echo $larp->EndTimeLARPTime; ?>"></td>
			</tr>
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
    </body>

</html>