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
            echo "<tr><th>Id</td><th>Till</th><th>Beskrivning</th><th>Belopp</th><th>Betalad</th><th>In-lajv<br>egenskaper</th><th>Innehavare</th><th>Bild</th><th>Används<br>i intrig</th><th></th></tr>\n";
            foreach ($invoice_array as $invoice) {
                echo "<tr>\n";
                echo "<td><a href='invoice_form.php?operation=update&id=$invoice->Id'>$invoice->Name</a></td>\n";
                echo "<td>" . $invoice->Description . "</td>\n";
                echo "<td>" . $invoice->Amount() . "</td>\n";
                echo "<td>";
                if (!$invoice->hasPayed() && $invoice->isPastDueDate()) {
                    showStatusIcon(false);
                    showStatusIcon(false);
                } else {
                    showStatusIcon($invoice->hasPayed());
                }
                echo "</td>\n";
                echo "<td>" . $owner . "<a href='prop_owner_form.php?id=" . $prop->Id . "'><i class='fa-solid fa-pen'></i></td>\n";

                
                echo "<td>";
                
                
                echo "<td>";
                if (empty($intrigues)) echo "<a href='prop_admin.php?operation=delete&id=" . $prop->Id . "'><i class='fa-solid fa-trash'></i>";
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