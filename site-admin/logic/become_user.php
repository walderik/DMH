<?php


global $root, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user = User::loadById($_GET['UserId']);
    

    $_SESSION['id'] = $user->Id;

    header('Location: ../../participant/index.php');
    exit;
    

}

header('Location: ../user_admin.php');
