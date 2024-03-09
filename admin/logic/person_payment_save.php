<?php
include_once '../header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registrationId = $_POST['RegistrationId'];
    $registration = Registration::loadById($registrationId);
    if (isset($registration)) {
        $registration->AmountToPay = $_POST['AmountToPay'];
        $registration->AmountPayed = $_POST['AmountPayed'];
        
        $payed = $_POST['Payed'];
        if (isset ($payed) && $payed != "") {
            $registration->Payed = $payed;
        }
 
        $registration->NotComing = $_POST['NotComing'];
        $registration->NotComingReason = $_POST['NotComingReason'];
        $registration->IsToBeRefunded = $_POST['IsToBeRefunded'];
        $registration->RefundAmount = $_POST['RefundAmount'];
        if (isset($_POST['LarpPartAcknowledged'])) $registration->LarpPartAcknowledged = $_POST['LarpPartAcknowledged'];
        
        $refundDate = $_POST['RefundDate'];
        if (isset ($refundDate) && $refundDate != "") {
            $registration->RefundDate = $refundDate;
        }
        
        $registration->update();
        
        if ($registration->isNotComing()) {
            //Kontrollera om användaren har någon annan deltagare som kommer, annars ska brev och telegram sättas till icke-godkänd
            $user = $registration->getPerson()->getUser();
            if (!$user->isComing($current_larp)) {
                $telegrams = $user->getTelegramsAtLarp($current_larp);
                foreach ($telegrams as $telegram) {
                    $telegram->Approved = 0;
                    $telegram->update();
                }
                $letters = $user->getLettersAtLarp($current_larp);
                foreach ($letters as $letter) {
                    $letter->Approved = 0;
                    $letter->update();
                }
            }
        }

        if (isset($_POST['Referer']) && $_POST['Referer']!="") {
            header('Location: ' . $_POST['Referer']);
            exit;
        }
        
        header('Location: ../registered_persons.php');
        exit;
        
    }
    
}
header('Location: ../index.php');
