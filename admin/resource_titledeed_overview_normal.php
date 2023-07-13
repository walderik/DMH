<?php
include_once 'header.php';

$titledeeds = Titledeed::allByCampaign($current_larp);
$resources = Resource::allNormalByCampaign($current_larp);

$sums = array();
$numberOfCards = array();
$money_sum = 0;
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
</style>
    <div class="content">
        <h1>Lagfarter - normala resurser - översikt</h1>
        <?php if (empty($resources) || empty($titledeeds)) {
            echo "Översikten kräver att det finns både resurder och lagfarter.";
            
        }
        else {
        ?>
        <p>Om man ändrar siffrorna sparas det direkt.</p>
        <table>
    	<tr>
    		<th></th>
    		
    	<?php
    	echo "<th>$currency</th>\n";
    	foreach ($resources as $key => $resource) {
    	    echo "<th><a href='resource_form.php?operation=update&Id=$resource->Id'>$resource->Name</a></th>\n";
    	    $sums[$key]=0;
    	    $numberOfCards[$key] = 0;
    	}
    	?>
    	<th>Resultat</th>
		</tr>
		
		<?php 
		
		foreach ($titledeeds as $titledeed) {
		    echo "<tr><th style='text-align:left'><a href='resource_titledeed_form.php?Id=$titledeed->Id'>$titledeed->Name</a></th>";
		    echo "<th style='text-align:left'>$titledeed->Money</th>\n";
		    $money_sum = $money_sum + $titledeed->Money;
		    foreach ($resources as $key => $resource) {
		        $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id);
		        if (empty($resource_titledeed)) echo "<td style='text-align:right'>0</td>\n";
		        else {
		            echo "<td style='text-align:right'>$resource_titledeed->Quantity</td>\n";
		            $sums[$key] = $sums[$key] + $resource_titledeed->Quantity;
		            if ($resource_titledeed->Quantity > 0) {
		                $numberOfCards[$key] = $numberOfCards[$key] + $resource_titledeed->Quantity;
		            }
		        }
		    }
		    echo "<td style='text-align:right'>".$titledeed->calculateResult()." $currency</td>\n";
		    echo "</tr>\n";
		}
		
		echo "<tr><th style='text-align:left'>Balans</th>\n";
		echo "<th style='text-align:right'>$money_sum</th>\n";
		foreach ($resources as $key => $resource) {
		    echo "<th style='text-align:right'>$sums[$key]</th>\n";
		}
		echo "<td></td>";
		echo "<tr><th style='text-align:left'>Antal kort</th>\n";
		echo "<th></th>\n";
		foreach ($resources as $key => $resource) {
		    echo "<th style='text-align:right'>$numberOfCards[$key]</th>\n";
		}
		echo "<td></td>";
		echo "</tr>\n";
		?>
		    
        
        
        </table>
        <?php }?>
    </body>
<?php 
include_once '../javascript/table_sort.js';
?>
</html>