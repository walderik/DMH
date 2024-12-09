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

			<i class="fa-solid fa-comments"></i>
			<?php echo default_value('action');?> rykte
		</div>

   		<div class='itemcontainer'>
    		Ryktet kommer att granskas av arrangörerna innan det godkäns för lajvet.<br />
    		När ryktet är godkänt går det inte längre att ändra.
		</div>

	<form id='main' action="logic/rumour_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Text</div>
		<textarea form='main' id="Text" name="Text" rows="4" style='width:100%;' maxlength="2000"
					 required><?php echo htmlspecialchars($rumour->Text); ?></textarea>		
		</div>
		
  		<div class='itemcontainer'>
       	<div class='itemname'>Anteckningar<br>(Beskriv vad som är sant<br>och inte i ryktet.)</div>
		<textarea form='main' id="Notes" name="Notes" rows="4" style='width:100%;' maxlength="2000"
					 ><?php echo htmlspecialchars($rumour->Notes); ?></textarea>		
		</div>

		<div class='center'><input id="submit_button" type="submit" class='button-18' value="<?php default_value('action'); ?>"></div>
	</form>
	</div>
    </body>

</html>