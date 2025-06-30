<?php
include_once 'header.php';
$bookkeeping = Bookkeeping::newWithDefault();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "add_expense";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if (isset($_GET['id'])) {
        $bookkeeping = Bookkeeping::loadById($_GET['id']);
    }
}

function default_value($field) {
    GLOBAL $bookkeeping, $operation;
    $output = "";
    
    switch ($field) {
        case "id":
            if (empty($bookkeeping->Id)) {
                $output = "";
                break;
            }
            $output = $bookkeeping->Id;
            break;
        case "action":
            if (empty($bookkeeping->Id)) {
                $output = "Lägg till";
                break;
            }
            $output = "Uppdatera";
            break;
        case "sort":
            if ($operation == "add_expense" || $operation == "update_expense") {
                $output = "utgift";
                break;
            }
            $output = "inkomst";
            break;
        case "who":
            if ($operation == "add_expense" || $operation == "update_expense") {
                $output = "Till vem?";
                break;
            }
            $output = "Från vem?";
            break;
    }
    return $output;
}
        
    
include 'navigation.php';

$booking_accounts = Bookkeeping_Account::allActive($current_larp);
if (empty($booking_accounts)) $error_message = "Bokföringskonton saknas. Be någon kampanjarrangör lägga upp konton.";
?>
    


    <div class="content"> 
    <h1><?php echo default_value('action');?> <?php echo default_value('sort');?> <a href="economy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
 
 	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }?>
	  <?php if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }?>
    

	<form action="economy.php" method="post" enctype="multipart/form-data">
		<input type="hidden" id="operation" name="operation" value="<?php echo $operation?>"> 
		<input type="hidden" id="Photographer" name="Photographer" value="Kvitto"> 
		<input type="hidden" id="id" name="id" value="<?php echo default_value('id');?>"> 
		
 		
		<table>
			<tr>
				<td><label for="Headline">Rubrik</label></td>
				<td><input type="text" id="Headline" name="Headline" value="<?php echo $bookkeeping->Headline ?>" size="100" maxlength="250" required></td>

			</tr>
			<tr>

				<td><label for="Text">Beskrivning</label></td>
				<td><textarea id="Text" name="Text" rows="4" cols="100"><?php echo htmlspecialchars($bookkeeping->Text)?></textarea></td>
			</tr>
			<tr>

				<td><label for="BookkeepingAccountId">Konto</label></td>
				
				<td><?php  
				    if (!empty($booking_accounts)) selectionDropDownByArray("BookkeepingAccountId", $booking_accounts, true, $bookkeeping->BookkeepingAccountId);
				    else echo "Bokföringskonton saknas.";
				    ?></td>
			</tr>
			<tr>
				<td><label for="Who"><?php echo default_value('who');?></label></td>
				<td><input type="text" id="Who" name="Who" value="<?php echo $bookkeeping->Who ?>" size="100" maxlength="250" ></td>

			</tr>
			<tr>
				<td><label for="Amount">Summa</label></td>
				<?php $value = abs($bookkeeping->Amount);
				if ($value == 0) $value = "";
				?>
				<td><input style="width: 100px;" type="number" id="Amount" name="Amount" step='0.01' value='<?php echo $value;?>' min="0" size="100" maxlength="250" style="text-align:right"> kr</td>

			</tr>
			<tr>
				<td><label for="CreationDate">Upplagd datum</label></td>
				<td><input type="date" id="CreationDate"
    					name="CreationDate" value="<?php echo $bookkeeping->CreationDate ?>" size="50" required></td>
			</tr>
			<tr>
				<td><label for="AccountingDate">Bokföringsdatum</label></td>
				<td><input type="date" id="AccountingDate"
    					name="AccountingDate" value="<?php echo $bookkeeping->AccountingDate ?>" size="50"></td>
			</tr>
			<?php if ((default_value('sort')=="utgift") && !$bookkeeping->hasImage()) {?>
			<tr>
				<td><label for="upload">Kvitto</label></td>
				<td><input type="file" name="upload"> (Enbart pdf, png, jpg och gif)</td>
			</tr>
			<?php } ?>
			<tr>
			<td><label for="PersonId">Ansvarig</label></td>
			<td><?php 
    			if (empty($bookkeeping->PersonId)) {
    			    $bookkeeping->PersonId = $current_person->Id;
    			}
			
			     $organizers = Person::getAllWithAccessToLarp($current_larp);
			     selectionDropDownByArray('PersonId', $organizers, true, $bookkeeping->PersonId) ?></td>

			</tr>

			

		</table>
          	<br><br>
		
		<input id="submit_button" type="submit" value="<?php echo default_value('action');?> <?php echo default_value('sort');?>" <?php if (empty($booking_accounts)) echo " disabled";?>>
	</form>
	</div>
    </body>

</html>