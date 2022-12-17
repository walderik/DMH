<?php
include_once 'includes/db.inc.php';
require 'telegram.php';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>

    <body>
    
    <?php
    $telegram = null;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = $_GET['operation'];
        if ($operation == 'new') {
        } else if ($operation == 'update') {
            $telegram = Telegram::loadById($_GET['id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $telegram;
        $output = "";
        if (is_null($telegram)) {
            switch ($field) {
                case "operation":
                    $output = "insert";
                    break;
                case "id":
                    $output = "-1";
                    break;
                case "deliverytime":
                    $output = "1868-09-13T17:00";
                    break;
                case "senderCity":
                    $output = "Junk City";
                    break;
                case "recieverCity":
                    $output = "Slow River";
                    break;
                case "action":
                    $output = "Lägg till";
                    break;
            }
        }
        else {

            switch ($field) {
                case "operation":
                    $output = "update";
                    break;
                case "id":
                    $output = $telegram->id;
                    break;
                case "deliverytime":
                    $output = $telegram->deliverytime;
                    break;
                case "sender":
                    $output = $telegram->sender;
                    break;
                case "senderCity":
                    $output = $telegram->senderCity;
                    break;
                case "reciever":
                    $output = $telegram->reciever;
                    break;
                case "recieverCity":
                    $output = $telegram->recieverCity;
                    break;
                case "message":
                    $output = $telegram->message;
                    break;
                case "notes":
                    $output = $telegram->organizerNotes;
                    break;
                case "action":
                    $output = "Uppdatera";
                    break;
            }
        }
        echo $output;
    }
    
    ?>
	<form action="telegram_admin.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<table>
			<tr>
				<td><label for="Deliverytime">Leveranstid</label></td>
				<td><input type="datetime-local" id="Deliverytime"
					name="Deliverytime" value="<?php default_value('deliverytime'); ?>" min="1868-09-13T17:00"
					max="1868-09-15T13:00" required></td>
			</tr>
			<tr>
				<td><label for="Sender">Avsändare</label></td>
				<td><input type="text" id="Sender" name="Sender" value="<?php default_value('sender'); ?>" required></td>
			</tr>
			<tr>

				<td><label for="SenderCity">Avsändarens stad</label></td>
				<td><input type="text" id="SenderCity" name="SenderCity"
					 value="<?php default_value('senderCity'); ?>" required></td>
			</tr>
			<tr>

				<td><label for="Reciever">Mottagare</label></td>
				<td><input type="text" id="Reciever" name="Reciever" value="<?php default_value('reciever'); ?>" required></td>
			</tr>
			<tr>

				<td><label for="RecieverCity">Mottagarens stad</label></td>
				<td><input type="text" id="RecieverCity" name="RecieverCity"
					 value="<?php default_value('recieverCity'); ?>" required></td>
			</tr>
			<tr>

				<td><label for="Message">Meddelande</label></td>
				<td><textarea id="Message" name="Message" rows="4" cols="50"
					 required><?php default_value('message'); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="OrganizerNotes">Anteckningar om telegrammet</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4"
						cols="50"><?php default_value('notes'); ?></textarea></td>

			</tr>

		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
    </body>

</html>