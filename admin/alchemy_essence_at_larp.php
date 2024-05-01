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
			<p>Saknas betyder att ingen ingrediens med essensen finns på lajvet. Om det finns ett utropstecken eteråt betyder det att det finns recept som 
			behöver ingrediensen och som därför inte kommer att kunna tillverkas.<br>
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
               "<th onclick='sortTable(3, \"$tableId\")'>Saknas</th>".
               "<th onclick='sortTable(4, \"$tableId\")'>Används i recept</th>";

           
           for ($level=1; $level <= 5; $level++) {
               foreach ($essences as $essence) {
                   $recipes = Alchemy_Recipe::allContainingEssence($essence, $level, $current_larp);
                   echo "<tr>\n";
                    echo "<td><a href ='alchemy_essence_form.php?operation=update&id=$essence->Id'>$essence->Name</a></td>\n";
                    echo "<td>$level</td>\n";
                    echo "<td>";
                    $ingredients = Alchemy_Ingredient::getIngredientsByEssenceLevel($essence, $level, $current_larp);
                    $sum = 0;
                    foreach ($ingredients as $ingredient) {
                        $count = $ingredient->countAtLarp($current_larp);
                        if (empty($count)) $count = 0;
                        echo $ingredient->Name." (".$count.")<br>";
                        $sum += $count;
                    }
                    echo "</td>\n";
                    echo "<td>";
                    if ($sum == 0) echo "Saknas";
                    if ($sum==0  && !empty($recipes)) echo " ".showStatusIcon(false);
                    echo "</td>\n";
                    echo "<td>";
                    foreach ($recipes as $recipe) {
                        echo "<a href='view_alchemy_recipe.php?id=$recipe->Id'>$recipe->Name</a><br>";
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