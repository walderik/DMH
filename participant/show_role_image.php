<?php 


require 'header.php';

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

<nav id="navigation">
<a href="#" class="logo"><?php echo $current_larp->Name; ?></a>
      <ul class="links">
        <li><a href="index.php"><i class="fa-solid fa-house"></i>Hem</a></li>
       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
      </ul>
    </nav>
	<div class="content">
		<h1>Bild för <?php echo $role->Name?></h1>

<?php 
echo '<img src="data:image/jpeg;base64,'.base64_encode($image).'"/>';
?>
