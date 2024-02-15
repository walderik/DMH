<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $RoleId = $_GET['RoleId'];
}


$role = Role::loadById($RoleId);
$person = $role->getPerson();

if ($person->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}
if (!Alchemy_Supplier::isSupplier($role)) {
    header('Location: index.php'); // karaktären är inte lövjerist
    exit;
}



include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Alla ingredienser <a href="view_alchemy_supplier.php?id=<?php echo $role->Id?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>

    	<a href='alchemy_ingredient_form.php?operation=insert&RoleId=<?php echo $role->Id?>'><i class='fa-solid fa-file-circle-plus'></i>Skapa ny ingrediens</a>&nbsp;&nbsp;
	    <a href='alchemy_ingredient_form.php?operation=insert&type=katalysator&RoleId=<?php echo $role->Id?>'><i class='fa-solid fa-file-circle-plus'></i>Skapa ny katalysator</a><br><br>
	    <form action="logic/alchemy_supplier_ingredient_save.php" method="post">
		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id;?>">

       <?php
    
       $ingredients = Alchemy_Ingredient::allByCampaign($current_larp);
       if (!empty($ingredients)) {
           $tableId = "ingredients";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th></th>".
               "<th onclick='sortTable(1, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Beskrivning</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(4, \"$tableId\")'>Ingrediens/<br>Katalysator</th>".
               "<th onclick='sortTable(5, \"$tableId\")'>Essenser</th>".
               "<th onclick='sortTable(6, \"$tableId\")'>Off-ingrediens</th>".
               "<th onclick='sortTable(7, \"$tableId\")'>Godkänd/<br>Ännu inte godkänd</th>".
               "";
           
           foreach ($ingredients as $ingredient) {
                echo "<tr>\n";
                echo "<td>";
                if ($ingredient->isApproved()) {
                    echo "<input type='checkbox' id='Ingridient$ingredient->Id' name='IngridientId[]' value='$ingredient->Id'>";
                }
                echo "</td>\n";
                echo "<td>$ingredient->Name</td>\n";
                echo "<td>$ingredient->Description</td>\n";
                echo "<td>$ingredient->Level</td>\n";
                echo "<td>";
                if ($ingredient->isCatalyst()) echo "Katalysator";
                else echo "Ingrediens";
                echo "</td>\n";
                echo "<td>";
                if ($ingredient->isIngredient()) {
                    echo $ingredient->getEssenceNames();
                }
                echo "</td>\n";
                
                echo "<td>$ingredient->ActualIngredient</td>\n";
                echo "<td>";
                echo showStatusIcon($ingredient->isApproved());
                
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
            echo "<br>";
            echo "<input type='submit' value='Välj ingredienser'></form>";
            
        }
        else {
            echo "<p>Inga registrerade ännu</p>";
        }
        ?>
    </div>
	
</body>

</html>