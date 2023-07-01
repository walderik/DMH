<?php
include_once 'header.php';


    $titledeed = Titledeed::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
            $titledeed = Titledeed::loadById($_GET['Id']);            
    }
      
    
    $resources = Resource::allNormalByCampaign($current_larp);
    
    $all_resources = Resource::allByCampaign($current_larp);
    $normally_produces_resourceIds = $titledeed->getSelectedProducesResourcesIds();
    $normally_requires_resourceIds = $titledeed->getSelectedRequiresResourcesIds();
    include 'navigation.php';
    ?>
    

    <div class="content"> 
    <h1>Redigera resurser för <?php echo $titledeed->Name ?> på <?php echo $current_larp->Name?></h1>
    <p>En lagfart kan antingen producera eller behöva en resurs. 
    Om man skriver in att den både producerar och behöver kommer skillnaden att räknas ut.</p>
	<form action="logic/resource_titledeed_form.php" method="post">
		<input type="hidden" id="Id" name="Id" value="<?php echo $titledeed->Id ?>">
		<table>
			<tr>
				<td>Producerar</td>
				<td>
				<?php 
				foreach ($resources as $resource) {
				    $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id);
				    $quantity = 0;
				    if (!empty($resource_titledeed) && $resource_titledeed->Quantity > 0) $quantity = abs($resource_titledeed->Quantity);
				    echo "<input type ='number' value = '$quantity' size='5' min='0' style='direction: rtl;'> ";
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
				foreach ($resources as $resource) {
				    $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id,);
				    $quantity = 0;
				    if (!empty($resource_titledeed) && $resource_titledeed->Quantity < 0) $quantity = abs($resource_titledeed->Quantity);
				    echo "<input type ='number' value = '$quantity' size='5' min='0' style='direction: rtl;'> ";
				    if (in_array($resource->Id, $normally_requires_resourceIds)) {
				        echo "<span style='color:green;font-weight: bold;'>$resource->Name</span>";
				    }
				    else echo $resource->Name;
				    echo "<br>";
				}
				
				
				?>
				</td>
			</tr>
			<tr>

				<td>Behöver<br>för<br>uppgradering</td>
    			<td>
				<?php 
				foreach ($all_resources as $resource) {
				    $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id, $current_larp->Id);
				    $quantity = 0;
				    if (!empty($resource_titledeed)) $quantity = abs($resource_titledeed->QuantityForUpggrade);
				    echo "<input type ='number' value = '$quantity' size='5' min='0' style='direction: rtl;'> $resource->Name<br>";
				}
				
				
				?>

    			</td>
			</tr>
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>