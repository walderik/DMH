<?php
include_once 'header.php';



$timeline = Timeline::newWithDefault();;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $timeline = Timeline::loadById($_GET['id']);
    } else {
    }
}

function default_value($field) {
    GLOBAL $timeline;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($timeline->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "id":
            $output = $timeline->Id;
            break;
        case "action":
            if (is_null($timeline->Id)) {
                $output = "Lägg till";
                break;
            }
            $output = "Uppdatera";
            break;
    }
    
    echo $output;
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}
$referer = (isset($referer)) ? $referer : '../timeline_admin.php';

$intrigue_array = Intrigue::allByLARP($current_larp);


include 'navigation.php';
?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> händelse <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="timeline_admin.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
    			<tr>
    				<td><label for="When">När</label></td>
    				<td><input type="datetime-local" id="When"
    					name="When" value="<?php echo $timeline->When; ?>" size="50" 
    					min="<?php echo $current_larp->StartDate;?>"
						max="<?php echo $current_larp->EndDate;?>" 
    					required></td>
    			</tr>
			<tr>
				<td><label for="Description">Beskrivning</label></td>
				<td><input type="text" id="Description" name="Description" value="<?php echo htmlspecialchars($timeline->Description); ?>" size="50" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="IntrigueId">Kopplad intrig</label></td>
				
				<td><?php  selectionDropDownByArray("IntrigueId", $intrigue_array, false, $timeline->IntrigueId);?></td>
			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>