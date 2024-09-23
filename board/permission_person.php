<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['PersonId'])) {
        $personId = $_GET['PersonId'];
        $person = Person::loadById($personId);
    }
    else $personId = NULL;
}


include 'navigation.php';
?>
    

    <div class="content"> 
    <h1>
    <?php 
    if (!empty($personId)) echo "Sätt behörigheter för $person->Name";
    else "Lägg användare med behörighet";
    ?>

    
    </h1>
	<form autocomplete="off" action="permissions.php" method="post">
		<input type="hidden" id="operation" name="operation" value="permission_user"> 

		<?php 
		if (empty($personId)) {
		    autocomplete_person_id('60%', false);
		    echo "<br>";
		    $current_permissions = array();
		}
		else {
		    echo "<input type='hidden' id='person_id' name='person_id' value='$personId'>";
		    $current_permissions = $person->getOtherAccess();
		}

		?>

		<?php 
		$permissions = AccessControl::ACCESS_TYPES;
		foreach ($permissions as $key => $permission) {
		    echo "<input type='checkbox' id='Permission$key' name='Permission[]' value='$key'";
		    if (in_array($key, $current_permissions)) echo " checked=checked ";
		    echo ">";
		    echo " <label for='Permission$key'>$permission</label><br>";
		}
		
		
		?>
		<br>
		<input type='submit' value='Spara'>
	</form>
	</div>
