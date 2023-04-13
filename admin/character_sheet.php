<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';
require_once $root . '/pdf/character_sheet_pdf.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}


if (isset($_GET['id'])) {
    $roleId = $_GET['id'];
    $role = Role::loadById($roleId);
    if (empty($role)) {
        header('Location: index.php'); // Karaktären finns inte
        exit;
    } elseif (!$role->isRegistered($current_larp)) {
        header('Location: index.php'); // Karaktären är inte anmäld
        exit;
    }
}

$pdf = new CharacterSheet_PDF();
$title = (empty($role)) ? 'Alla Karaktärer' : ('Karaktärsblad '.$role->Name) ;
$pdf->SetTitle(utf8_decode($title));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('Helvetica','');
$subject = (empty($role)) ? 'ALLA' : $role->Name;
$pdf->SetSubject(utf8_decode($subject));

if (empty($role)) {
    $pdf->all_character_sheets($current_larp);
} else {
    $pdf->new_character_sheet($role, $current_larp);
}

$pdf->Output();


// $doc = $pdf->Output('S');

// $attachments = ['Telegrammen' => $doc];
//BerghemMailer::send('Mats.rappe@yahoo.se', 'Admin', "Det här är alla telegrammen", "Alla Telegrammen som PDF", $attachments);