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
        <h1>Pengar karaktären hade i slutet av lajvet</h1>
        <h2>Huvudkaraktärer</h2>
        
     		<?php 
     		$roles = $current_larp->getAllMainRoles(false);
     		$currency = $current_larp->getCampaign()->Currency;
     		
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    $tableId = "main_roles";
    		    $colnum = 0;
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")' width='10%'>Yrke</th>";
    		    if (Wealth::isInUse($current_larp)) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Rikedom</th>";
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Pengar start ($currency)</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Pengar slut ($currency)</th>".
        		    "</tr>";
    		    foreach ($roles as $role)  {
    		        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        echo $role->getViewLink();
    		        echo "</td>\n";
    		        echo "<td>$role->Profession</td>\n";
    		        if (Wealth::isInUse($current_larp)) {
        		        if ($role->isMysLajvare()) {
        		            echo "<td>N/A</td>";
        		        }
        		        else {
            		        $wealth = $role->getWealth();
            		        echo "<td>";
            		        if (!empty($wealth)) echo $wealth->Name;
            		        echo "</td>\n";
        		        }
    		        }
   		           $group = $role->getGroup();
    		        if (is_null($group)) {
    		            echo "<td>&nbsp;</td>\n";
    		        } else {
    		            echo "<td>";
						echo $group->getViewLink();
						echo "</td>\n";
    		        }
    		        echo "<td>$larp_role->StartingMoney</td>";
    		        echo "<td><input type='number' id='$role->Id' value='$larp_role->EndingMoney' onchange='setMoneyEnd(this, $current_larp->Id)'></td>";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
                <h2>Övriga karaktärer</h2>
     		<?php 
     		$roles = $current_larp->getAllNotMainRoles(true);
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    $tableId = "other_roles";
    		    $colnum = 0;
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")' width='10%'>Yrke</th>";
    		    if (Wealth::isInUse($current_larp)) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Rikedom</th>";
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Pengar ($currency)</th>".
        		    "</tr>";
    		    foreach ($roles as $role)  {
    		        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        echo $role->getViewLink();
    		        echo "</td>\n";
    		        echo "<td>$role->Profession</td>\n";
    		        if (Wealth::isInUse($current_larp)) {
        		        if ($role->isMysLajvare()) {
        		            echo "<td>N/A</td>";
        		        }
        		        else {
        		            $wealth = $role->getWealth();
        		            echo "<td>";
        		            if (!empty($wealth)) echo $wealth->Name;
        		            echo "</td>\n";
        		        }
    		        }
    		        $group = $role->getGroup();
    		        if (is_null($group)) {
    		            echo "<td>&nbsp;</td>\n";
    		        } else {
    		            echo "<td>";
						echo $group->getViewLink();
						echo "</td>\n";
    		        }
    		        
    		        echo "<td><input type='number' id='$role->Id' value='$larp_role->StartingMoney' onchange='setMoney(this, $current_larp->Id)'></td>";
    		        
    		        echo "</tr>\n";
    		    
    		    }
    		    echo "</table>";
    		}
    		?>
        
	</div>
</body>


</html>
