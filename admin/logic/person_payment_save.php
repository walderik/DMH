<?php
include_once '../header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registrationId = $_POST['RegistrationId'];
    $registration = Registration::loadById($registrationId);
    if (isset($registration)) {
        $oldAmount = $registration->AmountToPay;
        $registration->AmountToPay = $_POST['AmountToPay'];
        if (empty($registration->AmountToPay)) $registration->AmountToPay=0;
        $registration->AmountPayed = $_POST['AmountPayed'];
        if (empty($registration->AmountPayed)) $registration->AmountPayed=0;
        
        
        $payed = $_POST['Payed'];
        if (isset ($payed) && $payed != "") {
            $registration->Payed = $payed;
        }
        $registration->PaymentComment = $_POST['PaymentComment'];
        
        $registration->NotComing = $_POST['NotComing'];
        $registration->NotComingReason = $_POST['NotComingReason'];
        $registration->IsToBeRefunded = $_POST['IsToBeRefunded'];
        $registration->RefundAmount = $_POST['RefundAmount'];
        if (empty($registration->RefundAmount)) $registration->RefundAmount=0;
        if (isset($_POST['LarpPartAcknowledged'])) $registration->LarpPartAcknowledged = $_POST['LarpPartAcknowledged'];
        
        $refundDate = $_POST['RefundDate'];
        if (isset ($refundDate) && $refundDate != "") {
            $registration->RefundDate = $refundDate;
        } else {
            $registration->RefundDate = null;
        }
        
        $registration->update();
        
        if ($registration->isNotComing()) {
            $person = $registration->getPerson();
            $user = $person->getUser();
            //brev och telegram ska sättas till icke-godkänd
            $telegrams = $person->getTelegramsAtLarp($current_larp);
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


        if ($oldAmount != $registration->AmountToPay) BerghemMailer::send_updatedpayment_mail($registration);
        
        if (isset($_POST['Referer']) && $_POST['Referer']!="") {
            header('Location: ' . $_POST['Referer']);
            exit;
        }
        
        header('Location: ../registered_persons.php');
        exit;
        
    }
    
}
header('Location: ../index.php');
