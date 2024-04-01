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
if (!Alchemy_Alchemist::isAlchemist($role)) {
    header('Location: index.php'); // karaktären är inte alkemist
    exit;
}

$alchemist = Alchemy_Alchemist::getForRole($role);


include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Alla recept <a href="view_alchemist.php?id=<?php echo $role->Id?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
		<p>Bocka för de recept du skulle vilja kunna. Arrangörerna godkänner sedan att du faktiskt kan dem.
		<br>Om du vill ha ett recept som inte redan finns i listan får du skapa ett nytt recept. Efter att du har skapat ett recept måste arrangörerna godkänna det innan du kan önska att få kunna det.</p>
		
		
		<?php if ($alchemist->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY)  echo "<strong>";?>
            <a href="alchemy_recipe_form.php?RoleId=<?php echo $role->Id?>&type=<?php echo Alchemy_Alchemist::INGREDIENT_ALCHEMY?>"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt traditionellt recept</a>&nbsp;&nbsp;  
		<?php if ($alchemist->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY)  echo "</strong>";?>
		
		<?php if ($alchemist->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY)  echo "<strong>";?>
        <a href="alchemy_recipe_form.php?RoleId=<?php echo $role->Id?>&type=<?php echo Alchemy_Alchemist::ESSENCE_ALCHEMY?>"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt experimentellt recept</a>  
		<?php if ($alchemist->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY)  echo "</strong>";?>

	    <form action="logic/alchemy_alchemist_recipe_save.php" method="post">
		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id;?>">

       <?php
    
       $recipes = Alchemy_Recipe::allNotSecretByCampaign($current_larp);
       if (!empty($recipes)) {
           $tableId = "recipes";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th></th>".
               "<th onclick='sortTable(1, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Typ</th>".
               "<th onclick='sortTable(4, \"$tableId\")'>Effekt</th>".
               "<th onclick='sortTable(5, \"$tableId\")'>Ingredienser / Essenser<br>Nivån anges inom parentes</th>".
               "<th onclick='sortTable(6, \"$tableId\")'>Godkänd/<br>Ännu inte godkänd</th>".
               "";
           
           foreach ($recipes as $recipe) {
                echo "<tr>\n";
                echo "<td>";
                if ($recipe->isApproved()) {
                    echo "<input type='checkbox' id='Ingridient$recipe->Id' name='RecipeId[]' value='$recipe->Id'>";
                }
                echo "</td>\n";
                
                echo "<td><a href='view_alchemy_recipe.php?recipeId=$recipe->Id&id=$role->Id'>$recipe->Name</a>";
                if ($recipe->AuthorRoleId == $role->Id) {
                    echo "<br><b>(Ditt egna)</b>";
                    if ($recipe->IsSecret) echo " HEMLIGT!";
                }
                
                echo "</td>\n";
                
                echo "<td>$recipe->Level</td>\n";
                
                echo "<td>";
                if ($alchemist->AlchemistType == $recipe->AlchemistType) echo "<strong>";
                echo $recipe->getRecipeType();
                if ($alchemist->AlchemistType == $recipe->AlchemistType) echo "</strong>";
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
            echo "</table>";
            echo "<br>";
            echo "<input type='submit' value='Välj recept'></form>";
            
        }
        else {
            echo "<p>Inga registrerade ännu</p>";
        }
        ?>
    </div>
	
</body>

</html>