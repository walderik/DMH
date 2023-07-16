<?php
include_once 'header.php';

    $account = Bookkeeping_Account::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $account = Bookkeeping_Account::loadById($_GET['id']);
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $account;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($account->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $account->Id;
                break;
            case "action":
                if (is_null($account->Id)) {
                    $output = "Lägg till";
                    break;
                }
                $output = "Uppdatera";
                break;
        }

        echo $output;
    }
    
    include "navigation.php";
    
    ?>
    
     
<style>

img {
  float: right;
}
</style>

    <div class="content"> 
    	<h1><?php echo default_value('action');?> konto</h1>
    	<form action="bookkeeping_account_admin.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
    		<table>
    			<tr>
    				<td><label for="Name">Namn</label></td>
    				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($account->Name); ?>" required></td>
    			</tr>
    			<tr>
    				<td><label for="Number">Nummer</label></td>
    				<td><input type="text" id="Number" name="Number" value="<?php echo htmlspecialchars($account->Number); ?>" required></td>
    			</tr>
    		</table>
     		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
    	</form>
    	</div>
    </body>

</html>