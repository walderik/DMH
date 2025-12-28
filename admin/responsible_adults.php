<?php
 include_once 'header.php';
 
 $persons = Person::getAllGuardians($current_larp, true);
 $personIdArr = array();
 foreach ($persons as $person) {
    $personIdArr[] = $person->Id;
 }
 
 
 include 'navigation.php';
?>

<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>

    <div class="content">   
        <h1>Ansvariga vuxna 
        	<?php echo contactSeveralEmailIcon('', $personIdArr, "Ansvarig vuxen", "Meddelande till alla som är ansvariga för en minerårig på $current_larp->Name") ?>
    	</h1>
        <br>
     		<?php 

    		if (empty($persons)) {
    		    echo "Inga anvariga vuxna";
    		} else {
    		    $tableId = "guardians";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		      "<th onclick='sortTableNumbers(1, \"$tableId\")'>Ålder<br>på lajvet</th>".
    		      "<th onclick='sortTable(2, \"$tableId\")'>Huvudkaraktär</th>".
    		      "<th onclick='sortTable(3, \"$tableId\")'>Ansvarig för</th>".
    		      "</tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        echo "<tr>\n";
    		        
    		        echo "<td>";
    		        echo $person->getViewLink(false) . " ".contactEmailIcon($person);
    		        if ($registration->isNotComing()) showStatusIcon(false);
    		        echo "</td>\n";
    		        
		            echo "<td>" . $person->getAgeAtLarp($current_larp) . " år</td>\n";
		            
		            $mainRole = $person->getMainRole($current_larp);
		            if (!empty($mainRole)) {
		              echo "<td>". $person->getMainRole($current_larp)->getViewLink() . "</td>\n";
		            } else {
		                echo "<td>Har ingen roll på lajvet</td>\n";
		            }
		            
		            echo "<td>";
		            $minors = $person->getGuardianFor($current_larp);
		            foreach ($minors as $minor) {
		                echo $minor->getViewLink()."<br>";
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
