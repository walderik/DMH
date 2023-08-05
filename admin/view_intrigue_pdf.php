<?php

include_once 'header.php';

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

$filename = $pdf->Filename;

header('Content-type: application/pdf'); 
header("Content-Disposition: inline; filename='$filename'");
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

echo $pdf->FileData;
