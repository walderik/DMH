<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

$campaignId = $current_larp->CampaignId;

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$year = $_GET['year'];
if (isset($_GET['capaignId'])) $campaignId = $_GET['capaignId'];

$campaign = Campaign::loadById($campaignId);

$name = "Redovisning av $campaign->Name för $year";

$pdf = new Report_TCP_PDF();

$pdf->init($current_user->Name, $name, $campaign->Name, false);

$bookkeepings = Bookkeeping::allFinishedCampaign($campaign, $year);



$header = array('Ver','Datum','Rubrik','Konto', 'Summa');
$sum = 0;
$rows = array();

foreach($bookkeepings as $bookkeeping) {
    $rows[] = array($bookkeeping->Number, $bookkeeping->AccountingDate,
        $bookkeeping->Headline, $bookkeeping->getBookkeepingAccount()->Name, number_format((float)$bookkeeping->Amount, 2, ',', ''));
    $sum += $bookkeeping->Amount;
}

$larps = LARP::getAllForYear($campaign->Id, $year);
if (!empty($larps)) {
    
    foreach ($larps as $larp) {
        
        $income = Registration::totalIncomeToday($larp) + Bookkeeping::sumRegisteredIncomes($larp);
        $refund = 0 - Registration::totalFeesReturned($larp);
        $expense = Bookkeeping::sumRegisteredExpenses($larp);
        $larp_sum = $income + $refund + $expense;
        
        $sum += $larp_sum;
        $rows[] = array("", substr($larp->EndDate,0,10),
            $larp->Name, "", number_format((float)$larp_sum, 2, ',', ''));
    }
    
}


$rows[] = array('', '', 'Summa', '', number_format((float)$sum, 2, ',', ''));

// add a page
$pdf->AddPage();
// print table
$pdf->Table($name, $header, $rows);





foreach ($larps as $larp) {
    $larpname = 'Kassabok för '.$larp->Name;
    $bookkeepings = Bookkeeping::allFinished($larp);
    
        
    $rows = array();
    $sum = 0;
    foreach($bookkeepings as $bookkeeping) {
        $rows[] = array($bookkeeping->Number, $bookkeeping->AccountingDate, 
            $bookkeeping->Headline, $bookkeeping->getBookkeepingAccount()->Name, number_format((float)$bookkeeping->Amount, 2, ',', ''));
        $sum += $bookkeeping->Amount;
    }
    
    
    $invoices = Invoice::getAllNormalInvoices($larp);
    foreach ($invoices as $invoice) {
        $rows[] = array("Faktura ".$invoice->Number, $invoice->PayedDate,
            $invoice->Recipient, "Fakturor", number_format((float)$invoice->FixedAmount, 2, ',', ''));
        $sum += $invoice->FixedAmount;
    }
    
    $registration_fees = Registration::totalFeesPayed($larp);
    $sum += $registration_fees;
    
    $rows[] = array('', substr($larp->EndDate,0,10), 'Deltagaravgifter', '', number_format((float)$registration_fees, 2, ',', ''));
    
    $returned_fees = Registration::totalFeesReturned($larp);
    $sum -= $returned_fees;
    $rows[] = array('', substr($larp->EndDate,0,10), 'Återbetalade deltagaravgifter', '', ' '.number_format((float)(0-$returned_fees), 2, ',', ''));
    
    $rows[] = array('', '', 'Summa', '', number_format((float)$sum, 2, ',', ''));
    
    // add a page
    $pdf->AddPage();
    // print table
    $pdf->Table($larpname, $header, $rows);
    
    //----------------------- Intäkter ----------------------------------------------------
    $pdf->AddPage();

    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Write(0, "Intäkter för $larp->Name per konto", '', 0, 'C', true, 0, false, false, 0);
    
    
    $bookkeeping_accounts = Bookkeeping_Account::allActive($larp);
    
    foreach ($bookkeeping_accounts as $account) {
        $bookeepings_for_account = Bookkeeping::allIncomesOnAccount($larp, $account);
        if (is_array($bookeepings_for_account) && !empty($bookeepings_for_account)) {
            $sum = 0;
            $rows = array();
            foreach ($bookeepings_for_account as $bookkeeping) {
                $rows[] = array($bookkeeping->Number, $bookkeeping->AccountingDate,
                    $bookkeeping->Headline, $bookkeeping->getBookkeepingAccount()->Name, number_format((float)$bookkeeping->Amount, 2, ',', ''));
                $sum += $bookkeeping->Amount;
                
            }
            $rows[] = array('', '', 'Summa', '', number_format((float)$sum, 2, ',', ''));
            
            // print table
            $pdf->Table("Intäkter på $account->Name", $header, $rows);
            
        }
    }
    
    //----------------------- Utgifter ----------------------------------------------------
    
    $pdf->AddPage();
    
    $pdf->SetFont('helvetica', 'B', 20);
    $pdf->Write(0, "Utgifter för $larp->Name per konto", '', 0, 'C', true, 0, false, false, 0);
    
    
    $bookkeeping_accounts = Bookkeeping_Account::allActive($larp);
    
    foreach ($bookkeeping_accounts as $account) {
        $bookeepings_for_account = Bookkeeping::allExpensesOnAccount($larp, $account);
        if (is_array($bookeepings_for_account) && !empty($bookeepings_for_account)) {
            $sum = 0;
            $rows = array();
            foreach ($bookeepings_for_account as $bookkeeping) {
                $rows[] = array($bookkeeping->Number, $bookkeeping->AccountingDate,
                    $bookkeeping->Headline, $bookkeeping->getBookkeepingAccount()->Name, number_format((float)$bookkeeping->Amount, 2, ',', ''));
                $sum += $bookkeeping->Amount;
                
            }
            $rows[] = array('', '', 'Summa', '', number_format((float)$sum, 2, ',', ''));
            
            
            // print table
            $pdf->Table("Utgifter på $account->Name", $header, $rows);
            
        }
    }
    
    
}

// close and output PDF document
$pdf->Output(scrub($name.'.pdf'), 'I');
