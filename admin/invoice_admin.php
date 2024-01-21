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
            <a href="invoice_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a><br>
        
       <?php
    
       $invoice_array = Invoice::allBySelectedLARP($current_larp);
       if (!empty($invoice_array)) {
            echo "<table class='data'>";
            echo "<tr><th>Nummer</th><th>Mottagare</th><th>Kontaktperson<br>Skicka</th><th>Belopp</th><th>Referens</th><th>Betalad</th><th></th><th></th></tr>\n";
            foreach ($invoice_array as $invoice) {
                echo "<tr>\n";
                echo "<td>$invoice->Number</td>\n";
                echo "<td>$invoice->Name</td>\n";
                echo "<td>";
                if ($invoice->hasContactPerson()) {
                    $name = $invoice->getContactPerson()->Name;
                    echo $name;
                    if (!$invoice->isSent()) {
                        echo "<form action='logic/invoice_save.php' class='fabutton' method='post'>\n".
                        "<input type=hidden name='operation' value='send_invoice'>\n".
                        "<input type=hidden name='Id' value='$invoice->Id'>\n".
                        "<button type='submit'>".
                        " Skicka fakturan".
                        "</button>\n".
                        "</form>\n";
                    }
                } else {
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
                $amount = $invoice->Amount();
                if (isset($amount)) echo $invoice->Amount() . " SEK";
                echo "</td>\n";
                echo "<td>" . $invoice->PaymentReference . " SEK</td>\n";
                echo "<td>";
                if ($invoice->isSent()) {
                    if (!$invoice->hasPayed() && $invoice->isPastDueDate()) {
                        echo showStatusIcon(false);
                        echo showStatusIcon(false);
                    } else {
                        echo showStatusIcon($invoice->hasPayed());
                    }
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