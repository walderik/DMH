<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

echo '$_POST :<br>';
print_r($_POST);

echo "<br />";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $group = Group::newFromArray($_POST);
        $group->create();
    } elseif ($operation == 'delete') {
        Group::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        
        $group = Group::newFromArray($_POST);
        $group->update();

    } else {
        echo $operation;
    }
    header('Location: ../index.php');
}
