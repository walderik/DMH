<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Alchemy_Recipe::delete($_GET['Id']);
        header('Location: alchemy_recipe_admin.php');
        exit;
    }
}


include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Recept <a href="alchemy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a></h1>

            <a href="alchemy_recipe_form.php?operation=insert&type=<?php echo Alchemy_Alchemist::INGREDIENT_ALCHEMY?>"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt traditionellt recept</a>&nbsp;&nbsp;  
            <a href="alchemy_recipe_form.php?operation=insert&type=<?php echo Alchemy_Alchemist::ESSENCE_ALCHEMY?>"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt experimentellt recept</a>  
       <?php
    
       $recipes = Alchemy_Recipe::allByCampaign($current_larp);
       if (!empty($recipes)) {
           $tableId = "recipes";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Typ</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Effekt</th>".
               "<th onclick='sortTable(4, \"$tableId\")'>Ingredienser/Essenser<br>Nivån anges inom parentes</th>".
               "<th onclick='sortTable(5, \"$tableId\")'>Godkänd</th>".
               "<th></th>";
           
           foreach ($recipes as $recipe) {
                echo "<tr>\n";
                echo "<td><a href ='alchemy_recipe_form.php?operation=update&id=$recipe->Id'>$recipe->Name</a></td>\n";
                echo "<td>$recipe->Level</td>\n";
                echo "<td>";
                echo $recipe->getRecipeType();
                echo "</td>\n";
                echo "<td>".nl2br(htmlspecialchars($recipe->Effect))."</td>";
                echo "<td>";
                echo $recipe->getComponentNames();
                echo "</td>\n";
                
                echo "<td>";
                echo showStatusIcon($recipe->isApproved());
                
                echo "</td>\n";
                
                echo "<td>";
                if ($recipe->mayDelete()) {
                    echo "<a href='alchemy_recipe_admin.php?operation=delete&Id=" . $recipe->Id . "'><i class='fa-solid fa-trash'></i>";
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