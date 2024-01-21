<?php
include_once 'header.php';


    $titledeed = Titledeed::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $titledeed = Titledeed::loadById($_GET['id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $titledeed;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($titledeed->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $titledeed->Id;
                break;
            case "action":
                if (is_null($titledeed->Id)) {
                    $output = "Lägg till";
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
    
    $resources = Resource::allNormalByCampaign($current_larp);
    
    
    include 'navigation.php';
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> lagfart <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="logic/titledeed_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($titledeed->Name); ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>
				<td><label for="Type">Typ</label> <small>(vad är det för något)</small></td>
				<td><input type="text" id="Type" name="Type" value="<?php echo htmlspecialchars($titledeed->Type); ?>" size="100" maxlength="250"></td>
			</tr>
			<tr>
				<td><label for="Size">Storlek</label></td>
				<td><input type="text" id="Size" name="Size" value="<?php echo htmlspecialchars($titledeed->Size); ?>" size="100" maxlength="250"></td>
			</tr>
			<tr>

				<td><label for="Location">Plats fritext</label></td>
				<td><input type="text" id="Location" name="Location"
					 value="<?php echo htmlspecialchars($titledeed->Location); ?>" size="100" maxlength="250" required></td>
			</tr>
			<?php if (TitledeedPlace::isInUse($current_larp)) {?>
			<tr>
				<td><label for="Location">Plats</label></td>
				<td>
	                <?php TitledeedPlace::selectionDropdown($current_larp, false,true, $titledeed->TitledeedPlaceId); ?>
				</td>
			</tr>
			<?php } ?>	

			<tr>

				<td><label for="Tradeable">Kan säljas</label></td>
    			<td>
    				<input type="radio" id="Tradeable_yes" name="Tradeable" value="1" <?php if ($titledeed->Tradeable == 1) echo 'checked="checked"'?>> 
        			<label for="Tradeable_yes">Ja</label><br> 
        			<input type="radio" id="Tradeable_no" name="Tradeable" value="0" <?php if ($titledeed->Tradeable == 0) echo 'checked="checked"'?>> 
        			<label for="Tradeable_no">Nej</label>
    			</td>
			</tr>
			</tr>
			<tr>

				<td><label for="IsTradingPost">Handelsstation</label></td>
    			<td>
    				<input type="radio" id="IsTradingPost_yes" name="IsTradingPost" value="1" <?php if ($titledeed->IsTradingPost == 1) echo 'checked="checked"'?>> 
        			<label for="IsTradingPost_yes">Ja</label><br> 
        			<input type="radio" id="IsTradingPost_no" name="IsTradingPost" value="0" <?php if ($titledeed->IsTradingPost == 0) echo 'checked="checked"'?>> 
        			<label for="IsTradingPost_no">Nej</label>
    			</td>
			</tr>
			<tr>

				<td><label for="Produces">Producerar (normalt)</label></td>
				<td><?php selectionByArray('Produces', $resources, true, false, $titledeed->getSelectedProducesResourcesIds()) ?>
				</td>
			</tr>
			<tr>

				<td><label for="Requires">Behöver (normalt)</label></td>
				<td><?php selectionByArray('Requires', $resources, true, false, $titledeed->getSelectedRequiresResourcesIds()) ?>
				</td>
			</tr>
			<tr>

				<td><label for="IsTradingPost">Är i spel</label></td>
    			<td>
    				<input type="radio" id="IsInUse_yes" name="IsInUse" value="1" <?php if ($titledeed->IsInUse()) echo 'checked="checked"'?>> 
        			<label for="IsInUse_yes">Ja</label><br> 
        			<input type="radio" id="IsInUse_no" name="IsInUse" value="0" <?php if (!$titledeed->isInUse()) echo 'checked="checked"'?>> 
        			<label for="IsInUse_no">Nej</label>
    			</td>
			</tr>
			<tr>
				<td><label for="PublicNotes">Anteckingar om lagfarten<br>som visas på lagfarten</label></td>
				<td><textarea id="PublicNotes" name="PublicNotes" rows="4" cols="100" maxlength="60000" ><?php echo nl2br(htmlspecialchars($titledeed->PublicNotes)); ?></textarea></td>
			</tr>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om lagfarten<br>för arrangörer</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" cols="100" maxlength="60000" ><?php echo nl2br(htmlspecialchars($titledeed->OrganizerNotes)); ?></textarea></td>
			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>