<?php

require 'header.php';


$npc_group = NPCGroup::newWithDefault();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    else {
        
    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $npc_group = NPCGroup::loadById($_GET['id']);
    } else {
    }
}


function default_value($field) {
    GLOBAL $npc_group;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($npc_group->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "action":
            if (is_null($npc_group->Id)) {
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

		<h1><?php echo $npc_group->Name;?></h1>
		<form action="logic/npc_group_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $npc_group->Id; ?>">

		
		<table>
 			<tr><td valign="top" class="header">Namn&nbsp;<font style="color:red">*</font></td>
 			<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($npc_group->Name); ?>" size="100" maxlength="250" required></td></tr>
			<tr><td valign="top" class="header">Beskrivning</td>
 			<td><input type="text" id="Description" name="Description" value="<?php echo htmlspecialchars($npc_group->Description); ?>" size="100" maxlength="250"></td></tr>

 			<tr><td valign="top" class="header">När ska gruppen spelas?<br>Om den ska spelas vid ett särskillt tillfälle.</td>
 			<td><input type="text" id="Time" name="Time" value="<?php echo htmlspecialchars($npc_group->Time); ?>" size="100" maxlength="250"></td></tr>

		</table>		
			<input type="submit" value="Spara">

			</form>

	</div>


</body>
</html>
