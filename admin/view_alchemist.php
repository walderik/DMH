<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $alchemistId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$alchemist = Alchemy_Alchemist::loadById($alchemistId);
$role = $alchemist->getRole();
$person = $role->getPerson();

$teacher = $alchemist->getTeacher();
if (isset($teacher)) $teacherRole = $teacher->getRole();



include 'navigation.php';
include 'alchemy_navigation.php';
?>

	<div class="content">
		<h1>
		<?php echo "Alkemist " . $role->getViewLink() . "&nbsp";?>
		<a href='alchemy_alchemist_sheet.php?id=<?php echo $alchemist->Id ?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Alkemistblad för <?php $role->Name?>'></i></a>&nbsp;

		<a href='alchemy_alchemist_form.php?Id=<?php echo $alchemist->Id;?>&operation=update'>
		<i class='fa-solid fa-pen'></i></a> <a href="alchemy_alchemist_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a> 
		</h1>
		

		<div>
		

    		<table>
    			<tr>
    				<td>Spelas av 
    				</td>
    				<td>
                		<?php 
                		if (!is_null($person)) echo $person->getViewLink()." ".contactEmailIcon($person); 
                		else echo "NPC";
                		?>
                    </td>
                </tr>
     			<tr>
    				<td>Typ 
    				</td>
    				<td><?php echo $alchemist->getAlchemistType() ?>
                    </td>
    			</tr>
    			<tr>
    				<td>Nivå 
    				</td>
    				<td>
    					<?php echo nl2br(htmlspecialchars($alchemist->Level)); ?>
                    </td>
    			</tr>

    			<tr>
    				<td>Lärare</td>
    				<td><?php if (isset($teacherRole)) echo "<a href ='view_alchemist.php?id=$teacher->Id'>$teacherRole->Name</a>"; ?></td>
    			</tr>
    			<?php 
    			$students = $alchemist->getStudents();
    			if (isset($students)) {?>
    			<tr>
    				<td>Elever</td>
    				<td><?php 
    				    $studentLinks = array();
    				    foreach($students as $student) {
    				        $studenttype = $student->getAlchemistType();
    				        $str = "<a href ='view_alchemist.php?id=$student->Id'>".$student->getRole()->Name."</a> (";
    				        $str.=$studenttype.", ";
    				        $str.="nivå $student->Level)";
    				        $studentLinks[] = $str;
    				    }
    				    echo implode("<br>", $studentLinks); 
    				    
    				    ?></td>
    			</tr>
    			<?php }?>
				<tr>
    				<td>Utrustning</td>
    				<td>
    					<?php 
    					echo "<a href='upload_image.php?id=$alchemist->Id&type=alchemist'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
    					if ($alchemist->hasEquipmentImage()) {
    					    echo "<br>";
    					    $image = Image::loadById($alchemist->ImageId);
    
    					        echo "<img width='300' src='../includes/display_image.php?id=$alchemist->ImageId'/>\n";
    					        if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
    
    					}
    					?>
    					
    				</td>
    			</tr>
     			<tr>
    				<td>Workshop datum</td>
    				<td><?php echo $alchemist->Workshop; ?></td>
    			</tr>
    			
    			
    			
    			<tr>
    				<td>Anteckningar</td>
    				<td><?php echo nl2br(htmlspecialchars($alchemist->OrganizerNotes)); ?></td>
    			</tr>
    			<tr><td></td></tr>
    		</table>

			<h2>Recept</h2>
			recept markerade med <i class="fa-solid fa-star"></i> är skapade av alkemisten.
			<?php 
			$recipes = $alchemist->getRecipes(false);
			if (empty($recipes)) {
			    echo "Inga recept, än.";
			} else {
				echo "<table class='small_data'>";
				echo "<tr><th>Namn</th><th>Nivå</th><th>Typ</th><th>Effekt</th><th>Fick på/till<br>lajvet</th><th></th></tr>";
				foreach ($recipes as $recipe) {
				    echo "<tr><td><a href='view_alchemy_recipe.php?id=$recipe->Id'>$recipe->Name</a>";
				    if ($recipe->AuthorRoleId == $alchemist->RoleId) echo " <i class='fa-solid fa-star'></i>";
				    echo "</td><td>$recipe->Level</td><td>".$recipe->getRecipeType()."</td><td>$recipe->Effect</td>";
				    echo "<td>";
				    $approvedLarpId = $alchemist->recipeApprovedLarp($recipe);
				    if (isset($approvedLarpId)) {
				        $larp = LARP::loadById($approvedLarpId);
				        echo $larp->Name;
				    } else {
				        if ($recipe->isApproved()) echo showStatusIcon(false,  "logic/approve_alchemist_recipe.php?recipeId=$recipe->Id&alchemistId=$alchemist->Id");
				        else echo "Receptet är inte godkänt";
				    }
				    echo "</td>\n";
				    
				    echo "<td>";
				    echo "<a href='logic/view_alchemist_logic.php?operation=remove_recipe&RecipeId=$recipe->Id&id=$alchemist->Id'><i class='fa-solid fa-xmark' title='Ta bort recept från alkemis'></i></a></td>";
				    echo "</td>\n";
				    
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			<p>
			<a href='choose_alchemy_recipe.php?id=<?php echo $alchemist->Id ?>&operation=add_alchemist_recipe'>Lägg till recept</a>

			<h2>Recept skapade av <?php echo $role->Name?> </h2>

			<?php 
			$recipes = Alchemy_Recipe::allByRole($role);
			if (empty($recipes)) {
			    echo "Inga recept, än.";
			} else {
				echo "<table class='small_data'>";
				echo "<tr><th>Namn</th><th>Nivå</th><th>Typ</th><th>Effekt</th><th>Godkänt</th></tr>";
				foreach ($recipes as $recipe) {
				    echo "<tr><td><a href='view_alchemy_recipe.php?id=$recipe->Id'>$recipe->Name</td><td>$recipe->Level</td><td>".$recipe->getRecipeType()."</td><td>$recipe->Effect</td>";
				    echo "<td>";
				    echo showStatusIcon($recipe->isApproved(),  "logic/toggle_approve_recipe.php?recipeId=$recipe->Id") . "\n";
				    
				    echo "</td>\n";
				    
				    
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>


		</div>
		


</body>
</html>
