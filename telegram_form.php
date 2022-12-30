<?php
include_once 'includes/db.inc.php';
require 'models/telegram.php';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>

    <body>
    
    <?php
    $telegram = Telegram::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = $_GET['operation'];
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $telegram = Telegram::loadById($_GET['id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $telegram;
        $output = "";

        switch ($field) {
            case "operation":
                $output = "update";
                break;
            case "id":
                $output = $telegram->id;
                break;
            case "action":
                if (is_null($telegram->id)) {
                    $output = "Lägg till";
                    break;
                }
                $output = "Uppdatera";
                break;
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
					name="Deliverytime" value="<?php echo $telegram->deliverytime; ?>" min="1868-09-13T17:00"
					max="1868-09-15T13:00" required></td>
			</tr>
			<tr>
				<td><label for="Sender">Avsändare</label></td>
				<td><input type="text" id="Sender" name="Sender" value="<?php echo $telegram->sender; ?>" required></td>
			</tr>
			<tr>

				<td><label for="SenderCity">Avsändarens stad</label></td>
				<td><input type="text" id="SenderCity" name="SenderCity"
					 value="<?php echo $telegram->senderCity; ?>" required></td>
			</tr>
			<tr>

				<td><label for="Reciever">Mottagare</label></td>
				<td><input type="text" id="Reciever" name="Reciever" value="<?php echo $telegram->reciever; ?>" required></td>
			</tr>
			<tr>

				<td><label for="RecieverCity">Mottagarens stad</label></td>
				<td><input type="text" id="RecieverCity" name="RecieverCity"
					 value="<?php echo $telegram->recieverCity; ?>" required></td>
			</tr>
			<tr>

				<td><label for="Message">Meddelande</label></td>
				<td><textarea id="Message" name="Message" rows="4" cols="50"
					 required><?php echo $telegram->message; ?></textarea></td>
			</tr>
			<tr>

				<td><label for="OrganizerNotes">Anteckningar om telegrammet</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4"
						cols="50"><?php echo $telegram->organizerNotes; ?></textarea></td>

			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
    </body>

</html>