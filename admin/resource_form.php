<?php
include_once 'header.php';


    $resource = Resource::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $resource = Resource::loadById($_GET['id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $resource;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($resource->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $resource->Id;
                break;
            case "action":
                if (is_null($resource->Id)) {
                    $output = "Lägg till";
                    break;
                }
                $output = "Uppdatera";
                break;
        }

        echo $output;
    }
    
    include 'navigation_subpage.php';
    
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> lagfart</h1>
	<form action="resource_admin.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo $resource->Name; ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="UnitSingular">Enhet singular</label></td>
				<td><input type="text" id="UnitSingular" name="UnitSingular"
					 value="<?php echo $resource->UnitSingular; ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="UnitPlural">Enhet plural</label></td>
				<td><input type="text" id="UnitPlural" name="UnitPlural"
					 value="<?php echo $resource->UnitPlural; ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="UnitSingular">Pris i Slow River</label></td>
				<td><input type="text" id="PriceSlowRiver" name="PriceSlowRiver"
					 value="<?php echo $resource->PriceSlowRiver; ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="PriceJunkCity">Pris i Junk City</label></td>
				<td><input type="text" id="PriceJunkCity" name="PriceJunkCity"
					 value="<?php echo $resource->PriceJunkCity; ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="IsRare">Ovanlig</label></td>
    			<td>
    				<input type="radio" id="IsRare_yes" name="IsRare" value="1" <?php if ($resource->IsRare == 1) echo 'checked="checked"'?>> 
        			<label for="IsRare_yes">Ja</label><br> 
        			<input type="radio" id="IsRare_no" name="IsRare" value="0" <?php if ($resource->IsRare == 0) echo 'checked="checked"'?>> 
        			<label for="IsRare_no">Nej</label>
    			</td>
			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>