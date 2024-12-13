<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $GroupId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$group = Group::loadById($GroupId); 

if ($group->CampaignId != $current_larp->CampaignId) {
    header('Location: index.php'); //Inte den här kampanjen
    exit;
}


if (!$group->isRegistered($current_larp)) {
    header('Location: index.php?error=not_registered'); //Gruppen är inte anmäld
    exit;
}

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

$main_characters_in_group = Role::getAllMainRolesInGroup($group, $current_larp);

function print_role(Role $role, Group $group) {
    global $type;
    
    if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
    else echo "<li style='display:table-cell; width:49%;'>\n";
    
    echo "<div class='name'><a href='view_known_role.php?id=$role->Id'>$role->Name</a>";
    echo "</div>\n";
    echo "Yrke: ".$role->Profession . "<br>";

    $person = $role->getPerson();
    if ($person->hasPermissionShowName()) echo "Spelas av ".$role->getPerson()->Name."<br>";
    
    if ($role->hasImage()) {
        $image = Image::loadById($role->ImageId);
        echo "<img src='../includes/display_image.php?id=$role->ImageId'/>\n";
        if (!empty($image->Photographer)) {
            echo "<div class='photographer'>Fotograf $image->Photographer</div>\n";
        }
    }
    echo "</li>\n\n";
    
}

include 'navigation.php';
?>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-people-group"></i>
			<?php echo $group->Name;?>
		</div>
		
		<?php 
		if ($group->hasImage()) {
		    echo "<div class='itemcontainer'>";
		    $image = Image::loadById($group->ImageId);
		    echo "<img width='300' src='../includes/display_image.php?id=$group->ImageId'/>\n";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    echo "</div>";
		}
		?>
		
		
	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning</div>
	   <?php echo nl2br(htmlspecialchars($group->DescriptionForOthers));?>
	   </div>

		
		

		<?php 
		echo "<div class='itemcontainer'>";
		echo "<div class='itemname'>Medlemmar i gruppen</div>";
		
		echo "<div class='container'>\n";
		
		echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		
		if (empty($main_characters_in_group)) {
		    echo "Inga anmälda i gruppen.";
		}
		else {
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		    foreach ($main_characters_in_group as $role) {
		        print_role($role, $group);
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
		echo "</div>\n";
		
		?>
  


	</div>


</body>
</html>
