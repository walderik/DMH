<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $invoiceId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        
        exit;
    }
}

$invoice = Invoice::loadById($invoiceId);

if ($invoice->LARPId != $current_larp->Id) {
    header('Location: index.php'); // fel lajv
    exit;
}

include 'navigation.php';
?>


	<div class="content">
		<h1>Hantera betalning för faktura</h1>
		<form action="logic/invoice_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="invoice_payment">
    		<input type="hidden" id="InvoiceId" name="Id" value="<?php echo $invoice->Id; ?>">
		<table>
			<tr><td valign="top" class="header">Mottagare</td><td><?php echo nl2br(htmlspecialchars($invoice->Recipient));?></td></tr>
			<tr><td valign="top" class="header">Fakturatext</td><td><?php echo nl2br(htmlspecialchars($invoice->Matter));?></td></tr>
			<tr><td valign="top" class="header">Betalningsreferens</td><td><?php echo $invoice->PaymentReference;?></td></tr>
			<tr><td valign="top" class="header">Belopp att betala</td><td><?php echo $invoice->Amount();?> SEK</td></tr>
			<tr><td valign="top" class="header">Belopp betalat</td><td><input type="number" id="AmountPayed" name="AmountPayed" value="<?php echo $invoice->AmountPayed; ?>"  min="0" size="10" maxlength="250" required> SEK</td></tr>
			<tr><td valign="top" class="header">Betalat datum</td><td><input type="date" id="PayedDate" name="PayedDate" value="<?php echo $invoice->PayedDate; ?>"  size="15" maxlength="250" required>
			</td></tr>
		</table>		
			<input type="submit" value="Spara">
		</form>		    