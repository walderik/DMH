<?php
include_once 'header.php';

include 'navigation.php';
?>


    <div class="content">
        <h1>Alla transaktioner</h1>

	   <?php 
	   $bookkeepings = Bookkeeping::allFinished($current_larp);
      $invoices = Invoice::getAllNormalInvoices($current_larp);
      $registrations = Registration::allBySelectedLARP($current_larp);
	   
      $all_payments = array();
      
      //Add all bookkeppings
      foreach ($bookkeepings as $bookkeeping) $all_payments[] = array($bookkeeping->AccountingDate, $bookkeeping->Headline, $bookkeeping->Amount, "economy_view_bookkeeping.php?id=$bookkeeping->Id");
      
      //Add all invoices
      foreach ($invoices as $invoice) $all_payments[] = array($invoice->PayedDate, "Faktura till ".$invoice->Recipient, $invoice->FixedAmount. "invoice_form.php?operation=update&id=$invoice->Id");
      
      //Add all fees and all returns
      foreach ($registrations as $registration) {
          $person = $registration->getPerson();
          if (!empty($registration->Payed)) $all_payments[] = array($registration->Payed, "Deltagaravgift för ".$person->Name . ", ref " . $registration->PaymentReference, $registration->AmountPayed,"person_payment.php?id=" . $person->Id);
          if (!empty($registration->RefundAmount) && !empty($registration->RefundDate)) {
              $all_payments[] = array($registration->RefundDate, "Återbetalning för ".$person->Name, $registration->RefundAmount, "person_payment.php?id=" . $person->Id);
          }
      }
      
      //Sort
      usort($all_payments, function ($a, $b) {
          if ($a[0] == $b[0]) {
              return 0;
          }
          return ($a[0] < $b[0]) ? -1 : 1;
      });

      $sum_account = 10000 ;
      $otherLarps = $current_larp->getOtherLarpsSameYear();
      if (!empty($otherLarps)) {
          
          
          foreach ($otherLarps as $larp) {
              
              $income = Registration::totalIncomeToday($larp) + Bookkeeping::sumRegisteredIncomes($larp);
              $refund = 0 - Registration::totalFeesReturned($larp);
              $expense = Bookkeeping::sumRegisteredExpenses($larp);
              $larp_sum = $income + $refund + $expense;
              $sum_account += $larp_sum;
          }
      }

      
      
      
       echo "<table id='bookkeeping' class='data'>";
       echo "<tr><th>Bokföringsdatum</th><th>Rubrik</th><th>Summa</th><th>På kontot</th></tr>\n";
       echo "<tr><td></td><td>Ingående</td><td></td>";
       echo "<td class='amount' style='text-align: right;'>".number_format((float)$sum_account, 2, ',', ''). "</td>\n";
       foreach ($all_payments as $payment) {
           echo "<tr>\n";
           echo "<td>" . $payment[0] . "</td>\n";
           echo "<td><a href='" .$payment[3] . "'>". $payment[1] . "</a></td>\n";
           echo "<td class='amount' style='text-align: right;'>".number_format((float)$payment[2], 2, ',', ''). "</td>\n";
           $sum_account += $payment[2];
           echo "<td class='amount' style='text-align: right;'>".number_format((float)$sum_account, 2, ',', ''). "</td>\n";
           
           echo "</tr>\n";
       }
       echo "<tr><td></td><td>Utgående</td><td></td>";
       echo "<td class='amount' style='text-align: right;'>".number_format((float)$sum_account, 2, ',', ''). "</td>\n";
       
       echo "</table>";
       
       ?>
       
</body>
</html>