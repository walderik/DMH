<?php
include_once 'header.php';

$csv = array();

if (isset($_FILES['csv'])) {

        
    // check there are no errors
    if($_FILES['csv']['error'] == 0){
        //$name = $_FILES['csv']['name'];
        //$ext = strtolower(end(explode('.', $name)));
        $type = $_FILES['csv']['type'];
        $tmpName = $_FILES['csv']['tmp_name'];
        
        // check the file is a csv
        if($type === 'text/csv' || $type=="application/vnd.ms-excel"){
            if(($handle = fopen($tmpName, 'r')) !== FALSE) {
                // necessary if a large csv file
                set_time_limit(0);
                
                $row = 0;
                
                while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                    // number of fields in the csv
                    $col_count = count($data);
                    $data = array_map( "convert", $data );
                    for($col=0;$col<=($col_count-1);$col++) {
                        // get the values from the csv
                        $csv[$row][$col] = $data[$col];
                    }
                    
                    
                    
                    // inc the row
                    $row++;
                }
                fclose($handle);
            }
    
        }
    }
}
function convert( $str ) {
    return iconv( "Windows-1252", "UTF-8", $str );
}


function matchingRegisteredPayment($bankRow, &$registeredPosts) {
    foreach($registeredPosts as $key => $payment) {
        
        if ($payment[0] < $bankRow[5]) return null; //Vi har passerat alla med samma datum
        
        $match = false;
        
        $message = trim(mb_strtolower($bankRow[8]));
        $paymentReference = trim(mb_strtolower($payment[5]));
        
        //echo "$bankRow[0] Rubrik: $message, OM: $paymentReference<br>";
        
        if ($payment[4] == TYPE_ParticipantPayment) { //Kolla om den matchar en inbetalad avgift
            if (!empty($message) && $paymentReference == $message) {
                $match = true;
            }
        } elseif (!empty($message) && $paymentReference == $message) {
            $match = true;
        } elseif (($payment[4] == TYPE_Bookkeeping || $payment[4] == TYPE_Invoice) && number_format($payment[2],2, '.', '') == $bankRow[10]) { //Kolla om summan matchar
            $match= true;
        }
        if ($match) {
            array_splice($registeredPosts, $key, 1);
            return $payment;
        }
    }
    return null;
}

$bookkeepings = Bookkeeping::allFinished($current_larp);
$invoices = Invoice::getAllNormalInvoices($current_larp);
$registrations = Registration::allBySelectedLARP($current_larp);

$all_registered_payments = array();

define("TYPE_ParticipantPayment", 0);
define("TYPE_ParticipantRefund", 1);
define("TYPE_Bookkeeping", 2);
define("TYPE_Invoice", 3);


//Add all bookkeppings
foreach ($bookkeepings as $bookkeeping) {
    if ($bookkeeping->Amount > 0) {
        $link =  "economy_form.php?operation=update_income&id=$bookkeeping->Id";
    } else {
        $link = "economy_form.php?operation=update_expense&id=$bookkeeping->Id";
    }
    $all_registered_payments[] = array($bookkeeping->AccountingDate, "Ver " . $bookkeeping->Number . " " . $bookkeeping->Headline, $bookkeeping->Amount, $link, TYPE_Bookkeeping, $bookkeeping->BankLabel);
}

//Add all invoices
foreach ($invoices as $invoice) $all_registered_payments[] = array($invoice->PayedDate, "Faktura till ".$invoice->Recipient, $invoice->FixedAmount. "invoice_form.php?operation=update&id=$invoice->Id", TYPE_Invoice, null);

//Add all fees and all returns
foreach ($registrations as $registration) {
    $person = $registration->getPerson();
    $paymentReference = $registration->PaymentReference;
    if (!empty($registration->BankLabelPayment)) $paymentReference = $registration->BankLabelPayment;
    if (!empty($registration->Payed)) $all_registered_payments[] = array($registration->Payed, "Deltagaravgift för ".$person->Name . ", ref " . $registration->PaymentReference, $registration->AmountPayed,"person_payment.php?id=" . $person->Id, TYPE_ParticipantPayment, $paymentReference);
    if (!empty($registration->RefundAmount) && !empty($registration->RefundDate)) {
        $all_registered_payments[] = array($registration->RefundDate, "Återbetalning för ".$person->Name, 0-$registration->RefundAmount, "person_payment.php?id=" . $person->Id, TYPE_ParticipantRefund, $registration->BankLabelRefund);
    }
}


    
    

include 'navigation.php';
?>

    <div class="content">
        <h1>Alla transaktioner</h1>
		<h3>Ladda upp en transaktionsfil för att matcha registrerade transaktioner</h3>
        <form action="economy_all_payments.php" method="post" enctype="multipart/form-data">
		<table>
        <tr><td>Fil</td>
        <td>
        <input type="file" name="csv" value="" />
        <input type="submit" name="submit" value="Ladda upp" />
        </td>
        </tr>
        </table>
		</form>
	   <?php 
	   
	   if (empty($csv)) {
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
    
          //Sort
          usort($all_registered_payments, function ($a, $b) {
              if ($a[0] == $b[0]) {
                  return 0;
              }
              return ($a[0] < $b[0]) ? -1 : 1;
          });
    
          
          
           echo "<table id='bookkeeping' class='data'>";
           echo "<tr><th>Bokföringsdatum</th><th>Rubrik</th><th>Summa</th><th>På kontot</th></tr>\n";
           echo "<tr><td></td><td>Ingående</td><td></td>";
           echo "<td class='amount' style='text-align: right;'>".number_format((float)$sum_account, 2, ',', ''). "</td>\n";
           foreach ($all_registered_payments as $payment) {
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
	   } else {
	       //Sort
	       usort($all_registered_payments, function ($a, $b) {
	           if ($a[0] == $b[0]) {
	               return 0;
	           }
	           return ($a[0] > $b[0]) ? -1 : 1;
	       });
	       
	       echo "Verifikationer, och fakturor matchas bara om de har samma summa och samma datum.<br>";
	       echo "Deltagaravgifter matchas bara om de har samma datum, samma summa och exakt rätt referens.<br>";
	       echo "<br>Poster registrerade i Omnes Mundi som inte har kunnat matchas mot något i bankfilen visas efter bankens transaktioner på det datumet.<br>"; 
	       
	       echo "<table id='bookkeeping' class='data'>";
	       echo "<tr><th>Rad</th><th>Bokföringsdatum</th><th>Rubrik</th><th>Text</th><th>Post i<br>Omnes Mundi</th><th>Summa</th><th>På kontot</th></tr>\n";

	       foreach ($csv as $key => $paymentRow) {

	           if (count($paymentRow) >= 11 && is_numeric($paymentRow[0])) {
	               //Registreringar som är nyare än nyaste 
	               $payment = reset($all_registered_payments);
	               while (!empty($payment) && $payment[0] > $paymentRow[5]) {
	                   echo "<tr>";
	                   echo "<td></td>";
	                   echo "<td>".$payment[0]."</td>";
	                   echo "<td></td>";
	                   echo "<td></td>";
	                   echo "<td><a href='" .$payment[3] . "'>". $payment[1] . "</a>, " . number_format((float)$payment[2], 2, ',', '');
	                   echo "<td></td>\n";
	                   echo "<td></td>";
	                   echo "</tr>";
	                   array_splice($all_registered_payments, 0, 1);
	                   $payment = reset($all_registered_payments);
	               }
	               
	               $payment = matchingRegisteredPayment($paymentRow, $all_registered_payments);
	               
	               echo "<tr>";
                   echo "<td>$paymentRow[0]</td>";               
                   echo "<td>$paymentRow[5]</td>";
                   echo "<td>$paymentRow[8]</td>";
                   echo "<td>$paymentRow[9]</td>";
                   
                   if (!empty($payment)) {
                       echo "<td><a href='" .$payment[3] . "'>". $payment[1] . "</a>, " . number_format((float)$payment[2], 2, ',', ''); 
                       if (number_format($payment[2],2, '.', '') != $paymentRow[10]) echo " ".showStatusIcon(false, null, null, "Summan stämmer inte");
                       echo "</td>\n";
                   }
                   else echo "<td></td>";
                   
                   echo "<td class='amount' style='text-align: right;'>".number_format((float)$paymentRow[10], 2, ',', ''). "</td>\n";
                   echo "<td class='amount' style='text-align: right;'>".number_format((float)$paymentRow[11], 2, ',', ''). "</td>\n";
                   echo "</tr>";
               }
               
               //Registreringar som är äldre än äldsta
               
               
               /*
	           $match = false;
	           if ($paymentReference == $message) $match = true;
	           if (str_contains($message, $paymentReference." ")) $match = true;
	           if (str_contains($message, $paymentReference.",")) $match = true;
	           if (str_ends_with($message, $paymentReference)) $match = true;
	           
	           if ($match) {
	               $row = $paymentRow;
	               unset($csv[$key]);
	               return $row;
	           }
               */
	           
	       }
	       foreach ($all_registered_payments as $payment) {
	           echo "<tr>";
	           echo "<td></td>";
	           echo "<td>".$payment[0]."</td>";
	           echo "<td></td>";
	           echo "<td></td>";
	           echo "<td><a href='" .$payment[3] . "'>". $payment[1] . "</a>, " . number_format((float)$payment[2], 2, ',', '');
	           echo "<td></td>\n";
	           echo "<td></td>";
	           echo "</tr>";
	       }
	       
	       echo "</table>";
	   }
       
       ?>
       
</body>
</html>