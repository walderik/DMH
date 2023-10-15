<?php
include_once 'header.php';

$advertisment = Advertisment::newWithDefault();;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'update') {
        $advertisment = Advertisment::loadById($_GET['id']);
        if ($advertisment->UserId != $current_user->Id) {
            header('Location: index.php'); //Inte din annons
            exit;
        } 
    }
}

function default_value($field) {
    GLOBAL $advertisment;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($advertisment->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "id":
            $output = $advertisment->Id;
            break;
        case "action":
            if (is_null($advertisment->Id)) {
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
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> annons</h1>
    <p>Annonsen kommer att kunna ses av alla deltagare som har en plats på lajvet.<br />
    Tänk ppå att ta bort den när den inte längre är aktuell.</p>
	<form action="logic/advertisment_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<table>
			<tr>
				<td>Typ av annons<br><?php AdvertismentType::helpBox($current_larp); ?></td>
       			<td>
                <?php AdvertismentType::selectionDropdown($current_larp, false, true, $advertisment->AdvertismentTypeId); ?>
                </td>
            </tr>
		
		
		
			<tr>
				<td><label for="ContactInformation">Kontaktinformation</label><br>Skriv ditt namn och något sätt du kan kontaktas</td>
				<td><input type="text" id="ContactInformation" name="ContactInformation" value="<?php echo htmlspecialchars($advertisment->ContactInformation); ?>" size="50" maxlength="200" required></td>
			</tr>
			<tr>
				<td><label for="Text">Text</label></td>
				<td><textarea id="Text" name="Text" rows="4" cols="100" maxlength="2000"
					 required><?php echo htmlspecialchars($advertisment->Text); ?></textarea></td>
			</tr>
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>