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
        <h1>Pengar som grupperna hade i slutet av lajvet</h1>
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
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Pengar start($currency)</th>".
        		     "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Pengar slut($currency)</th>".
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
    		        echo "<td>$larp_group->StartingMoney</td>";
    		        echo "<td><input type='number' id='$group->Id' value='$larp_group->EndingMoney' onchange='setMoneyGroupEnd(this, $current_larp->Id)'></td>";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
        
	</div>
</body>


</html>
