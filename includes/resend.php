<?php

session_start();

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
include_once $root . '/includes/all_includes.php';

// Now we check if the data was submitted, isset() function will check if the data exists.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit']) && isset($_POST['email']) && isset($_POST['action'])) {
        
        $email = $_POST['email'];
        $user = User::loadByEmail($email);
        if (is_null($user)) {
            header("location: ../index.php?error=noSubmit");
            exit();
        }

        if ($_POST['action'] == 'activation') {
            if ($user->isActivated()) {
                header("location: ../index.php?message=already_activated");
            } else {
                send_activation($user);
                header("location: ../index.php?message=email_sent");
            }
            exit();
        } elseif ($_POST['action'] == 'password') {
            if ($user->isActivated()) {
                $user->setEmailChangeCode();
                send_change_password($user);
                header("location: ../index.php?message=email_sent");
            } else { # Icke aktiverade konton får inte byta lösenord 
                header("location: ../index.php?error=notActivated");
            }
            exit();
        } else {
            header("location: ../index.php?error=noSubmit");
            exit();
        }
    } else {
        header("location: ../index.php?error=noSubmit");
        exit();
    }
    
    // NEdan är kod för tt som superadmin kunna skicka länkar. Den är inte klar utan behöver lite kärlek
} elseif ($_SERVER["REQUEST_METHOD"] == "GET") {
    //If the user isnt admin it may not see these pages
    
    // Sicka aktiveringsmail tiull användaren medd ett visst Id.
    if (!isset($_SESSION['admin'])) {
        header('Location: ../participant/index.php');
        exit();
    }

    if (isset($_GET['operation']) && $_GET['operation'] == 'activation') {
        $user = User::loadById($_GET['id']);
        
        if (is_null($user)) {
            header("location: ../index.php?error=noSubmit");
            exit();
        }
        if ($user->isActivated()) {
            header("location: ../index.php?error=noSubmit");
            exit();
        }
        send_activation($user);
        
        header("location: ../index.php?message=user_created");
        exit();
    } else {
        header("location: ../index.php?error=noSubmit");
        exit();
    }
} else {
    header("location: ../index.php?error=noSubmit");
    exit();
}

// Skicka aktiveringsmailet
function send_activation(User $user)  {
    $mail = $user->Email;
    $code = $user->ActivationCode;
    
    $url = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME'],
        "/regsys/includes/activate.php?email=$mail&code=$code"
        );
    
    $text  = "Du har begärt en ny aktiveringslänk för att kunna aktivera ditt konto.<br>\n";
    $text .= "<a href='$url'>Allt du behöver göra är att klicka på den här länken och sedan kan du logga in.</a><br>\n";
    
    BerghemMailer::send(null, $mail, $user->Name, $text, "Aktiveringsbrev", BerghemMailer::DaysAutomatic);
}

// Skicka mail med en länk för att byta lösenord
function send_change_password(User $user) {
    $mail = $user->Email;
    $code = $user->EmailChangeCode;
    
    $url = sprintf(
        "%s://%s%s",
        isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 'https' : 'http',
        $_SERVER['SERVER_NAME'],
        "/regsys/change_password.php?email=$mail&code=$code"
        );
    
    $text  = "Du har begärt en länk för att kunna byta lösenord på ditt konto.<br>\n";
    $text .= "<a href='$url'>Allt du behöver göra är att klicka på den här länken och sedan kan du byta lösenord.</a><br>\n";
    
    BerghemMailer::send(null, $mail, $user->Name, $text, "Byta Lösenord", BerghemMailer::DaysAutomatic);
}