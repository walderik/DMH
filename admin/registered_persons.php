<?php
 include_once 'header.php';
 
 include 'navigation_subpage.php';
?>


    <div class="content">   
        <h1>Anmälda deltagare</h1>
     		<?php 
    		$persons = Person::getAllRegistered($current_larp);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th></th><th>Epost</th><th>Ålder<br>på lajvet</th><th>Godkända<br>karaktärer</th>";
    		    echo "<th>Medlem</th><th>Betalnings-<br>referens</th><th colspan='2'>Betalat</th><th colspan='2'>Plats på lajvet</th></tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        echo "<tr>\n";
    		        echo "<td>" . $person->Name . "</td>\n";
    		        echo "<td>" . "<a href='view_person.php?id=" . $person->Id . "'><i class='fa-solid fa-eye'></i></a>\n";
    		        echo "<a href='edit_person.php?id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        echo "<td>" . $person->Email . " ".contactEmailIcon($person->Name,$person->Email)."</td>\n";
    		        echo "<td>" . $person->getAgeAtLarp($current_larp) . " år ";

    		        if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {

    		            if (empty($registration->GuardianId)) {
    		                echo showStatusIcon(false);
    		            }
    		                

		            }
    		        echo "</td>\n";

    		        
    		        echo "<td align='center'>" . showStatusIcon($registration->isApprovedCharacters()) . "</td>\n";
    		        echo "<td align='center'>" . showStatusIcon($registration->isMember()) . "</td>\n";
    		        echo "<td>".$registration->PaymentReference .  "</td>\n";
    		        echo "<td align='center'>" . showStatusIcon($registration->hasPayed()) . "</td>";
    		        echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        
    		        echo "<td>";
    		        if ($registration->SpotAtLARP == 1) {
    		            echo showStatusIcon(true);
    		        }
    		        else {
        		        echo showStatusIcon(false);
        		        echo "</td><td>";
        		        if ($registration->allChecksPassed()) {
            		        echo "<form method='post' action='give_spot.php'>";
            		        echo "<input type='hidden' id='RegistrationId' name='RegistrationId' value='$registration->Id'>";
            		        
            		        echo "<input type='button' value='Ge plats'>";
            		        echo "</form>";
        		        }
    		        }
    		        echo "</td>";
    		            
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
        
        
	</div>
</body>

</html>
