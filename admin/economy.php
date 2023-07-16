<?php
include_once 'header.php';

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
        }
    }
    header('Location: ' . 'economy.php');
    
}


include 'navigation.php';
?>

<style>
.amount {
  text-align: right;
}

</style>

    <div class="content">
        <h1>Kassabok</h1>
        <p>
        <a href="economy_add_income.php"><i class="fa-solid fa-file-circle-plus"></i> Lägg till inkomst</a>
        <a href="economy_add_expence.php"><i class="fa-solid fa-file-circle-plus"></i> Lägg till utgift</a>
        <a href="logic/economy__pdf.php"><i class="fa-solid fa-file-pdf"></i> Generera pdf</a><br>
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
           echo "<td><a href='economy_view_bookkeeping.php?id=$bookkeeping->Id'>" . $bookkeeping->Headline . "</a></td>\n";
           echo "<td>" . $bookkeeping->getBookkeepingAccount()->Name . "</td>"; 
           echo "<td class='amount'>" . $bookkeeping->Amount . "</td>\n";
           $sum += $bookkeeping->Amount;
           echo "</tr>\n";
       }
       $registration_fees = Registration::totalFeesPayed($current_larp);
       $sum += $registration_fees;
       echo "<tr><td></td><td>".substr($current_larp->EndDate,0,10)."</td><td>Deltagaravgifter</td><td></td><td class='amount'>$registration_fees</td></tr>";
       $returned_fees = Registration::totalFeesReturned($current_larp);
       $sum -= $returned_fees;
       echo "<tr><td></td><td>".substr($current_larp->EndDate,0,10)."</td><td>Återbetalade deltagaravgifter</td><td></td><td class='amount'>".(0-$returned_fees)."</td></tr>";
       echo "<tr></tr>";
       echo "<tr><th colspan='4'>Summa</th><th class='amount' style='text-align: right;'>$sum</th></tr>";
       echo "</table>";
       ?>
       
	


</body>
</html>