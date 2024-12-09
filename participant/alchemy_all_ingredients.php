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

$supplier = Alchemy_Supplier::getForRole($role);

function printIngredient(Alchemy_Ingredient $ingredient, Alchemy_Supplier $supplier) {
    global $current_larp;
    
    echo "<div class='itemcontainer'>";
    echo "<details><summary>";
    if ($ingredient->hasIngredient($supplier, $current_larp)) {
        echo showStatusIcon(true)." ";
    } elseif ($ingredient->wantsIngredient($supplier, $current_larp)) {
        echo showQuestionmarkIcon()." ";
    } elseif ($ingredient->isApproved()) {
        echo "<input type='checkbox' id='Ingridient$ingredient->Id' name='IngridientId[]' value='$ingredient->Id'> ";
    }
    
    echo "<span class='itemname'>$ingredient->Name";
    echo "</span></summary>";
    
    echo "Nivå: $ingredient->Level<br>";
    echo "Typ: ";
    if ($ingredient->isCatalyst()) echo "Katalysator";
    else echo "Ingrediens";
    echo "<br>";
    echo "Beskrivning: $ingredient->Description<br>";
    echo "<br>";
    if ($ingredient->isIngredient()) {
        echo "Essencer: ".$ingredient->getEssenceNames();
    }
    echo "</details>";
    echo "</div>";
    
}


include 'navigation.php';
?>

	<div class='itemselector'>
		<div class="header">
			<i class="fa-solid fa-leaf"></i> Alla ingredienser
		</div>
   		<div class='itemcontainer'>
			Lägg till de ingredienser som du tänker ta med till lajvet. Arrangörerna godkänner sedan att du får ta med ingrediensen och hur många du får ta med.<br>
			Om någon eller några ingredienser du har tänkt ta med inte finns i listan med ingredienser så får du skapa upp det du har tänkt ta med dig. Tänk på att det ska matcha på alla punkter, även off-ingrediens. 
			Efter att du har skapat en ingrediens måste arrangörerna godkänna den innan du kan välja att ta med den till lajvet.
		</div>

   		<div class='itemcontainer'>
        	<a href='alchemy_ingredient_form.php?operation=insert&RoleId=<?php echo $role->Id?>'><i class='fa-solid fa-file-circle-plus'></i>Skapa ny ingrediens</a><br>
    	    <a href='alchemy_ingredient_form.php?operation=insert&type=katalysator&RoleId=<?php echo $role->Id?>'><i class='fa-solid fa-file-circle-plus'></i>Skapa ny katalysator</a>
	    </div>
	</div>
	<div class='itemselector'>
	<div class="header">
		<i class="fa-solid fa-leaf"></i> Ingredienser
	</div>
	    
	    
	    
	    <form action="logic/alchemy_supplier_ingredient_save.php" method="post">
		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id;?>">

       <?php
    
       $ingredients = Alchemy_Ingredient::getAllIngredients($current_larp);
       if (!empty($ingredients)) {
           
           foreach ($ingredients as $ingredient) {
               printIngredient($ingredient, $supplier);
            }
             echo "<div class='center'><button class='button-18 type='submit'>Välj ingredienser</button></div>";
             
            
        }
        else {
            echo "<p>Inga registrerade ännu</p>";
        }
        ?>
    </div>

	<div class='itemselector'>
	<div class="header">
		<i class="fa-solid fa-leaf"></i> Katalysatorer
	</div>
	    
	    
	    
	    <form action="logic/alchemy_supplier_ingredient_save.php" method="post">
		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id;?>">

       <?php
    
       $ingredients = Alchemy_Ingredient::getAllCatalysts($current_larp);
       if (!empty($ingredients)) {
           
           foreach ($ingredients as $ingredient) {
               printIngredient($ingredient, $supplier);
            }
             echo "<div class='center'><button class='button-18 type='submit'>Välj katalysatorer</button></div>";
             
            
        }
        else {
            echo "<p>Inga registrerade ännu</p>";
        }
        ?>
    </div>

	
</body>

</html>