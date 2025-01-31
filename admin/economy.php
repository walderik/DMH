<?php
include_once 'header.php';
// include_once '../includes/error_handling.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];
        
        if ($operation == 'add_income') {
            $bookkeeping = Bookkeeping::newFromArray($_POST);
            $bookkeeping->create();
        } elseif ($operation == 'add_expense') {
            $bookkeeping = Bookkeeping::newFromArray($_POST);
            $bookkeeping->Amount = 0 - $_POST['Amount'];
            $bookkeeping->create();
            saveReceipt($bookkeeping);
        } elseif ($operation == 'update_income') {
            $bookkeeping = Bookkeeping::loadById($_POST['id']);
            $bookkeeping->setValuesByArray($_POST);
            $bookkeeping->update();
        } elseif ($operation == 'update_expense') {
            $bookkeeping = Bookkeeping::loadById($_POST['id']);
            $bookkeeping->setValuesByArray($_POST);
            $bookkeeping->Amount = 0 - $_POST['Amount'];
            
            $bookkeeping->update();
            saveReceipt($bookkeeping);
        }
        header("Location: economy.php");
        exit;
    }
    //if (isset($error)) header("Location: economy.php?error=$error");
    //else header('Location: ' . 'economy.php');
    //exit;
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Bookkeeping::delete($_GET['id']);
        header("Location: economy.php");
        exit;
    }
}


function saveReceipt(Bookkeeping $bookkeeping) {
    global $error_code, $error_message;
    if (!empty($_FILES["upload"]["name"])) {
        $error = Image::maySave(true);
        if (!isset($error)) {
            $id = Image::saveImage("Verifikation $bookkeeping->Number", true);
            $bookkeeping->ImageId = $id;
            $bookkeeping->update();
        } else {
            $error_code = $error;
            $error_message = getErrorText($error_code);
        }
    }
    
}


// include_once '../includes/error_handling.php';


include 'navigation.php';
?>


    <div class="content">
        <h1>Kassabok</h1>
        	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }?>
	  <?php if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }?>
        <p>Lägg in alla inkomster och utgifter som lajvet har. Deltagaravgifter kommer med automatiskt. 
        <br><br>
        En varning betyder att det saknas ett kvitto på en utgift.<br><br>
        Konton läggs upp under kampanj. Under kampanj finns även rapporter att skicka in till Berghems Vänners kassör vid årets slut.</a></p>
        <p>
        <a href="economy_payments.php">Kontrollera bankfil mot obetalade avgifter</a><br>
        <a href="economy_by_account.php">Visa kassabok uppdelad på konto</a><br>
        <a href="economy_form.php?operation=add_income"><i class="fa-solid fa-file-circle-plus"></i> Lägg till inkomst</a> &nbsp; 
        <a href="economy_form.php?operation=add_expense"><i class="fa-solid fa-file-circle-plus"></i> Lägg till utgift</a><br>
        
        </p>
        
        
		<?php 
	   $bookkeepings = Bookkeeping::allUnFinished($current_larp);
	   //if (!empty($bookkeepings)) {
           echo "<h2>Påbörjade</h2>"; 
           echo "Alla som saknar bokföringsdatum hamnar här.";
           echo "<table id='bookkeeping' class='data'>";
           echo "<tr><th>Upplagd datum</th><th>Rubrik</th><th>Ansvarig</th><th>Summa</th><th></th></tr>\n";
           foreach ($bookkeepings as $bookkeeping) {
               echo "<tr>\n";

               echo "<td>" . $bookkeeping->CreationDate . "</td>\n";
               echo "<td><a href='economy_view_bookkeeping.php?id=$bookkeeping->Id'>" . $bookkeeping->Headline."</a>";
               if ($bookkeeping->Amount < 0 && !$bookkeeping->hasImage()) {
                   echo " " . showStatusIcon(false);
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
               echo "<td>";
               echo "<a href='economy.php?operation=delete&id=$bookkeeping->Id'><i class='fa-solid fa-trash' title='Radera'></i></a>";
               echo "</td>";
               echo "</tr>\n";
           }
           echo "</table>";
	   //}
	
	
	   
	   $bookkeepings = Bookkeeping::allFinished($current_larp);
	   //if (!empty($bookkeepings)) {
	   echo "<h2>Klara</h2>";
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
               echo " <a href='economy_receipt_pdf.php?bookkeepingId=$bookkeeping->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa kvitto'></i></a>";
               
           }
           if ($bookkeeping->Amount > 0) {
               echo " <a href='economy_form.php?operation=update_income&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra inkomst'></i></a>";
               echo " <a href='economy_receipt_pdf.php?bookkeepingId=$bookkeeping->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa kvitto'></i></a>";
           } else {
               echo " <a href='economy_form.php?operation=update_expense&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra utgift'></i></a>";
               
           }
           echo "</td>\n";
           echo "<td>" . $bookkeeping->getBookkeepingAccount()->Name . "</td>"; 
           echo "<td>".$bookkeeping->getPerson()->Name."</td>";
           
           echo "<td class='amount'>" .number_format((float)$bookkeeping->Amount, 2, ',', '')."</td>\n";
           $sum += $bookkeeping->Amount;
           echo "</tr>\n";
       }
       $invoices = Invoice::getAllNormalInvoices($current_larp);
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
       
       $registration_fees = Registration::totalFeesPayed($current_larp);
       $sum += $registration_fees;
       echo "<tr><td></td><td>".substr($current_larp->EndDate,0,10)."</td><td>Deltagaravgifter</td><td></td><td></td><td class='amount'>".number_format((float)$registration_fees, 2, ',', '')."</td></tr>";
       $returned_fees = Registration::totalFeesReturned($current_larp);
       $sum -= $returned_fees;
       echo "<tr><td></td><td>".substr($current_larp->EndDate,0,10)."</td><td>Återbetalade deltagaravgifter</td><td></td><td></td><td class='amount'>".number_format((float)(0-$returned_fees), 2, ',', '')."</td></tr>";
       echo "<tr></tr>";
       echo "<tr><th colspan='5'>Balans</th><th class='amount' style='text-align: right;'>".number_format((float)$sum, 2, ',', '')."</th></tr>";
  
       echo "<tr></tr>";

	   //}
       ?>
       
       
	
		<?php 
		$otherLarps = $current_larp->getOtherLarpsSameYear();
		if (!empty($otherLarps)) {

		      
		    foreach ($otherLarps as $larp) {
    		
		        $income = Registration::totalIncomeToday($larp) + Bookkeeping::sumRegisteredIncomes($larp);
		        $refund = 0 - Registration::totalFeesReturned($larp);
		        $expense = Bookkeeping::sumRegisteredExpenses($larp);
		        $larp_sum = $income + $refund + $expense;
		        $sum += $larp_sum;
		        
		        echo "<tr><td></td><td>".substr($larp->EndDate, 0, 10)."</td><td>$larp->Name</td><td></td><td></td><td class='amount'>".number_format((float)$larp_sum, 2, ',', '')."</td></tr>";
		    }
		}
       echo "<tr><th colspan='5'>På kontot</th><th class='amount' style='text-align: right;'>".number_format((float)$sum+10000, 2, ',', '')."</th></tr>";
       echo "</table>";
       
       ?>
       
</body>
</html>