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
        <h1>Pengar till karaktärer vid början av lajvet</h1>
        <p>Du kan sätta <a href="role_money_setup.php">pengarna på många karaktärer samtidigt</a> utifrån rikedomsnivå och resultat från tidigare lajv.<br>
        Sedan kan man justera sifforna indivuduellt där det behövs.<br>
        Om de inte har någora pengar, sätt '0' så kommer möjligheten att skriva in pengar för karaktären vid utcheckningen upp.
        </p>
        
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
    		        
    		        echo "<td><input type='number' id='$role->Id' value='$larp_role->StartingMoney' onchange='setMoney(this, $current_larp->Id)'>";
    		        echo " <a href='logic/remove_set_money.php?roleId=$role->Id&larpId=$current_larp->Id'><i class='fa-solid fa-xmark' title='Sätt pengarna till osatta'></i></a>";
    		        echo "</td>";
    		        
    		        
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
    		        
    		        echo "<td><input type='number' id='$role->Id' value='$larp_role->StartingMoney' onchange='setMoney(this, $current_larp->Id)'>";
    		        echo " <a href='logic/remove_set_money.php?roleId=$role->Id&larpId=$current_larp->Id'><i class='fa-solid fa-xmark' title='Sätt pengarna till osatta'></i></a>"; 
    		        echo "</td>";
    		        
    		        echo "</tr>\n";
    		    
    		    }
    		    echo "</table>";
    		}
    		?>
        
	</div>
</body>


</html>
