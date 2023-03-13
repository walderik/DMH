<?php
include_once 'header.php';
include 'navigation_subpage.php';

?>

    
    <?php

    $payment = PaymentInformation::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $payment = PaymentInformation::loadById($_GET['id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $payment;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($payment->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $payment->Id;
                break;
            case "action":
                if (is_null($payment->Id)) {
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
    <h1><?php echo default_value('action');?> avgift</h1>
	<form action="payment_information_admin.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="LARPId" name="LARPId" value="<?php echo $current_larp->Id; ?>">
		<table>
			<tr>
				<td><label for="FromDate">Från och med datum</label>&nbsp;<font style="color:red">*</font></td>
				<td><input type="date" id="FromDate"
					name="FromDate" value="<?php echo $payment->FromDate; ?>" required></td>
			</tr>
			<tr>
				<td><label for="ToDate">Till och med datum</label>&nbsp;<font style="color:red">*</font></td>
				<td><input type="date" id="ToDate"
					name="ToDate" value="<?php echo $payment->ToDate; ?>" required></td>
			</tr>
			<tr>
				<td><label for="FromAge">Från och med ålder</label>&nbsp;<font style="color:red">*</font></td>
				<td><input type="text" id="FromAge" name="FromAge" value="<?php echo $payment->FromAge; ?>" required></td>
			</tr>
			<tr>
				<td><label for="ToAge">Till och med ålder</label>&nbsp;<font style="color:red">*</font></td>
				<td><input type="text" id="ToAge" name="ToAge" value="<?php echo $payment->ToAge; ?>" required></td>
			</tr>
			<tr>
				<td><label for="Cost">Avgift</label>&nbsp;<font style="color:red">*</font></td>
				<td><input type="text" id="Cost" name="Cost" value="<?php echo $payment->Cost; ?>" required></td>
			</tr>
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>