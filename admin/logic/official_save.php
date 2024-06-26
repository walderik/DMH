<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type = $_POST['type'];
    if ($type == "single") {
        $registrationId = $_POST['Id'];
        $registration = Registration::loadById($registrationId);
        if (isset($registration)) {
            if ($registration->IsOfficial == 0) {
                $registration->IsOfficial = 1;
            }
            else {
                $registration->IsOfficial = 0;
            }
            $registration->update();
        }
    }
    else if ($type == "multiple") {

       $personIds = $_POST['PersonId'];
       foreach ($personIds as $personId) {
           $registration = Registration::loadByIds($personId, $current_larp->Id);
           if (isset($registration)) {
               $registration->IsOfficial = 1;
               $registration->update();
           }
       }
    }
    header('Location: ../officials.php');
    exit;
    
}
header('Location: ../index.php');