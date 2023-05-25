<?php


global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $user = User::loadById($_GET['UserId']);
    

    $_SESSION['id'] = $user->Id;
    $isAdmin = $user->IsAdmin;
    
    if ($isAdmin == 1) {
        $_SESSION['admin'] = true;
    }
    else {
        unset($_SESSION['admin']);
    }

    header('Location: ../../participant/index.php');
    exit;
    

}

header('Location: ../user_admin.php');
