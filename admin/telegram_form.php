<?php
require 'header.php';

?>

    
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
                if (is_null($telegram->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $telegram->Id;
                break;
            case "action":
                if (is_null($telegram->Id)) {
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
					name="Deliverytime" value="<?php echo $telegram->Deliverytime; ?>" min="<?php echo $current_larp->StartTimeLARPTime;?>"
					max="<?php echo $current_larp->EndTimeLARPTime;?>" required></td>
			</tr>
			<tr>
				<td><label for="Sender">Avsändare</label></td>
				<td><input type="text" id="Sender" name="Sender" value="<?php echo $telegram->Sender; ?>" required></td>
			</tr>
			<tr>

				<td><label for="SenderCity">Avsändarens stad</label></td>
				<td><input type="text" id="SenderCity" name="SenderCity"
					 value="<?php echo $telegram->SenderCity; ?>" required></td>
			</tr>
			<tr>

				<td><label for="Reciever">Mottagare</label></td>
				<td><input type="text" id="Reciever" name="Reciever" value="<?php echo $telegram->Reciever; ?>" required></td>
			</tr>
			<tr>

				<td><label for="RecieverCity">Mottagarens stad</label></td>
				<td><input type="text" id="RecieverCity" name="RecieverCity"
					 value="<?php echo $telegram->RecieverCity; ?>" required></td>
			</tr>
			<tr>

				<td><label for="Message">Meddelande</label></td>
				<td><textarea id="Message" name="Message" rows="4" cols="50"
					 required><?php echo $telegram->Message; ?></textarea></td>
			</tr>
			<tr>

				<td><label for="OrganizerNotes">Anteckningar om telegrammet</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4"
						cols="50"><?php echo $telegram->OrganizerNotes; ?></textarea></td>

			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
    </body>

</html>