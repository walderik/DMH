<?php

require 'header.php';


$npc = NPC::newWithDefault();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    else {
        
    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $npc = NPC::loadById($_GET['id']);
    } else {
    }
}


function default_value($field) {
    GLOBAL $npc;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($npc->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "action":
            if (is_null($npc->Id)) {
                $output = "Registrera";
                break;
            }
            $output = "Uppdatera";
            break;
    }
    
    echo $output;
}

include 'navigation_subpage.php';

?>

	<div class="content">

		<h1><?php echo $npc->Name;?></h1>
		<form action="logic/npc_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $npc->Id; ?>">

		
		<table>
 			<tr><td valign="top" class="header">Namn&nbsp;<font style="color:red">*</font></td>
 			<td><input type="text" id="Name" name="Name" value="<?php echo $npc->Name; ?>" size="100" maxlength="250" required></td></tr>
			<tr><td valign="top" class="header">Spelas av</td>
			    <td><?php if ($npc->IsAssigned()) {
			                 echo $npc->getPerson()->Name; 
			                 echo "<input type='hidden' name='PersonId' value='$npc->PersonId'>";
			    }?></td></tr>

			<tr><td valign="top" class="header">Grupp</td>
			<td><?php selectionByArray('NPCGroup', NPCGroup::getAllForLARP($current_larp), false, false, $npc->NPCGroupId); ?></td></tr>

			<tr><td valign="top" class="header">Beskrivning</td>
 			<td><input type="text" id="Description" name="Description" value="<?php echo $npc->Description; ?>" size="100" maxlength="250"></td></tr>

 			<tr><td valign="top" class="header">När ska karaktären spelas?<br>Om den ska spelas vid ett särskillt tillfälle.</td>
 			<td><input type="text" id="Time" name="Time" value="<?php echo $npc->Time; ?>" size="100" maxlength="250"></td></tr>

		</table>		
			<input type="submit" value="Spara">

			</form>

	</div>


</body>
</html>
