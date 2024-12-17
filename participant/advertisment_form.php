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
        if ($advertisment->PersonId != $current_person->Id) {
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
    
	<div class='itemselector'>
	<div class="header">
		<i class="fa-solid fa-bullhorn"></i>
		<?php echo default_value('action');?> annons
	</div>
	
	<div class='itemcontainer'>
	Annonsen kommer att kunna ses av alla deltagare som har en plats på lajvet.<br />
    Tänk på att ta bort den när den inte längre är aktuell.
	</div>
     

	<form action="logic/advertisment_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">

   		<div class='itemcontainer'>
       	<div class='itemname'>Typ av annons</div>
		<?php AdvertismentType::selectionDropdown($current_larp, false, true, $advertisment->AdvertismentTypeId); ?>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Kontaktinformation</div>
       	Namn, telefonnummer och epostadress är bra att ha här.<br>
		<input type="text" id="ContactInformation" name="ContactInformation" value="<?php echo htmlspecialchars($advertisment->ContactInformation); ?>" size="50" maxlength="200" required>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Text</div>
		<textarea id="Text" name="Text" rows="4" maxlength="2000"
					 required><?php echo htmlspecialchars($advertisment->Text); ?></textarea>
		</div>
		
		<div class='center'><input id="submit_button" type="submit" class='button-18' value="<?php default_value('action'); ?>"></div>

	</form>
	</div>
    </body>

</html>