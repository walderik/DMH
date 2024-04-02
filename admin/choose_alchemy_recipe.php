<?php
include_once 'header.php';

global $purpose;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } elseif (isset($_POST['Id'])) {
        $id = $_POST['Id'];
    }
    
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $operation = $_GET['operation'];
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } elseif (isset($_GET['Id'])) {
        $id = $_GET['Id'];
    }
    
}

$multiple=false;

if ($operation == "add_alchemist_recipe") {
    $purpose = "Lägg till recept till alkemist";
    $url = "logic/view_alchemist_logic.php";
    $multiple=true;
}


if ($multiple) {
    $type = "checkbox";
    $array="[]";
    
} else {
    $type="radio";
    $array="";
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

include 'navigation.php';
include 'alchemy_navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

    <div class="content">   
        <h1><?php echo $purpose;?></h1>
        Enbart godkända recept kan väljas.<br><br>
            <a href="alchemy_recipe_form.php?operation=insert&type=<?php echo Alchemy_Alchemist::INGREDIENT_ALCHEMY?>"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt traditionellt recept</a>&nbsp;&nbsp;  
            <a href="alchemy_recipe_form.php?operation=insert&type=<?php echo Alchemy_Alchemist::ESSENCE_ALCHEMY?>"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt experimentellt recept</a>  
     		<?php 
     		$recipes = Alchemy_Recipe::allByCampaign($current_larp);
     		if (empty($recipes)) {
    		    echo "Inga registrerade recept";
    		} else {
    		    ?>
    		    <form action="<?php echo $url;?>" method="post">
    		    <input type="hidden" id="operation" name="operation" value="<?php echo $operation;?>">
    		    <?php 
    		    if (isset($id)) {
    		        echo "<input type='hidden' id='id' name='id' value='$id'>";
    		        echo "<input type='hidden' id='Id' name='Id' value='$id'>";
    		    }    
    		    ?> 
    			<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
    		    <?php 
    		    $tableId = "recipes";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTable(1, \"$tableId\")'>Nivå</th>".
        		    "<th onclick='sortTable(2, \"$tableId\")'>Typ</th>".
        		    "<th onclick='sortTable(3, \"$tableId\")'>Hemligt?</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")'>Effekt</th>".
        		    "<th onclick='sortTable(5, \"$tableId\")'>Ingredienser/Essenser<br>Nivån anges inom parentes</th>".
        		    "<th onclick='sortTable(6, \"$tableId\")'>Godkänd</th>";

    		    foreach ($recipes as $recipe)  {
    		        echo "<tr>\n";
    		        echo "<td>";
    		        if ($recipe->isApproved()) echo "<input type='$type' id='Recipe$recipe->Id' name='RecipeId$array' value='$recipe->Id'>";
    		        else echo "<input type='$type' id='Recipe$recipe->Id' name='RecipeId$array' value='$recipe->Id' disabled='true'>";
    		        
    		        echo "<label for='Recipe$recipe->Id'>$recipe->Name</label></td>\n";

    		        echo "<td>$recipe->Level</td>\n";
    		        echo "<td>";
    		        echo $recipe->getRecipeType();
    		        echo "</td>\n";
    		        
    		        echo "<td>";
    		        echo $recipe->IsSecret() ? 'JA' : '';		        
    		        echo "</td>\n";
    		        
    		        echo "<td>".nl2br(htmlspecialchars($recipe->Effect))."</td>";
    		        echo "<td>";
    		        echo $recipe->getComponentNames();
    		        echo "</td>\n";
    		        
    		        echo "<td>";
    		        echo showStatusIcon($recipe->isApproved());
    		        
    		        echo "</td>\n";
    		        
    		        echo "</tr>\n";
    		        
    		    
    		    }
    		}
    		?>
    		</table>
    		<br>
			<input type="submit" value="<?php echo $purpose;?>"></form>
        
        
        
	</div>
</body>

</html>
