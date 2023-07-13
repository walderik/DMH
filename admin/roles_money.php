<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>


    <div class="content">   
        <h1>Pengar till karaktärer vid början av lajvet</h1>
        <h2>Huvudkaraktärer</h2>
        <p>Börja med att köra vår <a href="role_money_wizard_pg1.php">"wizard" <i class="fa-solid fa-wand-sparkles"></i></a> för att sätta upp pengar utifrån rikedomsnivå och resultat från tidigare lajv.<br>
        Sedan kan man justera sifforna indivuduellt där det behövs.</p>
        
     		<?php 
     		$roles = $current_larp->getAllMainRoles(false);
     		$currency = $current_larp->getCampaign()->Currency;
     		
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    $tableId = "main_roles";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTable(1, \"$tableId\")' width='10%'>Yrke</th>".
        		    "<th onclick='sortTable(2, \"$tableId\")'>Rikedom</th>".
        		    "<th onclick='sortTable(3, \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")'>Pengar ($currency)</th>".
        		    "</tr>";
    		    foreach ($roles as $role)  {
    		        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        echo "<a href='view_role.php?id=" . $role->Id . "'>$role->Name</a>";
    		        if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
    		        echo "</td>\n";
    		        echo "<td nowrap>" . "<i class='fa-solid fa-eye' title='Se karaktären'></i></a>\n";
    		        echo "</td>\n";
    		        echo "<td>$role->Profession</td>\n";
    		        if ($role->isMysLajvare()) {
    		            echo "<td>N/A</td>";
    		        }
    		        else {
        		        $wealth = $role->getWealth();
        		        echo "<td>";
        		        if (!empty($wealth)) echo $wealth->Name;
        		        echo "</td>\n";
    		        }
   		           $group = $role->getGroup();
    		        if (is_null($group)) {
    		            echo "<td>&nbsp;</td>\n";
    		        } else {
    		            echo "<td><a href='view_group.php?id=$group->Id'>$group->Name</></td>\n";
    		        }
    		        
    		        echo "<td>$larp_role->StartingMoney</td>";
    		        
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
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTable(1, \"$tableId\")' width='10%'>Yrke</th>".
        		    "<th onclick='sortTable(2, \"$tableId\")'>Rikedom</th>".
        		    "<th onclick='sortTable(3, \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")'>Pengar ($currency)</th>".
        		    "</tr>";
    		    foreach ($roles as $role)  {
    		        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        echo "<a href='view_role.php?id=" . $role->Id . "'>$role->Name</a>";
    		        if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
    		        echo "</td>\n";
    		        echo "<td nowrap>" . "<i class='fa-solid fa-eye' title='Se karaktären'></i></a>\n";
    		        echo "</td>\n";
    		        echo "<td>$role->Profession</td>\n";
    		        if ($role->isMysLajvare()) {
    		            echo "<td>N/A</td>";
    		        }
    		        else {
    		            $wealth = $role->getWealth();
    		            echo "<td>";
    		            if (!empty($wealth)) echo $wealth->Name;
    		            echo "</td>\n";
    		        }
    		        $group = $role->getGroup();
    		        if (is_null($group)) {
    		            echo "<td>&nbsp;</td>\n";
    		        } else {
    		            echo "<td><a href='view_group.php?id=$group->Id'>$group->Name</></td>\n";
    		        }
    		        
    		        echo "<td>$larp_role->StartingMoney</td>";
    		        
    		        echo "</tr>\n";
    		    
    		    }
    		    echo "</table>";
    		}
    		?>
        
	</div>
</body>
<?php 
include_once '../javascript/table_sort.js';
?>
</html>
