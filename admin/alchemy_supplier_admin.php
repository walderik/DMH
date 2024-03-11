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
?>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Lövjerister  <a href="alchemy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a></h1>
        Först lägger man till en eller flera karaktärer som lövjerister. Sedan kan man maila dem och be den dels lägga upp nya ingredienser för godkännande och dels ange hur mycket de kommer att ha med sig av godkända ingredienser.<br>
        Antalet ingredienser lövjeristen har med sig kan sedan godkännas. 
        <br><br>
            <a href="choose_role.php?operation=add_alchemy_supplier"><i class="fa-solid fa-file-circle-plus"></i>Lägg till karaktärer som lövjerister.</a>&nbsp;&nbsp;  
       <?php
    
       $suppliers = Alchemy_Supplier::allByCampaign($current_larp);
       if (!empty($suppliers)) {
           $emailArr = array();
           foreach ($suppliers as $supplier) {
               $person = $supplier->getRole()->getPerson();
               $emailArr[] = $person->Email;
           }
           
           echo contactSeveralEmailIcon('Skicka mail till alla lövjerister', $emailArr, 'Lövjerist', "Meddelande till alla lövjerister i $current_larp->Name");
           
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
           
           foreach ($suppliers as $supplier) {
               $role = $supplier->getRole();
               $person = $role->getPerson();
                echo "<tr>\n";
                echo "<td><a href ='view_alchemy_supplier.php?id=$supplier->Id'>$role->Name</a></td>\n";
                echo "<td>";
                $group = $role->getGroup();
                if (isset($group)) echo "<a href='view_group.php?id=$group->Id'>$group->Name</a>";;
                echo "</td>";
                echo "<td>";
                echo "<a href='view_person.php?id=$person->Id'>$person->Name</a> ".contactEmailIcon($person->Name, $person->Email);
                echo "</td>";
                echo "<td>";
                $amount_per_level = $supplier->numberOfIngredientsPerLevel($current_larp);
                foreach ($amount_per_level as $level => $amount) {
                    if ($amount == 0) continue;
                    echo "Nivå $level: $amount st<br>";
                }
                echo "</td>\n";
                echo "<td>".$supplier->appoximateValue($current_larp)." $currency</td>\n";
                echo "<td>". showStatusIcon($supplier->allAmountOfIngredientsApproved($current_larp)) ."</td>\n";
                echo "<td>" . showStatusIcon($supplier->hasDoneWorkshop()) . "</td>\n";
                echo "<td>" . "<a href='alchemy_supplier_admin.php?operation=delete&Id=" . $supplier->Id . "' onclick='return confirm(\"Är du säker på att du vill ta bort $role->Name som lövjerist?\");'><i  class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        else {
            echo "<p>Inga registrerade ännu</p>";
        }
        ?>
    </div>
	
</body>

</html>