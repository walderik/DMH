<?php 


require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        //echo "Laddar " . $_GET['id'] . "<br>";
        
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




$image = Image::loadById($role->ImageId);

include 'navigation_subpage.php';
?>

	<div class="content">
		<h1>Bild för <?php echo $role->Name?></h1>

<?php 
echo '<img src="data:image/jpeg;base64,'.base64_encode($image->file_data).'"/>';
if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
?>
