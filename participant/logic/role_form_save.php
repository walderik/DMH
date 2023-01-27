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
        $role = Role::newFromArray($_POST);
        $role->create();
    } elseif ($operation == 'delete') {
        Role::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        
        $role = Role::newFromArray($_POST);
        $role->update();

    } else {
        echo $operation;
    }
    header('Location: ../index.php');
}
