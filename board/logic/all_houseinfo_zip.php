<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];


require_once $root . '/pdf/house_info.php';
include_once '../header.php';





$file = tempnam($root . '/tmp', 'zip');
register_shutdown_function('unlink', $file);
$zipname = encode_utf_to_iso("Husbrev.zip");
$zip = new ZipArchive;
$zip->open($file, ZipArchive::OVERWRITE);


//Skapa pdf med alla vanliga verifikationer
$houses = House::getAllHouses();

foreach ($houses as $house) {
    if (!empty(trim($house->NotesToUsers))) {
        $pdf = new HouseInfo();
        $pdf->init($current_person->Name, $house->Name, '', false);
        $pdf->AddPage();
        $pdf->printInfo($house->Name, $house->NotesToUsers);
        $zip->addFromString($house->Name.'.pdf',$pdf->Output($house->Name.'.pdf', 'S'));
    }
}

$zip->close();

///Then download the zipped file.
header('Content-Type: application/zip');
header('Content-disposition: attachment; filename='.$zipname);
header('Content-Length: ' . filesize($file));
readfile($file);