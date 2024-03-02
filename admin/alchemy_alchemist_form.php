<?php
include_once 'header.php';


$alchemist = Alchemy_Alchemist::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $alchemist = Alchemy_Alchemist::loadById($_GET['Id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $alchemist;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($alchemist->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $alchemist->Id;
                break;
            case "action":
                if (is_null($alchemist->Id)) {
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
    $referer = (isset($referer)) ? $referer : '../magic_magician_admin.php';
    

    $role = $alchemist->getRole();
    include 'navigation.php';
    
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> alkemist <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="logic/view_alchemist_logic.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><?php if (isset($role)) echo htmlspecialchars($role->Name); ?></td>
			</tr>
			<tr>
				<td><label for="Description">Typ av alkemist</label></td>
				<td>
				<?php selectionDropDownBySimpleArray('AlchemistType', Alchemy_Alchemist::ALCHEMY_TYPES, $alchemist->AlchemistType); ?>
				</td>
			</tr>
			<tr>
				<td><label for="Level">Nivå</label></td>
				<td><input type="number" id="Level" name="Level" value="<?php echo htmlspecialchars($alchemist->Level); ?>" maxlength="5" min="0" required></td>
			</tr>
			<tr>
				<td>Utrustning</td>
				<td>
					<?php 
					if ($alchemist->hasEquipmentImage()) {
					    $image = Image::loadById($alchemist->ImageId);

					        echo "<img width='300' src='../includes/display_image.php?id=$alchemist->ImageId'/>\n";
					        if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";

					    } else {
					        echo "<a href='upload_image.php?id=$alchemist->Id&type=alchemist'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
					    }
					    ?>
					
				</td>
			</tr>
			<tr>
				<td><label for="Workshop">Workshop datum</label></td>
				<td>
					<input type="date" id="Workshop" name="Workshop" value="<?php echo $alchemist->Workshop; ?>"  size="15" maxlength="250">
				</td>
			</tr>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om alkemisten</label><br>(Visas bara för arrangörer)</td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="100"><?php echo nl2br(htmlspecialchars($alchemist->OrganizerNotes)); ?></textarea></td>
			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>