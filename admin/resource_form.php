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
            $resource = Resource::loadById($_GET['Id']);            
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
    
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
    }
    else {
        $referer = "";
    }
    $referer = (isset($referer)) ? $referer : '../resource_admin.php';
    
    
    include 'navigation.php';
    
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> resurs <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<p>Ovanliga resurser har inget fast pris utan det beror på vilken köpare man hittar. Därför sätts deras priser alltid till 0.</p>
	<form action="logic/resource_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr>
				<td><label for="Name">Namn</label><br>i systemet</td>
				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($resource->Name); ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="UnitSingular">Enhet singular</label><br>för spelare</td>
				<td><input type="text" id="UnitSingular" name="UnitSingular"
					 value="<?php echo htmlspecialchars($resource->UnitSingular); ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="UnitPlural">Enhet plural</label><br>för spelare</td>
				<td><input type="text" id="UnitPlural" name="UnitPlural"
					 value="<?php echo htmlspecialchars($resource->UnitPlural); ?>" size="100" maxlength="250" required></td>
			</tr>			<tr>

				<td><label for="AmountPerWagon">Antal per vagn</label><br>Om man fyller en hel vagn med bara den här varan, hur många ryms det?</td>
				<td><input type="number" id="AmountPerWagon" name="AmountPerWagon" 
					 value="<?php echo htmlspecialchars($resource->AmountPerWagon); ?>" minvalue="0" required></td>
			</tr>
			
			<tr>

				<td><label for="UnitSingular">Pris</label></td>
				<td><input type="text" id="Price" name="Price"
					 value="<?php echo htmlspecialchars($resource->Price); ?>" size="100" maxlength="250" required></td>
			</tr>
			<tr>

				<td><label for="IsRare">Ovanlig</label><br>används vid förbättring<br>av verksamheter</td>
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