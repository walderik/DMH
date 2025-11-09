<?php
 include_once 'header.php';
 
 
 $hasFunctions = RoleFunction::isInUse($current_larp) || $current_larp->hasMagic() || $current_larp->hasAlchemy();
 $currency = $current_larp->getCampaign()->Currency;
 
 include 'navigation.php';
 include 'aktor_navigation.php';
 
 
?>


<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>

    <div class="content">   
        <h1>Avbokade karaktärer</h1>
     		<?php 
     		$roles = $current_larp->getNotComingRoles(true);
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    $tableId = "main_roles";
    		    $colnum = 0;
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
    		        "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Gruppering</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Intriger</th>";
        		if ($current_larp->hasRumours()) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Rykten</th>";
    		    if ($current_larp->hasCommerce()) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Verksamheter</th>";
    		    if ($hasFunctions) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Funktion</th>";
    		    echo "</tr>\n";
    		    foreach ($roles as $role)  {
    		        //Sidan visar bara anmälda karaktärer, aldrig npc.
    		        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    		        $group = $role->getGroup();
    		        
    		        //$person = $larp_role->getPerson();
    		        //$registration=$person->getRegistration($current_larp);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        echo $role->getViewLink();
    		        echo "</td>\n";
    		        echo "<td>";
    		        if (!empty($group)) {
    		            echo $group->getViewLink();
    		            $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);
    		            echo "<br>";
    		            if (!empty($intrigues)) echo "Intrig: ";
    		            foreach ($intrigues as $intrigue) {
 		                    echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
		                    echo $intrigue->Number;
    		                echo "</a>";
    		                echo " ";
    		            }
    		            
    		        }
    		        echo "</td>\n";

    		        echo "<td>";
    		        $subdivisions = Subdivision::allForRole($role, $current_larp);
    		        foreach ($subdivisions as $subdivision) {
    		            echo $subdivision->getViewLink();
    		            $intrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $current_larp->Id);
    		            echo "<br>";
    		            if (!empty($intrigues)) echo "Intrig: ";
    		            foreach ($intrigues as $intrigue) {
    		                echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
    		                if ($intrigue->isActive()) {
    		                    echo $intrigue->Number;
    		                    $hasOtherIntrigues = true;
    		                }
    		                else echo "<s>$intrigue->Number</s>";
    		                echo "</a>";
    		                echo " ";
    		            }
    		            echo "<br><br>";
    		        }
    		        echo "</td>\n";

		            echo "<td>"; 
		            if (!empty($larp_role->Intrigue)) {
		                echo showStatusIcon(false)." Har personlig intrig ". 
		                "<a href='edit_intrigue.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a><br>";
		            }

		            $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
		            if (!empty($intrigues)) echo "Intrig: ";
		            foreach ($intrigues as $intrigue) {
		                echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
		                if ($intrigue->isActive()) echo $intrigue->Number;
		                else echo "<s>$intrigue->Number</s>";
		                echo "</a>";
		                echo " ";
		            }
		            
		            echo "</td>\n";
		            
		            if ($current_larp->hasRumours()) {
    		            echo "<td>";
    		            $knows_count = count(Rumour::allKnownByRole($current_larp, $role));
    		            if ($knows_count > 0) echo "Känner till $knows_count rykten.<br>";
    		            
    		            $rumours_about = Rumour::allConcernedByRole($current_larp, $role);
    		            if (!empty($rumours_about)) {
    		                echo "Följande rykten handlar om $role->Name<br>";
    		                foreach($rumours_about as $rumour) {
    		                    echo mb_strimwidth(str_replace("\n", "<br>", $rumour->Text), 0, 20, "...") . " <a href='rumour_form.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a><br>";
    		                }
    		            }
    		            echo "</td>\n";
		            }
		            
		            if ($current_larp->hasCommerce()) {
    		            echo "<td>";
    		            $titledeeds = Titledeed::getAllActiveForRole($role);
    		            foreach ($titledeeds as $titledeed) {
    		                echo "<a href='view_titledeed.php?id=$titledeed->Id'>$titledeed->Name</a>";
    		                if (!$titledeed->isGeneric()) {
    		                    $numberOfOwners = $titledeed->numberOfOwners();
    		                    if ($numberOfOwners > 1) echo " 1/$numberOfOwners";
    		                }
    		                echo "<br>";
    		                
    		            }
    		            echo "</td>\n";
		            }
		            
		            if ($hasFunctions) {
    		            echo "<td>";
    		            $rolefunction = commaStringFromArrayObject($role->getRoleFunctions());
		                if (!empty($rolefunction)) echo $rolefunction. "<br>";

    
    		            $magician = Magic_Magician::getForRole($role);
    		            $alchemist = Alchemy_Alchemist::getForRole($role);
    		            $alchemy_supplier = Alchemy_Supplier::getForRole($role);
    		            
		                if (isset($magician)) echo "<a href='view_magician.php?id=$magician->Id'>Magiker</a><br>";
		                if (isset($alchemist)) echo "<a href='view_alchemist.php?id=$alchemist->Id'>Alkemist</a><br>";
		                if (isset($alchemy_supplier)) echo "<a href='view_alchemy_supplier.php?id=$alchemy_supplier->Id'>Lövjerist</a><br>";
    
    		            echo "</td>\n";
		            }
		            
		            
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

         
	</div>
</body>

</html>
