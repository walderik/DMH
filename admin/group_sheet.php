<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

include_once 'header.php';

require_once $root . '/pdf/group_sheet_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$no_history = false;

if (isset($_GET['id'])) {
    $groupId = $_GET['id'];
    $group = Group::loadById($groupId);
    if (empty($group)) {
        header('Location: index.php'); // Gruppen finns inte
        exit;
    } elseif (!$group->isRegistered($current_larp)) {
        header('Location: index.php'); // Gruppen är inte anmäld
        exit;
    }
} else {
    if (isset($_GET['no_history'])) $no_history = true;
}

$pdf = new Group_PDF();
$title = (empty($group)) ? 'Alla Grupper' : ('Gruppblad '.$group->Name) ;
$pdf->SetTitle(encode_utf_to_iso($title));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$subject = (empty($group)) ? 'ALLA' : $group->Name;
$pdf->SetSubject(encode_utf_to_iso($subject));
$all_info = (isset($_GET['all_info'])) ? true : false;

if (empty($group)) {
    $pdf->all_group_sheets($current_larp, $all_info, $no_history);
} else {
    $pdf->new_group_sheet($group, $current_larp, $all_info);
}

$pdf->Output();


