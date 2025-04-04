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

if (is_null($person) || $person->Id != $current_person->Id) {
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

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-wand-sparkles"></i>
			<?php echo "Magiker $role->Name";?>
			<a href='magic_magician_sheet.php?id=<?php echo $role->Id ?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Magikerblad för <?php $role->Name?>'></i></a>&nbsp;
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Nivå</div>
		<?php echo $magician->Level; ?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Magiskola</div>
		<?php if (!empty($school)) {
		    echo $school->Name; 
		    echo "<br><br>";
		    echo nl2br(htmlspecialchars($school->Description));
		}?>
		</div>
		

		<?php if (isset($master)) {?>
	   		<div class='itemcontainer'>
           	<div class='itemname'>Mästare</div>
			<?php echo $masterRole->Name; ?>
			</div>
		<?php }?>
		
		<?php 
		$apprenticeNames = $magician->getApprenticeNames();
		if (!empty($apprenticeNames)) {?>
	   		<div class='itemcontainer'>
           	<div class='itemname'>Lärlingar</div>
			<?php echo implode(", ", $apprenticeNames); ?>
			</div>
		<?php }?>
			
    			
   		<div class='itemcontainer'>
       	<div class='itemname'>Magifokus</div>
		<?php 
			if ($magician->hasStaffImage()) {
			    $image = Image::loadById($magician->ImageId);

		        echo "<img width='300' src='../includes/display_image.php?id=$magician->ImageId'/>\n";
		        if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";

			} else {
			    echo "<a href='upload_image.php?id=$magician->Id&type=magician'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
			}
		    
	    ?>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Magifokus godkänt datum</div>
		<?php 
		    if ($magician->isStaffApproved()) echo $magician->StaffApproved; 
		    else echo showParticipantStatusIcon(false, "Magifokus är inte godkänt.");
	    ?>			
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Workshop datum</div>
		<?php 
		    if ($magician->hasDoneWorkshop()) echo $magician->Workshop; 
		    else echo showParticipantStatusIcon(false, "Du har inte deltagit i workshop om magi.");
	    ?>			
		</div>
	</div>
	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-scroll"></i> Magier
			<a href='magic_magician_sheet.php?id=<?php echo $role->Id ?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Magikerblad för <?php $role->Name?>'></i></a>&nbsp;
		</div>

		<div  style='display:table'>
			

			<?php 
			$spells = $magician->getSpells();
			if (empty($spells)) {
			    echo "Inga magier, än.";
			} else {
				echo "<table class='participant_table' style='width:93%;padding: 6px; margin: 16px 16px 0px;'>";
				echo "<tr><th>Magi</th><th>Beskrivning</th></tr>";
				foreach ($spells as $spell) {
				    echo "<tr><td>$spell->Name<br>Nivå $spell->Level<br>(".Magic_Spell::TYPES[$spell->Type].")</td><td>$spell->Description</td>";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
		</div>

		</div>
		


</body>
</html>
