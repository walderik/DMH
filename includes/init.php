<?php


if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
    ]);
}
// All kod som skall köras först på varje sida gemensamt oavsett om det rör admin-header eller annan header
global $current_user, $current_person, $current_larp, $root;


include_once $root . '/includes/all_includes.php';

// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['is_loggedin'])) {
    header('Location: ../index.php');
    exit;
}

$current_user = User::loadById($_SESSION['id']);
if (isset($_SESSION['PersonId'])) $current_person = Person::loadById($_SESSION['PersonId']);
if (!isset($current_user) or is_null($current_user)) {
    header('Location: ../index.php');
    exit;
}
$current_user->Password = null;

// If the user has not chosen a larp, and is not on the choose larp page or the larp admin pages
$url = $_SERVER['REQUEST_URI'];

//Man måste välja person först
if (!isset($current_person) && strpos($url, "participant/index.php") == false) {
    header('Location: ../participant/index.php');
    exit;
}

//Och lajv 
//TODO man borde kunna gå till OM admin, styrelse och hus & läger utan att ha valt lajv.
if (!isset($_SESSION['larp']) && strpos($url, "choose_larp.php") == false && strpos($url, "participant/index.php") == false && strpos($url, "larp_admin.php") == false && strpos($url, "larp_form.php") == false) {
    header('Location: ../participant/index.php');
    exit;
}

if (isset($_SESSION['larp'])) {
    $current_larp = LARP::loadById($_SESSION['larp']);
}

# Körs efter att current_user och current_larp är satt, så blir det bäst.
if (!Email::handleEmailQueue()) {
//     echo "<h1>Failing sending Email</h1>"; # Vad gör vi nu? Skicka felnotering till admin?
}


