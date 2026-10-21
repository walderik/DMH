<?php
include_once 'header.php';

include 'navigation.php';
?>


    <div class="content">
        <h1>Kassabok</h1>

	   <?php 
	   $bookkeepings = Bookkeeping::allFinished($current_larp);
      $invoices = Invoice::getAllNormalInvoices($current_larp);
      $registrations = Registration::allBySelectedLARP($current_larp);
	   
      $all_payments = array();
      
      //Add all bookkeppings
      foreach ($bookkeepings as $bookkeeping) $all_payments[] = array($bookkeeping->AccountingDate, $bookkeeping->Headline, $bookkeeping->Amount);
      
      //Add all invoices
      foreach ($invoices as $invoice) $all_payments[] = array($invoice->PayedDate, "Faktura till ".$invoice->Recipient, $invoice->FixedAmount);
      
      //Add all fees
      foreach ($registrations as $registration) {
          if (!empty($registration->Payed)) $all_payments[] = array($registration->Payed, "Deltagaravgift " . $registration->PaymentReference, $registration->AmountPayed);
      }

      
      //Add all returns
      foreach ($registrations as $registration) {
          if (!empty($registration->RefundAmount) && !empty($registration->RefundDate)) {
              $person = $registration->getPerson();
              $all_payments[] = array($registration->RefundDate, "Återbetalning för ".$person->Name, $registration->RefundAmount);
          }
      }
      
      
      //Sort
      usort($all_payments, function ($a, $b) {
          if ($a[0] == $b[0]) {
              return 0;
          }
          return ($a[0] < $b[0]) ? -1 : 1;
      });

       echo "<table id='bookkeeping' class='data'>";
       echo "<tr><th>Bokföringsdatum</th><th>Rubrik</th><th>Summa</th></tr>\n";
       foreach ($all_payments as $payment) {
           echo "<tr>\n";
           echo "<td>" . $payment[0] . "</td>\n";
           echo "<td>" . $payment[1] . "</td>\n";
           echo "<td>" . $payment[2] . "</td>\n";  
           echo "</tr>\n";
       }

       echo "</table>";
       
       ?>
       
</body>
</html>