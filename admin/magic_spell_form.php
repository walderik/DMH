<?php
include_once 'header.php';


$spell = Magic_Spell::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $spell = Magic_Spell::loadById($_GET['Id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $spell;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($spell->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $spell->Id;
                break;
            case "action":
                if (is_null($spell->Id)) {
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
    $referer = (isset($referer)) ? $referer : '../magic_spell_admin.php';
    
    
    include 'navigation.php';
    
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> magi <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="logic/magic_spell_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<input type="hidden" id="CampaignId" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($spell->Name); ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>
				<td><label for="Description">Beskrivning av magin</label><br>(Visas för deltagare som har magin)</td>
				<td><textarea id="Description" name="Description" rows="4" maxlength="60000"
						cols="100"><?php echo htmlspecialchars($spell->Description); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="Level">Nivå</label><br></td>
				<td><input type="number" id="Level" name="Level" value="<?php echo htmlspecialchars($spell->Level); ?>" maxlength="5" min="0" required></td>
			</tr>

			<tr>
				<td><label for="Type">Typ</label><br></td>
				<td>                
					<?php selectionDropDownBySimpleArray('Type', Magic_Spell::TYPES, $spell->Type); ?>
				</td>
			</tr>
			<tr>
				<td><label for="Special">Speciella krav</label><br></td>
				<td><input type="text" id="Special" name="Special" value="<?php echo htmlspecialchars($spell->Special); ?>" size="100" maxlength="250"></td>
			</tr>

			<tr>
				<td><label for="OrganizerNotes">Anteckningar om magin</label><br>(Visas bara för arrangörer)</td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="100"><?php echo htmlspecialchars($spell->OrganizerNotes); ?></textarea></td>
			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>