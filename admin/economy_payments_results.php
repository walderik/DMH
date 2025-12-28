<?php
 include_once 'header.php';
 

 $csv = array();
 
 if (isset($_POST['file_format']) && isset($_FILES['csv'])) {
     $file_format = $_POST['file_format'];
     
     // check there are no errors
     if($_FILES['csv']['error'] == 0){
         //$name = $_FILES['csv']['name'];
         //$ext = strtolower(end(explode('.', $name)));
         $type = $_FILES['csv']['type'];
         $tmpName = $_FILES['csv']['tmp_name'];
         
         // check the file is a csv
         if($type === 'text/csv' || $type=="application/vnd.ms-excel"){
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
 }
 function convert( $str ) {
     return iconv( "Windows-1252", "UTF-8", $str );
 }
 
 
 function findPayment($paymentReference) {
     global $csv, $file_format;
     
     $paymentReference = mb_strtolower($paymentReference);
     foreach ($csv as $key => $paymentRow) {
         $message = "";
         if ($file_format == "swish") {
             if (count($paymentRow) >= 10) {
                 $message = trim(mb_strtolower($paymentRow[10]));
              }
         } elseif ($file_format == "transaction") {
             if (count($paymentRow) >= 8) {
                 $message = trim(mb_strtolower($paymentRow[8]));
             }
         }
         $match = false;

         if ($paymentReference == $message) $match = true;
         if (str_contains($message, $paymentReference." ")) $match = true;
         if (str_contains($message, $paymentReference.",")) $match = true;
         if (str_ends_with($message, $paymentReference)) $match = true;
         
         if ($match) {
             $row = $paymentRow;
             unset($csv[$key]);
             return $row;
         }
     }
 }
 $already_matched_payment_rows = array();
 
 include 'navigation.php';
?>

<style>
th {
  cursor: pointer;
}

</style>


<script src="../javascript/table_sort.js"></script>
<script src="../javascript/register_payment_ajax.js"></script>

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
    		      "<th onclick='sortTable(4, \"$tableId\")'>Betalnings-<br>referens</th>".
    		      "<th onclick='sortTable(5, \"$tableId\")'>Belopp</th>".
    		      "<th onclick='sortTable(6, \"$tableId\")' colspan='4'>Från banken</th>".
    		      "<th onclick='sortTable(11, \"$tableId\")' colspan='2'>Försenad betalning</th>".
    		      "</tr>\n";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        $paymentrow = findPayment($registration->PaymentReference);
    		        if ($registration->hasPayed() || $registration->isNotComing()) {
    		            if (!empty($paymentrow)) $already_matched_payment_rows[] = array_merge($paymentrow, array($person->Name, $registration->AmountToPay, $registration->PaymentReference));
    		            continue;
    		        }
    		        $amountToPay = $registration->AmountToPay;
    		        echo "<tr>\n";
    		        echo "<td>";
    		        echo $person->getViewLink(false);
    		        echo "</td>\n";
    		        echo "<td>";
    		        echo "<a href='edit_person.php?id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        echo "<td>";
    		        echo $person->Email;
    		        echo " ".contactEmailIcon($person)."</td>\n";
    		        echo "</td>\n";

		            echo "<td>" . $person->getAgeAtLarp($current_larp) . " år ";
    		            
    		        echo "<td>".$registration->PaymentReference .  "</td>\n";
    		        echo "<td>";
    		        if (is_null($amountToPay)) echo "<b>Betalning inte satt</b>";
                    else echo $amountToPay;
    		        echo "</td>\n";
    		        if (isset($paymentrow)) {
    		            if ($file_format=="swish") {
        		            $refence = $paymentrow[10];
        		            $amount = $paymentrow[12];
        		            $date = $paymentrow[4];
    		            } elseif ($file_format=="transaction") {
    		                $refence = $paymentrow[8];
    		                $amount = $paymentrow[10];
    		                $date = $paymentrow[6];
    		                
    		            }
    		            echo "<td>$refence</td><td>$amount</td><td>$date</td>";
    		            if ($amountToPay.".00" == $amount)
    		              echo "<td><button onclick='register_payment($registration->Id, $amount, \"$date\", this);'>Markera som betalad</button>";
		                else echo "<td>Summan stämmer inte</td>";
		                
    		        } else {
    		            echo "<td></td><td></td><td></td><td></td>";
    		        }
    		        echo "<td align='center'>";
    		        if (!$registration->hasPayed() && $registration->isPastPaymentDueDate()) echo showStatusIcon(false);
    		        "</td>";
    		        echo "<td><a href='person_payment.php?id=" . $person->Id . "'><i class='fa-solid fa-money-check-dollar'></i></a></td>\n";
    		        

    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		    		
    		    ?>

        <h2>Betalningar som inte kunde matchas</h2>
        <table class="small_data">
        <?php 
        if ($file_format == "swish") {
        
            foreach($csv as $rownum => $row) {
                if ($rownum == 0) {
                    echo "<tr>";
                    echo "<td colspan='5'>".trim($row[0])."</td>";
                    echo "</tr>";
                    
                } else {
                    echo "<tr>";
                    
                    foreach ($row as $key => $item) {
                        if (in_array($key, [1,2,3,5,6,7,13])) continue;
                        echo "<td>".trim($item)."</td>";
                    }
                    
                    echo "</tr>";
                }
            }
        } elseif ($file_format == "transaction") {
            foreach($csv as $rownum => $row) {
                if (isset($row) && is_array($row) && (count($row) > 9) && str_starts_with($row[9], "Swish")) continue;
                if ($rownum == 0) {
                    echo "<tr>";
                    echo "<td colspan='5'>".trim($row[0])."</td>";
                    echo "</tr>";
                    
                } else {
                    echo "<tr>";
                    foreach ($row as $key => $item) {
                        if (in_array($key, [1,2,3,4,5,7,11])) continue;
                        echo "<td>".trim($item)."</td>";
                    }
                    echo "</tr>";
                }
            }
            
        }
        
        
        ?>
        </table>
        
        <hr>
        <details><summary>Betalningar som matchar deltagare som är markerade som betalda</summary>
        

        <table class="small_data">
        <?php 
        if ($file_format == "swish") {
        
            foreach($already_matched_payment_rows as $rownum => $row) {
                echo "<tr>";
                
                foreach ($row as $key => $item) {
                    if (in_array($key, [1,2,3,5,6,7,13])) continue;
                    echo "<td>".trim($item)."</td>";
                }
                
                echo "</tr>";
                
            }
        } elseif ($file_format == "transaction") {
            foreach($already_matched_payment_rows as $rownum => $row) {
                //if (isset($row) && is_array($row) && (count($row) > 9) && str_starts_with($row[9], "Swish")) continue;
                echo "<tr>";
                foreach ($row as $key => $item) {
                    //if (in_array($key, [1,2,3,4,5,7,11])) continue;
                    echo "<td>".trim($item)."</td>";
                }
                echo "</tr>";
            }
            
        }
        ?>
        </table>
        </details>
	</div>
</body>

</html>
