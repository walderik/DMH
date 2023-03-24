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
        if (isset($_POST['IsMainRole'])) $mainRole = $_POST['IsMainRole'];
        // Skapa en ny registrering
        $registration = Registration::newFromArray($_POST);
        $person = Person::loadById($registration->PersonId);
        $age = $person->getAgeAtLarp($current_larp);
        $registration->AmountToPay = PaymentInformation::getPrice(date("Y-m-d"), $age);
            
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
                $registration->Guardian = $guardian->Id;
            }
        }


        $registration->create();
        
        $registration->saveAllOfficialTypes($_POST);
        
        $roleIdArr = $_POST['roleId'];

        
        if (!isset($mainRole) || is_null($mainRole)) $mainRole = array_key_first($roleIdArr);



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
        send_registration_mail($registration);
        

        if (!empty($registration->Guardian)) {           
            $guardian = Person::loadById($registration->Guardian);
            if ($guardian->UserId != $current_user->Id) {

                BerghemMailer::send_guardian_mail($guardian, $person, $current_larp);
            }
        }
    } 
    
    header('Location: ../index.php');
    exit;
}

function startsWithNumber($string) {
    return strlen($string) > 0 && ctype_digit(substr($string, 0, 1));
}

function send_registration_mail(Registration $registration) {
    $person = $registration->getPerson();
    
    $larp = $registration->getLARP();
    $roles = $person->getRolesAtLarp($larp);
    
    $campaign = $larp->getCampaign();
    
    $text  = "Du har nu anmält att du ska vara med i lajvet $larp->Name<br>\n";
    $text .= "För att vara helt anmäld måste du nu betala $registration->AmountToPay SEK till $campaign->Bankaccount ange referens: <b>$registration->PaymentReference</b>.<br>\n";
    if (!$person->isMember($larp)) {
        $text .= "Du måste också vara medlem i Berghems vänner. Om du inte redan är medlem kan du bli medlem <b><a href='https://ebas.sverok.se/signups/index/5915' target='_blank'>här</a></b><br>\n";
    }
    $text .= "<br>\n";
    $text .= "Vi kommer att gå igenom karaktärerna du har anmält och godkänna dom för spel.<br>\n";
    $text .= "<br>\n";
    $text .= "De karaktärer du har anmält är:<br>\n";
    $text .= "<br>\n";
    foreach ($roles as $role) {
        $text .= '* '.$role->Name;
        if ($role->isMain($larp)) {
            $text .= " - Din huvudkaraktär";
        } elseif ($role->IsNPC) {
            $text .= " - En NPC";
        }
        if (isset($role->GroupId)) {
            $group = $role->getGroup();
            $text .= ", medlem i $group->Name";
            send_registration_information_mail_to_group($role, $group, $larp);
        }
        
        $text .= "<br>\n";
    }

    BerghemMailer::send($person->Email, $person->Name, $text, "Bekräftan av anmälan till $larp->Name");
}

function send_registration_information_mail_to_group(Role $role, Group $group, Larp $larp) {
    $admin_person = $group->getPerson();
    
    $text  = "$role->Name är anmäld till $group->Name.<br>\n";
    $text .= "Det gäller lajvet $larp->Name.<br>\n";
    $text .= "<br>\n";
    $text .= "Du kan manuellt ta bort rollen ur gruppen om det är fel.";
    $text .= "<br>\n";
    
    BerghemMailer::send($admin_person->Email, $admin_person->Name, $text, "Anmälan till $group->Name i $larp->Name");
}



