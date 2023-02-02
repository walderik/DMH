<?php
$error_message = "";
$message_message = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['error'])) {
        $error_code = $_GET['error'];
        $error_message = getErrorText($error_code);
    }
    if (isset($_GET['message'])) {
        $message_code = $_GET['message'];
        $message_message = getMessageText($message_code);
    }
}

function getMessageText($code) {
    $output = "";
    
    switch ($code) {
        case "user_created":
            $output = "Kontot har skapats. Aktivera det för att kunna logga in. Följ anvisningarna i mailet vi skickade till dig.";
            break;
        default:
            $output = "Okänt meddelande: ". $code;
    }
    return $output;
}

function getErrorText($code) {
    $output = "";
    
    switch ($code) {
        case "stmtfailed":
            $output = "Kopplingen till databasen misslyckades. Kontakta administratören.";
            break;
        case "userNotFound":
            $output = "Använaren saknas";
            break;
        case "accountNotActivated":
            $output = "Kontot är inte aktiverat";
            break;
        case "emptyInput":
            $output = "Fyll i alla fält";
            break;
        case "invalidEmail":
            $output = "Ogiltig epostadress";
            break;
        case "passwordNotMatch":
            $output = "Lösenorden är inte lika";
            break;
        case "invalidPasswordLength":
            $output = "Lösenordet måste vara minst 5 och max 20 tecken.";
            break;
        case "userExists":
            $output = "Användaren finns redan";
            break;
        case "noSubmit":
            $output = "Försök igen";
            break;
        case "no_person":
            $output = "Du måste registrera en deltagare först";
            break;
        case "no_group":
            $output = "Du måste registrera en grupp först";
            break;
        case "no_role":
            $output = "Du måste registrera en karaktär först";
            break;
        default:
            $output = "Okänt fel: ". $code;
    }
    return $output;
}

