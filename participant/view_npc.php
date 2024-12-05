<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $NPCId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$npc = NPC::loadById($NPCId);

if ($npc->PersonId != $current_person->Id) {
    header('Location: index.php'); //Inte din npc
    exit;
}

if ($npc->LarpId != $current_larp->Id) {
    header('Location: index.php'); //Fel lajv
    exit;
}


if (isset($npc->NPCGroupId)) {
    $npc_group=NPCGroup::loadById($npc->NPCGroupId);
}


include 'navigation.php';
?>

	<div class="content">
		<h1><?php echo $npc->Name;?></h1>
		<table>
			<tr><td valign="top" class="header">Spelas av</td><td><?php echo $npc->getPerson()->Name; ?></td>
 			<?php 
 			if ($npc->hasImage()) {
 			    
 			    $image = Image::loadById($npc->ImageId);
 			    echo "<td rowspan='20' valign='top'>";
 			    echo "<img width='300' src='../includes/display_image.php?id=$bookkeeping->ImageId'/>\n";
 			    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
 			    echo "</td>";
 			}
 			
 			
 			
 			?>
			
			</tr>
		<?php if (isset($npc_group)) {?>
			<tr><td valign="top" class="header">Grupp</td><td><a href ="view_npc_group.php?id=<?php echo $npc_group->Id;?>"><?php echo $npc_group->Name; ?></a></td></tr>
		<?php }?>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo $npc->Description;?></td></tr>
			<tr><td valign="top" class="header">När ska NPC'n spelas</td><td><?php echo $npc->Time;?></td></tr>

		</table>		

		
	</div>


</body>
</html>
