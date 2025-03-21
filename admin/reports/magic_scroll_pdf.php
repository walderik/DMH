<?php 
global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];


require_once $root . '/pdf/magic_scroll_pdf.php';
include_once '../header.php';

$arrayOfSpells = Magic_Spell::allByCampaign($current_larp);
$pdf = new MagicScroll_PDF();
$pdf->SetTitle(encode_utf_to_iso('Skrollor'));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator(encode_utf_to_iso('Omnes Mundi'));
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject(encode_utf_to_iso('Skrollor'));


foreach ($arrayOfSpells as $spell)  {
    $pdf->PrintScroll($spell, $current_larp->Name);
}

$pdf->Output();
