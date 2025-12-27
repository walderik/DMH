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
        <h1>Reservlistan 
			<?php 
			$personIdArr = array();
			$persons = Person::getAllReserves($current_larp);

			foreach($persons as $person) {
				$personIdArr[] = $person->Id;
			}
			$ikon = contactSeveralEmailIcon("", $personIdArr, "du som är anmäld i reservlistan", "Meddelande till alla på reservlistan till $current_larp->Name");
			
			echo "$ikon &nbsp; &nbsp;";
			?>
		</h1>
        Detta är listan med deltagare som har gjort en anmälan efter att lajvet är fullt. De har fått meddelande om att de står på reservplats. Listan tar inte hänsyn till i vilken ordning de har komit in på den.<br><br>
        Genom att klicka på "Gör till en anmälan" omvandlas deltagare på reservlistan till en vanlig anmälan. Den får sedan hanteras i vanlig ordning med eventuella godkännanden.<br><br>
        Efter ett gruppnamn visas hur många i gruppen som redan är anmälda till lajet och hur många som står på reservlistan. Antalet i grupper inkluderar bara huvudkaraktärer.
     		<?php 
    		$persons = Person::getAllReserves($current_larp);
    		if (empty($persons)) {
    		    echo "<br><br>Reservlistan är tom";
    		} else {
    		    $tableId = "participants";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		      "<th onclick='sortTable(1, \"$tableId\")'>Epost</th>".
    		      "<th onclick='sortTable(2, \"$tableId\")'>Ålder<br>på lajvet</th>".
    		      "<th onclick='sortTable(3, \"$tableId\")'>Roller</th>".
    		      "<th onclick='sortTable(4, \"$tableId\")'>Funktionärsönskemål</th>".
    		      "<th onclick='sortTable(5, \"$tableId\")'>NPC-önskemål</th>".
    		      "<th onclick='sortTable(6, \"$tableId\")'>Boendeönskemål</th>";
    		    echo "<th></th></tr>\n";
    		    foreach ($persons as $person)  {
    		        $reserve_registration = $person->getReserveRegistration($current_larp);
    		        echo "<tr>\n";
    		        echo "<td>" . $person->Name . "</td>\n";
    		        echo "<td>" . $person->Email . " ".contactEmailIcon($person)."</td>\n";
    		        echo "<td>" . $person->getAgeAtLarp($current_larp) . " år ";

    		        if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {

    		            if (empty($reserve_registration->GuardianId)) {
    		                echo showStatusIcon(false);
    		            }
    		                

		            }
    		        echo "</td>\n";

    		        
    		        echo "<td>";
    		        $reserve_roles = Reserve_LARP_Role::getReserveRolesForPerson($current_larp->Id, $person->Id);
    		        foreach ($reserve_roles as $reserve_role) {
    		            $role = Role::loadById($reserve_role->RoleId);
    		            $group = $role->getGroup();
    		            echo $role->getViewLink() . "\n";

    		            if ($reserve_role->IsMainRole == 0) {
    		                echo " (Sidokaraktär)";
    		            }
    		            if (isset($group)) {
    		                echo "<br>  ".$group->getViewLink(); 
    		                if ($reserve_role->IsMainRole == 1) {
        		                $roles = Role::getAllMainRolesInGroup($group, $current_larp);
        		                echo "<br>". count($roles) . " deltagare, ";
        		                $reserves = Role::getAllMainReservesInGroup($group, $current_larp);
        		                echo count($reserves);
        		                echo " reserver<br>";
    		                }
    		            }
    		            echo "<br>\n";
    		        }
    		        echo "</td>\n";
    		        
    		        echo "<td>" .  commaStringFromArrayObject($reserve_registration->getOfficialTypes()) . "</td>\n";
    		        echo "<td>" .  $reserve_registration->NPCDesire . "</td>\n";
    		        
    		        echo "<td>" .  $reserve_registration->getHousingRequest()->Name . "</td>\n";
    		        
    		        
    		        echo "<td>";
    		        echo "<form method='post' action='logic/make_into_registration.php'>";
    		        echo "<input type='hidden' id='Reserve_RegistrationId' name='Reserve_RegistrationId' value='$reserve_registration->Id'>";
    		        
    		        echo "<input type='submit' value='Gör till en anmälan'>";
    		        echo "</form>";

					echo "<br>";

					echo "<form method='post' action='logic/remove_from_reserves.php'>";
    		        echo "<input type='hidden' id='Reserve_RegistrationId' name='Reserve_RegistrationId' value='$reserve_registration->Id'>";
    		        
    		        echo "<input type='submit' value='Ta bort från reservlistan'>";
    		        echo "</form>";

    		        echo "</td>";
    		            
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
        
        
	</div>
</body>

</html>
