<?php
include_once 'header.php';


$magician = Magic_Magician::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $magician = Magic_Magician::loadById($_GET['Id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $magician;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($magician->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $magician->Id;
                break;
            case "action":
                if (is_null($magician->Id)) {
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
    

    $role = $magician->getRole();
    $master = $magician->getMaster();
    if (isset($master)) $masterRole = $master->getRole();
    $schools = Magic_School::allByCampaign($current_larp);
    include 'navigation.php';
    include 'magic_navigation.php';
    
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> magiker <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="logic/view_magician_logic.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><?php if (isset($role)) echo htmlspecialchars($role->Name); ?></td>
			</tr>
			<tr>
				<td><label for="Description">Magiskolan</label></td>
				<td><?php 
				if (!empty($schools)) selectionDropDownByArray("MagicSchoolId", $schools, false, $magician->MagicSchoolId)?></td>
			</tr>
			<tr>
				<td><label for="Level">Nivå</label></td>
				<td><input type="number" id="Level" name="Level" value="<?php echo htmlspecialchars($magician->Level); ?>" maxlength="5" min="0" required></td>
			</tr>
			<tr>
				<td><label for="Level">Mästare</label></td>
				<td>
					<?php if (isset($masterRole)) {
					    echo "<a href ='view_magician.php?id=$master->Id'>$masterRole->Name</a>"; 
					    echo "<br>";
					    echo "<a href='choose_magician.php?id=$magician->Id&operation=set_master'>Byt mästare</a>";
					} else {
					    echo "<a href='choose_magician.php?id=$magician->Id&operation=set_master'>Sätt mästare</a>";
					    
					}
					
					
					?><br>
					 
				</td>
			</tr>
			<tr>
				<td>Magifokus</td>
				<td>
					<?php 
					if ($magician->hasStaffImage()) {
					    $image = Image::loadById($magician->ImageId);

					        echo "<img width='300' src='../includes/display_image.php?id=$magician->ImageId'/>\n";
					        if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";

					    } else {
					        echo "<a href='upload_image.php?id=$magician->Id&type=magician'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
					    }
					    ?>
					
				</td>
			</tr>
			<tr>
				<td><label for="StaffApproved">Magifokus godkänt datum</label></td>
				<td>
					<input type="date" id="StaffApproved" name="StaffApproved" value="<?php echo $magician->StaffApproved; ?>"  size="15" maxlength="250">
				</td>
			</tr>
			<tr>
				<td><label for="Workshop">Workshop datum</label></td>
				<td>
					<input type="date" id="Workshop" name="Workshop" value="<?php echo $magician->Workshop; ?>"  size="15" maxlength="250">
				</td>
			</tr>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om magikern</label><br>(Visas bara för arrangörer)</td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="100"><?php echo nl2br(htmlspecialchars($magician->OrganizerNotes)); ?></textarea></td>
			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>