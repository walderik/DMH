<?php
include_once 'header.php';

    $invoice = Invoice::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $invoice = Invoice::loadById($_GET['id']);            
        } else {
        }
    }
    
    if ($invoice->LARPId != $current_larp->Id) {
        header('Location: index.php');
        exit;
    }
    
      
    function default_value($field) {
        GLOBAL $invoice;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($invoice->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $invoice->Id;
                break;
            case "action":
                if (is_null($invoice->Id)) {
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
    
    include 'navigation.php';
    ?>
    
<style>

img {
  float: right;
}
</style>


    <div class="content"> 
    <h1><?php echo default_value('action');?> faktura <a href="invoice_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
    
   
	<form action="logic/invoice_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		
		<table>
			<tr>
				<td><label for="Name">Mottagare</label></td>
				<td><input type="text" id="Recipient" name="Recipient"  maxlength="250" value='<?php echo htmlspecialchars($invoice->Recipient); ?>' required></td>

			</tr>
			<tr>
				<td><label for="Name">Adressering<br>inkludera mottagarens namn</label></td>
				<td><textarea type="text" id="RecipientAddress" name="RecipientAddress"  rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($invoice->RecipientAddress); ?></textarea></td>

			</tr>
			<tr>

				<td><label for="Description">Fakturatext</label></td>
				<td><textarea id="Matter" name="Matter" rows="4" cols="100" maxlength="60000" required><?php echo htmlspecialchars($invoice->Matter); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="StorageLocation">Betalningsdatum</label></td>
				<td><input type="date" id="DueDate" name="DueDate" value="<?php echo $invoice->DueDate ?>" required></td>
			</tr>
			<tr>

				<td><label for="StorageLocation">Betalningsreferens</label></td>
				<td>
				<?php 
				if ($operation=='update') {
				    echo "<input type='text' id='PaymentReference' name='PaymentReference' value='$invoice->PaymentReference' size='100' maxlength='250' required></td>";
				    
				} else {
				    echo "En unik referens skapas när man skapar fakturan. Den går senare att redigera.";
				}
				
				
				?>
			</tr>
			<tr>
				<td>
					<label for="Text">Kontaktperson</label>
				</td>
				<td>
					
					<?php 
					if ($operation=='update') {
					    if (isset($invoice->ContactPersonId)) {
					        $contactPerson = Person::loadById($invoice->ContactPersonId);
					        echo "<a href='view_person.php?id=$contactPerson->Id'>$contactPerson->Name</a>";
					    }
					    echo "<a class='no_underline' href='choose_persons.php?operation=invoice_contact&Id=$invoice->Id'><i class='fa-solid fa-pen' title='Välj kontaktperson'></i></a>";
					} else {
					  echo "<strong>När fakturan är skapad kan man lägga in kontaktperson.</strong>";
					}?>
					
				</td>
			</tr>
			<tr>
				<td>
					Fakturan gäller avgifter för
				</td>
				<td>
				<?php 
					if ($operation=='update') {
					    echo "<a class='no_underline' href='choose_persons.php?operation=invoice_add_concerns&Id=$invoice->Id'>".
                           "<i class='fa-solid fa-plus' title='Lägg till deltagare som fakturan gäller'></i>".
				           "<i class='fa-solid fa-user' title='Lägg till deltagare som fakturan gäller'></i></a>";
                        echo "<br>";
					    
					    
					    $concerns_array = $invoice->getConcerendRegistrations();
					    foreach ($concerns_array as $registration) {
					        $person = $registration->getPerson();
					        echo "$person->Name $registration->AmountToPay SEK";
					        echo "<a href='logic/invoice_save.php?operation=delete_concerns&Id=$invoice->Id&registrationId=$registration->Id'><i class='fa-solid fa-trash' title='Ta bort från fakturan'></i></a>";
                            echo "<br>";
					    }
					} else {
					  echo "<strong>När fakturan är skapad kan man lägga in vilka deltagares avgifter den rör.</strong>";
					}?>
				
				
				
				
					<?php 
					?>
				</td>
			</tr>
			
			
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>