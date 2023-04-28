<?php
 include_once 'header.php';
 
 include 'navigation_subpage.php';
?>
<style>
th {
  cursor: pointer;
}

</style>

    <div class="content">   
        <h1>Anmälda deltagare</h1>
     		<?php 
    		$persons = Person::getAllRegistered($current_larp);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
    		} else {
    		    echo "<table id='participants' class='data'>";
    		    echo "<tr><th onclick='sortTable(0)'>Namn</th>".
    		      "<th></th>".
    		      "<th onclick='sortTable(2)'>Epost</th>".
    		      "<th onclick='sortTable(3)'>Ålder<br>på lajvet</th>".
    		      "<th onclick='sortTable(4)'>Godkända<br>karaktärer</th>".
    		      "<th onclick='sortTable(5)'>Medlem</th>".
    		      "<th onclick='sortTable(6)'>Betalnings-<br>referens</th>".
    		      "<th onclick='sortTable(7)' colspan='2'>Betalat</th>".
    		      "<th onclick='sortTable(9)' colspan='2'>Plats på lajvet</th></tr>\n";
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
            		        echo "<form method='post' action='logic/give_spot.php'>";
            		        echo "<input type='hidden' id='RegistrationId' name='RegistrationId' value='$registration->Id'>";
            		        
            		        echo "<input type='submit' value='Ge plats'>";
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


<script>
function sortTable(n) {
  var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
  table = document.getElementById("participants");
  switching = true;
  // Set the sorting direction to ascending:
  dir = "asc";
  /* Make a loop that will continue until
  no switching has been done: */
  while (switching) {
    // Start by saying: no switching is done:
    switching = false;
    rows = table.rows;
    /* Loop through all table rows (except the
    first, which contains table headers): */
    for (i = 1; i < (rows.length - 1); i++) {
      // Start by saying there should be no switching:
      shouldSwitch = false;
      /* Get the two elements you want to compare,
      one from current row and one from the next: */
      x = rows[i].getElementsByTagName("TD")[n];
      y = rows[i + 1].getElementsByTagName("TD")[n];
      /* Check if the two rows should switch place,
      based on the direction, asc or desc: */
      if (dir == "asc") {
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      } else if (dir == "desc") {
        if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
          // If so, mark as a switch and break the loop:
          shouldSwitch = true;
          break;
        }
      }
    }
    if (shouldSwitch) {
      /* If a switch has been marked, make the switch
      and mark that a switch has been done: */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
      switching = true;
      // Each time a switch is done, increase this count by 1:
      switchcount ++;
    } else {
      /* If no switching has been done AND the direction is "asc",
      set the direction to "desc" and run the while loop again. */
      if (switchcount == 0 && dir == "asc") {
        dir = "desc";
        switching = true;
      }
    }
  }
}
</script>

</html>
