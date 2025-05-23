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
        <h1>Deltagare som ska ha återbetalning</h1>
     		<?php 
    		$persons = Person::getAllToBeRefunded($current_larp, true);
    		if (empty($persons)) {
    		    echo "Inga ska ha återbetalning";
    		} else {
    		    $tableId = "participants";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		      "<th onclick='sortTable(1, \"$tableId\")'>Epost</th>".
    		      "<th onclick='sortTable(2, \"$tableId\")'>Betalat</th>".
    		      "<th onclick='sortTable(3, \"$tableId\")' >Betalning</th>".
    		      "</tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        echo "<div class='person' id='$person->Id' draggable='true' ondragstart='drag(event)'>\n";
					echo "  <div class='name'>";
					echo $registration->isNotComing() 
						? "<s>$person->Name</s>" 
						: $person->getViewLink();
					echo "</div>\n";
    		        echo "</td>\n";
    		        echo "<td>";
    		        if ($registration->isNotComing()) {
    		            echo "<s>";
    		        }
    		        
    		        echo $person->Email;
    		        if ($registration->isNotComing()) {
    		            echo "</s>";
    		        }
    		        
    		        echo " ".contactEmailIcon($person)."</td>\n";
    		        echo "</td>\n";
    		        
    		        echo "<td>$registration->AmountPayed</td>";
    		        

    		        echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-money-check-dollar'></i></a></td>\n";
        		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
        <h1>Deltagare som har fått återbetalning</h1>
                <a href="reports/refunded.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Återbetalningar</a><br><br> 
        
     		<?php 
     		$persons = Person::getAllRefunded($current_larp);
    		if (empty($persons)) {
    		    echo "Inga har fått  återbetalning";
    		} else {
    		    $tableId = "participants";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		      "<th onclick='sortTable(1, \"$tableId\")'>Epost</th>".
    		      "<th onclick='sortTable(2, \"$tableId\")'>Betalat</th>".
    		      "<th onclick='sortTable(3, \"$tableId\")' >Återbetalning</th>".
    		      "<th onclick='sortTable(3, \"$tableId\")' >Datum för<br>återbetalning</th>".
    		      "<th onclick='sortTable(4, \"$tableId\")' >Betalning</th>".
    		      "</tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        echo $registration->isNotComing() 
						? "<s>$person->Name</s>" 
						: $person->getViewLink();
    		        echo "</td>\n";
    		        echo "<td>";
    		        if ($registration->isNotComing()) {
    		            echo "<s>";
    		        }
    		        
    		        echo $person->Email;
    		        if ($registration->isNotComing()) {
    		            echo "</s>";
    		        }
    		        
    		        echo " ".contactEmailIcon($person)."</td>\n";
    		        echo "</td>\n";
    		        
    		        echo "<td>$registration->AmountPayed</td>";
    		        echo "<td>$registration->RefundAmount</td>";
    		        echo "<td>$registration->RefundDate</td>";
    		        

    		        echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-money-check-dollar'></i></a></td>\n";
        		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
        
        
        
	</div>
</body>

</html>
