<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>


    <div class="content">   
        <h1>Anmälda deltagare</h1>
        Genom att klicka på rubrikerna i tabellen kan du sortera tabellen. Klicka en gång till för att få omvänd ordning.
        <br><br>
        Om betalningskolumnen har två röda utropstecken har man gått över tiden för betalningen.
        <br>
     		<?php 
    		$persons = Person::getAllRegistered($current_larp);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
    		} else {
    		    $tableId = "participants";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		      "<th></th>".
    		      "<th onclick='sortTable(2, \"$tableId\")'>Epost</th>".
    		      "<th onclick='sortTable(3, \"$tableId\")'>Ålder<br>på lajvet</th>".
    		      "<th onclick='sortTable(4, \"$tableId\")'>Godkända<br>karaktärer</th>".
    		      "<th onclick='sortTable(5, \"$tableId\")'>Medlem</th>".
    		      "<th onclick='sortTable(6, \"$tableId\")'>Betalnings-<br>referens</th>".
    		      "<th onclick='sortTable(7, \"$tableId\")' colspan='2'>Betalat</th>".
    		      "<th onclick='sortTable(9, \"$tableId\")' colspan='2'>Plats på lajvet</th></tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        if ($registration->NotComing == 1) {
    		            echo "<s>";
    		        }
    		        echo $person->Name;
    		        if ($registration->NotComing == 1) {
    		            echo "</s>";
    		        }
    		        echo "</td>\n";
    		        echo "<td>" . "<a href='view_person.php?id=" . $person->Id . "'><i class='fa-solid fa-eye'></i></a>\n";
    		        echo "<a href='edit_person.php?id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        echo "<td>";
    		        if ($registration->NotComing == 1) {
    		            echo "<s>";
    		        }
    		        
    		        echo $person->Email;
    		        if ($registration->NotComing == 1) {
    		            echo "</s>";
    		        }
    		        
    		        echo " ".contactEmailIcon($person->Name,$person->Email)."</td>\n";
    		        echo "</td>\n";

    		        if ($registration->NotComing == 1) {
    		            echo "<td></td>";
    		            echo "<td></td>";
    		            echo "<td></td>";
    		            echo "<td></td>";
    		            echo "<td align='center'>";
    		            if ($registration->isToBeRefunded()) echo " ".showStatusIcon(false);
    		            "</td>";
    		            echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		            
    		            echo "<td>";
    		            echo "<td></td>";
    		            echo "<td></td>";
    		        }
    		        else {
    		            echo "<td>" . $person->getAgeAtLarp($current_larp) . " år ";
    		            
    		            if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
    		                
    		                if (empty($registration->GuardianId)) {
    		                    echo showStatusIcon(false);
    		                }
    		                
    		                
    		            }
    		            
        		        echo "<td align='center'>" . showStatusIcon($registration->isApprovedCharacters()) . "</td>\n";
        		        echo "<td align='center'>" . showStatusIcon($registration->isMember()) . "</td>\n";
        		        echo "<td>".$registration->PaymentReference .  "</td>\n";
        		        echo "<td align='center'>" . showStatusIcon($registration->hasPayed());
        		        if (!$registration->hasPayed() && $registration->isPastPaymentDueDate()) echo " ".showStatusIcon(false);
        		        "</td>";
        		        echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
        		        
        		        echo "<td>";
        		        if ($registration->SpotAtLARP == 1) {
        		            echo showStatusIcon(true);
        		        }
        		        else {
            		        echo showStatusIcon(false);
            		        echo "</td><td>";
            		        if ($registration->allChecksPassed()) {
                		        echo "<form method='post' action='logic/give_spot.php'>";
                		        echo "<input type='hidden' id='RegistrationId' name='RegistrationId' value='$registration->Id'>";
                		        
                		        echo "<input type='submit' value='Ge plats'>";
                		        echo "</form>";
            		        }
        		        }
        		        echo "</td>";
    		        }
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
        
        
	</div>
</body>

<?php include_once '../javascript/table_sort.js';?>
</html>
