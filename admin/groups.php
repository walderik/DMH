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
        <h1>Grupper</h1>
            <a href="create_group.php"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a> &nbsp; 
            <a href='group_sheet.php?all_info=true' target='_blank'><i class='fa-solid fa-file-pdf' title='Allt om alla grupper som en stor PDF (tar tid att generera)'></i> Allt om alla</a> &nbsp;
            <a href='group_sheet.php?' target='_blank'><i class='fa-solid fa-file-pdf' title='Alla grupper som det ser ut för deltagarna (tar tid att generera)'></i> Alla grupper som det ser ut för deltagarna</a> &nbsp;
 
     		<?php 
    		$groups = Group::getAllRegistered($current_larp);
    		if (empty($groups)) {
    		    echo "Inga anmälda grupper";
    		} else {
    		    $emailArr = array();
    		    foreach ($groups as $group) {
    		        $person = $group->getPerson();
    		        $personIdArr[] = $person->Id;
    		    }
    		    
    		    echo contactSeveralEmailIcon('Skicka till gruppledarna', $personIdArr, 'Gruppledare', "Meddelande till alla gruppledarna i $current_larp->Name");
    		    
    		    $tableId = "groups";
    		    $colnum=0;
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
        		    "<th>&nbsp; &nbsp; </th>";
    		    $colnum++;
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Gruppledare</th>";
				echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Antal medlemmar</th>";
    		    if (Wealth::isInUse($current_larp)) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Rikedom</th>";
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")' colspan='2'>Intrig</th></tr>\n";
    		    foreach ($groups as $group)  {
    		        echo "<tr>\n";
    		        echo "<td>";
					echo $group->getViewLink();
    		        if ($group->userMayEdit($current_larp)) echo "<br>Gruppledaren får ändra gruppen " . showStatusIcon(false);
    		        
    		        echo "</td>\n";
    		        echo "<td>";
    		        echo $group->getEditLinkPen(true);
					echo "\n";
    		        echo "<a href='group_sheet.php?id=" . $group->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Gruppblad för $group->Name'></i></a>\n";
    		        echo "</td>\n";
    		        $person = $group->getPerson();
    		        echo "<td>" . $person->Name . " " . contactEmailIcon($person) . "</td>\n";
					echo "<td>" . $group->countAllRolesInGroup($current_larp) . "</td>\n";
    		        if (Wealth::isInUse($current_larp)) echo "<td>" . $group->getWealth()->Name . "</td>\n";
    		        $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    		        echo "<td>" . showStatusIcon($group->hasIntrigue($current_larp));
    		        $intrigueWords = $group->intrigueWords($current_larp);
    		        
    		        if (!empty($intrigueWords)) echo "<br>$intrigueWords ord";
    		        $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);
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
    		        echo "<td><a href='edit_group_intrigue.php?id=" . $group->Id . "'><i class='fa-solid fa-pen'></i></a>\n";
    		        echo "</td>";
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
         
	</div>
</body>

</html>
