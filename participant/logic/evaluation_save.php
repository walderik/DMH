<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registration = Registration::loadById($_POST['Id']);
    if ($registration->hasDoneEvaluation()) {
        header('Location: ../index.php');
        exit;
    }
    $registration->EvaluationDone = 1;
    $registration->update();
    
    $evaluation = Evaluation::newFromArray($_POST);
    $evaluation->LarpId = $current_larp->Id;
    $evaluation->create();
}
header('Location: ../index.php');
