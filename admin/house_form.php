<?php
include_once 'header.php';

?>
        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>
    
    <?php
    $house = House::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $house = House::loadById($_GET['id']);
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $house;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($house->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $house->Id;
                break;
            case "action":
                if (is_null($house->Id)) {
                    $output = "Lägg till";
                    break;
                }
                $output = "Uppdatera";
                break;
        }

        echo $output;
    }
    
    
    ?>
    <div class="content"> 
    	<h1><?php echo default_value('action');?> lajv</h1>
    	<form action="larp_admin.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
    		<table>
    			<tr>
    				<td><label for="Name">Namn</label></td>
    				<td><input type="text" id="Name" name="Name" value="<?php echo $house->Name; ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="NumberOfBeds">Anta lsovplatser</label></td>
    				<td><input type="text" id="NumberOfBeds" name="NumberOfBeds" value="<?php echo $house->NumberOfBeds; ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="Information">Tag line</label></td>
    				<td><input type="text" id="Information" name="Information" value="<?php echo $house->Information; ?>"></td>
    			</tr>
    		</table>
    
    		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
    	</form>
    	</div>
    </body>

</html>