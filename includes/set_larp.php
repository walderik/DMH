<?php

session_start();

if (!isset($_SESSION['is_loggedin'])) {
    header('Location../index.php');
    exit;
}

// Now we check if the data from the larp select form was submitted, isset() will check if the data exists.
if ( !isset($_POST['larp']) ) {
    // Could not get the data that should have been sent.
    exit('Du måste välja ett lajv!');
}

$_SESSION['larp'] = $_POST['larp'];

// Redirect to the login page:
header('Location: ../participant/index.php');