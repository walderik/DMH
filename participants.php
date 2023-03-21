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
    echo "<DIV class='responsive'>";
    echo "<DIV class='gallery'>";
    echo "<DIV class='name'>$role->Name</DIV>";
    echo "<DIV class='desc'>$role->DescriptionForOthers</DIV>";
    if (isset($role->ImageId) && !is_null($role->ImageId)) {
        $image = $ih->loadImage($role->ImageId);
        if (!is_null($image) && strlen($image) > 0) {
            echo "<img width=300 src='data:image/jpeg;base64,".base64_encode($image)."'/>\n";
        }
    }
    
    echo "</DIV>";
    echo "</DIV>";
    
}
?>


<HTML>

<HEAD>

<STYLE type="text/css">

img{
  display: block;
    max-height:250px;
    max-width:200px;
    height:auto;
    width:auto;
}

body {
	padding: 25px;
}

.participants>p {
	box-shadow: 0 0 5px 0 rgba(0, 0, 0, 0.1);
	margin: 25px 0;
	padding: 25px;
	background-color: #fff;
	width: 100%;
	border: 1px solid green;
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
	width: 32.99999%;
	background-color: #f3f4f7;
	height: 100%;
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

.clearfix {
	clear: both;
	padding-top: 20px;
}
</STYLE>



		<meta charset="utf-8">
		<title><?php  echo $larp->Name; ?></title>
		<link href="css/style.css" rel="stylesheet" type="text/css">

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
		    
			echo "<DIV class='clearfix'>";
		    echo "<h2>$group->Name</h2>";
		    echo "</DIV>";
		    if ($group->DescriptionForOthers !="") {
		        echo "<p>$group->DescriptionForOthers</p>";
		    }
		    echo "<DIV class='responsive-container'>";
		    
		    if ((empty($roles) or count($roles)==0) &&(empty($non_main_roles) or count($non_main_roles)==0)) {
		        echo "Inga anmälda i gruppen än.";
		    }
		    else {
    		    foreach ($roles as $role) {
    		        print_role($role);
    		    }
		        foreach ($non_main_roles as $role) {
		            print_role($role);
		        }
		    }
		    
		echo "</DIV>";
		}
		
		
		$roles = Role::getAllMainRolesWithoutGroup($larp);
		$non_main_roles = Role::getAllNonMainRolesWithoutGroup($larp);
		if ((!empty($roles) && count($roles)!=0) or (!empty($non_main_roles) && count($non_main_roles)!=0)) {
		    echo "<DIV class='clearfix'>";
		    echo "<h2>Roller utan grupp</h2>";
		    echo "</DIV>";
		    foreach ($roles as $role) {
		        print_role($role);
		    }
		    foreach ($non_main_roles as $role) {
		        print_role($role);
		    }
		}
		
		
		
		?>



</BODY>

</HTML>

