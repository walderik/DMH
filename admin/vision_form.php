<?php
include_once 'header.php';

$vision = Vision::newWithDefault();;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "insert";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'insert') {
        //if (isset($_GET['IntrigueId'])) $vision->IntrigueId = $_GET['IntrigueId'];
    } elseif ($operation == 'update') {
        $vision = Vision::loadById($_GET['id']);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = "insert";
    $referer = $_POST['Referer'];
    if (isset($_POST['operation'])) $operation = $_POST['operation'];
    $vision = Vision::loadById($_POST['id']);
    
    if ($operation == 'delete_has') {
        $vision->removeRoleHas($_POST['hasId']);
        $operation = 'update';
    } elseif ($operation == 'add_has_role') {
        if (isset($_POST['RoleId'])) $vision->addRolesHas($_POST['RoleId']);
        $operation = 'update';
    }
    
}



function default_value($field) {
    GLOBAL $vision;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($vision->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "id":
            $output = $vision->Id;
            break;
        case "action":
            if (is_null($vision->Id)) {
                $output = "Lägg till";
                break;
            }
            $output = "Uppdatera";
            break;
    }
    
    echo $output;
}

if (isset($_POST['2ndReferer'])) {
    $referer = $_POST['2ndReferer'];
} elseif (isset($_POST['Referer'])) {
    $referer = $_POST['Referer'];
} elseif (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
} else {
    $referer = "vision_admin.php";
}

$intrigue_array = Intrigue::allByLARP($current_larp);

include 'navigation.php';
?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> syn <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form id="main" action="vision_admin.php" method="post"></form>
		<input form='main' type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input form='main' type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input form='main' type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr>

				<td><label for="WhenDate">Vilken dag ska synen ske?</label></td>
				<td>
					<select form='main' id="WhenDate" name="WhenDate"> 
            		<?php 
            		
            		$formatter = new IntlDateFormatter(
            		    'sv-SE',
            		    IntlDateFormatter::FULL,
            		    IntlDateFormatter::FULL,
            		    'Europe/Stockholm',
            		    IntlDateFormatter::GREGORIAN,
            		    'EEEE d MMMM'
            		    );
            		
            		$begin = new DateTime(substr($current_larp->StartDate,0,10));
            		$end   = new DateTime(substr($current_larp->EndDate,0,10));
            		
            		for($i = $begin; $i <= $end; $i->modify('+1 day')){
            		    $datestr = $i->format("Y-m-d");
            		    echo "<option value='$datestr'";
            		    if ($vision->WhenDate==$datestr) echo "selected";
            		    echo "> ".$formatter->format($i)."</option><br>";
            		}
            		
            		
            		?>
            		</select>

				 </td>
			</tr>
			<tr>
				<td><label for="WhenSpec">När på dagen ska synen ske?</label></td>
				<td>
				<?php selectionDropDownBySimpleArray('WhenSpec', Vision::TIME_OF_DAY, $vision->WhenSpec, "form='main'"); ?>
				</td>
			</tr>
		
		
		
			<tr>

				<td><label for="VisionText">Syn</label></td>
				<td><textarea form='main' id="VisionText" name="VisionText" rows="4" cols="100" maxlength="2000"
					 required><?php echo htmlspecialchars($vision->VisionText); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="VisionText">Källa</label></td>
				<td><textarea form='main' id="Source" name="Source" rows="4" cols="100" maxlength="2000"
					 ><?php echo htmlspecialchars($vision->Source); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="VisionText">Bieffekt</label></td>
				<td><textarea form='main' id="SideEffect" name="SideEffect" rows="4" cols="100" maxlength="2000"
					 ><?php echo htmlspecialchars($vision->SideEffect); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="VisionText">Arrangörens anteckningar</label><br>(Visas inte för deltagare)</td>
				<td><textarea form='main' id="OrganizerNotes" name="OrganizerNotes" rows="4" cols="100" maxlength="2000"
					 ><?php echo htmlspecialchars($vision->OrganizerNotes); ?></textarea></td>
			</tr>
				<tr>
				<td><label for="Text">Vem/vilka vet<br>får synen?</label></td>
				<td>
					<?php if ($operation=='update') {
					    
					    echo "<form id='has_role' action='choose_role.php' method='post'></form>";
					    echo "<input form='has_role' type='hidden' id='id' name='id' value='$vision->Id'>";
					    echo "<input form='has_role' type='hidden' id='2ndReferer' name='2ndReferer' value='$referer'>";
					    echo "<input form='has_role' type='hidden' id='operation' name='operation' value='add_has_role'>";
					    echo "<button form='has_role' class='invisible' type='submit'><i class='fa-solid fa-plus' title='Lägg till karaktär(er) som känner till ryktet'></i><i class='fa-solid fa-user' title='Lägg till karaktär(er) som känner till ryktet'></i></button>";
					    
					    
					} else {
					  echo "<strong>Efter att synen är skapad, kan du tilldela det.</strong>";
					}?>
					
					<?php 
					$role_has_array = $vision->getHas();
					foreach ($role_has_array as $role) {
					    echo "<form id='delete_has_$role->Id' action='vision_form.php' method='post'>";
					    echo "<a href='view_role.php?id=$role->Id'>$role->Name</a>";
					    echo " ";
					    echo "<input form='delete_has_$role->Id' type='hidden' id='operation' name='operation' value='delete_has'>";
					    echo "<input form='delete_has_$role->Id' type='hidden' id='id' name='id' value='$vision->Id'>";
					    echo "<input form='delete_has_$role->Id' type='hidden' id='Referer' name='Referer' value='$referer'>";
					    echo "<input form='delete_has_$role->Id' type='hidden' id='knowsId' name='hasId' value='$role->Id'>";
					    echo "<button form='delete_has_$role->Id' class='invisible' type='submit'><i class='fa-solid fa-trash' title='Ta bort från rykte'></i></button>";
					    echo "</form>";
					}
					?>
				</td>
			</tr>
 
 
 
		</table>
		<input form='main' id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</div>
    </body>

</html>