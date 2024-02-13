<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Alchemy_Ingredient::delete($_GET['Id']);
    }
}


include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Ingredienser <a href="alchemy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a></h1>

            <a href="alchemy_ingredient_form.php?operation=insert"><i class="fa-solid fa-file-circle-plus"></i>Lägg till ingrediens</a>  
            <a href="alchemy_ingredient_form.php?operation=insert&type=katalysator"><i class="fa-solid fa-file-circle-plus"></i>Lägg till katalysator</a>  
       <?php
    
       $ingredients = Alchemy_Ingredient::allByCampaign($current_larp);
       if (!empty($ingredients)) {
           $tableId = "ingredients";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Ingrediens/Katalysator</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Essenser</th>".
               "<th onclick='sortTable(4, \"$tableId\")'>Godkänd</th>".
               "<th></th>";
           
           foreach ($ingredients as $ingredient) {
                echo "<tr>\n";
                echo "<td><a href ='alchemy_ingredient_form.php?operation=update&id=$ingredient->Id'>$ingredient->Name</a></td>\n";
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
                
                echo "<td>";
                echo showStatusIcon($ingredient->isApproved());
                
                echo "</td>\n";
                
                echo "<td>";
                if ($ingredient->mayDelete()) {
                    echo "<a href='alchemy_ingredient_admin.php?operation=delete&Id=" . $ingredient->Id . "'><i class='fa-solid fa-trash'></i>";
                }
                echo "</td>\n";
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