<?php

$results = TitledeedResult::getAllResultsForTitledeed($titledeed);
if (!isset($currency)) $currency = $titledeed->getCampaign()->Currency;
if (!empty($results)) {
    echo "<h2>Tidigare resultat</h2>";
    foreach($results as $result) {
        
        echo "<h3>".$result->getLarp()->Name."</h3>";
        echo "<table>";
        echo "<tr><td>Klarade behov</td><td>". ja_nej($result->NeedsMet)."</td></tr>";
        echo "<tr><td>Resulterande pengar</td><td> $result->Money $currency</td></tr>";
        echo "<tr><td>Uppgradering</td><td>";
        
        
        $upgrade_results = $result->getAllUpgradeResults();
        foreach ($upgrade_results as $upgrade_result) {
            $checked = "";
            if ($upgrade_result->NeedsMet) $checked = "checked='checked'";
            if (empty($upgrade_result->ResourceId)) {
                echo "<input type='checkbox' id='MoneyForUpgradeMet' name='MoneyForUpgradeMet' value='MoneyForUpgradeMet' $checked disabled='true'> ";
                echo "$upgrade_result->QuantityForUpgrade $currency<br>";
            } else {
                $resource = $upgrade_result->getResource();
                $quantity = $upgrade_result->QuantityForUpgrade;
                echo "<input type='checkbox' id='resouceId$upgrade_result->ResourceId' name='resouceId[]' value='$upgrade_result->ResourceId' $checked disabled='true'> ";
                if ($quantity == 1) {
                    echo "1 $resource->UnitSingular<br>";
                } else {
                    echo "$quantity $resource->UnitPlural<br>";
                }
                
            }
            
            
        }
        
        
        if ($titledeed->SpecialUpgradeRequirements) {
            
            echo nl2br(htmlspecialchars($titledeed->SpecialUpgradeRequirements));
        }
        
        echo "</td></tr>";
        
        
        
        
        
        
        
        echo "<tr><td>Kommentarer</td><td>". nl2br(htmlspecialchars($result->Notes)."</td></tr>");
        echo "</table>";
    }
}

?>
