<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $pdfId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$pdf = Image::loadById($pdfId);

if (empty($pdf) || ($pdf->file_mime != "application/pdf")) {
    header('Location: index.php');
    exit;
}



$filename = $pdf->file_name;

header('Content-type: application/pdf');
header("Content-Disposition: inline; filename='$filename'");
header('Content-Transfer-Encoding: binary');
header('Accept-Ranges: bytes');

echo $pdf->file_data;
