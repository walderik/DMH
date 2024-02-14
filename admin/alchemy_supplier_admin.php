<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Alchemy_Supplier::delete($_GET['Id']);
    }
}


include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Lövjerister  <a href="alchemy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a></h1>
        Först lägger man till en eller flera karaktärer som lövjerister. Sedan kan man maila dem och be den dels lägga upp nya ingredienser för godkännande och dels ange hur mycket de kommer att ha med sig av godkända ingredienser.<br>
        Antalet ingredienser lövjeristen har med sig kan sedan godkännas. 
        <br><br>
            <a href="choose_role.php?operation=add_alchemy_supplier"><i class="fa-solid fa-file-circle-plus"></i>Lägg till karaktärer som lövjerister.</a>  
       <?php
    
       $suppliers = Alchemy_Supplier::allByCampaign($current_larp);
       if (!empty($suppliers)) {
           $tableId = "suppliers";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Antal ingredienser<br>per nivå</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Alla ingredienser<br>godkända</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Workshop<br>deltagit</th>".
               "<th></th>";
           
           foreach ($suppliers as $supplier) {
               $role = $supplier->getRole();
               $person = $role->getPerson();
                echo "<tr>\n";
                echo "<td><a href ='view_alchemy_supplier.php?id=$supplier->Id'>$role->Name</a> ".contactEmailIcon($person->Name,$person->Email)."</td>\n";
                echo "<td></td>\n";
                echo "<td>". showStatusIcon($supplier->allAmountOfIngredientsApproved($current_larp)) ."</td>\n";
                echo "<td>" . showStatusIcon($supplier->hasDoneWorkshop()) . "</td>\n";
                echo "<td>" . "<a href='alchemy_supplier_admin.php?operation=delete&Id=" . $supplier->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
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