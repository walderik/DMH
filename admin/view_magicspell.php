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
?>

	<div class="content">
		<h1><?php echo "Magi ".$spell->Name;?>&nbsp;

		
		<a href='magic_spell_form.php?id=<?php echo $spell->Id;?>'>
		<i class='fa-solid fa-pen'></i></a> 
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
    				<td>Antecknngar</td>
    				<td><?php echo nl2br(htmlspecialchars($spell->OrganizerNotes)); ?></td>
    			</tr>
    			<tr><td></td></tr>
    			<tr>
    				<td>Magiskolor som har magin</td>
    				<td>
    				<?php 
    				$schools = $spell->getAllSchools();
    				if (empty($schools)) {
    				    echo "Inga skolor har magin, än.";
    				} else {
    				    foreach ($schools as $school) {
        				    echo "<a href = view_magic_school.php?id=$school->Id'>$school->Name<br>";
        				}
    				}
    				?>
    				<p>
    				<a href='choose_magic_schools.php'>Lägg till i magiskolor</a>
    				</td>
    			</tr>
    			<tr>
    				<td>Magiker som har magin</td>
    				<td>
    				<?php 
    				$magicians = $spell->getAllMagicians();

    				if (empty($magicians)) {
    				    echo "Inga magiker har magin, än.";
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
    				<p>
    				<a href='choose_magic_schools.php'>Tilldela till magiker</a>
    				</td>
    			</tr>
    			
    		</table>


		</div>
		


</body>
</html>
