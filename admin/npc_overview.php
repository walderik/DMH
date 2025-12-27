<?php
include_once 'header.php';





include 'navigation.php';
include 'npc_navigation.php';

?>

<script src="../javascript/table_sort.js"></script>
<script src="../javascript/show_hide_rows.js"></script>

    <div class="content">   
        <h1>Alla NPC</h1>

			<?php 
			echo "NPC'er filtrerade på levande<br>";
			echo '<button id="btn_show" onclick="show_hide();">Visa alla</button>';
			echo "<br><br>";
			
		    $tableId = "npc_roles";
		    $colnum = 0;
		    echo "<table id='$tableId' class='data'>";
		    echo "<tr>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Godkänd</th>";
		    
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Grupp</th>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Spelas</th>";
		    echo "</tr>";

			$npcs = Role::getAllNPC($current_larp);
			foreach ($npcs as $npc) {
			    if ($npc->IsDead) echo "<tr class='show_hide hidden'>\n";
			    else echo "<tr>";
			    
			    echo "<td>".$npc->getViewLink()." ".$npc->getEditLinkPen(true);
			    
			    if($npc->mayDelete()) {
			        echo "&nbsp;<a href='logic/delete_role.php?id=" . $npc->Id . "'><i class='fa-solid fa-trash' title='Ta bort karaktär'></i></a>";
			    }
			    
			    echo "</td>";
			    
			    echo "<td>";
			    if ($npc->isApproved()) echo showStatusIcon(true);
			    elseif ($npc->userMayEdit()) echo showStatusIcon(false)."<br>Spelare får ändra på karaktären och därför kan den inte godkännas.";
			    else {
			        echo "<form action='logic/approve.php' method='post'>";
			        echo "<input type='hidden' id='RoleId' name='RoleId' value='$npc->Id'>";
			        echo "<button class='invisible' type='submit'>".showStatusIcon(false)."</button>";
			        echo "</form>";
			    }
			    $group = $npc ->getGroup();
			    if (!empty($group)) {
			        echo "<td>".$group->getViewLink()."</td>";
			    } else echo "<td></td>";
			    
			    echo "<td>";
			    $assignement = NPC_assignment::getAssignment($npc, $current_larp);
			    if (empty($assignement)) {
			        echo "<form action='npc_form.php' method='POST'><input type='hidden' id='roleId' name='roleId' value='$npc->Id'><input type='hidden' id='operation' name='operation' value='insert'><button class='invisible' type='submit'>".showStatusIcon(false)."</button></form>";
			    } elseif ($assignement->isAssigned()) {
			        echo showStatusIcon(true)." ";
			        $person = $assignement->getPerson();
			        $registration = $person->getRegistration($current_larp);
			        if ($registration->isNotComing()) echo "<s>".$person->getViewLink()."</s> ".showStatusIcon(false);
			        else echo $person->getViewLink();
			    } else {
			        echo "<form action='npc_form.php' method='POST'><input type='hidden' id='roleId' name='roleId' value='$npc->Id'><input type='hidden' id='operation' name='operation' value='update'><button class='invisible' type='submit'>".showWarningIcon()."</button></form>";
			    }
			    
			    echo "</td>";
			    
			    echo "<tr>";
			}
			echo "</table>";
			
			?>
			
  	</div>
</body>

</html>
  