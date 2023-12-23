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


$main_characters_in_group = Role::getAllMainRolesInGroup($group, $current_larp);

function print_role(Role $role, Group $group) {
    
    echo "<li>\n";
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


	<div class="content">
		<h1><?php echo $group->Name;?> 
		</h1>
		<div>
		<table>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo $group->DescriptionForOthers;?></td>
						<?php 
			if ($group->hasImage()) {
    		    
			    $image = Image::loadById($group->ImageId);
    		    echo "<td rowspan='20' valign='top'>";
    		    echo "<img width='300' src='../includes/display_image.php?id=$group->ImageId'/>\n";
    		    echo "</td>";
    		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
    		}
    		?>
			
			
			</tr>
		</table>		
		
		
		<h2>Medlemmar</h2>

		<?php 

		
		echo "<div class='container' style ='box-shadow: none; margin: 0px; padding: 0px;'>\n";
		if (empty($main_characters_in_group)) {
		    echo "Inga anmälda i gruppen.";
		}
		else {
		    echo "<ul class='image-gallery'>\n";
		    foreach ($main_characters_in_group as $role) {
		        print_role($role, $group);
		    }
		    echo "</ul>\n";
		}
		
		echo "</DIV>\n";
		
		
		?>
		</div>    


	</div>


</body>
</html>
