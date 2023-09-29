<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $roleId = $_GET['id'];

    $role = Role::loadById($roleId);
    
    $person = Person::loadById($role->PersonId);
    
    // Skapa en ny registrering
    $registration = Registration::newWithDefault();
    $registration->PersonId = $person->Id;
    $registration->LARPId = $current_larp->Id;
    $registration->HousingRequestId = HousingRequest::allActive($current_larp)[0]->Id;
    $registration->TypeOfFoodId = TypeOfFood::allActive($current_larp)[0]->Id;
    
    $age = $person->getAgeAtLarp($current_larp);
    $registration->AmountToPay = PaymentInformation::getPrice(date("Y-m-d"), $age, $current_larp, $registration->FoodChoice);
        
    $registration->PaymentReference = $registration->createPaymentReference();

    $now = new Datetime();
    $registration->RegisteredAt = date_format($now,"Y-m-d H:i:s");

 
    $registration->create();
    
    
    $larp_role = LARP_Role::newWithDefault();
    $larp_role->RoleId = $roleId;
    $larp_role->LARPId = $current_larp->Id;

    $larp_role->IsMainRole = 1;
    $larp_role->create();            
        
        
    BerghemMailer::send_registration_mail($registration);
    header('Location: ../index.php?message=registration_done');
    exit;

}
header('Location: ../index.php');




