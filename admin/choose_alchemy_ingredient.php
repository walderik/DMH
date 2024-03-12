<?php
include_once 'header.php';

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

if ($operation == "add_supplier_ingredient") {
    $purpose = "Lägg till ingredienser till lövjerist";
    $url = "logic/alchemy_supplier_ingredient_save.php";
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
?>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1><?php echo $purpose;?></h1>
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
                    echo "<input type='$type' id='Ingridient$ingredient->Id' name='IngridientId$array' value='$ingredient->Id'>";
                } else {
                    echo "<input type='$type' id='Ingridient$ingredient->Id' name='IngridientId$array' value='$ingredient->Id' disabled='true'>";
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