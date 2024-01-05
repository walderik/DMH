<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $magicianId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$magician = Magic_Magician::loadById($magicianId);
$role = $magician->getRole();
$master = $magician->getMaster();
if (isset($master)) $masterRole = $master->getRole();
$school = $magician->getMagicSchool();



include 'navigation.php';
?>

	<div class="content">
		<h1><?php echo "Magiker ".$role->Name;?>&nbsp;

		
		<a href='magic_magician_form.php?Id=<?php echo $magician->Id;?>&operation=update'>
		<i class='fa-solid fa-pen'></i></a> <a href="magic_magician_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till magiker"></i></a> 
		</h1>
		

		<div>
		

    		<table>
    			<tr>
    				<td>Magiskola 
    				</td>
    				<td>
    					<?php if (!empty($school)) {
    					    echo "<a href='view_magicschool.php?id=$magician->MagicSchoolId'>";
    					    echo $school->Name; 
    					    echo "</a>";
    					}?>
                    </td>
    			</tr>
    			<tr>
    				<td>Nivå 
    				</td>
    				<td>
    					<?php echo nl2br(htmlspecialchars($magician->Level)); ?>
                    </td>
    			</tr>
    			<tr>
    				<td>Mästare</td>
    				<td><?php if (isset($masterRole)) echo "<a href ='view_magician.php?id=$master->Id'>$masterRole->Name</a>"; ?></td>
    			</tr>
				<tr>
    				<td>Stav</td>
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
    				<td>Stav godkänd datum</td>
    				<td><?php echo $magician->StaffApproved; ?></td>
    			</tr>
    			<tr>
    				<td>Workshop datum</td>
    				<td><?php echo $magician->Workshop; ?></td>
    			</tr>
    			
    			
    			
    			<tr>
    				<td>Anteckningar</td>
    				<td><?php echo nl2br(htmlspecialchars($magician->OrganizerNotes)); ?></td>
    			</tr>
    			<tr><td></td></tr>
    		</table>

			<h2>Magier</h2>

			<?php 
			$spells = $magician->getSpells();
			if (empty($spells)) {
			    echo "Inga magier, än.";
			} else {
				echo "<table class='small_data'>";
				echo "<tr><th>Magi</th><th>Nivå</th><th>Typ</th><th>Beskrivning</th><th></th></tr>";
				foreach ($spells as $spell) {
				    echo "<tr><td><a href='view_magicspell.php?id=$spell->Id'>$spell->Name</td><td>$spell->Level</td><td>".Magic_Spell::TYPES[$spell->Type]."</td><td>$spell->Description</td>";
				    echo "<td><a href='logic/view_magician_logic.php?operation=remove_spell&SpellId=$spell->Id&Id=$magician->Id'><i class='fa-solid fa-xmark' title='Ta bort magi från magiskolan'></i></a></td>";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			<p>
			<a href='choose_magic_spell.php?id=<?php echo $magician->Id ?>&operation=add_magician_spell'>Lägg till magier</a>


		</div>
		


</body>
</html>
