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

if ($person->Id != $current_person->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}




include 'navigation.php';
?>

	<div class='itemselector'>
		<div class="header">
			<i class="fa-solid fa-eye"></i>
			 <?php echo "Syner för $role->Name";?>
		</div>
   		<div class='itemcontainer'>

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
	</div>
		


</body>
</html>
