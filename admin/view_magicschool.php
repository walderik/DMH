<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $schoolId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$school = Magic_School::loadById($schoolId);


include 'navigation.php';
?>

	<div class="content">
		<h1><?php echo "Magiskola ".$school->Name;?>&nbsp;

		
		<a href='magic_school_form.php?Id=<?php echo $school->Id;?>&operation=update'>
		<i class='fa-solid fa-pen'></i></a> <a href="magic_schools_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till magiskolor"></i></a> 
		</h1>
		

		<div>
		

    		<table>
    			<tr>
    				<td>Beskrivning 
    				</td>
    				<td>
    					<?php echo nl2br(htmlspecialchars($school->Description)); ?>
                    </td>
    			</tr>
    			<tr>
    				<td>Anteckningar</td>
    				<td><?php echo nl2br(htmlspecialchars($school->OrganizerNotes)); ?></td>
    			</tr>
    			<tr><td></td></tr>
    			<tr>
    				</td>
    			</tr>
    			
    		</table>
			<h2>Magier i skolan</h2>

			<?php 
			$spells = $school->getAllSpells();
			if (empty($spells)) {
			    echo "Inga magier i skolan, än.";
			} else {
				echo "<table class='small_data'>";
				echo "<tr><th>Magi</th><th>Nivå</th><th>Typ</th><th>Beskrivning</th><th></th></tr>";
				foreach ($spells as $spell) {
				    echo "<tr><td><a href='view_magicspell.php?id=$spell->Id'>$spell->Name</td><td>$spell->Level</td><td>".Magic_Spell::TYPES[$spell->Type]."</td><td>$spell->Description</td>";
				    echo "<td><a href='logic/view_magicschool_logic.php?operation=remove_spell&SpellId=$spell->Id&Id=$school->Id'><i class='fa-solid fa-xmark' title='Ta bort magi från magiskolan'></i></a></td>";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			<p>
			<a href='choose_magic_spell.php?id=<?php echo $school->Id ?>&operation=add_school_spell'>Lägg till magier</a>


			<h2>Magiker som har skolan</h2>
			<?php 
			$magicians = $school->getAllMagicians();

			if (empty($magicians)) {
			    echo "Inga magiker har skolan, än.";
			} else {
			    echo "Alla magiker som har skolan visas, även de som inte kommer på just det här lajvet.";
				echo "<table class='small_data'>";
				echo "<tr><th>Magiker</th><th>Nivå</th><th>Mästare</th><th>Kommer på lajvet</th></tr>";
				foreach ($magicians as $magician) {
				    $role = $magician->getRole();
				    $master = $magician->getMaster();
				    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
				    $isComing = !empty($larp_role);
				    echo "<tr><td><a href = 'view_magician.php?id=$magician->Id'>$role->Name</td><td>$magician->Level</td>";
				    echo "<td>";
				    if (isset($master)) {
				        $masterRole = $master->getRole(); 
				        echo "<a href = 'view_magician.php?id=$master->Id'>$masterRole->Name (nivå $master->Level)</td>";
				    }
				    echo "</td>";
				    echo "<td>".showStatusIcon($isComing)."</td>";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>



		</div>
		


</body>
</html>
