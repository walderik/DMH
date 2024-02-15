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

include 'navigation.php';
?>

	<div class="content">
		<h1><?php echo "Lövjerist <a href='view_role.php?id=$role->Id'>$role->Name</a>";?>&nbsp;

		
		<a href='alchemy_supplier_form.php?Id=<?php echo $supplier->Id;?>&operation=update'>
		<i class='fa-solid fa-pen'></i></a> <a href="alchemy_supplier_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a> 
		</h1>
		

		<div>
    		<table>
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
			Klicka på det röda utropstecknet för att godkänna. 
			<?php 
			$amounts = $supplier->getIngredientAmounts($current_larp);
			if (empty($amounts)) {
			    echo "Inga ingredienser, än.";
			} else {
				echo "<table class='small_data'>";
				echo "<tr><th>Antal</th><th>Ingrediens</th><th>Nivå</th><th>Ingrediens/Katalysator</th><th>Essenser</th><th></th><th></th></tr>";
				foreach ($amounts as $amount) {
				    $ingredient = $amount->getIngredient();
				    echo "<tr>";
				    echo "<td>$ingredient->Name</td>";
				    echo "<td>$amount->Amount</td>";
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
				    echo showStatusIcon($amount->isApproved(), "logic/alchemy_supplier_approve_amount.php?id=$amount->Id&supplierId=$amount->SupplierId");
				    
				    echo "</td>\n";
				    
				    echo "<td>";
				    echo "<a href='logic/alchemy_supplier_ingredient.php?operation=delete&id=$amount->Id&supplierId=$amount->SupplierId'><i class='fa-solid fa-trash'></i>";
				    
				    echo "</td>\n";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>

			<h2>Ingredienser på tidigare lajv</h2>
<?php //TODO hämta upp ingredienser på tidigare lajv ?>
		</div>
		


</body>
</html>
