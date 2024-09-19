<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['UserId'])) {
        $userId = $_GET['UserId'];
        $user = User::loadById($userId);
    }
    else $userId = NULL;
}
$users = User::all();

include 'navigation.php';
?>
    

    <div class="content"> 
    <h1>
    <?php 
    if (!empty($userId)) echo "Sätt behörigheter för $user->Name";
    else "Lägg användare med behörighet";
    ?>

    
    </h1>
	<form action="permissions.php" method="post">
		<input type="hidden" id="operation" name="operation" value="permission_user"> 

		<?php 
		if (empty($userId)) {
		    selectionDropDownByArray("UserId", $users, true);
		    echo "<br>";
		    $current_permissions = array();
		}
		else {
		    echo "<input type='hidden' id='UserId' name='UserId' value='$userId'>";
		    $current_permissions = $user->getOtherAccess();
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
