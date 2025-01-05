<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Alchemy_Supplier::delete($_GET['Id']);
        header('Location: alchemy_supplier_admin.php');
        exit;
    }
}

$currency = $current_larp->getCampaign()->Currency;

include 'navigation.php';
include 'alchemy_navigation.php';
?>

<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Lövjerister  <a href="alchemy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a></h1>
        Först lägger man till en eller flera karaktärer som lövjerister. Sedan kan man maila dem och be den dels lägga upp nya ingredienser för godkännande och dels ange hur mycket de kommer att ha med sig av godkända ingredienser.<br>
        Antalet ingredienser lövjeristen har med sig kan sedan godkännas. <br>
        Ett överstruket namn indikerar en karaktär som inte kommer på det här lajvet.
        <br><br>
            <a href="choose_role.php?operation=add_alchemy_supplier"><i class="fa-solid fa-file-circle-plus"></i>Lägg till karaktärer som lövjerister.</a>&nbsp;&nbsp;  
       <?php
 
       $suppliersComing = Alchemy_Supplier::allByComingToLarp($current_larp);
       $allSuppliers = Alchemy_Supplier::allByCampaign($current_larp);;
       $suppliersNotComing = array_udiff($allSuppliers, $suppliersComing,
           function ($objOne, $objTwo) {
               return $objOne->Id - $objTwo->Id;
           });
       
       
       
       if (!empty($suppliersComing)) {
           $personIdArr = array();
           foreach ($suppliersComing as $supplier) {
               $person = $supplier->getRole()->getPerson();
               $personIdArr[] = $person->Id;
           }
           echo "<h2>Lövjerister som är anmälda</h2>";
           echo contactSeveralEmailIcon('Skicka mail till alla anmälda lövjerister', $personIdArr, 'Lövjerist', "Meddelande till alla lövjerister i $current_larp->Name");
           
           $tableId = "suppliers";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Grupp</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Spelas av</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Antal ingredienser<br>per nivå</th>".
               "<th onclick='sortTable(4, \"$tableId\")'>Ungefärligt värde<br>på ingredienser</th>".
               "<th onclick='sortTable(5, \"$tableId\")'>Ingredienslistan<br>är godkänd</th>".
               "<th onclick='sortTable(6, \"$tableId\")'>Workshop<br>deltagit</th>".
               "<th></th>";
           
           foreach ($suppliersComing as $supplier) {
               $role = $supplier->getRole();
               $person = $role->getPerson();
                echo "<tr>\n";
                $roleNotComing = $person->isNotComing($current_larp);
                echo "<td>";
                if ($roleNotComing) echo "<s>";
                echo "<a href ='view_alchemy_supplier.php?id=$supplier->Id'>$role->Name</a>\n";
                if ($roleNotComing) echo "</s>";
                echo "</td>";
                echo "<td>";
                $group = $role->getGroup();
                if (isset($group)) echo $group->getViewLink();

                echo "</td>";
                echo "<td>";
                echo $person->getViewLink().contactEmailIcon($person);
                echo "</td>";
                echo "<td>";
                $hasAmount = false; 
                $amount_per_level = $supplier->numberOfIngredientsPerLevel($current_larp);
                foreach ($amount_per_level as $level => $amount) {
                    if ($amount == 0) continue;
                    $hasAmount = true;
                    echo "Nivå $level: $amount st<br>";
                }
                if ($roleNotComing && $hasAmount) {
                    echo "<form action='logic/clear_supplier_ingredient_list.php' method='post' style='display:inline-block'><input type='hidden' name='SupplierId' value='$supplier->Id'>\n";
                    echo "<button type='submit'>Töm listan</button>";
                    echo "</form>\n";
                }
                echo "</td>\n";
                echo "<td>";
                if ($hasAmount) echo $supplier->appoximateValue($current_larp)." $currency";
                echo "</td>\n";
                echo "<td>";
                if ($hasAmount) echo showStatusIcon($supplier->allAmountOfIngredientsApproved($current_larp));
                echo "</td>\n";
                echo "<td>" . showStatusIcon($supplier->hasDoneWorkshop()) . "</td>\n";
                echo "<td>" . "<a href='alchemy_supplier_admin.php?operation=delete&Id=" . $supplier->Id . "' onclick='return confirm(\"Är du säker på att du vill ta bort $role->Name som lövjerist?\");'><i  class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }

        if (!empty($suppliersNotComing)) {
            echo "<h2>Lövjerister som inte är anmälda</h2>";
            
            $tableId = "suppliersNotComing";
            echo "<table id='$tableId' class='data'>";
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
                "<th onclick='sortTable(1, \"$tableId\")'>Grupp</th>".
                "<th onclick='sortTable(2, \"$tableId\")'>Spelas av</th>".
                "<th onclick='sortTable(3, \"$tableId\")'>Workshop<br>deltagit</th>".
                "<th></th>";
            
            foreach ($suppliersNotComing as $supplier) {
                $role = $supplier->getRole();
                $person = $role->getPerson();
                echo "<tr>\n";
                $roleNotComing = $person->isNotComing($current_larp);
                echo "<td>";
                if ($roleNotComing) echo "<s>";
                echo "<a href ='view_alchemy_supplier.php?id=$supplier->Id'>$role->Name</a>\n";
                if ($roleNotComing) echo "</s>";
                echo "</td>";
                echo "<td>";
                $group = $role->getGroup();
                if (isset($group)) echo $group->getViewLink();
                
                echo "</td>";
                echo "<td>";
                echo $person->getViewLink().contactEmailIcon($person);
                echo "</td>";
                echo "<td>" . showStatusIcon($supplier->hasDoneWorkshop()) . "</td>\n";
                echo "<td>" . "<a href='alchemy_supplier_admin.php?operation=delete&Id=" . $supplier->Id . "' onclick='return confirm(\"Är du säker på att du vill ta bort $role->Name som lövjerist?\");'><i  class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        
        ?>
    </div>
	
</body>

</html>