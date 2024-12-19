<?php

include_once 'header.php';

if (!$current_larp->isIntriguesReleased()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $intriguePdfId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$pdf = Intrigue_Pdf::loadById($intriguePdfId); 

if (empty($pdf)) {
    header('Location: index.php');
    exit;
}

if ($pdf->getIntrigue()->LarpId != $current_larp->Id) {
    header('Location: index.php');
    exit;
}

if (!$pdf->mayView($current_person)) {
    header('Location: index.php');
    exit;
}

$filename = $pdf->Filename;

header('Content-type: application/pdf'); 
header("Content-Disposition: inline; filename='$filename'");
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

echo $pdf->FileData;
