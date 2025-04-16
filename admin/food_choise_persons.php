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
        <h1>Anmälda deltagare <?php echo contactAllEmailIcon() ?></h1>
        Genom att klicka på rubrikerna i tabellen kan du sortera tabellen. Klicka en gång till för att få omvänd ordning.
        <br><br>
        Om betalningskolumnen har två röda utropstecken har man gått över tiden för betalningen.<br>
        <?php if ($current_larp->chooseParticipationDates()) {         ?>
        <a href='registered_persons_parttime.php'>Lista med deltagare som bara är med del av lajvet.</a>    
        <?php } ?>
        

        
        <br>
     		<?php 
    		$persons = Person::getAllRegistered($current_larp, true);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
    		} else {
    		    $tableId = "participants";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTableNumbers(3, \"$tableId\")'>Ålder</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")'>Boende</th>".
        		    "<th onclick='sortTable(5, \"$tableId\")'>Vald mat</th>".
        		    "<th onclick='sortTable(6, \"$tableId\")'>Matalternativ</th>".
        		    "<th onclick='sortTable(7, \"$tableId\")'>Allergi</th>".
        		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        if ($registration->isNotComing()) continue;

    		        echo "<tr>\n";
    		        echo "<td>";
    		        
    		        $allergyStr = 'Nej';
    		        if (!empty($person->getNormalAllergyTypes()) || !empty($person->FoodAllergiesOther)) $allergyStr = 'Ja';
    		        $person_row = array($person->Name, $person->getAgeAtLarp($current_larp), $housingStr,
    		            $registration->getTypeOfFood()->Name);
    		        if ($hasFoodChoices) $person_row[] = $registration->FoodChoice;
    		        $person_row[] = $allergyStr;
    		        
    		        
    		        if ($registration->isNotComing()) {
    		            echo "</s>";
    		        } else {
    		            echo "</a>";
    		        }
    		        echo "</td>\n";
    		        if ($registration->isNotComing()) {
    		            echo "<td></td>";
    		        }
    		        else {
        		        echo "<td>";
        		        echo "<a href='edit_person.php?id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        }
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

    		        if ($registration->isNotComing()) {
    		            echo "<td></td>";
    		            echo "<td></td>";
    		            echo "<td></td>";
    		            echo "<td></td>";
    		            echo "<td></td>";
    		            echo "<td align='center'>";
    		            if ($registration->isToBeRefunded()) echo showStatusIcon(false);
    		            "</td>";
    		            echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-money-check-dollar'></i></a></td>\n";
    		            
    		            echo "<td>Avbokad</td>";
    		        }
    		        else {
    		            echo "<td>" . $person->getAgeAtLarp($current_larp) . " år ";
    		            
    		            if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
    		                
    		                if (empty($registration->GuardianId)) {
    		                    echo showStatusIcon(false);
    		                }
    		                
    		                
    		            }
    		            
    		            echo "<td align='center'>" . showStatusIcon($person->isApprovedCharacters($current_larp)) . "</td>\n";
        		        echo "<td align='center'>" . showStatusIcon($registration->isMember()) . "</td>\n";
        		        echo "<td>" . $registration->RegisteredAt . "</td>";
        		        echo "<td>".$registration->PaymentReference .  "</td>\n";
        		        echo "<td align='center'>" . showStatusIcon($registration->hasPayed());
        		        if (!$registration->hasPayed() && $registration->isPastPaymentDueDate()) echo " ".showStatusIcon(false);
        		        "</td>";
        		        echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-money-check-dollar'></i></a></td>\n";
        		        
        		        echo "<td>";
        		        if ($registration->hasSpotAtLarp()) {
        		            echo showStatusIcon(true);
        		        }
        		        else {
            		        echo showStatusIcon(false);
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

</html>
