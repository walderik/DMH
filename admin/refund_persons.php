<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>


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
    		      "<th></th>".
    		      "<th onclick='sortTable(2, \"$tableId\")'>Epost</th>".
    		      "<th onclick='sortTable(3, \"$tableId\")'>Betalat</th>".
    		      "<th onclick='sortTable(5, \"$tableId\")' >Betalning</th>".
    		      "</tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        if ($registration->isNotComing()) {
    		            echo "<s>";
    		        }
    		        echo $person->Name;
    		        if ($registration->isNotComing()) {
    		            echo "</s>";
    		        }
    		        echo "</td>\n";
    		        if ($registration->isNotComing()) {
    		            echo "<td></td>";
    		        }
    		        else {
        		        echo "<td>" . "<a href='view_person.php?id=" . $person->Id . "'><i class='fa-solid fa-eye'></i></a>\n";
    		        }
    		        echo "<td>";
    		        if ($registration->isNotComing()) {
    		            echo "<s>";
    		        }
    		        
    		        echo $person->Email;
    		        if ($registration->isNotComing()) {
    		            echo "</s>";
    		        }
    		        
    		        echo " ".contactEmailIcon($person->Name,$person->Email)."</td>\n";
    		        echo "</td>\n";
    		        
    		        echo "<td>$registration->AmountPayed</td>";
    		        

    		        echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-money-check-dollar'></i></a></td>\n";
        		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
        
        
	</div>
</body>

<?php include_once '../javascript/table_sort.js';?>
</html>
