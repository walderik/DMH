<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$name = 'Redovisning av '.$current_larp->Name;

$pdf = new Report_PDF();

$pdf->SetTitle(utf8_decode($name));
$pdf->SetAuthor(utf8_decode($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(utf8_decode($name));

$bookkeepings = Bookkeeping::allByLARP($current_larp);

    
$rows[] = array('Verifikation','Datum','Rubrik','Konto', 'Summa');
$sum = 0;
foreach($bookkeepings as $bookkeeping) {
    $rows[] = array($bookkeeping->Number, $bookkeeping->Date, 
        $bookkeeping->Headline, $bookkeeping->getBookkeepingAccount()->Name, number_format((float)$bookkeeping->Amount, 2, ',', ''));
    $sum += $bookkeeping->Amount;
    

}
$registration_fees = Registration::totalFeesPayed($current_larp);
$sum += $registration_fees;

$rows[] = array('', substr($current_larp->EndDate,0,10), 'Deltagaravgifter', '', number_format((float)$registration_fees, 2, ',', ''));

$returned_fees = Registration::totalFeesReturned($current_larp);
$sum -= $returned_fees;
$rows[] = array('', substr($current_larp->EndDate,0,10), 'Återbetalade deltagaravgifter', '', ' '.number_format((float)(0-$returned_fees), 2, ',', ''));

$rows[] = array('', '', 'Summa', '', number_format((float)$sum, 2, ',', ''));

$pdf->new_report($current_larp, $name, $rows);


$pdf->Output();
