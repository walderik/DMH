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
    
    include 'navigation_subpage.php';
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> telegram</h1>
	<form action="telegram_admin.php" method="post">
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
				<td><input type="text" id="Sender" name="Sender" value="<?php echo $telegram->Sender; ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="SenderCity">Avsändarens stad</label></td>
				<td><input type="text" id="SenderCity" name="SenderCity"
					 value="<?php echo $telegram->SenderCity; ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="Reciever">Mottagare</label></td>
				<td><input type="text" id="Reciever" name="Reciever" value="<?php echo $telegram->Reciever; ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="RecieverCity">Mottagarens stad</label></td>
				<td><input type="text" id="RecieverCity" name="RecieverCity"
					 value="<?php echo $telegram->RecieverCity; ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="Message">Meddelande</label></td>
				<td><textarea id="Message" name="Message" rows="4" cols="50" maxlength="60000"
					 required><?php echo $telegram->Message; ?></textarea></td>
			</tr>
			<tr>

				<td><label for="Approved">Godkänt</label></td>
				<td>
				<input type="radio" id="Approved_yes" name="Approved" value="1" <?php if ($telegram->Approved == 1) echo 'checked="checked"'?>> 
    			<label for="Approved_yes">Ja</label><br> 
    			<input type="radio" id="Approved_no" name="Approved" value="0" <?php if ($telegram->Approved == 0) echo 'checked="checked"'?>> 
    			<label for="Approved_no">Nej</label>
				</td>
			</tr>
			<tr>

				<td><label for="OrganizerNotes">Anteckningar om telegrammet</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="50"><?php echo $telegram->OrganizerNotes; ?></textarea></td>

			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>