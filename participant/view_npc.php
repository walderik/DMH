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

    <div class='itemselector'>
        <div class='header'>
        <i class='fa-solid fa-person'></i> <?php echo $npc->Name;?>
        </div>

   		<div class='itemcontainer'>

		<?php 
		if ($npc->hasImage()) {
		    
		    $image = Image::loadById($npc->ImageId);
		    echo "<img width='300' src='../includes/display_image.php?id=$npc->ImageId'/>\n";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		}
		?>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Spelas av</div>
		<?php echo $npc->getPerson()->Name;?>
		</div>

		<?php if (isset($npc_group)) {?>
	   		<div class='itemcontainer'>
           	<div class='itemname'>Grupp</div>
			<a href ="view_npc_group.php?id=<?php echo $npc_group->Id;?>"><?php echo $npc_group->Name; ?></a>
			</div>
		<?php }?>

   		<div class='itemcontainer'>
       	<div class='itemname'>Beskrivning</div>
		<?php echo $npc->Description;?>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>När ska NPC'n spelas</div>
		<?php echo $npc->Time;?>
		</div>
	</div>


</body>
</html>
