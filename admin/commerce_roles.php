<?php
 include_once 'header.php';
 
 include 'navigation.php';
 
 $currency = $current_larp->getCampaign()->Currency;
?>

<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>
<script src="../javascript/setmoney_ajax.js"></script>
<script src="../javascript/show_hide_rows.js"></script>


    <div class="content">   
        <h1>Karaktärer med handel</h1>
        <p>Alla karaktärer som har <?php if (Wealth::isInUse($current_larp)) echo "rikedom 3 eller högre eller"?> som äger en verksamhet visas här. Det gäller alla karaktärer i kampanjen, även de som inte kommer på ett visst lajv.</p>
        <p>Sidokaraktärer markeras med en * efter namnet. De är gömda från början.</p>
		    <?php 
	        echo "Karaktärer filtrerade på om de är sidokaraktär eller inte.<br>";
	        echo '<button id="btn_show" onclick="show_hide();">Visa alla</button>';
	        echo "<br><br>";
		    ?>
     		<?php 
     		$roles = Role::getAllInCampaign($current_larp->CampaignId);
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    $tableId = "main_roles";
    		    $colnum = 0;
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Kommer<br>på lajvet</th>";
    		    if (Wealth::isInUse($current_larp)) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Rikedom</th>";
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Verksamheter</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Pengar ($currency)</th>".
        		    "</tr>\n";
    		    foreach ($roles as $role)  {
    		        //Man vill se alla roller som kommer på lajvet och har handel och 
    		        //alla roller som äger verksamhet oavsett om de kommer eller inte
    		        $titledeeds = Titledeed::getAllForRole($role);
    		        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    		        $person = $role->getPerson();
    		        $registration=$person->getRegistration($current_larp);
    		        $isComing = !empty($larp_role);
    		        if (!empty($registration) && $registration->isNotComing()) $isComing=false;
    		        if (($isComing && $role->is_trading($current_larp)) || !empty($titledeeds)) {
    		            if ($larp_role->IsMainRole == 1) echo "<tr>\n";
    		            else echo "<tr class='show_hide hidden'>\n";
    		            echo "<td>";
    		            echo $role->getViewLink() . "\n";
                        if (!empty($larp_role) && ($larp_role->IsMainRole == 0)) echo " * ";
    		            if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
        		        echo "</td>\n";
        		        echo "<td align='center'>".showStatusIcon($isComing)."</td>\n";
        		        if (Wealth::isInUse($current_larp)) {
            		        $wealth = $role->getWealth();
            		        echo "<td>";
            		        if (!empty($wealth)) echo $wealth->Name;
            		        echo "</td>";
        		        }
       		            $group = $role->getGroup();
        		        if (is_null($group)) {
        		            echo "<td>&nbsp;</td>\n";
        		        } else {
        		            echo "<td><a href='view_group.php?id=$group->Id'>$group->Name</a></td>\n";
        		        }
        		        echo "<td>";
        		        
        		        foreach ($titledeeds as $titledeed) {
        		            $numberOfOwners = $titledeed->numberOfOwners();
        		            echo "<a href='titledeed_form.php?operation=update&id=" . $titledeed->Id . "'>$titledeed->Name</a>";
                            if ($numberOfOwners > 1) echo " 1/$numberOfOwners";
                            echo ", <a href='resource_titledeed_form.php?Id=$titledeed->Id'>Resultat ".$titledeed->calculateResult()." $currency</a>";
                            echo "<br>";
                            $produces_normally = $titledeed->ProducesNormally();
                            if (!empty($produces_normally)) echo "Tillgångar: ". commaStringFromArrayObject($produces_normally) . "<br>\n";
                            $requires_normally = $titledeed->RequiresNormally();
                            if (!empty($requires_normally)) echo "Behöver: " . commaStringFromArrayObject($requires_normally)."<br>\n";
                            echo "<br>";
                            
        		        }
                        echo "</td>";
        		        echo "<td><input type='number' id='$role->Id' value='$larp_role->StartingMoney' onchange='setMoney(this, $current_larp->Id)'></td>";
        		        echo "</td>";
        		        echo "</tr>\n";
    		        }
    		    }
    		}
    		echo "</table>";
    		
    		?>

 </body>

</html>
