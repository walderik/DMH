<?php
include_once '../header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $personId = $_POST['PersonId'];

    $personArr = $_POST;
    $personArr += ["Id" => $personId];
    
    $person = Person::loadById($personId);
    $person->setValuesByArray($personArr);
    $person->update();
        
    $person->deleteAllNormalAllergyTypes();
    $person->saveAllNormalAllergyTypes($_POST);
    
    
    $registrationId = $_POST['RegistrationId'];
    $registrationArr = $_POST;
    $registrationArr += ["Id" => $registrationId];
    
    $registration = Registration::loadById($registrationId);

    $registration->setValuesByArray($registrationArr);
    $registration->update();
    
    $registration->deleteAllOfficialTypes();
    if (isset($_POST['OfficialTypeId'])) {
        $registration->saveAllOfficialTypes($_POST['OfficialTypeId']);
    }
    


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
            if ($guardian->UserId != $current_user->Id) {
                BerghemMailer::send_guardian_mail($guardian, $person, $current_larp);
            }
        }
        else {
            $registration->GuardianId = null;
        }
        $registration->update();
  
        
        
        
    }

    if (isset($_POST['Referer']) && $_POST['Referer']!="") {
        header('Location: ' . $_POST['Referer']);
        exit;
    }
}
header('Location: ../index.php');