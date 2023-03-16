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
    $ih = ImageHandler::newWithDefault();
    //echo "<td valign='top'>\n";
    echo "<div class='responsive'>";
    echo "<div class='gallery'>";
    echo "<div class='name'>$role->Name</div>";
    echo "<div class='desc'>$role->DescriptionForOthers</div>";
    // Visa eventuell bild
    if (isset($role->ImageId) && !is_null($role->ImageId)) {
        $image = $ih->loadImage($role->ImageId);
        if (!is_null($image) && strlen($image) > 0) {
            echo "<img width=300 src='data:image/jpeg;base64,".base64_encode($image)."'/>\n";
        }
    }
    //echo "</td>";
    echo "</div>";
    echo "</div>";
    
}
?>


<!DOCTYPE html>
<html>
	<head>
	<style>
div.gallery {
  border: 1px solid #ccc;
  border-radius: 10px;
  background: white;
}

div.gallery:hover {
  border: 1px solid #777;
  border-radius: 10px;
}

div.gallery img {
padding 10px;
  width: 100%;
  height: auto;
}

div.desc {
  padding: 15px;
  text-align: left;
}

div.name {
  font-weight: bold;
  padding: 15px;
  text-align: center;
}

* {
  box-sizing: border-box;
}

.responsive {
  padding: 6px 6px;
  float: left;
  width: 24.99999%;
}

@media only screen and (max-width: 700px) {
  .responsive {
    width: 49.99999%;
    margin: 6px 0;
  }
}

@media only screen and (max-width: 500px) {
  .responsive {
    width: 100%;
  }
}

.clearfix:after {
  content: "";
  display: table;
  clear: both;
}
</style>
	
	
	
	
		<meta charset="utf-8">
		<title><?php  echo $larp->Name; ?></title>
		<link href="css/style.css" rel="stylesheet" type="text/css">

		<link rel="icon" type="image/x-icon" href="images/<?php echo $current_larp->getCampaign()->Icon; ?>">

	</head>
	<body>
		<div>
	  <h1>Roller på <?php  echo $larp->Name; ?></h1>
		<?php 
		$groups = Group::getRegistered($larp);
		
		foreach ($groups as $group) {
		    $roles = Role::getAllMainRolesInGroup($group, $larp);

		    echo "<h2>$group->Name</h2>";
		    //echo "<table border=1><tr>\n";
		    //echo "<div class='container'>";
		    if (empty($roles) or count($roles)==0) {
		        echo "Inga anmälda i gruppen än.";
		    }
		
    		foreach ($roles as $role) {
    		    print_role($role);
    		}
    		
    		//echo "</tr></table>\n";

    		echo "<div class='clearfix'></div>";
    		
		}
		

		$roles = Role::getAllMainRolesWithoutGroup($larp);
		if (!empty($roles) && count($roles)!=0) {
    		echo "<h2>Roller utan grupp</h2>";
     		foreach ($roles as $role) {
    		    print_role($role);
    		}
		}


		
		?>
		</div>
	</body>
</html>