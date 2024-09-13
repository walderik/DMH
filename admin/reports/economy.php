<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$name = 'Redovisning av '.$current_larp->Name;

$pdf = new Report_TCP_PDF();

$pdf->init($current_user->Name, $name, $current_larp->Name, false);

$bookkeepings = Bookkeeping::allFinished($current_larp);

    
$rows = array();
$header = array('Verifikation','Datum','Rubrik','Konto', 'Summa');
$sum = 0;
foreach($bookkeepings as $bookkeeping) {
    $rows[] = array($bookkeeping->Number, $bookkeeping->Date, 
        $bookkeeping->Headline, $bookkeeping->getBookkeepingAccount()->Name, number_format((float)$bookkeeping->Amount, 2, ',', ''));
    $sum += $bookkeeping->Amount;
}


$invoices = Invoice::getAllNormalInvoices($current_larp);
foreach ($invoices as $invoice) {
    $rows[] = array("Faktura ".$invoice->Number, $invoice->PayedDate,
        $invoice->Recipient, "Fakturor", number_format((float)$invoice->FixedAmount, 2, ',', ''));
    $sum += $invoice->FixedAmount;
}

$registration_fees = Registration::totalFeesPayed($current_larp);
$sum += $registration_fees;

$rows[] = array('', substr($current_larp->EndDate,0,10), 'Deltagaravgifter', '', number_format((float)$registration_fees, 2, ',', ''));

$returned_fees = Registration::totalFeesReturned($current_larp);
$sum -= $returned_fees;
$rows[] = array('', substr($current_larp->EndDate,0,10), 'Återbetalade deltagaravgifter', '', ' '.number_format((float)(0-$returned_fees), 2, ',', ''));

$rows[] = array('', '', 'Summa', '', number_format((float)$sum, 2, ',', ''));

// add a page
$pdf->AddPage();
// print table
$pdf->Table($name, $header, $rows);



// close and output PDF document
$pdf->Output($name.'.pdf', 'I');