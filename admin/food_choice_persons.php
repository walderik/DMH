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
        <h1>Deltagares matval</h1>
        

        
        <br>
     		<?php 
     		$foodChoises = Registration::getFoodVariants($current_larp);
     		$hasFoodChoices = false;
     		foreach($foodChoises as $foodChoise) {
     		    if (!empty($foodChoise[0])) {
     		        $hasFoodChoices = true;
     		        break;
     		    }
     		}
     		
    		$persons = Person::getAllRegistered($current_larp, false);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
    		} else {
    		    $tableId = "participants";
    		    echo "<table id='$tableId' class='data'>";
    		    $col=0;
    		    echo "<tr><th onclick='sortTable(".$col++.", \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTableNumbers(".$col++.", \"$tableId\")'>Ålder</th>".
        		    "<th onclick='sortTable(".$col++.", \"$tableId\")'>Boende</th>".
        		    "<th onclick='sortTable(".$col++.", \"$tableId\")'>Vald mat</th>";
    		    if ($hasFoodChoices) echo "<th onclick='sortTable(".$col++.", \"$tableId\")'>Matalternativ</th>";
    		    echo "<th onclick='sortTable(".$col++.", \"$tableId\")'>Har allergi</th>";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);


    		        
    		        $allergyStr = 'Nej';
    		        if (!empty($person->getNormalAllergyTypes()) || !empty($person->FoodAllergiesOther)) $allergyStr = 'Ja';
    		        
    		        $housingStr = "";
    		        $housing = $person->getHouseAtLarp($current_larp);
    		        if (isset($housing)) $housingStr=$housing->Name;
    		        
    		        echo "<tr>\n";
    		        echo "<td>".$person->getViewLink(false)."</td>\n";
    		        echo "<td>".$person->getAgeAtLarp($current_larp)."</td>\n";
                    echo "<td>$housingStr</td>\n";
    		        echo "<td>".$registration->getTypeOfFood()->Name."</td>\n";
    		        if ($hasFoodChoices) echo "<td>".$registration->FoodChoice."</td>\n";
    		        echo "<td>".$allergyStr."</td>\n";
    		        
 		         echo "</tr>\n";
		      }
		      echo "</table>";
		  }
    		?>

        
        
        
	</div>
</body>

</html>
