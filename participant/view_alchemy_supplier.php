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
    
    if (isset($_GET['operation']) && $_GET['operation']=='delete') {
        Alchemy_Supplier_Ingredient::delete($_GET['supplierIngredientId']);
    }
    
}

$role = Role::loadById($RoleId);
$person = $role->getPerson();

if (is_null($person) || $person->Id != $current_person->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}

$supplier = Alchemy_Supplier::getForRole($role);



include 'navigation.php';
?>
<style>
input {
  width: 60px;
  text-align: right; 
}
</style>
<script src="../javascript/setringredientamount_ajax.js"></script>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-leaf"></i>
			<?php echo "Löjverist $role->Name";?>
		</div>
		
   		<div class='itemcontainer'>
           	<div class='itemname'>Workshop datum</div>
			<?php 
			if ($supplier->hasDoneWorkshop()) echo $supplier->Workshop; 
			    else echo showParticipantStatusIcon(false, "Du har inte deltagit i workshop om alkemi/lövjeri");
		    ?>			
		</div>
	</div>
	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-leaf"></i> Ingredienser till <?php echo $current_larp->Name?>
		</div>
		<?php if ($current_larp->isAlchemySupplierInputOpen()) {?>
   		<div class='itemcontainer'>
		Lägg till de ingredienser som du tänker ta med till lajvet. Arrangörerna godkänner sedan att du får ta med ingrediensen 
		och hur många du får ta med.<br>
		<a href='alchemy_all_ingredients.php?RoleId=<?php echo $role->Id?>'>Visa alla ingredienser som finns / Välj ingredienser att ta med</a>
		</div>
		<?php } ?>
   		<div class='itemcontainer'>

			<div  style='display:table'>

 
			<?php 
			$amounts = $supplier->getIngredientAmounts($current_larp);
			if (empty($amounts)) {
			    echo "Inga valda ingredenser, än.";
			} else {
			    if (!empty($current_larp->getLastDayAlchemySupplier())) {
			        echo "Fram till ".$current_larp->getLastDayAlchemySupplier()." har du möjlighet att redigera din ingredienslista.<br>";
			    } 
		         echo "Så länge arrangörerna inte har godkänt att du får ta med dig ingrediensen och hur många du tar med dig, så kan du ändra hur många du vill ha med dig och du kan även ta bort den helt från din lista. Om du vill ändra efter att den är godkänd behöver du kontakta arrangörerna.";
			    
			    echo "<table class='participant_table' style='width:100%;'>";
				echo "<tr><th>Ingrediens</th><th>Essenser</th><th>Effekt</th><th>Antal</th><th>Godkänd/<br>Ännu inte godkänd</th><th></th></tr>";
				foreach ($amounts as $amount) {
				    $ingredient = $amount->getIngredient();
				    echo "<tr>";
				    echo "<td>$ingredient->Name<br>";
				    if ($ingredient->isCatalyst()) echo "Katalysator";
				    else echo "Ingrediens";
				    echo "<br>";
				    echo "Nivå $ingredient->Level<br>\n";
				    echo "</td>\n";
				    echo "<td>";
				    if ($ingredient->isIngredient()) {
				        echo $ingredient->getEssenceNames();
				    }
				    echo "</td>\n";
				    echo "<td>$ingredient->Effect</td>";
				    echo "<td>";
				    if ($amount->isApproved() || !$current_larp->isAlchemySupplierInputOpen()) echo $amount->Amount;
				    else {
				        echo "<input type='number' id='$amount->Id' min='1' value='$amount->Amount' onchange='saveAmount(this)' maxlength='3' size='4'>";
				    }
				    echo "</td>";

				    echo "<td>";
				    echo showStatusIcon($amount->isApproved());
				    echo "</td>\n";
				    echo "<td>";
				    if (!$amount->isApproved() && $current_larp->isAlchemySupplierInputOpen()) echo "<a href='view_alchemy_supplier.php?operation=delete&supplierIngredientId=$amount->Id&id=$role->Id'><i class='fa-solid fa-trash'></i>";
				    
				    
				    echo "</td>\n";
				    echo "</tr>";
				}
				echo "</table>";
			}
			?>

		</div>
		</div>
	</div>
		


</body>
</html>
