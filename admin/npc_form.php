<?php

require 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['roleId'])) {
        $role = Role::loadById($_POST['roleId']);
    } else {
        header('Location: ../index.php');
        exit;
    }
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];
        if ($operation == 'insert') $assignment = NPC_assignment::newWithDefault();
        elseif ($operation == 'update') $assignment = NPC_assignment::getAssignment($role, $current_larp);
    }
}


function default_value($field) {
    GLOBAL $operation;
    $output = "";
    
    switch ($field) {
        case "operation":
            $output = $operation;
            break;
        case "action":
            if ($operation == 'insert') {
                $output = "Skapa";
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


include 'navigation.php';
include 'npc_navigation.php';

?>

	<div class="content">

		<h1><?php default_value("action");?> NPC uppdrag</h1>
		
		Här tilldelar du en NPC till en deltagare på lajvet. Ange vem som skall spela NPC:n, när den skall spelar och vad den skall göra.<br>
		När deltagaren blir tilldelad ett uppdrag blir den inte automatiskt meddelad om uppdraget. Du kan lugnt redigera och ändra uppdraget tills du är nöjd med texten.<br><br>
		
		<form action="logic/npc_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>">
    		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">

		
		<table>
 			<tr><td valign="top" class="header">Namn</td>
 			<td><?php echo $role->Name ?></td>
 			<?php 
 			if ($role->hasImage()) {
 			    echo "<td rowspan='20' valign='top'>";
 			    echo "<img width='300' src='../includes/display_image.php?id=role->ImageId'/>\n";
 			    echo "</td>";
 			}
 			
 			
 			
 			?>
 			
 			</tr>
			<tr><td valign="top" class="header">Spelas av</td>
			    <td>
			    <?php if ($assignment->IsAssigned()) {
			        echo $assignment->getPerson()->Name; 
	                echo "<input type='hidden' name='PersonId' value='$assignment->PersonId'>";
			    } else {
			        $persons=Person::getAllInterestedNPC($current_larp);
			        echo selectionDropDownByArray("PersonId", $persons, false);
			        
			    }
			    
			    
			    ?>
			    </td></tr>

			<tr><td valign="top" class="header">Grupp</td>
			<td>
			<?php
			  $group = $role->getGroup();
			  if (isset($group)) echo $group->getViewLink();
			  ?>
			  </td></tr>

			<tr><td valign="top" class="header">Beskrivning</td>
 			<td><?php echo $role->Description?></td></tr>

 			<tr><td valign="top" class="header">När ska karaktären spelas?<br>Om den ska spelas vid ett särskillt tillfälle.</td>
 			<td><input type="text" id="Time" name="Time" value="<?php echo htmlspecialchars($assignment->Time); ?>" size="100" maxlength="250"></td></tr>

 			<tr><td valign="top" class="header">Hur ska karaktären spelas?<br>Om den behöver särskilda instruktioner.</td>
 			<td><textarea id="Instructions" name="Instructions" rows="3" cols="121" maxlength="60000"><?php echo htmlspecialchars($assignment->Instructions); ?></textarea></td></tr>



		</table>		
			<input type="submit" value="<?php default_value("action");?>">

			</form>

	</div>


</body>
</html>
