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
$ih = ImageHandler::newWithDefault();
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php  echo $larp->Name; ?></title>
		<link rel="icon" type="image/x-icon" href="../images/bv.ico">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body>
	  <h1>Roller på <?php  echo $larp->Name; ?></h1>
		<?php 
		$roles = Role::getAllMainRoles($larp);
		$i = 1;
		echo "<table border=1><tr>\n";
		foreach ($roles as $role) {
		    echo "<td>\n";
		    // Visa eventuell bild
		    if (isset($role->ImageId) && !is_null($role->ImageId)) {
    		    $image = $ih->loadImage($role->ImageId);
    		    if (!is_null($image) && strlen($image) > 0) {
    		      echo "<img width=100 src='data:image/jpeg;base64,".base64_encode($image)."'/><br>\n";
    		    }
    		}
    		
            echo "<b>$role->Name</b></td>";
            $i++;
            // Ny rad
    		if ($i > 10) {
    		    $i = 1;
    		    echo "</td></tr><tr><td>\n";
    		}
		}
		echo "</tr></table>\n";
		?>
	</body>
</html>