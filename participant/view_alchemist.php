<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RoleId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$role = Role::loadById($RoleId);
$person = $role->getPerson();

if ($person->Id != $current_person->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}

$alchemist = Alchemy_Alchemist::getForRole($role);

if (isset($_GET['operation']) && $_GET['operation']=='remove_recipe') {
    $alchemist->removeRecipe($_GET['RecipeId']);
}

$teacher = $alchemist->getTeacher();
if (isset($teacher)) $teacherRole = $teacher->getRole();


include 'navigation.php';
?>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-flask"></i>
			<?php echo "Alkemist $role->Name";?>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Typ</div>
		<?php echo $alchemist->getAlchemistType(); ?>
		</div>

   		<div class='itemcontainer'>
       	<div class='itemname'>Nivå</div>
		<?php echo $alchemist->Level; ?>
		</div>

		<?php if (isset($teacherRole)) {?>
	   		<div class='itemcontainer'>
           	<div class='itemname'>Lärare</div>
			<?php echo $teacherRole->Name; ?>
			</div>
		<?php }?>
		
		<?php 
		$students = $alchemist->getStudents();
		if (!empty($students)) {?>
		<div class='itemcontainer'>
           	<div class='itemname'>Elever</div>
			<?php 
			    $studentLinks = array();
			    foreach($students as $student) {
			        $studenttype = $student->getAlchemistType();
			        $str = $student->getRole()->Name." (";
			        $str.=$studenttype.", ";
			        $str.="nivå $student->Level)";
			        $studentLinks[] = $str;
			    }
			    echo implode("<br>", $studentLinks); 
			    
		    ?>
    	</div>
		<?php }?>

   		<div class='itemcontainer'>
       	<div class='itemname'>Utrustning</div>
		<?php 
			if ($alchemist->hasEquipmentImage()) {
			    echo "<br>";
			    $image = Image::loadById($alchemist->ImageId);

		        echo "<img width='300' src='../includes/display_image.php?id=$alchemist->ImageId'/>\n";
		        if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
			} else {
			    echo "<a href='upload_image.php?id=$alchemist->Id&type=alchemist'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
			}
		?>
		</div>

   		<div class='itemcontainer'>
           	<div class='itemname'>Workshop datum</div>
			<?php 
			if ($alchemist->hasDoneWorkshop()) echo $alchemist->Workshop; 
			    else echo showParticipantStatusIcon(false, "Du har inte deltagit i workshop om alkemi/lövjeri");
		    ?>			
		</div>
	</div> 

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-scroll"></i> Recept du kan
		</div>
   		<div class='itemcontainer'>
		Här är en lista på de recept du kan. Om något saknas kan du titta på listan med alla recept. Där kan du markera, bland de godkända recepten vilka du skulle vilja kunna. Och du kan även lägga till nya recept. 
		Dessa kommer att godkännas av arrangörerna innan du får möjlighet att önska att du kan dem.
		</div>
   		<div class='itemcontainer'>

			<div  style='display:table'>
			<a href='alchemy_all_recipes.php?RoleId=<?php echo $role->Id?>'>Visa alla recept som finns / Önska recept du vill kunna / Skapa nya recept</a><br><br>

			<?php 
			$recipes = $alchemist->getRecipes(false);
			if (empty($recipes)) {
			    echo "Inga recept, än.";
			} else {
				echo "<table class='small_data'>";
				echo "<tr><th>Namn</th><th>Nivå</th><th>Typ</th><th>Effekt</th><th>Fick på/till<br>lajvet</th><th></th></tr>";
				foreach ($recipes as $recipe) {
				    echo "<tr><td><a href='view_alchemy_recipe.php?recipeId=$recipe->Id&id=$role->Id'>$recipe->Name</td><td>$recipe->Level</td><td>".$recipe->getRecipeType()."</td><td>$recipe->Effect</td>";
				    echo "<td>";
				    $approvedLarpId = $alchemist->recipeApprovedLarp($recipe);
				    if (isset($approvedLarpId)) {
				        $larp = LARP::loadById($approvedLarpId);
				        echo $larp->Name;
				    } else {
				        echo "Inte godkänt, än.";
				    }
				    echo "</td>\n";
				    echo "<td>";
				    if (!isset($approvedLarpId)) {
				        echo "<a href='view_alchemist.php?operation=remove_recipe&RecipeId=$recipe->Id&id=$role->Id'><i class='fa-solid fa-xmark' title='Ta bort recept från alkemis'></i></a></td>";
				    }
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			</div>
			</div>
			</div>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-scroll"></i> Recept du har skapat
		</div>
   		<div class='itemcontainer'>
			Här är alla recept du har skapat. När receptet är godkänt av arrangör går det inte längre att redigera. Om du vill ändra något på receptet efter att det är godkänt får du kontakta arrangör.<br>
			Om du vill skapa ett nytt recept får du gå till sidan med alla recept, för att se så att det inte redan finns ett sådant recept.
		</div>
   		<div class='itemcontainer'>

			<div  style='display:table'>

       <?php
    
       $recipes = Alchemy_Recipe::allByRole($role);
       if (!empty($recipes)) {
           $tableId = "recipes";
           echo "<table id='$tableId' class='data'>";
           echo "<tr>".
               "<th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Typ</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Effekt</th>".
               "<th onclick='sortTable(4, \"$tableId\")'>Ingredienser / Essenser<br>Nivån anges inom parentes</th>".
               "<th onclick='sortTable(5, \"$tableId\")'>Godkänd/<br>Ännu inte godkänd</th>".
               "";
           
           foreach ($recipes as $recipe) {
                echo "<tr>\n";
                echo "<td><a href='view_alchemy_recipe.php?recipeId=$recipe->Id&id=$role->Id'>$recipe->Name</a> ";
                if (!$recipe->IsApproved()) {
                    echo "<a href='alchemy_recipe_form.php?RoleId=$role->Id&recipeId=$recipe->Id'><i class='fa-solid fa-pen'></i></a>";
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
		</div>		
		</div>		


</body>
</html>
