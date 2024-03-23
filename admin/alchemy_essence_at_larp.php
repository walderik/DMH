<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Alchemy_Ingredient::delete($_GET['Id']);
        header('Location: alchemy_ingredient_admin.php');
        exit;
    }
}


include 'navigation.php';
include 'alchemy_navigation.php';
?>

<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Vilka essenser finns på lajvet i form av ingredienser<a href="alchemy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a></h1>

            <a href="alchemy_ingredient_form.php?operation=insert"><i class="fa-solid fa-file-circle-plus"></i>Lägg till ingrediens</a>&nbsp;&nbsp;  
            <a href="alchemy_ingredient_form.php?operation=insert&type=katalysator"><i class="fa-solid fa-file-circle-plus"></i>Lägg till katalysator</a>  
       <?php
    
       $essences = Alchemy_Essence::allByCampaign($current_larp);
       if (!empty($essences)) {
           $tableId = "essence";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Finns i ingrediens<br>(Antal på lajvet)</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Används i recept</th>";

           
           for ($level=1; $level <= 5; $level++) {
               foreach ($essences as $essence) {
                    echo "<tr>\n";
                    echo "<td><a href ='alchemy_essence_form.php?operation=update&id=$essence->Id'>$essence->Name</a></td>\n";
                    echo "<td>$level</td>\n";
                    echo "<td>";
                    $ingredients = Alchemy_Ingredient::getIngredientsByEssenceLevel($essence, $level, $current_larp);
                    foreach ($ingredients as $ingredient) {
                        $sum = $ingredient->countAtLarp($current_larp);
                        if (empty($sum)) $sum = 0;
                        echo $ingredient->Name."(".$sum.")<br>";
                    }
                    echo "</td>\n";
                    echo "<td>";
                    $recipes = Alchemy_Recipe::allContainingEssence($essence, $level, $current_larp);
                    foreach ($recipes as $recipe) {
                        echo $recipe->Name."<br>";
                    }
                    echo "</td>\n";
                    
                    echo "</tr>\n";
                }
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