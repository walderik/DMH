<?php

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

if (!$current_larp->mayRegister()) {
    header('Location: index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $operation = $_POST['operation'];

    
    if ($operation == 'insert') {

        if (empty($_POST['roleId'])) {
            header('Location: ../index.php?error=no_role_chosen');
            exit;
        }
        
        //Redan anmäld
        if (!empty(Registration::loadByIds($_POST['PersonId'], $current_larp->Id))) {
            header('Location: ../index.php');
            exit; 
        }
        
        //Redan på reservlistan
        if (!empty(Reserve_Registration::loadByIds($_POST['PersonId'], $current_larp->Id))) {
            header('Location: ../index.php');
            exit;
        }
        
        if (isset($_POST['IsMainRole'])) $mainRole = $_POST['IsMainRole'];
        
        
        if ($current_larp->isFull() || Reserve_Registration::isInUse($current_larp) || $current_larp->isPastLatestRegistrationDate()) {
            //Sätt på reservlistan
            $reserve_registration = Reserve_Registration::newFromArray($_POST);

            //Spara dagar man inte kommer att vara med
            if (isset($_POST['ChooseParticipationDates'])) $reserve_registration->LarpPartNotAttending = Registration::calculateDaysNotComing($current_larp, $_POST['ChooseParticipationDates']);
            if (empty($reserve_registration->LarpPartNotAttending)) $reserve_registration->LarpPartNotAttending = null;
            
            $now = new Datetime();
            $reserve_registration->RegisteredAt = date_format($now,"Y-m-d H:i:s");
            
            
            
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
                    $reserve_registration->GuardianId = $guardian->Id;
                }
            }
            
            
            $reserve_registration->create();
            if (isset($_POST['OfficialTypeId'])) {
                $reserve_registration->saveAllOfficialTypes($_POST['OfficialTypeId']);
            }
            
            
            if ($current_larp->NoRoles == 0) {
                
            } elseif ($current_larp->NoRoles == 1) {
                $roleId = $_POST['roleId'];
                $role = Role::loadById($roleId);
                $role->UserMayEdit = 0;
                $role->update();
                
                $larp_role = Reserve_LARP_Role::newWithDefault();
                $larp_role->RoleId = $role->Id;
                $larp_role->LARPId = $current_larp->Id;
                $larp_role->PersonId = $role->PersonId;
                if (isset($_POST['IntrigueIdeas'])) $larp_role->IntrigueIdeas = $_POST['IntrigueIdeas'];
                $larp_role->IsMainRole = 1;
                
                $larp_role->create();
                
                if (isset($_POST['IntrigueTypeId'])) {
                    $larp_role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
                }
                
                
            } else {
                
                /* TODO spara anmälan av karaktärer */
                /*
                if (!isset($mainRole) || is_null($mainRole)) $mainRole = array_key_first($roleIdArr);
                
                $roleIdArr = $_POST['roleId'];
                
                foreach ($roleIdArr as $roleId) {
                    $role = Role::loadById($roleId);
                    $role->UserMayEdit = 0;
                    $role->update();
                    
                    $larp_role = Reserve_LARP_Role::newWithDefault();
                    $larp_role->RoleId = $roleId;
                    $larp_role->LARPId = $current_larp->Id;
                    if ($mainRole == $roleId) {
                        
                        $larp_role->IsMainRole = 1;
                    }
                    $larp_role->create();
                    
                    if (isset($_POST['IntrigueTypeId'])) {
                        $larp_role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
                    }
                    
                    
                }
                */
            }
            
            BerghemMailer::send_reserve_registration_mail($reserve_registration);
            
            
            if (!empty($reserve_registration->GuardianId)) {
                $guardian = Person::loadById($reserve_registration->GuardianId);
                $person = $reserve_registration->getPerson();
                if ($guardian->UserId != $current_user->Id) {
                    
                    BerghemMailer::send_guardian_mail($guardian, $person, $current_larp);
                }
            }
            
            
        }
        else {
            // Skapa en ny registrering
            $registration = Registration::newFromArray($_POST);
            $person = Person::loadById($registration->PersonId);
            $age = $person->getAgeAtLarp($current_larp);
            $registration->AmountToPay = PaymentInformation::getPrice(date("Y-m-d"), $age, $current_larp, $registration->FoodChoice);
                
            $registration->PaymentReference = $registration->createPaymentReference();
    
            
            //Spara vilka dagar man inte kommer att vara med
            if (isset($_POST['ChooseParticipationDates'])) {
                $registration->LarpPartNotAttending = Registration::calculateDaysNotComing($current_larp, $_POST['ChooseParticipationDates']);
            } 
            if (empty($registration->LarpPartNotAttending)) $registration->LarpPartNotAttending = null;
            
            if (isset($registration->LarpPartNotAttending)) $registration->LarpPartAcknowledged = 0;
            
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
            
            if (isset($_POST['OfficialTypeId'])) {
                $registration->saveAllOfficialTypes($_POST['OfficialTypeId']);
            }
            
    
    
            if ($current_larp->NoRoles == 0) {
                
            } elseif ($current_larp->NoRoles == 1) {
                $roleId = $_POST['roleId'];
                $role = Role::loadById($roleId);
                $role->UserMayEdit = 0;
                $role->update();
                
                $larp_role = LARP_Role::newWithDefault();
                $larp_role->RoleId = $role->Id;
                $larp_role->LARPId = $current_larp->Id;
                $larp_role->PersonId = $role->PersonId;
                if (isset($_POST['IntrigueIdeas'])) $larp_role->IntrigueIdeas = $_POST['IntrigueIdeas'];
                $larp_role->IsMainRole = 1;

                $larp_role->create();
                
                if (isset($_POST['IntrigueTypeId'])) {
                    $larp_role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
                }
                
                
            } else {
            
                /* TODO spara anmälan av flera karaktärer */
                /*
                if (!isset($mainRole) || is_null($mainRole)) $mainRole = array_key_first($roleIdArr);
        

        
                foreach ($roleIdArr as $roleId) {
                    $role = Role::loadById($roleId);
                    $role->UserMayEdit = 0;
                    $role->update();

                    $larp_role = LARP_Role::newWithDefault();
                    $larp_role->RoleId = $role->Id;
                    $larp_role->LARPId = $current_larp->Id;
                    $larp_role->PersonId = $role->PersonId;
                    if ($mainRole == $roleId) {
                        
                        $larp_role->IsMainRole = 1;
                    }
                    $larp_role->create();  
                    
                    if (isset($_POST['IntrigueTypeId'])) {
                        $larp_role->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
                    }
                    
                }
                */
            }
            
            BerghemMailer::send_registration_mail($registration);
            
    
            if (!empty($registration->GuardianId)) {           
                $guardian = Person::loadById($registration->GuardianId);
                if ($guardian->UserId != $current_user->Id) {
    
                    BerghemMailer::send_guardian_mail($guardian, $person, $current_larp);
                }
            }
        } 
    }
    
    header('Location: ../index.php');
    exit;
}






