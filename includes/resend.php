<?php

session_start();

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root . '/includes/all_includes.php';

// Now we check if the data was submitted, isset() function will check if the data exists.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit']) && isset($_POST['email'])) {
        
        //Grabbing the data
        $email = $_POST['email'];
        $user = User::loadByEmail($email);
        if (is_null($user)) {
            header("location: ../index.php?error=noSubmit");
        }
        if ($user->isActivated()) {
            header("location: ../index.php?error=noSubmit");
        }
        send_activation($user);
        
        header("location: ../index.php?message=user_created");
    } else {
        header("location: ../index.php?error=noSubmit");
    }
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    //If the user isnt admin it may not see these pages
    
    // Sicka aktiveringsmail tiull användaren medd ett visst Id.
    if (!isset($_SESSION['admin'])) {
        header('Location: ../participant/index.php');
    }

    if (isset($_GET['operation']) && $_GET['operation'] == 'activation') {
        $user = User::loadById($_GET['id']);
        
        if (is_null($user)) {
            header("location: ../index.php?error=noSubmit");
        }
        if ($user->isActivated()) {
            header("location: ../index.php?error=noSubmit");
        }
        send_activation($user);
        
        header("location: ../index.php?message=user_created");  
    } else {
        header("location: ../index.php?error=noSubmit");
    }
} else {
    header("location: ../index.php?error=noSubmit");
}

// Skicka aktiveringsmailet
function send_activation($user)  {
    $mail = $user->Email;
    $code = $user->ActivationCode;
    
    $url = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME'],
        "/includes/activate.php?email=$mail&code=$code"
        );
    
    $text  = "Du har begärt en ny aktiveringslänk för att kunna aktivera kontot.<br>\n";
    $text .= "<a href='$url'>Allt du behöver göra är att klicka på den här länken och sedan kan du logga in.</a><br>\n";
    
    DmhMailer::send($mail, 'Mate', $text, "Aktiveringsbrev");
}