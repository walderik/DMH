<?php
include_once '../header.php';

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


