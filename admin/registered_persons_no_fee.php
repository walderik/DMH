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
        <h1>Anmälda deltagare som inte har fått avgiften automatiskt beräknad</h1>
        <br>
     		<?php 
     		$campaign = $current_larp->getCampaign();
     		$persons = Person::getAllRegisteredNoFee($current_larp);
    		if (empty($persons)) {
    		    echo "Alla anmälda har avgift satt";
    		} else {
    		    $tableId = "participants";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		      "<th onclick='sortTable(1, \"$tableId\")'>Epost</th>".
    		      "<th onclick='sortTableNumbers(2, \"$tableId\")'>Ålder<br>på lajvet</th>".
    		      "<th onclick='sortTable(3, \"$tableId\")' >Sätt avgift</th>".
    		      "</tr>\n";
    		    foreach ($persons as $person)  {
    		        echo "<tr>\n";
    		        echo "<td>";
		            echo $person->getViewLink();
    		        echo "</td>\n";
     		        echo "<td>";
    		        echo $person->Email;
    		        echo " ".contactEmailIcon($person)."</td>\n";
    		        echo "</td>\n";

		            echo "<td>";
		            $age = $person->getAgeAtLarp($current_larp);
                    echo $age." år";
		            if ($age < $campaign->MinimumAgeWithoutGuardian) {
		                echo "<br>Ansvarig vuxen är ";
		                $registration = $person->getRegistration($current_larp);
		                if (!empty($registration->GuardianId)) {
		                    $guardian = $registration->getGuardian();
		                    echo $guardian->getViewLink();
		                } else echo showStatusIcon(false);
		            }
		            echo "</td>";
		            
    		        echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-money-check-dollar'></i></a></td>\n";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
        
        
	</div>
</body>

</html>
