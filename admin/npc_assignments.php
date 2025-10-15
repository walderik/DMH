<?php
include_once 'header.php';

$persons=Person::getAllInterestedNPC($current_larp);

include 'navigation.php';
include 'npc_navigation.php';

?>

<script src="../javascript/table_sort.js"></script>

    <div class="content">   
        <h1>NPC uppdrag</h1>
			<?php 
			
		    $tableId = "npc_roles";
		    $colnum = 0;
		    echo "<table id='$tableId' class='data'>";
		    echo "<tr>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>";

		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Grupp</th>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Spelare</th>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Släppt</th>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Tid</th>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Instruktioner</th>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'></th>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Intriger</th>";
		    echo "</tr>";

		    $npcs = Role::getAllNPCToBePlayed($current_larp);
			foreach ($npcs as $npc) {
			    $assignement = NPC_assignment::getAssignment($npc, $current_larp);
			    $person = $assignement->getPerson();
			    echo "<tr>";
			    echo "<td>".$npc->getViewLink()." ".$npc->getEditLinkPen(true)."</td>";
			    $group = $npc ->getGroup();
			    if (!empty($group)) {
			        echo "<td>".$group->getViewLink()."</td>";
			    } else echo "<td></td>";
			    
			    echo "<td>";
			    if (empty($assignement)) {
			        echo "<form action='logic/npc_tobeplayed.php' method='post'><input type='hidden' id='roleId' name='roleId' value='$npc->Id'><input type='submit' value='Sätt att karaktären ska spelas på lajvet'></form>";
			    } elseif ($assignement->isAssigned()) {
			        $registration = $person->getRegistration($current_larp);
			        if ($registration->isNotComing()) {
			            echo "<s>$person->Name</s> ".showStatusIcon(false);
			        } else echo $person->getViewLink();
			
			        echo " <form action='logic/assign_npc.php' method='post' style='display:inline-block'";
			        if ($assignement->isAssigned() && $assignement->isReleased() && !$registration->isNotComing()) {
 			            echo " onsubmit='return confirm(\"Karaktären är redan skickad till $person->Name. Är du säker på att du ska ta bort uppdraget från hen? Glöm i så fall inte att kommunicera det till $person->Name.\")'";
 			        }
 			        echo "><input type='hidden' name='roleId' value='$npc->Id'>\n";
			        echo "<input type='hidden' name='PersonId' value='null'>\n";
			        echo "<button class='invisible' type='submit'><i class='fa-solid fa-xmark' title='Ta bort från deltagaren'></i></button>";
			        echo "</form>\n";
			        if (!$registration->isNotComing()) {
    			        echo " <form action='logic/release_npc.php' method='post' style='display:inline-block'><input type='hidden' name='roleId' value='$npc->Id'>\n";
    			        echo " <button class='invisible' type ='submit'><i class='fa-solid fa-envelope' title=''Skicka NPC:n till deltagaren'></i></button>\n";
    			        echo "</form>\n";
			        }
			        
			    } else {
			        echo "<form action='logic/assign_npc.php' method='post'><input type='hidden' name='roleId' value=$npc->Id>";
			        echo selectionDropDownByArray("PersonId", $persons);
			        echo "<input type ='submit' value='Tilldela'>";
			        echo "</form>";
			    }
			    
			    echo "</td>";
			    
			    echo "<td>";
			    if ($assignement->isReleased()) echo showStatusIcon(true);
			    elseif ($assignement->isAssigned() && !$registration->isNotComing()) {
    			    echo " <form action='logic/release_npc.php' method='post' style='display:inline-block'><input type='hidden' name='roleId' value='$npc->Id'>\n";
    			    echo " <button class='invisible' type ='submit'>".showStatusIcon(false)."</button>\n";
    			    echo "</form>\n";
			    } else {
			        echo showStatusIcon(false);
			    }
			    echo "</td>";
			    echo "<td>$assignement->Time</td>";
			    echo "<td>$assignement->Instructions</td>";
			    
			    echo "<td>";
			    echo "<form action='npc_form.php' method='POST'><input type='hidden' id='roleId' name='roleId' value='$npc->Id'><input type='hidden' id='operation' name='operation' value='update'><button class='invisible' type='submit'><i class='fa-solid fa-pen' title='Redigera uppdrag'></i></button></form>";
			    
			    echo "<form action='logic/npc_not_to_be_played.php' method='POST'";
			    if ($assignement->isAssigned() && $assignement->isReleased() && !$registration->isNotComing()) {
			        echo " onsubmit='return confirm(\"Karaktären är redan skickad till $person->Name. Är du säker på att du ska ta bort uppdraget? Glöm i så fall inte att kommunicera det till $person->Name.\")'";
			    }
			    echo "><input type='hidden' id='roleId' name='roleId' value='$npc->Id'><input type='hidden' id='operation' name='operation' value='update'><button class='invisible' type='submit'><i class='fa-solid fa-trash' title='Ta bort uppdrag'></i></button></form>";
			    
			    echo "</td>";
			    
			    
			    echo "<td>";
			    $intrigues = Intrigue::getAllIntriguesForRole($npc->Id, $current_larp->Id);
			    if (!empty($intrigues)) echo "Intrig: ";
			    foreach ($intrigues as $intrigue) {
			        echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
			        if ($intrigue->isActive()) echo $intrigue->Number;
			        else echo "<s>$intrigue->Number</s>";
			        echo "</a>";
			        echo " ";
			    }
			    
			    echo "</td>\n";
			    
			    
			    echo "<tr>";
			}
			echo "</table>";
			
			?>
			
  	</div>
</body>

</html>
  