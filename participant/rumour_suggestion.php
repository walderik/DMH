<?php
include_once 'header.php';

$rumour = Rumour::newWithDefault();;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $rumour = Rumour::loadById($_GET['id']);
        if ($rumour->UserId != $current_user->Id) {
            header('Location: index.php'); //Inte ditt rykte
            exit;
        } elseif ($rumour->isApproved()) {
            header('Location: index.php'); //Ryktet är godkänt
            exit;
        }
        
    } else {
        header('Location: index.php');
        exit;
    }
}

function default_value($field) {
    GLOBAL $rumour;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($rumour->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "id":
            $output = $rumour->Id;
            break;
        case "action":
            if (is_null($rumour->Id)) {
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
    <h1><?php echo default_value('action');?> rykte</h1>
    <p>Ryktet kommer att granskas av arrangörerna innan det godkäns för lajvet.<br />
    När ryktet är godkänt går det inte längre att ändra.</p>
	<form id='main' action="logic/rumour_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<table>
			<tr>

				<td><label for="Text">Text</label></td>
				<td><textarea form='main' id="Text" name="Text" rows="4" cols="100" maxlength="2000"
					 required><?php echo htmlspecialchars($rumour->Text); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="Notes">Anteckningar<br>(Beskriv vad som är sant<br>och inte i ryktet.)</label></td>
				<td><textarea form='main' id="Notes" name="Notes" rows="4" cols="100" maxlength="2000"
					 required><?php echo htmlspecialchars($rumour->Notes); ?></textarea></td>
			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>