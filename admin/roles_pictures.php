<?php
include_once 'header.php';

include 'navigation.php';
include 'aktor_navigation.php';


// Check if the "mobile" word exists in User-Agent
$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));

if($isMob){
    $columns=2;
    $type="Mobile";
    // echo 'Using Mobile Device...';
}else{
    
    $columns=5;
    $type="Computer";
    // echo 'Using Desktop...';
}


$temp=0;


function print_role(Role $role) {
    global $type;
    if($type=="Computer")
    {
        echo "<li style='display:table-cell; width:19%;'>\n";
    } else
    {
        echo "<li style='display:table-cell; width:49%;'>\n";
    }
    
    echo "<div class='name'>".$role->getViewLink()."</div>\n";
    $person = $role->getPerson();
    echo "<div>Spelas av ".$person->getViewLink()."</div>";

    //Alla anmälda roller, de förutsätter att det finns en person
    //echo "<div class='description'>$role->DescriptionForOthers</div>\n";
    if (isset($role->ImageId) && !is_null($role->ImageId)) {
        $image = Image::loadById($role->ImageId);
         echo "<img class='roleImage' src='../includes/display_image.php?id=$image->Id'/>\n";

        if (!empty($image->Photographer) && $image->Photographer!="") {
            echo "<div class='photographer'>Fotograf $image->Photographer</div>\n";
        }
    }
    echo "</li>\n\n";
    
}

$personIdArrMissingImages = array();
$allMainRoles = Role::getAllMainRoles($current_larp, false);
foreach ($allMainRoles as $role) {
    if (!$role->hasImage() && !empty($role->PersonId)) $personIdArrMissingImages[] = $role->PersonId;
}


?>

	<DIV class="participants">

		<H1>Bildgalleri över huvudkaraktärer</H1>

		<?php 
		if (!empty($personIdArrMissingImages)) {
		    echo contactSeveralEmailIcon("", $personIdArrMissingImages, "Bild till $current_larp->Name", "Saknar bild på $current_larp->Name");
    		echo "Maila alla som saknar bild på huvudkaraktären.";  
		} 

		$isMareld = $current_larp->getCampaign()->is_me();
		$groups = Group::getAllVisible($current_larp);

		foreach ($groups as $group) {
		    $roles = Role::getAllMainRolesInGroup($group, $current_larp);
            $allMainRoles = array_udiff($allMainRoles, $roles,
                function ($objOne, $objTwo) {
                    return $objOne->Id - $objTwo->Id;
                });
            if (!empty($roles)) {

                echo "<h2>$group->Name</h2>\n";
                if ($group->hasImage() || $group->DescriptionForOthers !="") echo "<p>";
                if ($group->hasImage()) {
                    $image = $group->getImage();
                    echo "<img class='groupImage' width='300px' src='../includes/display_image.php?id=$image->Id'/><br>\n";
                    
                    if (!empty($image->Photographer) && $image->Photographer!="") {
                        echo "Fotograf $image->Photographer<br><br>\n";
                    }
                }
                if ($group->DescriptionForOthers !="") {
                    echo nl2br(htmlspecialchars($group->DescriptionForOthers));
                    if ($isMareld) echo "<br>Färg: $group->Colour";
                }
                if ($group->hasImage() || $group->DescriptionForOthers !="") echo "</p>";
                
                echo "<div class='container'>\n";
                
                echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
                foreach ($roles as $role) {
                    print_role($role);
                   $temp++;
                    if($temp==$columns)
                    {
                        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
                        $temp=0;
                    } 
                    
                }
                $temp=0;

                echo "</ul>\n";
                echo "</DIV>\n";
            }

            
            
		}
		
		
		
		/* Karaktärer utan grupp */	
		$roles = $allMainRoles;
		

		if ((!empty($roles) && count($roles)!=0)) {
		
		  echo "<h2>Karaktärer utan grupp</h2>\n";
		
		
    		echo "<div class='container'>\n";
    		if ((empty($roles) or count($roles)==0)) {
    		    echo "Inga anmälda än.";
    		}
    		else {
    		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
    		    foreach ($roles as $role) {
    		        print_role($role);
    		        $temp++;
                    if($temp==$columns)
                    {
                        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
                        $temp=0;
                    }
    		    }
    		    echo "</ul><ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
    		    $temp=0;
    		    echo "</ul>\n";
    		}
    		
    		echo "</DIV>\n";
		}
		
		?>
	</DIV>

		
