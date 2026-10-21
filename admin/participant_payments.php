<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>

<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>

    <div class="content">   
        <h1>Betalningsinfo för anmälda deltagare</h1>
     		<?php 
     		$registrations = Registration::allBySelectedLARP($current_larp);
     		
     		usort($registrations, function (Registration $a, Registration $b) {
     		    if ($a->Payed == $b->Payed) {
     		        return 0;
     		    }
     		    return ($a->Payed < $b->Payed) ? -1 : 1;
     		});
     		
    		$amountToPay = 0;
    		$amountPayed = 0;
		    $tableId = "participants";
		    echo "<table id='$tableId' class='data'>";
		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		    "<th onclick='sortTable(1, \"$tableId\")'>Betalnings-<br>referens</th>".
		      "<th onclick='sortTable(2, \"$tableId\")'>Ska betala</th>".
		      "<th onclick='sortTable(3, \"$tableId\")'>Betalnings-<br>datum</th>".
		      "<th onclick='sortTable(4, \"$tableId\")'>Har betalat</th>".
		      "</tr>\n";
		    foreach ($registrations as $registration)  {
		        $person = $registration->getPerson();
		        echo "<tr>\n";
		        echo "<td>";
	            echo $person->getViewLink(). " ".contactEmailIcon($person);
		        echo "</td>\n";
		        
		        echo "<td>".$registration->PaymentReference .  "</td>\n";
		        echo "<td>".$registration->AmountToPay .  "</td>\n";
		        echo "<td>".$registration->Payed .  "</td>\n";
		        echo "<td>";
		        if (!empty($registration->AmountPayed) && ($registration->AmountToPay != $registration->AmountPayed)) {
		            echo "<strong>$registration->AmountPayed</strong>";
		        } elseif (!empty($registration->Payed)) echo $registration->AmountPayed;
		        else if (!empty($registration->AmountPayed)) echo $registration->AmountPayed." Inget datum satt för betalningen";

		        echo "</td>\n";
		        echo "</tr>\n";
		        
		        $amountPayed += $registration->AmountPayed;
		        $amountToPay += $registration->AmountToPay;
		    }
		    echo "<tr><th>Sum</th><th></th><th>$amountToPay</th><th></th><th>$amountPayed</th></tr>";
		    echo "</table>";

    		?>

        
        
        
	</div>
</body>

</html>
