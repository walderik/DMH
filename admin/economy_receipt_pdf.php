<?php
# Läs mer på http://www.fpdf.org/

# http://localhost/regsys/admin/group_sheet.php?id=4

global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

include_once 'header.php';

require_once $root . '/pdf/receipt_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $bookkeepingId = $_GET['id'];
    $bookkeeping = Bookkeeping::loadById($bookkeepingId);
    if (empty($bookkeeping)) {
        header('Location: index.php'); // Posten finns inte
        exit;
    } elseif ($bookkeeping->LarpId != $current_larp->Id) {
        header('Location: index.php'); // Tillhr inte aktuellt lajv
        exit;
    }
}

$pdf = new Receipt_PDF();
$pdf->SetTitle('Kvitto');
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('SpecialElite','');
$pdf->SetSubject('Kvitto');
$pdf->nytt_kvitto($bookkeeping);

$pdf->Output();


