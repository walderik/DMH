<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/house_info.php';
require_once $root . '/includes/init.php';

if ($_SERVER["REQUEST_METHOD"] != "GET" || !isset($_GET['houseId'])) {
    header('Location: ../../admin/index.php');
    exit;
}

if (isset($_GET['houseId'])) {
    $house = House::loadById($_GET['houseId']);
}


$pdf = new HouseInfo();

$pdf->init($current_person->Name, $house->Name, $current_larp->Name, false);


if (!empty(trim($house->NotesToUsers))) {
    // add a page
    $pdf->AddPage();
    $pdf->printInfo($house->Name, $house->NotesToUsers);
}

// close and output PDF document
$pdf->Output($house->Name.'.pdf', 'I');
