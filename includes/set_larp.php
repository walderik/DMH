<?php

session_start([
    'cookie_lifetime' => 86400,
]);


if (!isset($_SESSION['is_loggedin'])) {
    header('Location../index.php');
    exit;
}

if (isset($_POST['larp'])) $larp = $_POST['larp'];
elseif (isset($_GET['larp'])) $larp = $_GET['larp'];

// Now we check if the data from the larp select form was submitted, isset() will check if the data exists.
if ( !isset($larp) ) {
    // Could not get the data that should have been sent.
    exit('Du måste välja ett lajv!');
}

$_SESSION['larp'] = $larp;

if (isset($_SESSION['admin']) || AccessControl::hasAccessLarp($current_user, $larp)) header('Location: ../admin/index.php');
else header('Location: ../participant/index.php');