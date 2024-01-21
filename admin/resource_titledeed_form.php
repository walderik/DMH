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
    
    
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
    }
    else {
        $referer = "";
    }
    
    include 'navigation.php';
    ?>
    
<style>
input[type="number"] {
  width: 75px;
  text-align: right;
}
</style>


<script>

function updateResult() {

	var result = 0;
    result = result + document.getElementById("Produces_Money").value;
    result = result - document.getElementById("Requires_Money").value;


	const producesArr = document.getElementsByClassName("Produces");
	const requiresArr = document.getElementsByClassName("Requires");


    for (let i = 0; i < producesArr.length; i++) {
      var cost = document.getElementById(producesArr[i].name + "Cost").value;
      result = result + producesArr[i].value * cost;
    }


    for (let i = 0; i < requiresArr.length; i++) {
      var cost = document.getElementById(requiresArr[i].name + "Cost").value;
      result = result - requiresArr[i].value * cost;
    }


    var currency = document.getElementById("Currency").value; 
	document.getElementById("result").innerHTML = result + " " + currency;

}

</script>
    <div class="content"> 
    <h1>Redigera resurser för <?php echo $titledeed->Name ?></h1>
	<form action="logic/resource_titledeed_form_save.php" method="post">
		<input type="hidden" id="Id" name="Id" value="<?php echo $titledeed->Id ?>">
		<input type="hidden" id="Currency" value="<?php echo $currency ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
		<tr><td colspan="2"><h2>Normal drift</h2>
		<p>En lagfart kan antingen ha eller behöva en resurs. 
    	Om man skriver in att den både har och behöver kommer skillnaden att räknas ut.</p>
    	<p>Resultatet räknas om när man ändrar siffrorna, men för att spara behöver man klicka på "Uppdatera".</p>
    	</td>
			<tr>
				<td>Tillgångar</td>
				<td>
				<?php 
				$money = 0;
				if ($titledeed->Money > 0) $money = abs($titledeed->Money);
				echo "<input type='number' id='Produces_Money' name='Produces_Money' value = '$money' min='0' onchange='updateResult()' required> ";
				echo $currency;
				echo "<br>";
				
				foreach ($resources as $resource) {
				    $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id);
				    $quantity = 0;
				    if (!empty($resource_titledeed) && $resource_titledeed->Quantity > 0) $quantity = abs($resource_titledeed->Quantity);
				    echo "<input class='Produces' type ='number' name='Produces_$resource->Id' value = '$quantity' min='0' onchange='updateResult()' required> ";
                    echo "<input type='hidden' id='Produces_".$resource->Id."Cost' value='$resource->Price'>";
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
				<td>Tillgångar<br>ovanliga resurser</td>
				<td>
                <?php 
                
                foreach ($rare_resources as $resource) {
                    $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id);
                    $quantity = 0;
                    if (!empty($resource_titledeed) && $resource_titledeed->Quantity > 0) $quantity = abs($resource_titledeed->Quantity);
                    echo "<input class='Produces' type ='number' name='Produces_$resource->Id' value = '$quantity' min='0' onchange='updateResult()' required> ";
                    echo "<input type='hidden' id='Produces_".$resource->Id."Cost' value='$resource->Price'>";
                    if (in_array($resource->Id, $normally_produces_resourceIds)) {
                        echo "<span style='color:green;font-weight: bold;'>$resource->Name</span>";
                    }
                    else echo $resource->Name;
                    echo "<br>";
                }
                
                
                ?>

				</td>
			</tr>
			<tr>

				<td>Behöver</td>
				<td>
				<?php 
				$money = 0;
				if ($titledeed->Money < 0) $money = abs($titledeed->Money);
				echo "<input type ='number' id='Requires_Money' name='Requires_Money' value = '$money' min='0' onchange='updateResult()' required> ";
				echo $currency;
				echo "<br>";
				
				foreach ($resources as $resource) {
				    $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id,);
				    $quantity = 0;
				    if (!empty($resource_titledeed) && $resource_titledeed->Quantity < 0) $quantity = abs($resource_titledeed->Quantity);
				    echo "<input type ='number' class='Requires' name='Requires_$resource->Id' value = '$quantity' min='0' onchange='updateResult()' required> ";
				    echo "<input type='hidden' id='Requires_".$resource->Id."Cost' value='$resource->Price'>";
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
				<?php 
				foreach ($rare_resources as $resource) {
				    $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id, $current_larp->Id);
				    $quantity = 0;
				    if (!empty($resource_titledeed)) $quantity = abs($resource_titledeed->QuantityForUpgrade);
				    echo "<input type ='number' name='Upgrade_Required_$resource->Id' value = '$quantity' min='0' required> ";
				    echo $resource->Name;
				    echo "<br>";
				}
				
				
				
				?>
				</td>
			</tr>
			<tr>
				<td><label for="SpecialUpgradeRequirements">Speciella krav<br>Dvs krav som inte är i handelsystemet<br>utan något i spel, tex kontrakt</label></td>
				<td><textarea id="SpecialUpgradeRequirements" name="SpecialUpgradeRequirements" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($titledeed->SpecialUpgradeRequirements); ?></textarea></td>
			</tr>
		</table>

		<input id="submit_button" type="submit" value="Uppdatera">
	</form>
	</div>
    </body>

</html>