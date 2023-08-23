<?php
include_once 'header.php';

$titledeeds = Titledeed::allByCampaign($current_larp, false);
$resources = Resource::allNormalByCampaign($current_larp);

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

input[type="number"].produces {
background-color: lightgreen;
}

input.requires {
background-color: lightcoral;
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
        <h1>Resursfördelning - normala resurser <a href="commerce.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till handel"></i></a></h1>
		<input type="hidden" id="Currency" value="<?php echo $currency ?>">
        <?php if (empty($resources) || empty($titledeeds)) {
            echo "Översikten kräver att det finns både resurder och lagfarter.";
            
        }
        else {
        ?>
        <p>Här ställer du in vad alla producerar eller behöver av olika vanliga resurser. 
        Vill du ange att de producerar något anger du en positiv siffra, om du vill att de 
        behöver något anger du en negativ siffra.<br>
        Om du anger en positiv siffra på pengarna, så är det pengar som följer med lagfarten vid lajvstart, att handla resurser för.<br>
        På balans kan du se hur mycket överskott/underskott det finns av en viss resurs på lajvet.<br>
        Antal kort är så många kort som kommer att behöva skrivas ut.</p>
        <p>Rutor med grön bakgrund är resurser som lagfarten normalt producerar och bör därför innehålla en positiv siffra.<br>
        Rutor med röd bakgrund är resurser som lagfarten normalt behöver och bör därför innehålla en negativ siffra.</p>
        <p>Om man ändrar siffrorna sparas det direkt.</p>
        <table>
    	<tr>
    		
    	<?php
    	echo "<th>Namn<br>$currency/st</th>";
    	echo "<th>$currency</th>\n";
    	foreach ($resources as $key => $resource) {
    	    echo "<th><a href='resource_form.php?operation=update&Id=$resource->Id'>$resource->Name</a><br>$resource->Price</th>\n";
    	}
    	?>
    	<th>Resultat</th>
		</tr>
		
		<?php 
		
		foreach ($titledeeds as $titledeed) {
		    echo "<tr><td style='text-align:left'><a href='titledeed_form.php?operation=update&id=$titledeed->Id'>$titledeed->Name</a></td>";
		    echo "<th style='text-align:left'>";
		    echo "<input type='number' id='$titledeed->Id' value='$titledeed->Money' onchange='recalculateMoney(this, $current_larp->Id)'>";
		    echo "</th>\n";
		    $produces = $titledeed->ProducesNormally();
		    $requires = $titledeed->RequiresNormally();
		    
		    foreach ($resources as $key => $resource) {
		        $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id);
		        $quantity = 0;
		        if (!empty($resource_titledeed)) $quantity = $resource_titledeed->Quantity;
		        $class = "";
		        if (in_array($resource, $produces)) $class = "produces";
		        elseif (in_array($resource, $requires)) $class="requires";
	            echo "<td style='text-align:right'>";
	            echo "<input type='number'  class='$class' id='$resource->Id:$titledeed->Id' value='$quantity' onchange='recalculate(this, $current_larp->Id)'>";
	            echo "</td>\n";
		    }
		    echo "<td style='text-align:right' id='Result_$titledeed->Id'>".$titledeed->calculateResult()." $currency</td>\n";
		    echo "</tr>\n";
		}
		
		echo "<tr><th style='text-align:left'>Balans</th>\n";
		echo "<th style='text-align:right' id='Money_sum'>".$titledeed->moneySum($current_larp)."</th>\n";
		foreach ($resources as $resource) {
		    echo "<th style='text-align:right' id='Balance_$resource->Id'>".$resource->countBalance($current_larp)."</th>\n";
		}
		echo "<td></td>";
		echo "<tr><th style='text-align:left'>Antal kort</th>\n";
		echo "<th></th>\n";
		foreach ($resources as $resource) {
		    echo "<th style='text-align:right' id='Cards_$resource->Id' >".$resource->countNumberOfCards($current_larp)."</th>\n";
		}
		echo "<td></td>";
		echo "</tr>\n";
		?>
		    
        
        
        </table>
        <?php }?>
    </body>
    

</html>