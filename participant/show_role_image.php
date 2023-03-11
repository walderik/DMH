<?php 


require 'header_subpage.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        echo "Laddar " . $_GET['id'] . "<br>";
        
        $role = Role::loadById($_GET['id']);
    } else {
        
        header('Location: index.php');
        exit;
    }
}

//Finns ingen sådan roll, eller rollen har ingen bild
if (!isset($role) or !isset($role->ImageId)){
    header('Location: index.php');
    exit;
}

//Ingen behörighetskontroll. Alla får se

$ih = ImageHandler::newWithDefault();
$image = $ih->loadImage($role->ImageId);
?>

<div class="content">
	<h1><?php echo $role->Name?></h1>

<?php 
echo '<img src="data:image/jpeg;base64,'.base64_encode($image).'"/>';
?>
