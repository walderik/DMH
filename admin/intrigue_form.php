<?php
include_once 'header.php';



$intrigue = Intrigue::newWithDefault();;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $intrigue = Intrigue::loadById($_GET['id']);
    } else {
    }
}

function default_value($field) {
    GLOBAL $intrigue;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($intrigue->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "id":
            $output = $intrigue->Id;
            break;
        case "action":
            if (is_null($intrigue->Id)) {
                $output = "Skapa";
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

include 'navigation.php';
?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> intrigspår <a href="<?php echo $referer; ?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="logic/view_intrigue_logic.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<table>
			<tr>
				<td><label for="Recipient">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($intrigue->Name); ?>" size="50" maxlength="250" required></td>
			</tr>
			<tr>
				<td><label for="Active">Aktuell</label><br>(Kommer att visas för deltagarna när intrigerna släpps.<br>Intrigspår går inte att radera, bara att sättas till inte aktuell.)</td>
				<td>
					<input type="radio" id="Active_yes" name="Active" value="1" <?php if ($intrigue->Active == 1) echo 'checked="checked"'?>> 
        			<label for="Active_yes">Ja</label><br> 
        			<input type="radio" id="Active_no" name="Active" value="0" <?php if ($intrigue->Active == 0) echo 'checked="checked"'?>> 
        			<label for="Active_no">Nej</label>
    			</td>
				
			</tr>
			<tr>

				<td><label for="Greeting">Huvudintrig</label></td>
				<td>
					<input type="radio" id="MainIntrigue_yes" name="MainIntrigue" value="1" <?php if ($intrigue->MainIntrigue == 1) echo 'checked="checked"'?>> 
        			<label for="MainIntrigue_yes">Ja</label><br> 
        			<input type="radio" id="MainIntrigue_no" name="MainIntrigue" value="0" <?php if ($intrigue->MainIntrigue == 0) echo 'checked="checked"'?>> 
        			<label for="MainIntrigue_no">Nej</label>
				</td>
			</tr>
			<tr>

				<td><label for="Message">Intrigtyper</label></td>
			<td><?php selectionByArray('IntrigueType' , IntrigueType::allActive($current_larp), true, false, $intrigue->getSelectedIntrigueTypeIds());?></td>
			</tr>
			<tr>

				<td><label for="CommonText">Text som visas för alla aktörer i intrigen</label></td>
			<td><textarea id="CommonText" name="CommonText" rows="15" cols="100" maxlength="60000" ><?php echo htmlspecialchars($intrigue->CommonText); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="Notes">Anteckningar</label></td>
			<td><textarea id="Notes" name="Notes" rows="15" cols="100" maxlength="60000" ><?php echo htmlspecialchars($intrigue->Notes); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="Notes">Ansvarig arrangör</label></td>
			<td><?php 
			if (empty($intrigue->ResponsiblePersonId)) $intrigue->ResponsiblePersonId = $current_user->getOrganizer($current_larp)->Id;
			$organizers = Person::getAllWithAccessToLarp($current_larp);
			selectionDropDownByArray('ResponsiblePerson', $organizers, true, $intrigue->ResponsiblePersonId) ?></td>
			</tr>
		</table>


				

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	
	</div>
    </body>

</html>