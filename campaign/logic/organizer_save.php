<?php

include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $userIds = $_POST['UserId'];
    $larpId = $_POST['LarpId'];
    $larp = LARP::loadById($larpId);
    foreach ($userIds as $userId) {
        $user = User::loadById($userId);
        
        AccessControl::grantLarp($user, $larp);
       
    }
    
    header('Location: ../settings.php');
    exit;
    
}
header('Location: ../index.php');