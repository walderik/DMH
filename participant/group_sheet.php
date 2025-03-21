<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

include_once 'header.php';

require_once $root . '/pdf/group_sheet_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $groupId = $_GET['id'];
    $group = Group::loadById($groupId);
}

if (empty($group)) {
    header('Location: index.php'); // Gruppen finns inte
    exit;
}


if (!$current_person->isMember($group) && !$current_person->isGroupLeader($group)) {
    header('Location: index.php?error=no_member'); //Inte medlem i gruppen
    exit;
}

if (!$group->isRegistered($current_larp)) {
    header('Location: index.php?error=not_registered'); //Gruppen är inte anmäld
    exit;
}

$pdf = new Group_PDF();
$title = (empty($group)) ? 'Alla Grupper' : ('Gruppblad '.$group->Name) ;
$pdf->SetTitle(encode_utf_to_iso($title));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$subject = (empty($group)) ? 'ALLA' : $group->Name;
$pdf->SetSubject(encode_utf_to_iso($subject));

if (empty($group)) {
    $pdf->all_group_sheets($current_larp);
} else {
    $pdf->new_group_sheet($group, $current_larp, false);
}

$pdf->Output();

