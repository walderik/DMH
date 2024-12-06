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

if (!$npc_group->IsMember($current_person)) {
    header('Location: index.php?error=no_member'); //Inte medlem i gruppen
    exit;
}

if ($npc_group->LarpId != $current_larp->Id) {
    header('Location: index.php?error=not_registered'); //Fel lajv
    exit;
}

$group_members = $npc_group->getNPCsInGroup();


$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));

if($isMob){
    $columns=2;
    $type="Mobile";
    //echo 'Using Mobile Device...';
}else{
    $columns=5;
    $type="Computer";
    //echo 'Using Desktop...';
}
$temp=0;


function print_npc(NPC $npc) {
    global $current_larp, $type;
    
    if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
    else echo "<li style='display:table-cell; width:49%;'>\n";
    echo "<div class='name'>$npc->Name";    echo "</div>\n";
    $person = $npc->getPerson();
    if (isset($person)) echo "Spelas av $person->Name<br>";
    else echo "Inte tilldelad<br>";
    
    echo "<div class='description'>$npc->Description</div>\n";
    if ($npc->hasImage()) {
        echo "<img width='30' src='../includes/display_image.php?id=$npc->ImageId'/>\n";
    }
    else {
        echo "<img src='../images/man-shape.png' />\n";
        echo "<div class='photographer'><a href='https://www.flaticon.com/free-icons/man' title='man icons'>Man icons created by Freepik - Flaticon</a></div>\n";
    }
    
    echo "</li>\n\n";
    
}

include 'navigation.php';
?>

    <div class='itemselector'>
        <div class='header'>
        <i class='fa-solid fa-people-group'></i> <?php echo $npc_group->Name;?>
        </div>
        
   		<div class='itemcontainer'>
       	<div class='itemname'>Beskrivning</div>
		<?php echo $npc_group->Description;?>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>När ska gruppen spelas</div>
		<?php echo $npc_group->Time;?>
		</div>
        
   		<div class='itemcontainer'>
       	<div class='itemname'>NPC'er i gruppen</div>

		<?php 

		
		echo "<div class='container'>\n";
		if ((empty($group_members) or count($group_members)==0)) {
		    echo "Inga anmälda i gruppen än.";
		}
		else {
		    $temp=0;
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		    foreach ($group_members as $role) {
		        print_npc($role);
		        $temp++;
		        if($temp==$columns)
		        {
		            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		            $temp=0;
		        }
		    }
		    echo "</ul>\n";
		}
		
		echo "</div>\n";
		
		
		?>
		</div>		    
	</div>


</body>
</html>
