<?php
 include_once 'header.php';
 

 $csv = array();
 
 // check there are no errors
 if($_FILES['csv']['error'] == 0){
     //$name = $_FILES['csv']['name'];
     //$ext = strtolower(end(explode('.', $name)));
     $type = $_FILES['csv']['type'];
     $tmpName = $_FILES['csv']['tmp_name'];
     
     // check the file is a csv
     if($type === 'text/csv'){
         if(($handle = fopen($tmpName, 'r')) !== FALSE) {
             // necessary if a large csv file
             set_time_limit(0);
             
             $row = 0;
             
             while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
                 // number of fields in the csv
                 $col_count = count($data);
                 $data = array_map( "convert", $data );
                 for($col=0;$col<=($col_count-1);$col++) {
                     // get the values from the csv
                     $csv[$row][$col] = $data[$col];
                 }
                 
                 
                 
                 // inc the row
                 $row++;
             }
             fclose($handle);
         }
     }
 }
 
 function convert( $str ) {
     return iconv( "Windows-1252", "UTF-8", $str );
 }
 
 
 function findPayment($paymentReference) {
     global $csv;
     
     $paymentReference = mb_strtolower($paymentReference);
     foreach ($csv as $key => $paymentRow) {
         if (count($paymentRow) >= 12) {
             if (str_contains(trim(mb_strtolower($paymentRow[10])),$paymentReference)) {
                 
                 $row = $paymentRow;
                 unset($csv[$key]);
                 return $row;
             }
         }
     }
 }
 
 
 include 'navigation.php';
?>

<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>

    <div class="content">   
        <h1>Deltagare som kommer och som inte har betalat</h1>
        Genom att klicka på rubrikerna i tabellen kan du sortera tabellen. Klicka en gång till för att få omvänd ordning.
        
        <br>
     		<?php 
     		$persons = Person::getAllRegistered($current_larp, true);
     		

    		    $tableId = "participants";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		      "<th></th>".
    		      "<th onclick='sortTable(2, \"$tableId\")'>Epost</th>".
    		      "<th onclick='sortTableNumbers(3, \"$tableId\")'>Ålder<br>på lajvet</th>".
    		      "<th onclick='sortTable(6, \"$tableId\")'>Betalnings-<br>referens</th>".
    		      "<th onclick='sortTable(6, \"$tableId\")'>Belopp</th>".
    		      "<th onclick='sortTable(6, \"$tableId\")' colspan='3'>Från Swish</th>".
    		      "<th onclick='sortTable(7, \"$tableId\")' colspan='2'>Försenad betalning</th>".
    		      "</tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        $paymentrow = findPayment($registration->PaymentReference);
    		        if ($registration->hasPayed()) continue;
    		        echo "<tr>\n";
    		        echo "<td>";
    		        echo "<a href='view_person.php?id=$person->Id'>";
    		        
    		        echo $person->Name;
    		        echo "</a>";
    		        echo "</td>\n";
    		        echo "<td>";
    		        echo "<a href='edit_person.php?id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        echo "<td>";
    		        echo $person->Email;
    		        echo " ".contactEmailIcon($person->Name,$person->Email)."</td>\n";
    		        echo "</td>\n";

		            echo "<td>" . $person->getAgeAtLarp($current_larp) . " år ";
    		            
    		        echo "<td>".$registration->PaymentReference .  "</td>\n";
    		        echo "<td>".$registration->AmountToPay .  "</td>\n";
    		        if (isset($paymentrow)) {
    		            echo "<td>$paymentrow[10]</td>";
    		            echo "<td>$paymentrow[12]</td>";
    		            echo "<td>$paymentrow[9]</td>";
    		        } else {
    		            echo "<td></td><td></td><td></td>";
    		        }
    		        echo "<td align='center'>";
    		        if (!$registration->hasPayed() && $registration->isPastPaymentDueDate()) echo showStatusIcon(false);
    		        "</td>";
    		        echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-money-check-dollar'></i></a></td>\n";
    		        
    		        echo "<td>";
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		    		
    		    ?>

        <h2>Betalningar som inte kunde matchas</h2>
        <table class="small_data">
        <?php 
        foreach($csv as $rownum => $row) {
            if ($rownum == 0) {
                echo "<tr>";
                echo "<td colspan='5'>".trim($row[0])."</td>";
                echo "</tr>";
                
            } else {
                echo "<tr>";
                
                foreach ($row as $key => $item) {
                    if (in_array($key, [1,2,4,5,6,7,13])) continue;
                    echo "<td>".trim($item)."</td>";
                }
                
                echo "</tr>";
            }
        }
        
        
        ?>
        </table>
	</div>
</body>

</html>
