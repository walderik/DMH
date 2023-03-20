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

    echo "</div>";
    echo "</div>";
    
}
?>


<!DOCTYPE html>
<html>
	<head>
	<style>
	
	
body {
	padding: 25px;
}

.participants > p {
	box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.1);
	margin: 25px 0;
	padding: 25px;
	background-color: #fff;
	width: 100%
}
    
div {
-webkit-box-shadow: none;
	-moz-box-shadow: none;
	box-shadow: none;
	    background-color: none;


}
	
div.gallery {
  border: 1px solid #ccc;
  border-radius: 10px;
  background: white;
  
  
  height: 100%;

}

div.gallery:hover {
  border: 1px solid #777;
  border-radius: 10px;
}

div.gallery img {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width: 90%;
  height: auto;
  padding-bottom: 10px;
}

div.desc {
  padding-top: 0px;
  padding-right: 15px;
  padding-bottom: 15px;
  padding-left: 15px;
  text-align: left;
}

div.name {
  font-weight: bold;
  padding-top: 15px;
  padding-right: 15px;
  padding-bottom: 5px;
  padding-left: 15px;
  text-align: center;
}

* {
  box-sizing: border-box;
}

.responsive {
  padding: 6px 6px;
  float: left;
  width: 24.99999%;
      background-color: #f3f4f7;
  /* flex: 1; */
}

/* 
.responsive-container {
  display: flex;
}
*/

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
		<div class="participants">
	  <h1>Roller på <?php  echo $larp->Name; ?></h1>
		<?php 
		$groups = Group::getRegistered($larp);
		
		foreach ($groups as $group) {
		    $roles = Role::getAllMainRolesInGroup($group, $larp);

		    echo "<h2>$group->Name</h2>";
		    if ($group->DescriptionForOthers !="") {
		        echo "<p>$group->DescriptionForOthers</p>";
		    }


		    if (empty($roles) or count($roles)==0) {
		        echo "Inga anmälda i gruppen än.";
		    }
		    else {
		        echo "<div class='responsive-container'>";
        		foreach ($roles as $role) { 
        		    print_role($role);
        		}
    		echo "</div>";
		    }


    		echo "<span class='clearfix'></span>";
    		
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