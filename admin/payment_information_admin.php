<?php
include_once 'header.php';
include 'navigation_subpage.php';
?>



<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $payment_information = PaymentInformation::newFromArray($_POST);
        $payment_information->create();
    } elseif ($operation == 'delete') {
        PaymentInformation::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $payment_information = PaymentInformation::newFromArray($_POST);
        $payment_information->update();
    } 
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        PaymentInformation::delete($_GET['id']);
    }
}

?>

    <div class="content">
        <h1>Avgift</h1>
        <p>Var noga när du sätter upp det här så att det inte blir några "hål" varken i ålder eller datum. <br><br>
        Börja med att köra vår <a href="payment_wizard_pg1.php">"wizard" <i class="fa-solid fa-wand-sparkles"></i></a> för att sätta upp inställningarna. </p>
  
         <?php
    
        $payment_array = PaymentInformation::allBySelectedLARP();
        if (!empty($payment_array)) {
            echo "<a href='payment_information_form.php?operation=new'><i class='fa-solid fa-file-circle-plus'></i>Lägg till</a>";
            
            echo "<table class='data'>";
            echo "<tr><th>Id</td><th>Från datum</th><th>Till datum</th><th>Från ålder</th><th>Till ålder</th><th>Pris</th><th></th><th></th></tr>\n";
            foreach ($payment_array as $payment) {
                echo "<tr>\n";
                echo "<td>" . $payment->Id . "</td>\n";
                echo "<td>" . $payment->FromDate . "</td>\n";
                echo "<td>" . $payment->ToDate . "</td>\n";
                echo "<td>" . $payment->FromAge . "</td>\n";
                echo "<td>" . $payment->ToAge . "</td>\n";
                echo "<td>" . $payment->Cost . "</td>\n";
                
                echo "<td>" . "<a href='payment_information_form.php?operation=update&id=" . $payment->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='payment_information_admin.php?operation=delete&id=" . $payment->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
 
        //echo PaymentInformation::errorReportBySelectedLARP();
        ?>
    </div>
	
</body>

</html>