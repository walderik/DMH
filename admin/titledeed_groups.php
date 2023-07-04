<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>


    <div class="content">   
        <h1>Grupper med handel</h1>
     		<?php 
     		$groups = Group::getAllInCampaign($current_larp->CampaignId);
     		if (empty($groups)) {
    		    echo "Inga anmälda grupper";
    		} else {
    		    $tableId = "groups";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTable(1, \"$tableId\")'>Kommer<br>på lajvet</th>".
        		    "<th onclick='sortTable(2, \"$tableId\")'>Rikedom</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")'>Lagfarter</th>".
                    "</tr>\n";
    		    foreach ($groups as $group)  {
    		        //Man vill se alla grupper som kommer på lajvet och har handel och 
    		        //alla grupper som äger lagfart oavsett om de kommer eller inte
    		        $titledeeds = Titledeed::getAllForGroup($group);
    		        $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    		        $isComing = !empty($larp_group);
    		        if (($isComing && $group->is_trading($current_larp)) || !empty($titledeeds)) {
        		        echo "<tr>\n";
        		        echo "<td>";
    		            echo "<a href='view_group.php?id=$group->Id'>$group->Name</a>\n";
        		        echo "</td>\n";
        		        echo "<td align='center'>".showStatusIcon($isComing)."</td>\n";
        		        $wealth = $group->getWealth();
        		        echo "<td>";
        		        if (!empty($wealth)) echo $wealth->Name;
        		        echo "<td>";
        		        
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

 </body>
<?php 
include_once '../javascript/table_sort.js';
?>
</html>
