<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $registration = Registration::loadById($_POST['Id']);
    if ($registration->hasDoneEvaluation()) {
        header('Location: ../index.php');
        exit;
    }
    
    $evaluation = Evaluation::newFromArray($_POST);
    $evaluation->LarpId = $current_larp->Id;
    $evaluation->create();

    $registration->EvaluationDone = 1;
    $registration->update();
    
}
header('Location: ../index.php');
