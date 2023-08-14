<?php
 include_once 'header.php';
 
 include 'navigation.php';
 
 $currency = $current_larp->getCampaign()->Currency;
 
?>
<script src="../javascript/setmoney_ajax.js"></script>


    <div class="content">   
        <h1>Grupper med handel</h1>
                <p>Alla grupper som har rikedom 3 eller högre eller som äger en lagfart visas här. Det gäller alla grupper i kampanjen, även de som inte kommer på ett visst lajv.</p>
        
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
        		    "<th onclick='sortTable(3, \"$tableId\")'>Lagfarter</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")'>Pengar ($currency)</th>".
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
        		            $numberOfOwners = $titledeed->numberOfOwners();
        		            echo "<a href='titledeed_form.php?operation=update&id=" . $titledeed->Id . "'>$titledeed->Name</a>";
        		            if ($numberOfOwners > 1) echo " 1/$numberOfOwners";
        		            echo ", <a href='resource_titledeed_form.php?Id=$titledeed->Id'>Resultat ".$titledeed->calculateResult()." $currency</a>";
        		            echo "<br>";
        		            $produces_normally = $titledeed->ProducesNormally();
        		            if (!empty($produces_normally)) echo "Producerar: ". commaStringFromArrayObject($produces_normally) . "<br>\n";
        		            $requires_normally = $titledeed->RequiresNormally();
        		            if (!empty($requires_normally)) echo "Behöver: " . commaStringFromArrayObject($requires_normally)."<br>\n";
        		            echo "<br>";
        		        }
        		        echo "</td>";
        		        echo "<td><input type='number' id='$group->Id' value='$larp_group->StartingMoney' onchange='setMoneyGroup(this, $current_larp->Id)'></td>";
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
