<?php
include_once 'header.php';

include 'navigation.php';
?>

    <div class="content">
        <h1>Kassabok uppdelad på konton</h1>
        	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }?>
	  <?php if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }?>

<?php 


$bookkeepings = Bookkeeping::allFinished($current_larp);
$bookkeeping_accounts = Bookkeeping_Account::allActive($current_larp);

foreach ($bookkeeping_accounts as $account) {
    $accountSum = 0;
    echo "<h2>$account->Name</h2>";
    echo "<table id='account_$account->Id' class='data'>";
    echo "<tr><th>Verifikation<br>nummer</th><th>Bokföringsdatum</th><th>Rubrik</th><th>Ansvarig</th><th>Summa</th></tr>\n";
    foreach ($bookkeepings as $bookkeeping) {
        if ($bookkeeping->BookkeepingAccountId == $account->Id) {
            echo "<tr>\n";
            echo "<td>" . $bookkeeping->Number . "</td>\n";
            echo "<td>" . $bookkeeping->AccountingDate . "</td>\n";
            echo "<td><a href='economy_view_bookkeeping.php?id=$bookkeeping->Id'>" . $bookkeeping->Headline."</a>";
            if ($bookkeeping->Amount < 0 && !$bookkeeping->hasImage()) {
                echo " " . showStatusIcon(false);
                echo " <a href='economy_receipt_pdf.php?bookkeepingId=$bookkeeping->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa kvitto'></i></a>";
                
            }
            if ($bookkeeping->Amount > 0) {
                echo " <a href='economy_form.php?operation=update_income&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra inkomst'></i></a>";
                echo " <a href='economy_receipt_pdf.php?bookkeepingId=$bookkeeping->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa kvitto'></i></a>";
            } else {
                echo " <a href='economy_form.php?operation=update_expense&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra utgift'></i></a>";
                
            }
            echo "</td>\n";
            echo "<td>".$bookkeeping->getPerson()->Name."</td>";
            
            echo "<td class='amount'>" .number_format((float)$bookkeeping->Amount, 2, ',', '')."</td>\n";
            $accountSum += $bookkeeping->Amount;
            echo "</tr>\n";
            
        }
        
    }
    echo "<tr><th colspan='4'>Summa</th><th class='amount' style='text-align: right;'>".number_format((float)$accountSum, 2, ',', '')."</th></tr>";
    echo "</table>";
}

$invoices = Invoice::getAllNormalInvoices($current_larp);
if (!empty($invoices)) {
    echo "<h2>Fakturor</h2>";
    echo "<table id='invoices' class='data'>";
    echo "<tr><th>Verifikation<br>nummer</th><th>Bokföringsdatum</th><th>Rubrik</th><th>Ansvarig</th><th>Summa</th></tr>\n";
    
    
    $accountSum = 0;
    foreach ($invoices as $invoice) {
        echo "<tr>\n";
        echo "<td>Faktura $invoice->Number </td>\n";
        echo "<td>$invoice->PayedDate</td>\n";
        echo "<td>$invoice->Recipient";
        echo " <a href='invoice_pdf.php?invoiceId=$invoice->Id&showPayed=1' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa faktura'></i></a>";
        echo "</td>\n";
        echo "<td>Fakturor</td>";
        echo "<td class='amount'>" .number_format((float)$invoice->FixedAmount, 2, ',', '')."</td>";
        $accountSum += $invoice->FixedAmount;
        echo "</tr>\n";
    }
    echo "<tr><th colspan='4'>Summa</th><th class='amount' style='text-align: right;'>".number_format((float)$accountSum, 2, ',', '')."</th></tr>";
    echo "</table>";
}

echo "<h2>Deltagaravgifter</h2>";
echo "<table id='fees' class='data'>";
echo "<tr><th>Verifikation<br>nummer</th><th>Bokföringsdatum</th><th>Rubrik</th><th>Ansvarig</th><th>Summa</th></tr>\n";
$accountSum = 0;
$registration_fees = Registration::totalFeesPayed($current_larp);
$accountSum += $registration_fees;
echo "<tr><td></td><td>".substr($current_larp->EndDate,0,10)."</td><td>Deltagaravgifter</td><td></td><td class='amount'>".number_format((float)$registration_fees, 2, ',', '')."</td></tr>";
$returned_fees = Registration::totalFeesReturned($current_larp);
$accountSum -= $returned_fees;
echo "<tr><td></td><td>".substr($current_larp->EndDate,0,10)."</td><td>Återbetalade deltagaravgifter</td><td></td><td class='amount'>".number_format((float)(0-$returned_fees), 2, ',', '')."</td></tr>";
echo "<tr><th colspan='4'>Summa</th><th class='amount' style='text-align: right;'>".number_format((float)$accountSum, 2, ',', '')."</th></tr>";
echo "</table>";



?>