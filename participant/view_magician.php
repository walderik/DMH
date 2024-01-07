<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RoleId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$role = Role::loadById($RoleId);
$person = $role->getPerson();

if ($person->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}

$magician = Magic_Magician::getForRole($role);
$master = $magician->getMaster();
if (isset($master)) $masterRole = $master->getRole();
$school = $magician->getMagicSchool();



include 'navigation.php';
?>

	<div class="content">
		<h1><?php echo "Magiker $role->Name";?></h1>
		

		<div>
    		<table>
    			<tr>
    				<td>Nivå 
    				</td>
    				<td>
    					<?php echo $magician->Level; ?>
                    </td>
    			</tr>
    			<tr>
    				<td>Magiskola 
    				</td>
    				<td>
    					<?php if (!empty($school)) {
    					    echo $school->Name; 
    					    echo "<br><br>";
    					    echo nl2br(htmlspecialchars($school->Description));
    					}?>
                    </td>
    			</tr>
    			<tr>
    				<td>Mästare</td>
    				<td><?php echo $masterRole->Name; ?></td>
    			</tr>
				<tr>
    				<td>Stav</td>
    				<td>
    					<?php 
    					echo "<a href='upload_image.php?id=$magician->Id&type=magician'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
    					if ($magician->hasStaffImage()) {
    					    echo "<br>";
    					    $image = Image::loadById($magician->ImageId);
    
					        echo "<img width='300' src='../includes/display_image.php?id=$magician->ImageId'/>\n";
					        if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
    
					    }
					    
    					    ?>
    					
    				</td>
    			</tr>
    			<tr>
    				<td>Stav godkänd datum</td>
    				<td><?php 
    				    echo showStatusIcon($magician->isStaffApproved())." ";
    				    if ($magician->isStaffApproved()) echo $magician->StaffApproved; 
    				    else echo "Staven är inte godkänd.";
    				    ?></td>
    			</tr>
    			<tr>
    				<td>Workshop datum</td>
     				<td><?php 
     				    echo showStatusIcon($magician->hasDoneWorkshop())." ";
     				
    				    if ($magician->hasDoneWorkshop()) echo $magician->Workshop; 
    				    else echo "Du har inte deltagit i workshop om magi.";
    				    ?></td>
    			</tr>
    		</table>

			<h2>Magier</h2>

			<?php 
			$spells = $magician->getSpells();
			if (empty($spells)) {
			    echo "Inga magier, än.";
			} else {
				echo "<table class='small_data'>";
				echo "<tr><th>Magi</th><th>Nivå</th><th>Typ</th><th>Beskrivning</th></tr>";
				foreach ($spells as $spell) {
				    echo "<tr><td>$spell->Name</td><td>$spell->Level</td><td>".Magic_Spell::TYPES[$spell->Type]."</td><td>$spell->Description</td>";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>


		</div>
		


</body>
</html>
