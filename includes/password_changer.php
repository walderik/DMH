<?php

if (!isset($_POST['the_code']) || !isset($_POST['submit']) || !isset($_POST['password'])  || !isset($_POST['passwordrepeat'] ) )  {
    header("location: ../index.php?error=noSubmit");
    exit();
}

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root . '/includes/all_includes.php';

//Grabbing the data
$password = $_POST['password'];
$passwordrepeat = $_POST['passwordrepeat'];
$code = $_POST['the_code'];

if ($password != $passwordrepeat) {
    header("location: ../index.php?error=passwordNotMatch");
    exit();
}
if (strlen($password) < 5 || strlen($password) > 20) {
    header("location: ../index.php?error=invalidPasswordLength");
    exit();  
}

if (strlen($code) < 15) {
    header("location: ../index.php?error=noSubmit2");
//     print_r($_POST);
    exit();
}
$user = User::loadByEmailChangeCode($code);

if (is_null($user)) {
    header("location: ../index.php?error=userNotFound");
    exit();
}

$user->Password = password_hash($password, PASSWORD_DEFAULT);
$user->update();


//Going back to front page
header("location: ../index.php?message=user_updated");  
