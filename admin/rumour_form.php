<?php
include_once 'header.php';



$rumour = Rumour::newWithDefault();;
$rumour->Approved = 1;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $rumour = Rumour::loadById($_GET['id']);
    } else {
    }
    if (isset($_GET['IntrigueId'])) {
        $rumour->IntrigueId = $_GET['IntrigueId'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $operation = "new";
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];
    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $rumour = Rumour::loadById($_POST['id']);
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

if (isset($_POST['Referer'])) {
    $referer = $_POST['Referer'];
} elseif (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
} else {
    $referer = "rumour_admin.php";
}

$intrigue_array = Intrigue::allByLARP($current_larp);

include 'navigation.php';
?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> rykte <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form id="main" action="rumour_admin.php" method="post"></form>
		<input form='main' type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input form='main' type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input form='main' type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr>

				<td><label for="Text">Text</label></td>
				<td><textarea form='main' id="Text" name="Text" rows="4" cols="100" maxlength="2000"
					 required><?php echo htmlspecialchars($rumour->Text); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="Approved">Godkänt</label></td>
				<td>
				<input form='main' type="radio" id="Approved_yes" name="Approved" value="1" <?php if ($rumour->Approved == 1) echo 'checked="checked"'?>> 
    			<label for="Approved_yes">Ja</label><br> 
    			<input form='main' type="radio" id="Approved_no" name="Approved" value="0" <?php if ($rumour->Approved == 0) echo 'checked="checked"'?>> 
    			<label for="Approved_no">Nej</label>
				</td>
			</tr>
			<tr>

				<td><label for="IntrigueId">Kopplad intrig</label></td>
				
				<td><?php  selectionDropDownByArray("IntrigueId", $intrigue_array, false, $rumour->IntrigueId);?></td>
			</tr>
			<tr>

				<td><label for="Text">Vem/vilka handlar<br>ryktet om?</label></td>
				<td>
					
					<?php if ($operation=='update') {
					    echo "<form id='concerns_$rumour->Id' action='rumour_actor_form.php' method='post'></form>";
					    
					    echo "<input form='concerns_$rumour->Id' type='hidden' id='id' name='id' value='$rumour->Id'>";
					    echo "<input form='concerns_$rumour->Id' type='hidden' id='Referer' name='Referer' value='$referer'>";
					    echo "<input form='concerns_$rumour->Id' type='hidden' id='type' name='type' value='concerns'>";
					    echo "<button form='concerns_$rumour->Id' class='invisible' type='submit'><i class='fa-solid fa-pen' title='Ändra vilka ryktet händlar om'></i></button>";
					    echo "<br>";
					    
					} else {
					  echo "<strong>När ryktet är skapat, lägga in vilka det handlar om.</strong>";
					}?>
					
					<?php 
					$concerns_array = $rumour->getConcerns();
					foreach ($concerns_array as $concern) {
					    echo $concern->getViewLink();
					    echo "<br>";
					}
					?>
				</td>
				</tr>
				<tr>
				<td><label for="Text">Vem/vilka vet<br>om ryktet?</label></td>
				<td>
					
					<?php if ($operation=='update') {
					    echo "<form id='knows_$rumour->Id' action='rumour_actor_form.php' method='post'>";
					    
					    echo "<input form='knows_$rumour->Id' type='hidden' id='id' name='id' value='$rumour->Id'>";
					    echo "<input form='knows_$rumour->Id' type='hidden' id='Referer' name='Referer' value='$referer'>";
					    echo "<input form='knows_$rumour->Id' type='hidden' id='type' name='type' value='knows'>";
					    echo "<button form='knows_$rumour->Id' class='invisible' type='submit'><i class='fa-solid fa-pen' title='Ändra vilka som ska veta om ryktet'></i></button>";
					    echo "</form>";

					    
					} else {
					  echo "<strong>När ryktet är skapat, kan du sprida det.</strong>";
					}?>
					
					<?php 
					$knows_array = $rumour->getKnows();
					foreach ($knows_array as $knows) {
					    echo $knows->getViewLink();
					    echo "<br>";
					}
					?>
				</td>
			</tr>
 
 
 
		</table>
		<input form='main' id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</div>
    </body>

</html>