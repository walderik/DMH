<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>


    <div class="content">   
        <h1>Karaktärer</h1>
        <h2>Huvudkaraktärer</h2>
     		<?php 
     		$roles = $current_larp->getAllMainRoles(true);
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    $tableId = "main_roles";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTable(1, \"$tableId\")'>Plats<br>på lajvet</th>".
        		    "<th onclick='sortTable(2, \"$tableId\")'>Rikedom</th>".
        		    "<th onclick='sortTable(3, \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")'>Lagfarter</th>".
                    "</tr>\n";
    		    foreach ($roles as $role)  {
    		        if ($role->is_trading($current_larp)) {
        		        $person = $role->getPerson();
        		        $registration=$person->getRegistration($current_larp);
        		        echo "<tr>\n";
        		        echo "<td>";
        		        if ($registration->isNotComing()) {
        		            echo "<s>";
        		            echo $role->Name;
        		            echo "</s>";
        		            echo "</td>";
        		        }
        		        else {
        		            echo "<a href='view_role.php?id=$role->Id'>$role->Name</a>\n";
            		        if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
            		        echo "</td>\n";
            		        echo "<td align='center'>".showStatusIcon($person->getRegistration($current_larp)->hasSpotAtLarp())."</td>\n";
            		        $wealth = $role->getWealth();
            		        echo "<td>";
            		        if (!empty($wealth)) echo $wealth->Name;
           		           $group = $role->getGroup();
            		        if (is_null($group)) {
            		            echo "<td>&nbsp;</td>\n";
            		        } else {
            		            echo "<td><a href='view_group.php?id=$group->Id'>$group->Name</a></td>\n";
            		        }
        		        }
        		        echo "<td>";
        		        $titledeeds = Titledeed::getAllForRole($role);
        		        
        		        foreach ($titledeeds as $titledeed) {
        		            echo "$titledeed->Name<br>";
        		        }
        		        echo "</td>";
        		        echo "</tr>\n";
    		        }
    		    }
    		}
    		echo "</table>";
    		
    		?>

        
                <h2>Övriga karaktärer</h2>
                <p>Antal ord på intrig och vilka intriger de är med i visas inte för nedanstående karaktärer.</p>
        
	</div>
</body>
<?php 
include_once '../javascript/table_sort.js';
?>
</html>
