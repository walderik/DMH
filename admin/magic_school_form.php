<?php
include_once 'header.php';


$school = Magic_School::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $school = Magic_School::loadById($_GET['Id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $school;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($school->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $school->Id;
                break;
            case "action":
                if (is_null($school->Id)) {
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
    $referer = (isset($referer)) ? $referer : '../magic_school_admin.php';
    
    
    include 'navigation.php';
    include 'magic_navigation.php';
    
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> magiskola <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="logic/view_magicschool_logic.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<input type="hidden" id="CampaignId" name="CampaignId" value="<?php echo $school->CampaignId;?>">
		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($school->Name); ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>
				<td><label for="Description">Beskrivning om magiskolan</label><br>(Visas för deltagare som har skolan)</td>
				<td><textarea id="Description" name="Description" rows="4" maxlength="60000"
						cols="100"><?php echo htmlspecialchars($school->Description); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om magiskolan</label><br>(Visas bara för arrangörer)</td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="100"><?php echo nl2br(htmlspecialchars($school->OrganizerNotes)); ?></textarea></td>
			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>