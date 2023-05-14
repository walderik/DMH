<?php
include_once 'header.php';

    $telegram = Telegram::newWithDefault();;

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $telegram = Telegram::loadById($_GET['id']);
            if ($telegram->UserId != $current_user->Id) {
                header('Location: index.php'); //Inte ditt telegram
                exit;
            }
            
        } else {

            header('Location: index.php'); //Inte ditt telegram
            exit;

            
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
    
    include 'navigation.php';
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> telegram</h1>
    <p>Telegrammet kommer att granskas av arrangörerna innan det godkäns för lajvet.<br /></p>
	<form action="logic/telegram_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<table>
			<tr>
				<td><label for="Deliverytime">Leveranstid</label></td>
				<td><input type="datetime-local" id="Deliverytime"
					name="Deliverytime" value="<?php echo $telegram->Deliverytime; ?>" min="<?php echo $current_larp->StartTimeLARPTime;?>"
					max="<?php echo $current_larp->EndTimeLARPTime;?>" size="50" required></td>
			</tr>
			<tr>
				<td><label for="Sender">Avsändare</label></td>
				<td><input type="text" id="Sender" name="Sender" value="<?php echo htmlspecialchars($telegram->Sender); ?>" size="50" maxlength="50" required></td>
			</tr>
			<tr>

				<td><label for="SenderCity">Avsändarens stad</label></td>
				<td><input type="text" id="SenderCity" name="SenderCity"
					 value="<?php echo htmlspecialchars($telegram->SenderCity); ?>" size="50" maxlength="50" required></td>
			</tr>
			<tr>

				<td><label for="Reciever">Mottagare</label></td>
				<td><input type="text" id="Reciever" name="Reciever" value="<?php echo htmlspecialchars($telegram->Reciever); ?>" size="50" maxlength="50" required></td>
			</tr>
			<tr>

				<td><label for="RecieverCity">Mottagarens stad</label></td>
				<td><input type="text" id="RecieverCity" name="RecieverCity"
					 value="<?php echo htmlspecialchars($telegram->RecieverCity); ?>" size="50" maxlength="50" required></td>
			</tr>
			<tr>

				<td><label for="Message">Meddelande</label></td>
				<td>Tänk på att telegram var <strong>dyrt</strong> håll det kort och använd alla förkortningar du kan komma på.<br>
				<textarea id="Message" name="Message" rows="4" cols="50" maxlength="500"
					 required><?php echo htmlspecialchars($telegram->Message); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="OrganizerNotes">Anteckningar om telegrammet</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="50"><?php echo htmlspecialchars($telegram->OrganizerNotes); ?></textarea></td>

			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>