<?php
include_once 'header.php';

include 'navigation.php';
?>
<div class="content">   
    <h1>PDF'er i intriger</h1>
    
   <?php 
   $intrigue_pdfs = Intrigue_Pdf::getAllByLarp($current_larp);
   
   $tableId = "pdfs";
   $colnum = 0;
   echo "<table id='$tableId' class='data'>";
   echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Fil</th>".
        "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Intrig</th>".
       "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Antal som känner till den</th>".
       "</tr>\n";
   foreach ($intrigue_pdfs as $intrigue_pdf)  {
       echo "<tr>";
       echo "<td>";
       echo "<a href='view_intrigue_pdf.php?id=$intrigue_pdf->Id' target='_blank'>$intrigue_pdf->Filename</a>";
       echo "</td>";
       echo "<td>";
       if (isset($intrigue_pdf->IntrigueId)) {
           $intrigue = Intrigue::loadById($intrigue_pdf->IntrigueId);
           echo "<a href='view_intrigue.php?Id=$intrigue->Id'>Intrig: $intrigue->Number. $intrigue->Name</a>";
           if (!$intrigue->isActive()) echo " (inte aktuell)";
           
       }
       echo "</td>";
       echo "<td>";
       echo count($intrigue_pdf->getAllKnownPdfs());
       echo "</td>";
       echo "</tr>";
   }
   echo "</table>";    
   
   
   
   
   
   
   
   ?>