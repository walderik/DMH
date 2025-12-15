<?php
include_once '../header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registrationId = $_POST['RegistrationId'];
    $registration = Registration::loadById($registrationId);
    if (isset($registration)) {
        $oldAmount = $registration->AmountToPay;
        $oldNotComing = $registration->isNotComing();
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
        
        
        //Avbokning
        if (!$oldNotComing && $registration->isNotComing()) {
            $person = $registration->getPerson();
            //brev och telegram ska sättas till icke-godkänd
            $telegrams = $person->getTelegramsAtLarp($current_larp);
            foreach ($telegrams as $telegram) {
                $telegram->Approved = 0;
                $telegram->update();
            }
            $letters = $person->getLettersAtLarp($current_larp);
            foreach ($letters as $letter) {
                $letter->Approved = 0;
                $letter->update();
            }
            BerghemMailer::send_unregistration_mail($registration);
        }  elseif ($oldNotComing && !$registration->isNotComing()) {  //Av-avbokning
            $person = $registration->getPerson();
            BerghemMailer::send_ununregistration_mail($registration);
        }
        

        if ($oldAmount != $registration->AmountToPay) BerghemMailer::send_updatedpayment_mail($registration, $current_person->Id);
        
        if (isset($_POST['Referer']) && $_POST['Referer']!="") {
            header('Location: ' . $_POST['Referer']);
            exit;
        }
        
        header('Location: ../registered_persons.php');
        exit;
        
    }
    
}
header('Location: ../index.php');
