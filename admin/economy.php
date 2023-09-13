<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];
        
        echo "Operation: ".$operation;
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
    }
    //if (isset($error)) header("Location: economy.php?error=$error");
    //else header('Location: ' . 'economy.php');
    //exit;
    
}

function saveReceipt(Bookkeeping $bookkeeping) {
    if (!empty($_FILES["upload"]["name"])) {
        $error = Image::maySave(true);
        if (!isset($error)) {
            $id = Image::saveImage("Verifikation $bookkeeping->Number", true);
            $bookkeeping->ImageId = $id;
            $bookkeeping->update();
        }
    }
    
}


include_once '../includes/error_handling.php';


include 'navigation.php';
?>

<style>
.amount {
  text-align: right;
}

</style>

    <div class="content">
        <h1>Kassabok</h1>
	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'<br>Du kan ladda upp kvittot i efterhand genom att klicka på utgiften du just lade in i listan nedan.</div>';
	  }
	  ?>
        <p>Lägg in alla inkomster och utgifter som lajvet har. Deltagaravgifter kommer med automatiskt. 
        När lajvet är klart behöver du bara generera rapporten och skicka till kassören för att bokföringen ska vara avklarad.<br><br>
        En varning betyder att det saknas ett kvitto på en utgift.<br><br>
        Konton läggs upp under <a href="settings.php">inställningar</a></p>
        <p>
        <a href="economy_form.php?operation=add_income"><i class="fa-solid fa-file-circle-plus"></i> Lägg till inkomst</a> &nbsp; 
        <a href="economy_form.php?påeration=add_expense"><i class="fa-solid fa-file-circle-plus"></i> Lägg till utgift</a><br>
        <a href="reports/economy.php" target="_blank"><i class="fa-solid fa-file-pdf"></i> Generera rapport till kassör</a> &nbsp; 
        <a href="logic/all_bookkeeping_zip.php" target="_blank"><i class="fa-solid fa-file-zipper"></i> Alla verifikationer till kassör</a><br>
        
        </p>
	<?php 
       $bookkeepings = Bookkeeping::allByLARP($current_larp);
       $sum = 0;
       echo "<table id='bookkeeping' class='data'>";
       echo "<tr><th>Verifikation<br>nummer</th><th>Datum</th><th>Rubrik</th><th>Konto</th><th>Summa</th></tr>\n";
       foreach ($bookkeepings as $bookkeeping) {
           echo "<tr>\n";
           echo "<td>" . $bookkeeping->Number . "</td>\n";
           echo "<td>" . $bookkeeping->Date . "</td>\n";
           echo "<td><a href='economy_view_bookkeeping.php?id=$bookkeeping->Id'>" . $bookkeeping->Headline."</a>";
           if ($bookkeeping->Amount < 0 && !$bookkeeping->hasImage()) {
               echo " " . showStatusIcon(false);
           }
           if ($bookkeeping->Amount > 0) {
               echo " <a href='economy_form.php?operation=update_income&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra inkomst'></i></a>";
               echo " <a href='economy_receipt_pdf.php?bookkeepingId=$bookkeeping->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Skapa kvitto'></i></a>";
           } else {
               echo " <a href='economy_form.php?operation=update_expense&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra utgift'></i></a>";
               
           }
           echo "</td>\n";
           echo "<td>" . $bookkeeping->getBookkeepingAccount()->Name . "</td>"; 
           echo "<td class='amount'>" .number_format((float)$bookkeeping->Amount, 2, ',', '')."</td>\n";
           $sum += $bookkeeping->Amount;
           echo "</tr>\n";
       }
       $registration_fees = Registration::totalFeesPayed($current_larp);
       $sum += $registration_fees;
       echo "<tr><td></td><td>".substr($current_larp->EndDate,0,10)."</td><td>Deltagaravgifter</td><td></td><td class='amount'>".number_format((float)$registration_fees, 2, ',', '')."</td></tr>";
       $returned_fees = Registration::totalFeesReturned($current_larp);
       $sum -= $returned_fees;
       echo "<tr><td></td><td>".substr($current_larp->EndDate,0,10)."</td><td>Återbetalade deltagaravgifter</td><td></td><td class='amount'>".number_format((float)(0-$returned_fees), 2, ',', '')."</td></tr>";
       echo "<tr></tr>";
       echo "<tr><th colspan='4'>Summa</th><th class='amount' style='text-align: right;'>".number_format((float)$sum, 2, ',', '')."</th></tr>";
       echo "</table>";
       ?>
       
	


</body>
</html>