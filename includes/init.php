<?php



session_start();
// All kod som skall köras först på varje sida gemensamt oavsett om det rör admin-header eller annan header
global $current_user, $current_larp, $root;


include_once $root . '/includes/all_includes.php';

// If the user is not logged in redirect to the login page...
if (!isset($_SESSION['is_loggedin'])) {
    header('Location: ../index.php');
    exit;
}

$current_user = User::loadById($_SESSION['id']);
if (!isset($current_user) or is_null($current_user)) {
    header('Location: ../index.php');
    exit;
}
$current_user->Password = null;

// If the user has not chosen a larp, and is not on the choose larp page or the larp admin pages
$url = $_SERVER['REQUEST_URI'];

if (!isset($_SESSION['larp']) && strpos($url, "choose_larp.php") == false && strpos($url, "populater.php") == false && strpos($url, "larp_admin.php") == false && strpos($url, "larp_form.php") == false) {
    header('Location: ../participant/choose_larp.php');
    exit;
}

if (isset($_SESSION['larp'])) {
    $current_larp = LARP::loadById($_SESSION['larp']);
}

# Körs efter att current_user och current_larp är satt, så blir det bäst.
if (!handleEmailQueue()) {
//     echo "<h1>Failing sending Email</h1>"; # Vad gör vi nu? Skicka felnotering till admin?
}


function handleEmailQueue() {
    $current_queue = Email::allUnsent();
    if (empty($current_queue)) return;
    if (!Email::okToSendNow()) return;
//     echo "Send an email";
    $to_send = array_pop($current_queue); # Hämta äldsta mailet i kön
    return $to_send->sendNow();
}