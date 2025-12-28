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
			echo "NPC'er filtrerade på levande<br><br>\n";
			echo '<button id="btn_show" onclick="show_hide();">Visa alla</button>\n';
			echo "<br><br>\n";
			
		    $tableId = "npc_roles";
		    $colnum = 0;
		    echo "<table id='$tableId' class='data'>\n";
		    echo "<tr>\n";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>\n";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Godkänd NPC</th>\n";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Grupptillhörighet</th>\n";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Tilldelad i ett uppdrag</th>\n";
		    echo "</tr>";

			$npcs = Role::getAllNPC($current_larp);
			foreach ($npcs as $npc) {
			    if ($npc->IsDead) echo "<tr class='show_hide hidden'>\n";
			    else echo "<tr>\n";
			    
			    echo "<td>".$npc->getViewLink()." ".$npc->getEditLinkPen(true);
			    
			    if($npc->mayDelete()) {
			        echo "&nbsp;<a href='logic/delete_role.php?id=" . $npc->Id . "'><i class='fa-solid fa-trash' title='Ta bort karaktär'></i></a>";
			    }
			    
			    echo "</td>\n";
			    
			    echo "<td>\n";
			    if ($npc->isApproved()) echo showStatusIcon(true);
			    elseif ($npc->userMayEdit()) echo showStatusIcon(false)."<br>Spelare får ändra på karaktären och därför kan den inte godkännas.";
			    else echo showPostStatusIcon(false, 'logic/approve.php', null, 'Klicka för att godkänna NPC', NULL, ['RoleId'=>$npc->Id], null);

			    $group = $npc ->getGroup();
			    if (!empty($group)) {
			        echo "<td>".$group->getViewLink().' '.$group->getVisibilityText()."</td>\n";
			    } else echo "<td></td>\n";
			    
			    echo "<td>\n";
			    $assignement = NPC_assignment::getAssignment($npc, $current_larp);
			    if (empty($assignement)) {
			        echo showPostStatusIcon(false, 'npc_form.php', null, 'Skapa NPC-Uppdrag', 'Har uppdrag', ['roleId'=>$npc->Id, 'operation'=>'insert'], null);
			    } elseif ($assignement->isAssigned()) {
			        echo showStatusIcon(true)." ";
			        $person = $assignement->getPerson();
			        $registration = $person->getRegistration($current_larp);
			        if ($registration->isNotComing()) echo "<s>".$person->getViewLink()."</s>  ".showStatusIcon(false, NULL, NULL, 'Kommer inte på lajvet');
			        else echo $person->getViewLink();
			    } else {
			        echo "<form action='npc_form.php' method='POST'><input type='hidden' id='roleId' name='roleId' value='$npc->Id'>\n";
			        echo "<input type='hidden' id='operation' name='operation' value='update'><button class='invisible' type='submit'>".showWarningIcon('Saknar spelare')."</button></form>\n";
			    }
			    
			    echo "</td>\n";
			    
			    echo "<tr>\n";
			}
			echo "</table>";
			
			?>
			
  	</div>
</body>

</html>
  