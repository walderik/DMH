<?php
include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['id']) && isset($_POST['type'])) {
        if ($_POST['type']=="group") {
            $group = Group::loadById($_POST['id']);
            $object = $group;
            if (!empty($group)) {
                //Hämta alla i gruppen utan hus och tilldela dem till huset
            }
        }
        else if ($_POST['type']=="person") {
            $person = Person::loadById($_POST['id']);
            $object = $person;
            //Om man inte har boende ska man tilldelas till huset
        }
    } else {
        
        header('Location: ../index.php');
        exit;
    }
}


include 'navigation.php';
?>


    <div class="content">   
        <h1>Tilldela <?php echo $object->Name;?> till ett hus</h1>

    	<form method="post" action="logic/assign_to_house_save.php">
        	<input type="hidden" id="id" name="id" value="<?php echo $object->Id; ?>">
        	<input type="hidden" id="type" name="type" value="<?php echo $_POST['type'] ?>">
			<?php selectionByArray('House', House::all(), false, true); ?>				
          	<input type="submit" name="submit" value="Tilldela">
        </form>
    </div>
</body>
</html>
  
