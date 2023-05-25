<?php

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registration = Registration::loadByIds($_POST['PersonId'], $current_larp->Id);
    //Hitta rätt Guardian
    if (isset($_POST['GuardianInfo'])) {
        $guardianInfo = $_POST['GuardianInfo'];
        
        //Kolla om man har angett ett personnummer så att det innehåller ett streck
        if (startsWithNumber($guardianInfo)) {
            if (strpos($guardianInfo, "-") == false) {
                $guardianInfo = substr($guardianInfo, 0, 8) . "-" . substr($guardianInfo, 8);
            }
        }
        
        
        
        $guardian = Person::findGuardian($guardianInfo, $current_larp);

        if (!empty($guardian)) {
            $registration->GuardianId = $guardian->Id;
            $registration->update();
            
            
            if ($guardian->UserId != $current_user->Id) {
                $person=$registration->getPerson();
                BerghemMailer::send_guardian_mail($guardian, $person, $current_larp);
            }
        }
        
        
        
    }

}
header('Location: ../index.php');



