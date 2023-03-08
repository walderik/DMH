<?php

require 'header.php';

// (A) SAVE IMAGE INTO DATABASE
if (isset($_FILES["upload"])) {
    $ih = Image::newWithDefault();
    echo "<div class='note'>". 
        $ih->saveImage() . 
    "</div>";
}


$role = Role::newWithDefault();

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['id'])) {
        $role = Role::loadById($_GET['id']);
    } else {
        header('Location: index.php');
        exit;
    }
}

if ($role->isRegistered($current_larp)) {
    header('Location: view_role.php?id='.$role->Id);
    exit;
}

if (Person::loadById($current_role->PersonId)->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din roll
    exit;
}

?>
 
     <nav id="navigation">
      <a href="#" class="logo"><?php echo $current_larp->Name; ?></a>
      <ul class="links">
        <li><a href="index.php"><i class="fa-solid fa-house"></i>Hem</a></li>
       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
      </ul>
    </nav>
	<div class="content">
		<h1>Ladda upp bild för Registrering av deltagare</h1>
<!-- (B) HTML FILE UPLOAD FORM -->
<form method="post" enctype="multipart/form-data">
  <input type="file" name="upload" required>
  <input type="submit" name="submit" value="Upload File">
</form>

<a href="show_image?id=1">Visa bild 1</a>