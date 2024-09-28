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
<script src="../javascript/setmoney_ajax.js"></script>


    <div class="content">   
        <h1>Pengar till grupper vid början av lajvet</h1>
        <p>Du kan sätta <a href="group_money_setup.php">pengarna på många grupper samtidigt</a> utifrån rikedomsnivå och resultat från tidigare lajv.<br>
        Sedan kan man justera sifforna indivuduellt där det behövs.</p>
     		<?php 
     		$groups = Group::getAllRegistered($current_larp);
     		$currency = $current_larp->getCampaign()->Currency;
     		
     		if (empty($groups)) {
    		    echo "Inga anmälda grupper";
    		} else {
    		    $tableId = "main";
    		    $colnum = 0;
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>";
    		    if (Wealth::isInUse($current_larp))  echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Rikedom</th>";
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Pengar ($currency)</th>".
        		    "</tr>";
    		    foreach ($groups as $group)  {
    		        $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        echo $group->getViewLink();
    		        echo "</td>\n";
    		        if (Wealth::isInUse($current_larp)) {
        		        $wealth = $group->getWealth();
        		        echo "<td>";
        		        if (!empty($wealth)) echo $wealth->Name;
        		        echo "</td>\n";
    		        }
    		        echo "<td><input type='number' id='$group->Id' value='$larp_group->StartingMoney' onchange='setMoneyGroup(this, $current_larp->Id)'>";
    		        echo " <a href='logic/remove_set_money.php?groupId=$group->Id&larpId=$current_larp->Id'><i class='fa-solid fa-xmark' title='Sätt pengarna till osatta'></i></a>";
    		        
    		        echo "</td>";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
        
	</div>
</body>


</html>
