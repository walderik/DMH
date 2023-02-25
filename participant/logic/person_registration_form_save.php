<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';

echo '$_POST :<br>';
print_r($_POST);

echo "<br /><br /><br />";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    $mainRole = $_POST['IsMainRole'];
    echo "Mainrole = x" . $mainRole."x <br /><br /><br />";
    if ($operation == 'insert') {
        // Skapa en ny registrering
        $registration = Registration::newFromArray($_POST);
        $registration->AmountToPay = PaymentInformation::getPrice(date("Y-m-d"), 
            Person::loadById($registration->PersonId)->getAgeAtLarp($current_larp->StartDate));
        $registration->PaymentReference = $registration->LARPId . $registration->PersonId;

        $now = new Datetime();
        $registration->RegisteredAt = date_format($now,"Y-m-d H:i:s");
        $registration->create();
        
        $roleIdArr = $_POST['roleId'];
        
        # TODO Hantera om man inte anmäler någon roll
        
        foreach ($roleIdArr as $roleId) {
            $larp_role = LARP_Role::newWithDefault();
            $larp_role->RoleId = $roleId;
            $larp_role->LARPId = $current_larp->Id;
            if ($mainRole == $roleId) {
                echo "Main role: " . $larp_role->Id;
                $larp_role->IsMainRole = 1;
            }
            $larp_role->create();            
        }
        $intrigueTypeRoleArr = $_POST['IntrigueTypeId'];

        foreach ($intrigueTypeRoleArr as  $key => $intrigueTypeRole) {
            $larp_role = LARP_Role::loadByIds($key, $current_larp->Id);
            $larp_role->saveAllIntrigueTypes($intrigueTypeRole);
        }
        send_registration_mail($registration);
    } else {
        echo $operation;
    }
    
     header('Location: ../index.php');
}


function send_registration_mail(Registration $registration) {
    $person = $registration->getPerson();
    $mail = $person->Email;
    
    $larp = $registration->getLARP();
    $roles = $person->getRolesAtLarp($larp);
    
//     $campaign = 
    
    $text  = "Du har nu anmält att du ska vara med i lajvet $larp->Name<br>\n";
    $text .= "För att vara helt anmäld måste du nu betala $registration->AmountToPay SEK till xxxxxxxxxx ange referens: <b>$registration->PaymentReference</b>.<br>\n";
    $text .= "Du måste också vara medlem i Berghems vänner. Om du inte redan är medlem kan du bli medlem <b><a href='https://ebas.sverok.se/signups/index/5915' target='_blank'>här</a></b><br>\n";
    $text .= "<br>\n";
    $text .= "Vi kommer att gå igenom karaktärerna du har anmält och godkänna dom för spel.<br>\n";
    $text .= "<br>\n";
    $text .= "De karaktärer du har anmält är:<br>\n";
    foreach ($roles as $role) {
        $text .= '* '.$role->Name;
        if ($role->isMain($larp)) {
            $test .= " Huvudkaraktär";
        } elseif ($role->IsNPC) {
            $test .= " NPC";
        }
        $text .= "<br>\n";
    }

    DmhMailer::send($mail, $person->Name, $text, "Bekräftan av anmälan till ".$larp->Name);
}