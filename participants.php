<?php

global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

include_once $root . '/includes/all_includes.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $Id = $_GET['id'];
        
    }
    else {
        
        header('Location: index.php');
        exit;
    }
}


$larp = Larp::loadById($Id);
$current_larp=$larp;

if (is_null($larp)) {
    header('Location: index.php');
    exit;
}


function print_role($role) {
    global $current_larp;
    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    echo "<li>\n";
    echo "<div class='name'>$role->Name</div>\n";
    if($larp_role->IsMainRole == 0) echo "<div>Sidokaraktär</div>\n";
    echo "<div class='description'>$role->DescriptionForOthers</div>\n";
    if (isset($role->ImageId) && !is_null($role->ImageId)) {
        $image = Image::loadById($role->ImageId);
        if (!is_null($image)) {
            
            echo "<img src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
            if (!empty($image->Photographer) && $image->Photographer!="") {
                echo "<div class='photographer'>Fotograf $image->Photographer</div>\n";
            }
        }
    }
    else {
        echo "<img src='images/man-shape.png' />\n";
        echo "<div class='photographer'><a href='https://www.flaticon.com/free-icons/man' title='man icons'>Man icons created by Freepik - Flaticon</a></div>\n";
    }
    echo "</li>\n\n";
    
}
?>
<HTML>

<HEAD>


		<meta charset="utf-8">
		<title><?php  echo $larp->Name; ?></title>
		<link href="css/style.css" rel="stylesheet" type="text/css">
		<link href="css/gallery.css" rel="stylesheet" type="text/css">

		<link rel="icon" type="image/x-icon" href="images/<?php echo $current_larp->getCampaign()->Icon; ?>">




</HEAD>

<BODY>


	<DIV class="participants">

		<H1>Roller på <?php  echo $larp->Name; ?></H1>

		<?php 
		$groups = Group::getRegistered($larp);
		foreach ($groups as $group) {
		    $roles = Role::getAllMainRolesInGroup($group, $larp);
		    $non_main_roles = Role::getAllNonMainRolesInGroup($group, $larp);
		    
		    echo "<h2>$group->Name</h2>\n";
		    if ($group->DescriptionForOthers !="") {
		        echo "<p>$group->DescriptionForOthers</p>\n";
		    }
		    


            echo "<div class='container'>\n";
            if ((empty($roles) or count($roles)==0) &&(empty($non_main_roles) or count($non_main_roles)==0)) {
                echo "Inga anmälda i gruppen än.";
            }
            else {
                echo "<ul class='image-gallery'>\n";
                foreach ($roles as $role) {
                    print_role($role);
                }
                foreach ($non_main_roles as $role) {
                    print_role($role);
                }
                echo "</ul>\n";
            }
            
            echo "</DIV>\n";
            
            
		}
		
		
		
		/* Karaktärer utan grupp */	
		$roles = Role::getAllMainRolesWithoutGroup($larp);
		$non_main_roles = Role::getAllNonMainRolesWithoutGroup($larp);
		
		if ((!empty($roles) && count($roles)!=0) or (!empty($non_main_roles) && count($non_main_roles)!=0)) {
		
		  echo "<h2>Roller utan grupp</h2>\n";
		
		
    		echo "<div class='container'>\n";
    		if ((empty($roles) or count($roles)==0) &&(empty($non_main_roles) or count($non_main_roles)==0)) {
    		    echo "Inga anmälda i gruppen än.";
    		}
    		else {
    		    echo "<ul class='image-gallery'>\n";
    		    foreach ($roles as $role) {
    		        print_role($role);
    		    }
    		    foreach ($non_main_roles as $role) {
    		        print_role($role);
    		    }
    		    echo "</ul>\n";
    		}
    		
    		echo "</DIV>\n";
		}
		
		?>
	</DIV>



</BODY>

</HTML>
		
