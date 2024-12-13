<?php
include_once 'header.php';
// include_once '../includes/error_handling.php';

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
    case "role":
        $object = Role::loadById($id);
        if ($object->PersonId != $current_person->Id) {
            header('Location: ../index.php'); //Inte din karaktär

            exit;
        }
        $name = $object->Name;
        break;
    case "group":
        $object = Group::loadById($id);
        if (!$current_person->isGroupLeader($object)) {
            header('Location: ../index.php');
            exit;
        }
        $name = $object->Name;
        break;
    case "npc":
        $object = NPC::loadById($id);
        if ($object->PersonId != $current_person->Id) {
            header('Location: ../index.php');
            exit;
        }
        $name = $object->Name;
        break;
    case "magician":
        $object = Magic_Magician::loadById($id);
        $name = "stav";
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
        $imageId=$object->ImageId;
        $id = Image::saveImage("$name - $type");
        $object->ImageId = $id;
        if ($object instanceof Magic_Magician) {
            $object->StaffApproved=null;
        }
        $object->update();
        
        //Ta bort den gamla bilden
        if (isset($imageId)) Image::delete($imageId);
        
        if (isset($_POST['Referer']) && $_POST['Referer']!="") {
            header('Location: ' . $_POST['Referer']);
            exit;
        }
        header('Location: index.php?message=image_uploaded');
        exit;
    }
    else {
        $error_code = $error;
        $error_message = getErrorText($error_code);
        
    }
}


if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}


include 'navigation.php';
?>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-portrait"></i>
			Ladda upp bild för <?php echo $name;?>
		</div>

   		<div class='itemcontainer'>

    
    	  <?php if (isset($error_message) && strlen($error_message)>0) {
    	      echo '<div class="error">'.$error_message.'</div>';
    	  }?>
    	  <?php if (isset($message_message) && strlen($message_message)>0) {
    	      echo '<div class="message">'.$message_message.'</div>';
    	  }?>
    
        	<form method="post" enctype="multipart/form-data">
            	<input type="hidden" id="id" name="id" value="<?php echo $object->Id; ?>">
            	<input type="hidden" id="type" name="type" value="<?php echo $type; ?>">
            	<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
              	<input type="file" name="upload" required accept="image/png, image/gif, image/jpeg"><br><br>
    				<label for=Photographer>Fotograf</label>
    				
    				<input class="input_field" type="text" id="Photographer" name="Photographer" value=""  size="25" maxlength="100">
              	<br><br>
              	<input type="submit" name="submit" value="Ladda upp bild">
              	<p><strong>OBS:</strong> Bara .jpg, .jpeg, .gif, .png bilder är tillåtna.</p>
            </form>
	    </div>
	</div>
</body>
</html>
  
