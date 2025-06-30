<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        $recipe = Alchemy_Recipe::loadById($_GET['Id']);
        if ($recipe->mayDelete()) Alchemy_Recipe::delete($_GET['Id']);
        header('Location: alchemy_recipe_admin.php');
        exit;
    }
}


include 'navigation.php';
include 'alchemy_navigation.php';
?>
<style>

.NotAtLARP > td { 
    background: lightgrey;
    color: gray;
}
</style>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Recept <a href="alchemy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a></h1>
        <p>Recept som ingen alkemist på lajvet har är utgråade och saknade ingredienser visas inte.</p>

            <a href="alchemy_recipe_form.php?operation=insert&type=<?php echo Alchemy_Alchemist::INGREDIENT_ALCHEMY?>"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt traditionellt recept</a>&nbsp;&nbsp;  
            <a href="alchemy_recipe_form.php?operation=insert&type=<?php echo Alchemy_Alchemist::ESSENCE_ALCHEMY?>"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt experimentellt recept</a>  
       <?php
    
       $all_missing_ingredients = array();
       $recipes = Alchemy_Recipe::allByCampaign($current_larp);
       if (!empty($recipes)) {
           $tableId = "recipes";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Typ</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Skapare</th>".
               "<th onclick='sortTable(4, \"$tableId\")'>Hemligt</th>".
               "<th onclick='sortTable(5, \"$tableId\")'>Effekt</th>".
               "<th onclick='sortTable(6, \"$tableId\")'>Ingredienser/Essenser<br>Nivån anges inom parentes</th>".
               "<th onclick='sortTable(7, \"$tableId\")'>Godkänd</th>".
               "<th onclick='sortTable(8, \"$tableId\")'>Alla ingredienser finns på lajvet/<br>Vilka ingredienser saknas</th>".
               "<th></th>";
           
           foreach ($recipes as $recipe) {
                $isReciepeAtLarp = $recipe->IsAtLARP($current_larp);
                echo "<tr";
                if (!$isReciepeAtLarp) echo " class=NotAtLARP ";
                echo ">\n";
                echo "<td>";
                echo "<a href ='view_alchemy_recipe.php?id=$recipe->Id'>$recipe->Name</a> ";
                echo "<a href ='alchemy_recipe_form.php?operation=update&id=$recipe->Id'><i class='fa-solid fa-pen'></i></a>";
                echo " <a href='reports/alchemy_recipe_pdf.php?RecipeId=$recipe->Id?' target='_blank'><i class='fa-solid fa-file-pdf' title='Receptet som skrolla'></i></a>";
                
                echo "</td>\n";
                echo "<td>$recipe->Level</td>\n";
                echo "<td>";
                echo $recipe->getRecipeType();
                echo "</td>\n";

                echo "<td>";
                $authorRole = $recipe->getAuthorRole();
                if (isset($authorRole)) {
                    $alchemist = Alchemy_Alchemist::getForRole($authorRole);
                    if (isset($alchemist)) echo "<a href='view_alchemist.php?id=$alchemist->Id'>$authorRole->Name</a>";
                    else echo $authorRole->getViewLink()." (ej alkemist)";
                }
                echo "</td>\n";
                
                echo "<td>";
                echo $recipe->IsSecret() ? 'JA' : '';
                echo "</td>\n";
                
                echo "<td>".nl2br(htmlspecialchars($recipe->Effect))."</td>";
                echo "<td>";
                echo $recipe->getComponentNames();
                echo "</td>\n";
                
                echo "<td>";
                echo showStatusIcon($recipe->isApproved(),  "logic/toggle_approve_recipe.php?recipeId=$recipe->Id") . "\n";
                
                echo "</td>\n";
                
                echo "<td>";
                if ($isReciepeAtLarp && $recipe->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
                    $ingredients = $recipe->getMissingIngredients($current_larp);
                    if (empty($ingredients)) {
                        echo showStatusIcon(true);
                    } else {
                        $all_missing_ingredients = array_merge($all_missing_ingredients, $ingredients);
                        foreach($ingredients as $ingredient) {
                            echo "<a href='alchemy_ingredient_form.php?operation=update&id=$ingredient->Id'>$ingredient->Name</a>, nivå $ingredient->Level<br>";
                        }
                    }
                } 
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
        <?php 
        if (!empty($all_missing_ingredients)) {
            $all_missing_ingredients = array_unique($all_missing_ingredients, SORT_REGULAR);
            
            function cmp(Alchemy_Ingredient $a, Alchemy_Ingredient $b) {
                if ($a->Level == $b->Level) return strcmp($a->Name, $b->Name);
                else return $a->Level > $b->Level;
            }
            
            usort($all_missing_ingredients, "cmp");
            echo "<h2>Alla ingredienser som saknas, sammanställning av ovanstående tabell</h2>";
            foreach($all_missing_ingredients as $ingredient) {
                echo "<a href='alchemy_ingredient_form.php?operation=update&id=$ingredient->Id'>$ingredient->Name</a>, nivå $ingredient->Level<br>";
            }
            
            
        }?>

    </div>
	
</body>

</html>