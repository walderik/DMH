<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['campaignId'])) {
        $campaignId = $_GET['campaignId'];
        $year = $_GET['year'];
    } elseif (isset($_GET['larpId'])) {
        $larpId = $_GET['larpId'];
    }
    else {
        header("Location: economy_overview.php");
        exit;
    }
}

if (isset($campaignId)) {
    $campaign = Campaign::loadById($campaignId);
    $name = "$campaign->Name ($year)";
    $bookkeepings = Bookkeeping::allFinishedCampaign($campaign, $year);
} else {
    $larp = LARP::loadById($larpId);
    $name = $larp->Name;
    $bookkeepings = Bookkeeping::allFinished($larp);
}

include 'navigation.php';
?>

<style>
.amount {
  text-align: right;
}

</style>

    <div class="content">
        <h1>Kassabok för <?php echo $name;?></h1>
		<?php 
       $sum = 0;
       echo "<table id='bookkeeping' class='data'>";
       echo "<tr><th>Verifikation<br>nummer</th><th>Bokföringsdatum</th><th>Rubrik</th><th>Konto</th><th>Ansvarig</th><th>Summa</th></tr>\n";
       foreach ($bookkeepings as $bookkeeping) {
           echo "<tr>\n";
           echo "<td>" . $bookkeeping->Number . "</td>\n";
           echo "<td>" . $bookkeeping->AccountingDate . "</td>\n";
           echo "<td><a href='economy_view_bookkeeping.php?id=$bookkeeping->Id'>" . $bookkeeping->Headline."</a>";
           if ($bookkeeping->Amount < 0 && !$bookkeeping->hasImage()) {
               echo " " . showStatusIcon(false);
           }
           echo "</td>\n";
           echo "<td>" . $bookkeeping->getBookkeepingAccount()->Name . "</td>"; 
           echo "<td>".$bookkeeping->getPerson()->Name."</td>";
           
           echo "<td class='amount'>" .number_format((float)$bookkeeping->Amount, 2, ',', '')."</td>\n";
           $sum += $bookkeeping->Amount;
           echo "</tr>\n";
       }
       if (isset($larp))  {
           $invoices = Invoice::getAllNormalInvoices($larp);
           foreach ($invoices as $invoice) {
               echo "<tr>\n";
               echo "<td>Faktura $invoice->Number </td>\n";
               echo "<td>$invoice->PayedDate</td>\n";
               echo "<td>$invoice->Recipient";
               echo " <a href='invoice_pdf.php?invoiceId=$invoice->Id&showPayed=1' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa faktura'></i></a>";
               echo "</td>\n";
               echo "<td>Fakturor</td>";
               echo "<td></td>";
               echo "<td class='amount'>" .number_format((float)$invoice->FixedAmount, 2, ',', '')."</td>";
               $sum += $invoice->FixedAmount;
               echo "</tr>\n";
           }
           
           $registration_fees = Registration::totalFeesPayed($larp);
           $sum += $registration_fees;
           echo "<tr><td></td><td>".substr($larp->EndDate,0,10)."</td><td>Deltagaravgifter</td><td></td><td></td><td class='amount'>".number_format((float)$registration_fees, 2, ',', '')."</td></tr>";
           $returned_fees = Registration::totalFeesReturned($larp);
           $sum -= $returned_fees;
           echo "<tr><td></td><td>".substr($larp->EndDate,0,10)."</td><td>Återbetalade deltagaravgifter</td><td></td><td></td><td class='amount'>".number_format((float)(0-$returned_fees), 2, ',', '')."</td></tr>";
       } else {
           $larps = LARP::getAllForYear($campaign->Id, $year);
           if (!empty($larps)) {
               
               foreach ($larps as $larp) {
                   
                   $income = Registration::totalIncomeToday($larp) + Bookkeeping::sumRegisteredIncomes($larp);
                   $refund = 0 - Registration::totalFeesReturned($larp);
                   $expense = Bookkeeping::sumRegisteredExpenses($larp);
                   $larp_sum = $income + $refund + $expense;
                   
                   $sum += $larp_sum;
                   
                   echo "<tr><td></td><td></td><td><a href='view_economy.php?larpId=$larp->Id'>$larp->Name</a></td><td></td><td></td><td class='amount'>".number_format((float)$larp_sum, 2, ',', '')."</td></tr>";
               }
               
       }
       
       echo "<tr></tr>";
       echo "<tr><th colspan='5'>Balans</th><th class='amount' style='text-align: right;'>".number_format((float)$sum, 2, ',', '')."</th></tr>";
  
       echo "</table>";
       }
       ?>
       
</body>
</html>