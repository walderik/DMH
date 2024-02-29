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




include 'navigation.php';
?>

	<div class="content">
		<h1>Alkemist <a href='view_role.php?id=<?php echo $role->Id?>'><?php echo $role->Name?></a>&nbsp;

		
		<a href='alchemy_alchemist_form.php?Id=<?php echo $alchemist->Id;?>&operation=update'>
		<i class='fa-solid fa-pen'></i></a> <a href="alchemy_alchemist_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a> 
		</h1>
		

		<div>
		

    		<table>
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

			<?php 
			$recepies = $alchemist->getRecipes();
			if (empty($recepies)) {
			    echo "Inga recept, än.";
			} else {
				echo "<table class='small_data'>";
				echo "<tr><th>Namn</th><th>Nivå</th><th>Typ</th><th>Effekt</th><th>Fick på/till<br>lajvet</th><th></th></tr>";
				foreach ($recepies as $recepie) {
				    echo "<tr><td><a href='view_alchemyrecepie.php?id=$recepie->Id'>$recepie->Name</td><td>$recepie->Level</td><td>$recepie->getRecipeType()</td><td>$recepie$recepie->Effect</td>";
				    echo "<td>";
				    //TODO skriv vilket lajv de fick receptet och kunna lägga till /godkänna att någon ha ett recept.
				    
				    echo "</td>\n";
				    
				    
				    echo "<td><a href='logic/view_magician_logic.php?operation=remove_recepi&RecepieId=$recepie->Id&Id=$alchemist->Id'><i class='fa-solid fa-xmark' title='Ta bort recept från alkemis'></i></a></td>";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			<p>
			<a href='choose_alchemy_recepie.php?id=<?php echo $alchemist->Id ?>&operation=add_alchemist_recipe'>Lägg till recept</a>


		</div>
		


</body>
</html>
