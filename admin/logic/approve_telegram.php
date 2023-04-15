<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php?error=userNotFound');
    exit;
}




if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $telegram = Telegram::loadById($_GET['id']);
    if (isset($telegram)) {
        
        $telegram->Approved=1;
        $telegram->update();
        header('Location: ../telegram_admin.php');
        exit;
    }
    
}
header('Location: ../index.php?');
exit;


