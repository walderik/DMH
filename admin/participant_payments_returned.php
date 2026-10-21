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
        <h1>Återbetalade deltagaravgifter</h1>
     		<?php 
     		$registrations = Registration::allBySelectedLARP($current_larp);
     		
     		usort($registrations, function (Registration $a, Registration $b) {
     		    if ($a->RefundDate == $b->RefundDate) {
     		        return 0;
     		    }
     		    return ($a->RefundDate < $b->RefundDate) ? -1 : 1;
     		});
     		$refunded = 0;
    		$toRefund = 0;

		    $tableId = "participants";
		    echo "<table id='$tableId' class='data'>";
		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
		      "<th onclick='sortTable(1, \"$tableId\")'>Betalnings-<br>referens</th>".
		      "<th onclick='sortTable(3, \"$tableId\")'>Datum för återbetalning</th>".
		      "<th onclick='sortTable(2, \"$tableId\")'>Summa återbetala</th>".
		      "</tr>\n";
		    foreach ($registrations as $registration)  {
		        $person = $registration->getPerson();
		        if (!empty($registration->RefundAmount)) {
    		        echo "<tr>\n";
    		        echo "<td>";
   		            echo $person->getViewLink(). " ".contactEmailIcon($person);
    		        echo "</td>\n";
    		        
    		        echo "<td>".$registration->PaymentReference .  "</td>\n";
    		        echo "<td>".$registration->RefundDate .  "</td>\n";
    		        echo "<td>".$registration->RefundAmount .  "</td>\n";

    		        echo "</tr>\n";
    		        
   
    		        $toRefund += $registration->RefundAmount;
    		        if (!empty($registration->RefundDate)) $refunded += $registration->RefundAmount;
		        }
		    }
		    echo "<tr><th>Sum</th><th></th><th></th><th>$refunded</th></tr>";
		    echo "</table>";
		    if ($toRefund != $refunded) echo "Summa som ska återbetalas: $toRefund ";

    		?>

        
        
        
	</div>
</body>

</html>
