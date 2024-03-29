<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";


require_once $root . '/pdf/magic_scroll_pdf.php';
include_once '../header.php';

$arrayOfSpells = Magic_Spell::allByCampaign($current_larp);
$pdf = new MagicScroll_PDF();
$pdf->SetTitle(utf8_decode('Skrollor'));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator(utf8_decode('Omnes Mundi'));
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject(utf8_decode('Skrollor'));

$pdf->PrintScroll($arrayOfSpells[0], $current_larp->Name);


foreach ($arrayOfSpells as $spell)  {
    $pdf->PrintScroll($spell, $current_larp->Name);
}

$pdf->Output();
