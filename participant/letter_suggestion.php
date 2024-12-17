<?php
include_once 'header.php';

$letter = Letter::newWithDefault();;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $letter = Letter::loadById($_GET['id']);
        if ($letter->PersonId != $current_person->Id) {
            header('Location: index.php'); //Inte ditt brev
            exit;
        }
        
    } else {
        header('Location: index.php');
        exit;
    }
}

function default_value($field) {
    GLOBAL $letter;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($letter->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "id":
            $output = $letter->Id;
            break;
        case "action":
            if (is_null($letter->Id)) {
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

		<i class="fa-solid fa-envelope"></i>
		<?php echo default_value('action');?> brev
	</div>

	<div class='itemcontainer'>
		Brevet kommer att granskas av arrangörerna innan det godkäns för lajvet.<br />
		När brevet är godkänt går det inte längre att ändra.
	</div>
    
	<form action="logic/letter_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Mottagare</div>
		<input type="text" id="Recipient" name="Recipient" value="<?php echo htmlspecialchars($letter->Recipient); ?>" size="50" maxlength="50" required>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Ort och datum</div>
		<input type="text" id="WhenWhere" name="WhenWhere" value="<?php echo htmlspecialchars($letter->WhenWhere); ?>" size="50" maxlength="50" required>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Inledande hälsningsfras</div>
		<input type="text" id="Greeting" name="Greeting" value="<?php echo htmlspecialchars($letter->Greeting); ?>" size="50" maxlength="50" required>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Meddelande</div>
		<textarea id="Message" name="Message" rows="4" maxlength="2000"
					 required><?php echo htmlspecialchars($letter->Message); ?></textarea> 		
		 </div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Avslutande hälsning</div>
		<input type="text" id="EndingPhrase" name="EndingPhrase"
					 value="<?php echo htmlspecialchars($letter->EndingPhrase); ?>" size="50" maxlength="50" required>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Underskrift</div>
		<input type="text" id="Signature" name="Signature"
					 value="<?php echo htmlspecialchars($letter->Signature); ?>" size="50" maxlength="50" required>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Typsnitt</div>
       	<a href="../includes/font_test.php" target="_blank">Se alla fonterna</a><br>
		<?php fontDropDown("Font", $letter->Font); ?>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Anteckningar om brevet</div>
		<textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"><?php echo htmlspecialchars($letter->OrganizerNotes); ?></textarea>
		 </div>


		<div class='center'><input id="submit_button" type="submit" class='button-18' value="<?php default_value('action'); ?>"></div>
	</form>
	</div>
    </body>

</html>