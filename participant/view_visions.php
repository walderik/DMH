<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RoleId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$role = Role::loadById($RoleId);
$person = $role->getPerson();

if ($person->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}




include 'navigation.php';
?>

	<div class="content">
		<h1><?php echo "Syner för $role->Name";?>
		</h1>
		

		<div>
		<p>
		<strong>Tider du ska ha syner</strong><br>
		<?php 
		
		$visions = Vision::allVisionsByRole($current_larp, $role);
		foreach ($visions as $vision) {
		    echo $vision->getWhenStr() . "<br>";
		}
		?>
		<br><br>
		<strong>Gå till sekretariatet och hämta ut synen vid den anvisade tiden.</strong>
		
 

		</div>
		


</body>
</html>
