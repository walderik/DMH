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
        <h1>Anmälda deltagare på torsdagen</h1>
        Karaktärer som är märkta med "Torsdag" visas med fetstil.
     		<?php 
    		$persons = Person::getAllRegistered($current_larp, true);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
    		} else {
    		    $tableId = "thursday";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		      "<th onclick='sortTable(1, \"$tableId\")'>Exakt en karaktär på torsdagen</th>".
    		      "<th onclick='sortTable(2, \"$tableId\")'>Karaktär(er)</th>".
    		      "<th onclick='sortTable(3, \"$tableId\")' >Plats på lajvet</th></tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        if ($registration->isNotComing()) continue;
    		        if (str_contains($registration->LarpPartNotAttending, "2026-09-13")) continue; 

    		        echo "<tr>\n";
    		        
    		        echo "<td>";
                    echo $person->getViewLink(true);
    		        echo "</td>\n";
    		        
    		        $roles = $person->getRolesAtLarp($current_larp);
    		        $thursdayRoles = array();
    		        $notThyrsdayRoles = array();
    		        foreach ($roles as $role) {
    		            $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    		            $intrrigueTypes = commaStringFromArrayObject($larp_role->getIntrigueTypes());
    		            if (str_contains($intrrigueTypes, "Torsdag")) $thursdayRoles[] = $role;
    		            else $notThyrsdayRoles[] = $role;
    		        }
    		        echo "<td>";
    		        echo showStatusIcon(count($thursdayRoles) == 1);
    		        echo "</td>\n";

    		        echo "<td>";
    		        foreach ($thursdayRoles as $role) {
    		            echo "<b>".$role->getViewLink()."</b>";
    		            $wealth = $role->getWealth();
    		            if (!empty($wealth)) echo ", rikedom: $wealth->Name";
    		            echo ", $role->Profession<br>";
    		        }
    		        foreach ($notThyrsdayRoles as $role) {
    		            echo $role->getViewLink();
    		            $wealth = $role->getWealth();
    		            if (!empty($wealth)) echo ", rikedom: $wealth->Name";
    		            echo ", $role->Profession<br>";
    		        }
    		        echo "</td>\n";
    		            

    		        echo "<td>";

		            echo showStatusIcon($registration->hasSpotAtLarp());
     		        echo "</td>";

    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
        
        
	</div>
</body>

</html>
