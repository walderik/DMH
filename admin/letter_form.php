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
    } else {
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
    <h1><?php echo default_value('action');?> brev</h1>
	<form action="letter_admin.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<table>
			<tr>
				<td><label for="Recipient">Mottagare</label></td>
				<td><input type="text" id="Recipient" name="Recipient" value="<?php echo htmlspecialchars($letter->Recipient); ?>" size="50" maxlength="50" required></td>
			</tr>
			<tr>
				<td><label for="WhenWhere">Ort och datum</label></td>
				<td><input type="text" id="WhenWhere" name="WhenWhere" value="<?php echo htmlspecialchars($letter->WhenWhere); ?>" size="50" maxlength="50" required></td>
			</tr>
			<tr>

				<td><label for="Greeting">Hälsningsfras</label></td>
				<td><input type="text" id="Greeting" name="Greeting" value="<?php echo htmlspecialchars($letter->Greeting); ?>" size="50" maxlength="50" required></td>
			</tr>
			<tr>

				<td><label for="Message">Meddelande</label></td>
				<td><textarea id="Message" name="Message" rows="4" cols="50" maxlength="2000"
					 required><?php echo htmlspecialchars($letter->Message); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="EndingPhrase">Hälsning</label></td>
				<td><input type="text" id="EndingPhrase" name="EndingPhrase"
					 value="<?php echo htmlspecialchars($letter->EndingPhrase); ?>" size="50" maxlength="50" required></td>
			</tr>
			<tr>

				<td><label for="Signature">Underskrift</label></td>
				<td><input type="text" id="Signature" name="Signature"
					 value="<?php echo htmlspecialchars($letter->Signature); ?>" size="50" maxlength="50" required></td>
			</tr>
			<tr>

				<td>
					<label for="Font">Font</label><br>
					<a href="../includes/font_test.php" target="_blank">Se alla fonterna</a>
				</td>
				<td>
					<?php fontDropDown("Font", $letter->Font); ?><br>
			

				</td>
			</tr>
			<tr>

				<td><label for="Approved">Godkänt</label></td>
				<td>
				<input type="radio" id="Approved_yes" name="Approved" value="1" <?php if ($letter->Approved == 1) echo 'checked="checked"'?>> 
    			<label for="Approved_yes">Ja</label><br> 
    			<input type="radio" id="Approved_no" name="Approved" value="0" <?php if ($letter->Approved == 0) echo 'checked="checked"'?>> 
    			<label for="Approved_no">Nej</label>
				</td>
			</tr>
			<tr>

				<td><label for="OrganizerNotes">Anteckningar om brevet</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="50"><?php echo htmlspecialchars($letter->OrganizerNotes); ?></textarea></td>

			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>