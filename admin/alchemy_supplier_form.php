<?php
include_once 'header.php';


$supplier = Alchemy_Supplier::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $supplier = Alchemy_Supplier::loadById($_GET['Id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $supplier;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($supplier->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $supplier->Id;
                break;
            case "action":
                if (is_null($supplier->Id)) {
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
    $referer = (isset($referer)) ? $referer : '../alchemy_supplier_admin.php';
    

    $role = $supplier->getRole();

    include 'navigation.php';
    
    ?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> löjverist <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form action="logic/view_alchemy_supplier_logic.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><?php if (isset($role)) echo htmlspecialchars($role->Name); ?></td>
			</tr>
			<tr>
				<td><label for="Workshop">Workshop datum</label></td>
				<td>
					<input type="date" id="Workshop" name="Workshop" value="<?php echo $supplier->Workshop; ?>"  size="15" maxlength="250">
				</td>
			</tr>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om löjveristen</label><br>(Visas bara för arrangörer)</td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
						cols="100"><?php echo nl2br(htmlspecialchars($supplier->OrganizerNotes)); ?></textarea></td>
			</tr>
 
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>