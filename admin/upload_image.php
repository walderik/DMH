<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id']) && isset($_GET['type'])) {
        //echo "Laddar " . $_GET['id'] . "<br>";
        $type = $_GET['type'];
        $id = $_GET['id'];

    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['id']) && isset($_POST['type'])) {
        $type = $_GET['type'];
        $id = $_GET['id'];

    }
}


switch ($type) {
    case "house":
        $object = House::loadById($id);
        break;
    case "prop":
        $object = Prop::loadById($id);
        break;
}
        

if (!isset($object)) {
    header('Location: index.php');
    exit;
}


// (A) SAVE IMAGE INTO DATABASE
if (isset($_FILES["upload"])) {
    
    $error = Image::maySave();
    if (!isset($error)) {
        $id = Image::saveImage();
        $object->ImageId = $id;
        $object->update();

        if (isset($_POST['Referer']) && $_POST['Referer']!="") {
            header('Location: ' . $_POST['Referer']);
            exit;
        }
        header('Location: index.php?message=image_uploaded');
        exit;
    }
}


if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}


include 'navigation_subpage.php';
?>


	<div class="content">
		<h1>Ladda upp bild för <?php echo $object->Name;?></h1>
        	  <?php if (isset($error) && strlen($error)>0) {
        	      echo '<div class="error">'.$error.'</div>';
        	  }?>

    	<form method="post" enctype="multipart/form-data">
        	<input type="hidden" id="id" name="id" value="<?php echo $object->Id; ?>">
        	<input type="hidden" id="type" name="type" value="<?php echo $type; ?>">
        	<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
          	<input type="file" name="upload" required><br><br>
				<label for=Photographer>Fotograf</label>
				
				<input class="input_field" type="text" id="Photographer" name="Photographer" value=""  size="25" maxlength="100">
          	<br><br>
          	<input type="submit" name="submit" value="Ladda upp bild">
          	<p><strong>OBS:</strong> Bara .jpg, .jpeg, .gif, .png bilder är tillåtna och max storlek 0.5 MB.</p>
        </form>
    </div>
</body>
</html>
  