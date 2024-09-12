<?php
include_once '../header.php';

if ($current_larp->RegistrationOpen == 0) {
    $current_larp->RegistrationOpen = 1;
    $current_larp->VisibleToParticipants = 1;
    $current_larp->update();
}
else {
    $current_larp->RegistrationOpen = 0;
    $current_larp->update();  
}

header('Location: ../index.php');
