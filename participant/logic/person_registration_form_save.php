<?php

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

if (!$current_larp->mayRegister()) {
    header('Location: index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $operation = $_POST['operation'];

    
    if ($operation == 'insert') {
        if (empty($_POST['roleId']) or count($_POST['roleId']) ==0) {
            header('Location: ../index.php?error=no_role_chosen');
            exit;
        }
        
        //Redan anmäld
        if (!empty(Registration::loadByIds($_POST['PersonId'], $current_larp->Id))) {
            header('Location: ../index.php');
            exit; 
        }

        if (isset($_POST['IsMainRole'])) $mainRole = $_POST['IsMainRole'];
        // Skapa en ny registrering
        $registration = Registration::newFromArray($_POST);
        $person = Person::loadById($registration->PersonId);
        $age = $person->getAgeAtLarp($current_larp);
        $registration->AmountToPay = PaymentInformation::getPrice(date("Y-m-d"), $age, $current_larp);
            
        $registration->PaymentReference = $registration->LARPId . $registration->PersonId;

        $now = new Datetime();
        $registration->RegisteredAt = date_format($now,"Y-m-d H:i:s");

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
            }
        }


        $registration->create();
        
        $registration->saveAllOfficialTypes($_POST);
        


        
        if (!isset($mainRole) || is_null($mainRole)) $mainRole = array_key_first($roleIdArr);

        $roleIdArr = $_POST['roleId'];

        foreach ($roleIdArr as $roleId) {
            
            $larp_role = LARP_Role::newWithDefault();
            $larp_role->RoleId = $roleId;
            $larp_role->LARPId = $current_larp->Id;
            if ($mainRole == $roleId) {
                
                $larp_role->IsMainRole = 1;
            }
            $larp_role->create();            
        }
        
        
        if (isset($_POST['IntrigueTypeId'])) {
            $intrigueTypeRoleArr = $_POST['IntrigueTypeId'];
    
            foreach ($intrigueTypeRoleArr as  $key => $intrigueTypeRole) {
                $larp_role = LARP_Role::loadByIds($key, $current_larp->Id);
                $larp_role->saveAllIntrigueTypes($intrigueTypeRole);
            }
        }
        BerghemMailer::send_registration_mail($registration);
        

        if (!empty($registration->GuardianId)) {           
            $guardian = Person::loadById($registration->GuardianId);
            if ($guardian->UserId != $current_user->Id) {

                BerghemMailer::send_guardian_mail($guardian, $person, $current_larp);
            }
        }
    } 
    
    header('Location: ../index.php');
    exit;
}




