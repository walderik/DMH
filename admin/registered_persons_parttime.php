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
        <h1>Anmälda deltagare som inte är med på hela lajvet</h1>
        <br>
     		<?php 
     		$persons = Person::getAllRegisteredPartTime($current_larp);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
    		} else {
    		    $tableId = "participants";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		      "<th></th>".
    		      "<th onclick='sortTable(2, \"$tableId\")'>Epost</th>".
    		      "<th onclick='sortTableNumbers(3, \"$tableId\")'>Ålder<br>på lajvet</th>".
    		      "<th onclick='sortTable(4, \"$tableId\")'>Dagar frånvarande</th>".
    		      "<th onclick='sortTable(5, \"$tableId\")'>Avgift kontrollerad</th>".
    		      "<th onclick='sortTable(6, \"$tableId\")'>Betalnings-<br>referens</th>".
    		      "<th onclick='sortTable(7, \"$tableId\")' colspan='2'>Betalat</th>".
    		      "</tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        echo "<tr>\n";
    		        echo "<td>";
		            echo "<a href='view_person.php?id=$person->Id'>";
    		        echo $person->Name;
		            echo "</a>";
    		        echo "</td>\n";
    		        echo "<td>";
    		        echo "<a href='edit_person.php?id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        echo "<td>";
    		        echo $person->Email;
    		        echo " ".contactEmailIcon($person->Name,$person->Email)."</td>\n";
    		        echo "</td>\n";

		            echo "<td>" . $person->getAgeAtLarp($current_larp) . " år</td>";
		            echo "<td>" . $registration->LarpPartNotAttending . "</td>";
		            echo "<td align='center'>" . showStatusIcon($registration->LarpPartAcknowledged) . "</td>\n";
		            echo "<td>".$registration->PaymentReference .  "</td>\n";
		            
    		        echo "<td align='center'>" . showStatusIcon($registration->hasPayed());
    		        if (!$registration->hasPayed() && $registration->isPastPaymentDueDate()) echo " ".showStatusIcon(false);
    		        "</td>";
    		        echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-money-check-dollar'></i></a></td>\n";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
        
        
	</div>
</body>

</html>
