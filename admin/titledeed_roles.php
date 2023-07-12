<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>


    <div class="content">   
        <h1>Karaktärer med handel</h1>
        <p>Sidokaraktärer markeras med en * efter namnet</p>
     		<?php 
     		$roles = Role::getAllInCampaign($current_larp->CampaignId);
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    $tableId = "main_roles";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTable(1, \"$tableId\")'>Kommer<br>på lajvet</th>".
        		    "<th onclick='sortTable(2, \"$tableId\")'>Rikedom</th>".
        		    "<th onclick='sortTable(3, \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")'>Lagfarter</th>".
                    "</tr>\n";
    		    foreach ($roles as $role)  {
    		        //Man vill se alla roller som kommer på lajvet och har handel och 
    		        //alla roller som äger lagfart oavsett om de kommer eller inte
    		        $titledeeds = Titledeed::getAllForRole($role);
    		        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    		        $person = $role->getPerson();
    		        $registration=$person->getRegistration($current_larp);
    		        $isComing = !empty($larp_role);
    		        if (!empty($registration) && $registration->isNotComing()) $isComing=false;
    		        if (($isComing && $role->is_trading($current_larp)) || !empty($titledeeds)) {
        		        echo "<tr>\n";
        		        echo "<td>";
    		            echo "<a href='view_role.php?id=$role->Id'>$role->Name</a>\n";
                        if (!empty($larp_role) && ($larp_role->IsMainRole == 0)) echo " * ";
    		            if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
        		        echo "</td>\n";
        		        echo "<td align='center'>".showStatusIcon($isComing)."</td>\n";
        		        $wealth = $role->getWealth();
        		        echo "<td>";
        		        if (!empty($wealth)) echo $wealth->Name;
       		           $group = $role->getGroup();
        		        if (is_null($group)) {
        		            echo "<td>&nbsp;</td>\n";
        		        } else {
        		            echo "<td><a href='view_group.php?id=$group->Id'>$group->Name</a></td>\n";
        		        }
        		        echo "<td>";
        		        
        		        foreach ($titledeeds as $titledeed) {
        		            $numberOfOwners = $titledeed->numberOfOwners();
        		            echo "$titledeed->Name";
                            if ($numberOfOwners > 1) echo " 1 / $numberOfOwners";
                            echo "<br>";
        		        }
        		        echo "</td>";
        		        echo "</tr>\n";
    		        }
    		    }
    		}
    		echo "</table>";
    		
    		?>

 </body>
<?php 
include_once '../javascript/table_sort.js';
?>
</html>
