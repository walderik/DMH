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
        case "user_updated":
            $output = "Kontot har uppdaterats.";
            break;
        case "activated":
            $output = "Kontot är aktiverat. Du kan nu logga in.";
            break;
        case "already_activated":
            $output = "Kontot är redan aktiverat. Du logga in.";
            break;
        case "email_sent":
            $output = "Ett mail med instruktioner har skickats till dig. Om det inte kommer om några sekunder har det kanske hamnat i din spam-låda.";
            break;
        case "contact_email_sent":
            $output = "Ett mail har skickats till dig och din kontakt. Om det inte kommer om några sekunder har det kanske hamnat i din spam-låda.";
            break;
        case "image_uploaded":
            $output = "Bilden har laddats upp.";
            break;
        case "image_deleted":
            $output = "Bilden har raderats.";
            break;
        case "person_deleted":
            $output = "Deltagare har raderats.";
            break;
        case "role_deleted":
            $output = "Karaktären har raderats.";
            break;
        case "group_deleted":
            $output = "Gruppen har raderats.";
            break;
        case "registration_done":
            $output = "Anmälan har registrerats.";
            exit;
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
            $output = "Användaren eller lösenord är fel";
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
        case "enterBothNames":
            $output = "Ange både för- och efternamn";
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
        case "no_role_chosen":
            $output = "Du måste välja minst en karaktär";
            break;
        case "too_young_for_larp":
            $output = "Deltagaren är för ung för att få vara med på lajvet";
            break;
        case "activation_not_possible":
            $output = "Kontot är redan aktiverat eller finns inte.";
            break;
        case "registration_not_open":
            $output = "Anmälan är inte öppen nu.";
            break;
        case "no_member":
            $output = "Du är inte medlem i gruppen.";
            break;
        case "group_not_registered":
            $output = "Gruppen är inte anmäld.";
            break;
        case "SSN_already_in_use":
            $output = "En deltagare med det personnumret finns redan i systemet.";
            break;
        case "no_role_may_register":
            $output = "Du har ingen karaktär som går att anmäla eftersom gruppen den är med i inte är anmäld än.";
            break;
        case "may_not_edit_role":
            $output = "Karaktären får inte ändras.";
            break;
        case "no_email":
            $output = "Ingen korrekt epostadress angiven.";
            break;
        case "no_text":
            $output = "Ingen text angiven.";
            break;
        case "image_format":
            $output = "Fel format på filen. Var vänlig välj en fil med ett av de godkända formaten (jpg, gif, png).";
            break;
        case "image_size":
            $output = "Filen är för stor. Minska ner den i ett bildhanteringsprogram.";
            break;
        case "group_cannot_be_deleted":
            $output = "Gruppen kan inte raderas eftersom den har varit med på ett lajv.";
            break;
        default:
            $output = "Okänt fel: ". $code;
    }
    return $output;
}

