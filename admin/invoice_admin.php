<?php
include_once 'header.php';





if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Invoice::delete($_GET['id']);
    }
}


include 'navigation.php';
?>

    <div class="content">
        <h1>Fakturor</h1>
        <p>Man kan lägga upp en faktura och redigera den så mycket man vill fram tills dess den är skickad. <br>
        När den är skickad, eller markerad som skickad går den inte längre att redigera utan bara markera som betalad.<br>
        När den är makerad som betalad kommer det att finnas ett kvitto på den.</p>
        
            <a href="invoice_form.php?operation=new&InvoiceType=<?php echo Invoice::FEE_INVOICE?>"><i class="fa-solid fa-file-circle-plus"></i>Ny faktura för deltagaravgifter</a><br>
            <a href="invoice_form.php?operation=new&InvoiceType=<?php echo Invoice::NORMAL_INVOICE?>"><i class="fa-solid fa-file-circle-plus"></i>Ny faktura på fast summa</a><br>
        
       <?php
    
       $invoice_array = Invoice::allBySelectedLARP($current_larp);
       if (!empty($invoice_array)) {   
            echo "<table class='data'>";
            echo "<tr><th>Nummer</th><th>Mottagare</th><th>Kontaktperson<br>Skicka</th><th>Belopp</th><th>Gäller</th><th>Referens</th><th>Betalad</th><th>Faktura<br>pdf</th><th></th></tr>\n";
            foreach ($invoice_array as $invoice) {
                $amount = $invoice->Amount();
                echo "<tr>\n";
                echo "<td>$invoice->Number</td>\n";
                echo "<td>$invoice->Recipient</td>\n";
                echo "<td>";
                if ($invoice->hasContactPerson()) {
                    $name = $invoice->getContactPerson()->Name;
                    echo $name;
                    if (!$invoice->isSent() && isset($amount)) {
                        echo "<form action='logic/invoice_save.php' class='fabutton' method='post'>\n".
                        "<input type=hidden name='operation' value='send_invoice'>\n".
                        "<input type=hidden name='Id' value='$invoice->Id'>\n".
                        "<button type='submit'>".
                        " Skicka fakturan".
                        "</button>\n".
                        "</form>\n";
                    }
                } elseif (isset($amount)) {
                    if (!$invoice->isSent()) {
                        echo "<form action='logic/invoice_save.php' class='fabutton' method='post'>\n".
                            "<input type=hidden name='operation' value='mark_invoice_sent'>\n".
                            "<input type=hidden name='Id' value='$invoice->Id'>\n".
                            "<button type='submit' >".
                            "Markera fakturan som skickad".
                            "</button>\n".
                            "</form>\n";
                    }
                    
                }
                echo "</td>\n";
                echo "<td>";

                if (isset($amount)) echo $amount . " SEK";
                echo "</td>\n";
                
                echo "<td>";
                $concerns_array = $invoice->getConcerendRegistrations();
                if (empty($concerns_array)) echo $invoice->Matter;
                else {
                    echo "Avgifter för:<br>";
                    foreach ($concerns_array as $registration) {
                        $person = $registration->getPerson();
                        echo "<a href=view_person.php?id=$person->Id'>$person->Name</a> $registration->AmountToPay SEK<br>";
                    }
                }
                echo "</td>";
                
                
                echo "<td>" . $invoice->PaymentReference . "</td>\n";
                echo "<td>";
                if ($invoice->isSent()) {
                    echo "<a href='invoice_payment.php?id=$invoice->Id'>";
                    if (!$invoice->isPayed() && $invoice->isPastDueDate()) {
                        echo showStatusIcon(false);
                        echo showStatusIcon(false);
                    } else {
                        echo showStatusIcon($invoice->isPayed());
                    }
                    echo "</a>";
                } 
                if($invoice->isPayed()) {
                    echo " <a href='economy_receipt_pdf.php?invoiceId=$invoice->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa kvitto'></i></a>";
                }
                echo "</td>\n";
                
                echo "<td>";
                echo " <a href='invoice_pdf.php?invoiceId=$invoice->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa faktura'></i></a>";
                echo "</td>\n";

                
                echo "<td>";
                if (!$invoice->isSent()) {
                    echo "<a href='invoice_form.php?operation=update&id=$invoice->Id'><i class='fa-solid fa-pen'></i></a>";
                    echo " ";
                    echo "<a href='invoice_admin.php?operation=delete&id=$invoice->Id'><i class='fa-solid fa-trash'></i></a>";
                }
                echo "</td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        else {
            echo "<p>Inga registrerade ännu</p>";
        }
        ?>
    </div>
	
</body>

</html>