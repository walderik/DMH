<?php
include_once 'header.php';


    $titledeedresult = TitledeedResult::newWithDefault();
    $currency = $current_larp->getCampaign()->Currency;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $titledeed = Titledeed::loadById($_GET['id']);  

        $tmpresult = $titledeed->getResult($current_larp);
        if (isset($tmpresult)) {
            $titledeedresult = $tmpresult;
        } else {
            $titledeedresult ->TitledeedId = $titledeed->Id;
        }
    }
      
    function default_value($field) {
        GLOBAL $titledeedresult;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($titledeedresult->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                if (!is_null($titledeedresult->Id)) {
                    $output = $titledeedresult->Id;
                }
                break;
            case "action":
                if (is_null($titledeedresult->Id)) {
                    $output = "Rapportera";
                    break;
                }
                $output = "Uppdatera";
                break;
        }

        echo $output;
    }
    
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
    }
    else {
        $referer = "";
    }
    $referer = (isset($referer)) ? $referer : '../titledeed_admin.php';
    
    include 'navigation.php';
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> resultat på <?php echo $titledeed->Name ?> <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="logic/titledeed_result_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<input type="hidden" id="TitledeedId" name="TitledeedId" value="<?php echo $titledeed->Id;?>">
		<table>
			<tr>
				<td><label for="Name">Klarade behov</label></td>
				<td>
					<?php echo $titledeed->RequiresString()."<br>";?>
       				<input type="radio" id="HNeedsMet_yes" name="NeedsMet" value="1" <?php if ($titledeedresult->NeedsMet == 1) echo 'checked="checked"'?> required> 
        			<label for="NeedsMet_yes">Ja</label><br> 
        			<input type="radio" id="NeedsMet_no" name="NeedsMet" value="0" <?php if ($titledeedresult->NeedsMet != 1) echo 'checked="checked"'?>> 
        			<label for="NeedsMet_no">Nej</label>
			</tr>
			<tr>
				<td><label for="Type">Uppgradering<br>Bocka i de krav som uppfylldes</label></td>
				<td>
				
					<?php 
					if (is_null($titledeedresult->Id)) {
					    $resource_titledeeds = $titledeed->RequiresForUpgrade();
					   if ($titledeed->MoneyForUpgrade > 0) {
					       echo "<input type='checkbox' id='MoneyForUpgradeMet' name='MoneyForUpgradeMet' value='MoneyForUpgradeMet' > ";
					       echo "$titledeed->MoneyForUpgrade $currency<br>";
					   }
					   

					   foreach ($resource_titledeeds as $resource_titledeed) {
					       $resource = $resource_titledeed->getResource();
					       $quantity = abs($resource_titledeed->QuantityForUpgrade);
					       echo "<input type='checkbox' id='resouceId$resource->Id' name='resouceId[]' value='$resource->Id' > ";
					       if ($quantity == 1) {
					           echo "1 $resource->UnitSingular<br>";
					       } else {
					           echo "$quantity $resource->UnitPlural<br>";
					       }
					   }
					} else {
					    $upgrade_results = $titledeedresult->getAllUpgradeResults();
					    foreach ($upgrade_results as $upgrade_result) {
					        $checked = "";
					        if ($upgrade_result->NeedsMet) $checked = "checked='checked'";
					        if (empty($upgrade_result->ResourceId)) {
					            echo "<input type='checkbox' id='MoneyForUpgradeMet' name='MoneyForUpgradeMet' value='MoneyForUpgradeMet' $checked> ";
					            echo "$upgrade_result->QuantityForUpgrade $currency<br>";
					        } else {
					            $resource = $upgrade_result->getResource();
					            $quantity = $upgrade_result->QuantityForUpgrade;
					            echo "<input type='checkbox' id='resouceId$upgrade_result->ResourceId' name='resouceId[]' value='$upgrade_result->ResourceId' $checked> ";
					            if ($quantity == 1) {
					                echo "1 $resource->UnitSingular<br>";
					            } else {
					                echo "$quantity $resource->UnitPlural<br>";
					            }
					            
					        }
					    }
					    
					}
					
					
					if ($titledeed->SpecialUpgradeRequirements) {
					    
					    echo nl2br(htmlspecialchars($titledeed->SpecialUpgradeRequirements));
					}
					
					?>
					
				
				
			</tr>
			<tr>
				<td><label for="Size">Resulterande pengar</label></td>
				<td><input type="number" id="Money" name="Money" value="<?php echo $titledeedresult->Money; ?>" step = '1' size="50" style="text-align:right"> <?php echo $currency; ?></td>
			</tr>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om resultatet<br>för arrangörer</label></td>
				<td><textarea id="Notes" name="Notes" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($titledeedresult->Notes); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om lagfarten<br>för arrangörer</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" cols="100" maxlength="60000" ><?php echo htmlspecialchars($titledeed->OrganizerNotes); ?></textarea></td>
			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>