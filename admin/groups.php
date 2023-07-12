<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>


    <div class="content">   
        <h1>Grupper</h1>
            <a href="create_group.php"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a> &nbsp; 
            <a href='group_sheet.php?' target='_blank'><i class='fa-solid fa-file-pdf' title='Allt om alla grupper som en stor PDF (tar tid att generera)'></i> Allt om alla</a> &nbsp;
            <?php echo contactAllGroupLeadersEmailIcon('Skicka till gruppledarna') ?>

     		<?php 
    		$groups = Group::getAllRegistered($current_larp);
    		if (empty($groups)) {
    		    echo "Inga anmälda grupper";
    		} else {
    		    $tableId = "main_roles";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
        		    "<th>&nbsp; &nbsp; </th>".
        		    "<th onclick='sortTable(2, \"$tableId\")'>Gruppledare</th>".
        		    "<th onclick='sortTable(3, \"$tableId\")'>Rikedom</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")' colspan='2'>Intrig</th></tr>\n";
    		    foreach ($groups as $group)  {
    		        echo "<tr>\n";
    		        echo "<td>" . $group->Name;
    		        if ($group->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
    		        if ($group->userMayEdit($current_larp)) echo "<br>Gruppledaren får ändra gruppen " . showStatusIcon(false);
    		        
    		        echo "</td>\n";
    		        echo "<td>" . "<a href='view_group.php?id=" . $group->Id . "'><i class='fa-solid fa-eye'></i></a>\n";
    		        echo "<a href='edit_group.php?id=" . $group->Id . "'><i class='fa-solid fa-pen'></i></a>\n";
    		        echo "<a href='group_sheet.php?id=" . $group->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Gruppblad för $group->Name'></i></a>\n";
    		        echo "</td>\n";
    		        $person = $group->getPerson();
    		        echo "<td>" . $person->Name . " " . contactEmailIcon($person->Name,$person->Email) . "</td>\n";
    		        echo "<td>" . $group->getWealth()->Name . "</td>\n";
    		        $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    		        echo "<td>" . showStatusIcon($larp_group->hasIntrigue());

    		        if ($larp_group->hasIntrigue()) echo "<br>".str_word_count($larp_group->Intrigue)." ord";
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
<?php include_once '../javascript/table_sort.js';?>

</html>
