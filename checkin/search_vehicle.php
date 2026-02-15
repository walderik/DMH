<?php
include_once 'header.php';

$type = "checkin";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['type'])) $type = $_GET['type'];
}


if ($type == "checkin") {
    $action = "checkin_person.php";
    $actionText = "checka in";
} elseif ($type == "checkout") {
    $action = "checkout_person.php";
    $actionText = "checka ut";
}  else {
    header('Location: index.php');
    exit;
}



include 'navigation.php';
?>
	<div class="header">
		<i class="fa-solid fa-user"></i>
		Sök fordon
	</div>
	<div class='itemcontainer'>
    	<form autocomplete="off" method="post">
    		<input type="text" 
    		<?php autocomplete_person_id('60%', true, $current_larp->Id); ?>
    	</form>
	</div>