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
     		<?php 
    		$persons = Person::getAllRegistered($current_larp, false);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
    		} else {
    		    $tableId = "participants";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		      "<th onclick='sortTableNumbers(1, \"$tableId\")'>Ålder<br>på lajvet</th>".
    		      "<th onclick='sortTable(2, \"$tableId\")'>Karaktärer</th>".
    		      "<th onclick='sortTable(3, \"$tableId\")'>Boende</th>".

    		      "</tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        if ($registration->isCheckedOut()) continue;
    		        echo "<tr>\n";
    		        
    		        echo "<td>";
                    echo "<a href='checkin_person.php?id=$person->Id'>$person->Name</a>";
    		        echo "</td>\n";
    		        
                    echo "<td>" . $person->getAgeAtLarp($current_larp) . " år ";
    		       echo "</td>";       
    		            
		            echo "<td>";
                    $roles = $person->getRolesAtLarp($current_larp);
                    foreach ($roles as $role) {
                        echo $role->Name;
                        $group= $role->getGroup();
                        if (!empty($group)) echo " - ".$group->Name;
                        echo "<br>";
                    }
		            echo "</td>\n";
		            echo "<td>";
		            $house = House::getHouseAtLarp($person, $current_larp);
		            if (!empty($house)) {
		                 echo "<a href='view_house.php?id=$house->Id&action=checkin'>$house->Name</a>"; 
		            echo "</td>\n";
    		        echo "</tr>\n";
    		    }

    		}
    		echo "</table>";
		}
    		?>

        
        
        
	</div>
</body>

</html>
