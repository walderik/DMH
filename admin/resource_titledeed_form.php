<?php
include_once 'header.php';


    $titledeed = Titledeed::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $titledeed = Titledeed::loadById($_GET['Id']);            
    }
      
    
    $resources = Resource::allNormalByCampaign($current_larp);
    
    $rare_resources = Resource::allRareByCampaign($current_larp);
    
    $normally_produces_resourceIds = $titledeed->getSelectedProducesResourcesIds();
    $normally_requires_resourceIds = $titledeed->getSelectedRequiresResourcesIds();
    $currency = $current_larp->getCampaign()->Currency;
    
    include 'navigation.php';
    ?>
    
<style>
input[type="number"] {
  width: 75px;
  text-align: right;
}
</style>
    <div class="content"> 
    <h1>Redigera resurser för <?php echo $titledeed->Name ?></h1>
	<form action="logic/resource_titledeed_form_save.php" method="post">
		<input type="hidden" id="Id" name="Id" value="<?php echo $titledeed->Id ?>">
		<table>
		<tr><td colspan="2"><h2>Normal drift</h2><p>En lagfart kan antingen producera eller behöva en resurs. 
    Om man skriver in att den både producerar och behöver kommer skillnaden att räknas ut.</p></td>
			<tr>
				<td>Producerar</td>
				<td>
				<?php 
				$money = 0;
				if ($titledeed->Money > 0) $money = abs($titledeed->Money);
				echo "<input type='number' name='Produces_Money' value = '$money' min='0' required> ";
				echo $currency;
				echo "<br>";
				
				foreach ($resources as $resource) {
				    $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id);
				    $quantity = 0;
				    if (!empty($resource_titledeed) && $resource_titledeed->Quantity > 0) $quantity = abs($resource_titledeed->Quantity);
				    echo "<input type ='number' name='Produces_$resource->Id' value = '$quantity' min='0' required> ";
				    if (in_array($resource->Id, $normally_produces_resourceIds)) {
				        echo "<span style='color:green;font-weight: bold;'>$resource->Name</span>";
				    }
				    else echo $resource->Name;
				    echo " ($resource->Price $currency / st)";
				    echo "<br>"; 
				}
				
				
				?>
				</td>
			</tr>
			<tr>
				<td>Producerar<br>ovanliga resurser</td>
				<td>
				</td>
			</tr>
			<tr>

				<td>Behöver</td>
				<td>
				<?php 
				$money = 0;
				if ($titledeed->Money < 0) $money = abs($titledeed->Money);
				echo "<input type ='number' name='Requires_Money' value = '$money' min='0' required> ";
				echo $currency;
				echo "<br>";
				
				foreach ($resources as $resource) {
				    $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id,);
				    $quantity = 0;
				    if (!empty($resource_titledeed) && $resource_titledeed->Quantity < 0) $quantity = abs($resource_titledeed->Quantity);
				    echo "<input type ='number' name='Requires_$resource->Id' value = '$quantity' min='0' required> ";
				    if (in_array($resource->Id, $normally_requires_resourceIds)) {
				        echo "<span style='color:green;font-weight: bold;'>$resource->Name</span>";
				    }
				    else echo $resource->Name;
				    echo " ($resource->Price $currency / st)";
				    echo "<br>";
				}
				
				
				?>
				</td>
			</tr>
			<tr>
			<td>Resultat<br>Överskott här innebär att ägarna får<br>kontanter att använda under lajvet.</td>
			<td id="result"><?php echo $titledeed->calculateResult()." $currency" ?>
			</tr>
		<tr><td colspan="2"><h2>Uppgradering</h2></td>
			<tr>

				<td>Behöver<br>normala resurser</td>
    			<td>
				<?php 
				echo "<input type ='number' name='MoneyForUpgrade' value = '$titledeed->MoneyForUpgrade' min='0' required> ";
				echo $currency;
				echo "<br>";
				foreach ($resources as $resource) {
				    $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id, $current_larp->Id);
				    $quantity = 0;
				    if (!empty($resource_titledeed)) $quantity = abs($resource_titledeed->QuantityForUpgrade);
				    echo "<input type ='number' name='Upgrade_Required_$resource->Id' value = '$quantity' min='0' required> ";
				    echo $resource->Name;
				    echo " ($resource->Price $currency / st)";
				    echo "<br>";
				}
				
				
				?>

    			</td>
			</tr>
			<tr>
				<td>Behöver<br>ovanliga resurser</td>
				<td>
				</td>
			</tr>
			<tr>
				<td><label for="SpecialUpgradeRequirements">Speciella krav<br>Dvs krav som inte är i handelsystemet<br>utan något i spel, tex kontrakt</label></td>
				<td><textarea id="SpecialUpgradeRequirements" name="SpecialUpgradeRequirements" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars(nl2br($titledeed->SpecialUpgradeRequirements)); ?></textarea></td>
			</tr>
		</table>

		<input id="submit_button" type="submit" value="Uppdatera">
	</form>
	</div>
    </body>

</html>