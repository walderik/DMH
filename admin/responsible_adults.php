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
    		        if ($registration->isNotComing()) {
    		            echo "<s>$person->Name</s>" . " ".contactEmailIcon($person). " " .showStatusIcon(false);
    		        } else echo $person->getViewLink() . " ".contactEmailIcon($person);
    		        

		            echo "<td>" . $person->getAgeAtLarp($current_larp) . " år</td>";
		            
		            echo "<td>". $person->getMainRole($current_larp)->getViewLink() . "</td>";
		            
		            echo "<td>";
		            $minors = $person->getGuardianFor($current_larp);
		            foreach ($minors as $minor) {
		                echo $minor->getViewLink() . " " . $minor->getAgeAtLarp($current_larp) . " år<br>";
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
