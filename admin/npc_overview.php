<?php
include_once 'header.php';





include 'navigation.php';
include 'npc_navigation.php';

?>

<script src="../javascript/table_sort.js"></script>

    <div class="content">   
        <h1>NPC</h1>
            <a href="edit_role.php?operation=insert&type=npc"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC</a>&nbsp;  
            <a href="edit_group.php?operation=insert"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC grupp</a>  

			<?php 
		    $tableId = "npc_roles";
		    $colnum = 0;
		    echo "<table id='$tableId' class='data'>";
		    echo "<tr>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>";

		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Grupp</th>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Spelare</th>";
		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Intriger</th>";
		    echo "</tr>";

			$npcs = Role::getAllNPC($current_larp);
			foreach ($npcs as $npc) {
			    echo "<tr>";
			    echo "<td>".$npc->getViewLink()." ".$npc->getEditLinkPen(true)."</td>";
			    $group = $npc ->getGroup();
			    if (!empty($group)) {
			        echo "<td>".$group->getViewLink()."</td>";
			    } else echo "<td></td>";
			    
			    //TODO tilldelning, också info om mail
			    echo "<td></td>";
			    
			    echo "<td>";
			    if ($npc->hasIntrigue($current_larp)) echo showStatusIcon(true);
			    $intrigueWords = $npc->intrigueWords($current_larp);
			    
			    if (!empty($intrigueWords)) echo "<br>$intrigueWords ord";
			    $intrigues = Intrigue::getAllIntriguesForRole($npc->Id, $current_larp->Id);
			    echo "<br>";
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
  