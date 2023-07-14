<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/titledeed_pdf.php';
include_once '../header.php';

$arrayOfTitledeeds = Titledeed::allByCampaign($current_larp);
$pdf = new TITLEDEED_PDF();
$pdf->SetTitle('Ägarbevis');
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject('Alla ägarbevis');
foreach ($arrayOfTitledeeds as $titledeed)  {
    $pdf->new_titledeed($titledeed);
}

$pdf->Output();
