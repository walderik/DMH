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
                    $output = "Skapa";
                    break;
                }
                $output = "Uppdatera";
                break;
        }
        
        echo $output;
    }
    
    include 'navigation.php';
    ?>
    
    	<div class='itemselector'>
		<div class="header">

			<i class="fa-brands fa-telegram"></i>
			<?php echo default_value('action');?> telegram
		</div>

   		<div class='itemcontainer'>
    		Telegrammet kommer att granskas av arrangörerna innan det godkäns för lajvet.
		</div>
	<form action="logic/telegram_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Leveranstid</div>
		<input type="datetime-local" id="Deliverytime"
			name="Deliverytime" value="<?php echo formatDateTimeForInput($telegram->Deliverytime); ?>" min="<?php echo formatDateTimeForInput($current_larp->StartTimeLARPTime);?>"
			max="<?php echo formatDateTimeForInput($current_larp->EndTimeLARPTime);?>" size="50" required>		
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Avsändare</div>
		<input type="text" id="Sender" name="Sender" value="<?php echo htmlspecialchars($telegram->Sender); ?>" size="50" maxlength="50" required>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Avsändarens stad</div>
		<input type="text" id="SenderCity" name="SenderCity"
					 value="<?php echo htmlspecialchars($telegram->SenderCity); ?>" size="50" maxlength="50" required>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Mottagare</div>
		<input type="text" id="Reciever" name="Reciever" value="<?php echo htmlspecialchars($telegram->Reciever); ?>" size="50" maxlength="50" required>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Mottagarens stad</div>
		<input type="text" id="RecieverCity" name="RecieverCity"
					 value="<?php echo htmlspecialchars($telegram->RecieverCity); ?>" size="50" maxlength="50" required>
 		</div>


   		<div class='itemcontainer'>
       	<div class='itemname'>Meddelande</div>
		Tänk på att telegram var <strong>dyrt</strong>. Håll det kort och använd alla förkortningar du kan komma på.<br>
		<textarea id="Message" name="Message" rows="4" style='width:100%;' maxlength="500"
					 required><?php echo htmlspecialchars($telegram->Message); ?></textarea> 		
		 </div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Anteckningar om telegrammet</div>
		<textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						style='width:100%;'><?php echo htmlspecialchars($telegram->OrganizerNotes); ?></textarea>
		 </div>


		<div class='center'><input id="submit_button" type="submit" class='button-18' value="<?php default_value('action'); ?>"></div>
	</form>
	</div>
    </body>

</html>