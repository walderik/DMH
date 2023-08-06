<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $NPCGroupId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$npc_group = NPCGroup::loadById($NPCGroupId);

if (!$npc_group->IsMember($current_user)) {
    header('Location: index.php?error=no_member'); //Inte medlem i gruppen
    exit;
}

if ($npc_group->LarpId != $current_larp->Id) {
    header('Location: index.php?error=not_registered'); //Fel lajv
    exit;
}

$group_members = $npc_group->getNPCsInGroup();


function print_npc(NPC $npc) {
    global $current_larp;
    
    echo "<li>\n";
    echo "<div class='name'>$npc->Name";    echo "</div>\n";
    $person = $npc->getPerson();
    if (isset($person)) echo "Spelas av $person->Name<br>";
    else echo "Inte tilldelad<br>";
    
    echo "<div class='description'>$npc->Description</div>\n";
    if ($npc->hasImage()) {
        $image = Image::loadById($npc->ImageId);
        if (!is_null($image)) {
            
            echo "<img src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
            if (!empty($image->Photographer) && $image->Photographer!="") {
                echo "<div class='photographer'>Fotograf $image->Photographer</div>\n";
            }
        }
    }
    else {
        echo "<img src='../images/man-shape.png' />\n";
        echo "<div class='photographer'><a href='https://www.flaticon.com/free-icons/man' title='man icons'>Man icons created by Freepik - Flaticon</a></div>\n";
    }
    
    echo "</li>\n\n";
    
}

include 'navigation.php';
?>


	<div class="content">
		<h1><?php echo $npc_group->Name;?></h1>
		<table>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo $npc_group->Description;?></td></tr>
			<tr><td valign="top" class="header">När ska gruppen spelas</td><td><?php echo $npc_group->Time;?></td></tr>
		</table>		
		
		
		<h2>NPC'er i gruppen</h2>

		<?php 

		
		echo "<div class='container' style ='background-color: #f3f4f7;box-shadow: none; margin: 0px; padding: 0px;'>\n";
		if ((empty($group_members) or count($group_members)==0)) {
		    echo "Inga anmälda i gruppen än.";
		}
		else {
		    echo "<ul class='image-gallery'>\n";
		    foreach ($group_members as $role) {
		        print_npc($role);
		    }
		    echo "</ul>\n";
		}
		
		echo "</DIV>\n";
		
		
		?>
		    
	</div>


</body>
</html>
