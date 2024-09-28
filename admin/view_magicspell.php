<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $spellId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$spell = Magic_Spell::loadById($spellId);


include 'navigation.php';
include 'magic_navigation.php';
?>

	<div class="content">
		<h1><?php echo "Magi ".$spell->Name;?>&nbsp;

		
		<a href='magic_spell_form.php?Id=<?php echo $spell->Id;?>&operation=update'>
		<i class='fa-solid fa-pen'></i></a> <a href="magic_spells_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till magier"></i></a> 
		</h1>
		

		<div>
		

    		<table>
    			<tr>
    				<td>Beskrivning 
    				</td>
    				<td>
    					<?php echo nl2br(htmlspecialchars($spell->Description)); ?>
                    </td>
    			</tr>
    			<tr>
    				<td>Nivå 
    				</td>
    				<td>
    					<?php echo nl2br(htmlspecialchars($spell->Level)); ?>
                    </td>
    			</tr>
    			<tr>
    				<td>Typ 
    				</td>
    				<td>
    					<?php echo Magic_Spell::TYPES[$spell->Type]; ?>
                    </td>
    			</tr>
    			<tr>
    				<td>Speciella krav</td>
    				<td><?php echo nl2br(htmlspecialchars($spell->Special)); ?></td>
    			</tr>
    			<tr>
    				<td>Anteckningar</td>
    				<td><?php echo nl2br(htmlspecialchars($spell->OrganizerNotes)); ?></td>
    			</tr>
    			<tr><td></td></tr>
    		</table>

			<h2>Magiskolor som har magin</h2>
			<?php 
			$schools = $spell->getAllSchools();
			if (empty($schools)) {
			    echo "Inga skolor har magin, än.";
			} else {
			    echo "<table class='small_data'>";
			    echo "<tr><th>Namn</th><th></th></tr>";
			    
			    foreach ($schools as $school) {
				    echo "<tr><td><a href = 'view_magicschool.php?id=$school->Id'>$school->Name</td>";
				    echo "<td><a href='logic/view_magicspell_logic.php?operation=remove_school&SchoolId=$school->Id&Id=$spell->Id'><i class='fa-solid fa-xmark' title='Ta bort magi från magiskolan'></i></a></td>";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			<p>
			<a href='choose_magic_school.php?id=<?php echo $spell->Id ?>&operation=add_spell_school'>Lägg till i magiskolor</a>

			<h2>Magiker som har tilldelats magin</h2>
			<?php 
			$magicians = $spell->getAllAssignedMagicians();

			if (empty($magicians)) {
			    echo "Inga magiker har tilldelats magin, än.";
			} else {
			    echo "Alla magiker som har tilldelats magin visas, även de som inte kommer på just det här lajvet.";
				echo "<table class='small_data'>";
				echo "<tr><th>Magiker</th><th>Nivå</th><th>Mästare</th><th>Kommer på lajvet</th><th></th></tr>";
				foreach ($magicians as $magician) {
				    $role = $magician->getRole();
				    $master = $magician->getMaster();
				    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
				    $isComing = !empty($larp_role);
				    echo "<tr><td>";
					echo $role->getViewLink();
					echo "</td><td>$magician->Level</td>";
				    echo "<td>";
				    if (isset($master)) {
				        $masterRole = $master->getRole(); 
				        echo $masterRole->getViewLink();
						echo " (nivå $master->Level)</td>";
				    }
				    echo "</td>";
				    echo "<td align='center'>".showStatusIcon($isComing)."</td>";
				    echo "<td><a href='logic/view_magicspell_logic.php?operation=remove_magician&MagicianId=$magician->Id&Id=$spell->Id'><i class='fa-solid fa-xmark' title='Ta bort magi från magiker'></i></a></td>";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			<h2>Magiker som har magin genom sin skola</h2>
			<?php 
			$magicians = $spell->getAllMagiciansThroughSchool();

			if (empty($magicians)) {
			    echo "Inga magiker har magin genom skola, än.";
			} else {
			    echo "Alla magiker som har skolan visas, även de som inte kommer på just det här lajvet.";
				echo "<table class='small_data'>";
				echo "<tr><th>Magiker</th><th>Nivå</th><th>Mästare</th><th>Kommer på lajvet</th></tr>";
				foreach ($magicians as $magician) {
				    $role = $magician->getRole();
				    $master = $magician->getMaster();
				    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
				    $isComing = !empty($larp_role);
				    echo "<tr><td>";
					echo $role->getViewLink();
					echo "</td><td>$magician->Level</td>";
				    echo "<td>";
				    if (isset($master)) {
				        $masterRole = $master->getRole(); 
				        echo $masterRole->getViewLink();
						echo " (nivå $master->Level)</td>";
				    }
				    echo "</td>";
				    echo "<td align='center'>".showStatusIcon($isComing)."</td>";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			<p>
			<a href='choose_magician.php?id=<?php echo $spell->Id?>&operation=add_spell_magician'>Tilldela till magiker</a>


		</div>
		


</body>
</html>
