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
    $larp = LARP::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $larp = LARP::loadById($_GET['id']);
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $larp;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($larp->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $larp->Id;
                break;
            case "action":
                if (is_null($larp->Id)) {
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
    				<td><input type="text" id="Name" name="Name" value="<?php echo $larp->Name; ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="Abbreviation">Förkortning</label></td>
    				<td><input type="text" id="Abbreviation" name="Abbreviation" value="<?php echo $larp->Abbreviation; ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="TagLine">Tag line</label></td>
    				<td><input type="text" id="TagLine" name="TagLine" value="<?php echo $larp->TagLine; ?>"></td>
    			</tr>
    			<tr>
    				<td><label for="StartDate">Startdatum</label></td>
    				<td><input type="datetime-local" id="StartDate"
    					name="StartDate" value="<?php echo $larp->StartDate; ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="EndDate">Slutdatum</label></td>
    				<td><input type="datetime-local" id="EndDate"
    					name="EndDate" value="<?php echo $larp->EndDate; ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="MaxParticipants">Max antal deltagare</label></td>
    				<td><input type="number" id="MaxParticipants" name="MaxParticipants" value="<?php echo $larp->MaxParticipants; ?>"></td>
    			</tr>
    			<tr>
    				<td><label for="LatestRegistrationDate">Sista anmälningsdag</label></td>
    				<td><input type="date-local" id="LatestRegistrationDate"
    					name="LatestRegistrationDate" value="<?php echo $larp->LatestRegistrationDate; ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="StartTimeLARPTime">Start lajvtid</label></td>
    				<td><input type="datetime-local" id="StartTimeLARPTime"
    					name="StartTimeLARPTime" value="<?php echo $larp->StartTimeLARPTime; ?>" ></td>
    			</tr>
    			<tr>
    				<td><label for="EndTimeLARPTime">Slut lajvtid</label></td>
    				<td><input type="datetime-local" id="EndTimeLARPTime"
    					name="EndTimeLARPTime" value="<?php echo $larp->EndTimeLARPTime; ?>"></td>
    			</tr>
    		</table>
    
    		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
    	</form>
    	</div>
    </body>

</html>