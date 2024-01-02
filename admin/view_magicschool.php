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

		
		<a href='magic_school_form.php?id=<?php echo $school->Id;?>'>
		<i class='fa-solid fa-pen'></i></a> 
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
    				<td>Antecknngar</td>
    				<td><?php echo nl2br(htmlspecialchars($school->OrganizerNotes)); ?></td>
    			</tr>
    			<tr><td></td></tr>
    			<tr>
    				<td>Magier i skolan</td>
    				<td>
    				<?php 
    				$spells = $school->getAllSpells();
    				if (empty($spells)) {
    				    echo "Inga magier i skolan, än.";
    				} else {
        				echo "<table>";
        				echo "<tr><th>Magi</th><th>Nivå</th></tr>";
        				foreach ($spells as $spell) {
        				    echo "<tr><td><a href = view_magic_spell.php?id=$spell->Id'>$spell->Name</td><td>$spell->Level</td></tr>";
        				}
        				echo "</table>";
    				}
    				?>
    				<p>
    				<a href='choose_magic_spells.php'>Lägg till magier</a>
    				</td>
    			</tr>
    			    			<tr>
    				<td>Magiker som har skolan</td>
    				<td>
    				<?php 
    				$magicians = $school->getAllMagicians();

    				if (empty($magicians)) {
    				    echo "Inga magiker har skolan, än.";
    				} else {
    				    echo "Alla magiker som har skolan visas, även de som inte kommer på just det här lajvet.";
        				echo "<table>";
        				echo "<tr><th>Magiker</th><th>Nivå</th><th>Mästare</th><th>Kommer på lajvet</th></tr>";
        				foreach ($magicians as $magician) {
        				    $role = $magician->getRole();
        				    $master = $magician->getMaster();
        				    echo "<tr><td><a href = view_role.php?id=$role->Id'>$role->Name</td><td>$magician->Level</td>";
        				    echo "<td>";
        				    if (isset($master)) {
        				        $masterRole = $master->getRole(); 
        				        echo "<a href = view_role.php?id=$masterRole->Id'>$masterRole->Name (nivå $master->Level)</td>";
        				    }
        				    echo "</td>";
        				    echo "<td>".$role->isComing($current_larp)."</td>";
        				    echo "</tr>";
        				}
        				echo "</table>";
    				}
    				?>
    				</td>
    			</tr>
    			
    		</table>


		</div>
		


</body>
</html>
