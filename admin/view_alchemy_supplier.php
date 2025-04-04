<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $supplierId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$supplier = Alchemy_Supplier::loadById($supplierId);
$role = $supplier->getRole();
$person = $role->getPerson();

include 'navigation.php';
include 'alchemy_navigation.php';
?>
<script src="../javascript/setringredientamount_ajax.js"></script>


	<div class="content">
		<h1>
			<?php echo "Lövjerist " . $role->getViewLink() . "&nbsp"?>;
			<a href='alchemy_supplier_sheet.php?id=<?php echo $supplier->Id ?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Lövjeristblad för <?php $role->Name?>'></i></a>&nbsp;
			<a href='alchemy_supplier_form.php?Id=<?php echo $supplier->Id;?>&operation=update'>
			<i class='fa-solid fa-pen'></i></a> <a href="alchemy_supplier_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a> 
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
    				<td>Workshop datum</td>
    				<td><?php echo $supplier->Workshop; ?></td>
    			</tr>
    			<tr>
    				<td>Anteckningar</td>
    				<td><?php echo nl2br(htmlspecialchars($supplier->OrganizerNotes)); ?></td>
    			</tr>
    			<tr><td></td></tr>
    		</table>

			<h2>Ingredienser på <?php echo $current_larp->Name?></h2>
			Klicka på det röda utropstecknet för att godkänna. <br>
			<?php 
			$amounts = $supplier->getIngredientAmounts($current_larp);
			if (empty($amounts)) {
			    echo "Inga ingredienser, än.<br>";
			} else {
				echo "<table class='small_data'>";
				echo "<tr><th>Ingrediens</th><th>Antal</th><th>Nivå</th><th>Ingrediens/Katalysator</th><th>Essenser</th><th>";
				echo showStatusIcon($supplier->allAmountOfIngredientsApproved($current_larp), "logic/alchemy_supplier_approve_amount.php?all=1&supplierId=$supplier->Id", "logic/alchemy_supplier_approve_amount.php?all=1&supplierId=$supplier->Id&unapprove=1");
				
				echo "</th><th></th></tr>";
				foreach ($amounts as $amount) {
				    $ingredient = $amount->getIngredient();
				    echo "<tr>";
				    echo "<td>$ingredient->Name</td>";
				    echo "<td>";
				    if ($amount->isApproved()) echo $amount->Amount;
				    else {
				        echo "<input type='number' id='$amount->Id' min='1' value='$amount->Amount' onchange='saveAmount(this)'>";
				    }
				    echo "</td>";
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
				    
				    echo "<td>";
				    echo showStatusIcon($amount->isApproved(), "logic/alchemy_supplier_approve_amount.php?id=$amount->Id&supplierId=$amount->SupplierId", "logic/alchemy_supplier_approve_amount.php?id=$amount->Id&supplierId=$amount->SupplierId&unapprove=1");
				    
				    echo "</td>\n";
				    
				    echo "<td>";
				    echo "<a href='logic/alchemy_supplier_ingredient.php?operation=delete&id=$amount->Id&supplierId=$amount->SupplierId'><i class='fa-solid fa-trash'></i>";
				    
				    echo "</td>\n";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>
			<br>
			<a href='choose_alchemy_ingredient.php?id=<?php echo $supplier->Id ?>&operation=add_supplier_ingredient'>Lägg till ingredienser</a>

			<h2>Ingredienser på tidigare lajv</h2>
<?php //TODO hämta upp ingredienser på tidigare lajv ?>
		</div>
		


</body>
</html>
