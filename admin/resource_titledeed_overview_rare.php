<?php
include_once 'header.php';

$titledeeds = Titledeed::allByCampaign($current_larp, false);
$resources = Resource::allRareByCampaign($current_larp);

$currency = $current_larp->getCampaign()->Currency;

include 'navigation.php';
?>
<style>
th, td {
  border-style:solid;
  border-color: #d4d4d4;
  padding: 10px;
  margin: 0px;  
}

input {
  width: 60px;
  text-align: right; 
}

</style>
<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>
<script src="../javascript/setresource_ajax.js"></script>

    <div class="content">
        <h1>Resursfördelning - ovanliga resurser <a href="commerce.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till handel"></i></a></h1>
		<input type="hidden" id="Currency" value="<?php echo $currency ?>">
        <?php if (empty($resources) || empty($titledeeds)) {
            echo "Översikten kräver att det finns både resurser och lagfarter.";
            
        }
        else {
        ?>
        <p>En positiv siffra betyder att verksamheten har varan, en negativ att det behövs för uppgradering.</p>
        <table>
    	<tr>
    		<th></th>
    	<?php 
    	foreach ($resources as $key => $resource) {
    	    echo "<th><a href='resource_form.php?operation=update&Id=$resource->Id'>$resource->Name</a></th>\n";
    	}
    	?>
		</tr>
		
		<?php 
		
		foreach ($titledeeds as $titledeed) {
		    echo "<tr><th style='text-align:left'><a href='titledeed_form.php?operation=update&id=$titledeed->Id'>$titledeed->Name</a></th>";
		    foreach ($resources as $key => $resource) {
		        $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id);
		        $quantity = 0;
		        if (!empty($resource_titledeed)) {
		            $quantity = 0;
		            if ($resource_titledeed->Quantity > 0) $quantity = $resource_titledeed->Quantity;
		            elseif ($resource_titledeed->QuantityForUpgrade > 0) $quantity = 0 - $resource_titledeed->QuantityForUpgrade;
		        }
		        
		        echo "<td style='text-align:right'>";
		        echo "<input type='number' id='$resource->Id:$titledeed->Id' value='$quantity' onchange='recalculate(this, $current_larp->Id)'>";
		        echo "</td>\n";
		    }
		    echo "</tr>\n";
		}
		
		echo "<tr><th style='text-align:left'>Balans</th>\n";
		foreach ($resources as $resource) {
		    echo "<th style='text-align:right' id='Balance_$resource->Id'>".$resource->countBalance($current_larp)."</th>\n";
		}
		echo "<tr><th style='text-align:left'>Antal kort</th>\n";
		foreach ($resources as $resource) {
		    echo "<th style='text-align:right' id='Cards_$resource->Id' >".$resource->countNumberOfCards($current_larp)."</th>\n";
		}
		echo "</tr>\n";

		echo "<tr>\n";
		echo "<th></th>\n";
		foreach ($resources as $key => $resource) {
		    echo "<th><a href='resource_form.php?operation=update&Id=$resource->Id'>$resource->Name</a><br>$resource->Price</th>\n";
		}
		echo "</tr>\n";
		
		
		?>
		    
        
        
        </table>
        <?php }?>
    </body>

</html>