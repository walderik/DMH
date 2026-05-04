<?php
require 'header.php';
$_SESSION['navigation'] = Navigation::OFFICIAL;
include "navigation.php";
?>
<h1>Funktionär på <?php echo $current_larp->Name?></h1>



<?php 
if (AccessControl::hasOfficialAccess($current_person, $current_larp, AccessControl::OFFICIAL_EXPENSES) || AccessControl::hasAccessLarp($current_person, $current_larp)) {
    echo "<h2>Utlägg</h2>";    

    echo "<a href='expense_form.php?operation=add_expense'><i class='fa-solid fa-file-circle-plus'></i> Ladda upp kvitto</a><br>";
    
    $bookkeepings = Bookkeeping::allForPerson($current_larp, $current_person);
    if (!empty($bookkeepings)) {
        echo "<h3>Mina utlägg</h3>";
        echo "<table id='bookkeeping' class='data'>";
        echo "<tr><th>Upplagd datum</th><th>Rubrik</th><th>Summa</th><th>Utbetald datum</th><th></th></tr>\n";
        foreach ($bookkeepings as $bookkeeping) {
            echo "<tr>\n";
            
            echo "<td>" . $bookkeeping->CreationDate . "</td>\n";
            echo "<td><a href='view_expense.php?id=$bookkeeping->Id'>" . $bookkeeping->Headline."</a>";
            if (!$bookkeeping->hasImage()) {
                echo " " . showStatusIcon(false);
            }
            
            if (empty($bookkeeping->AccountingDate)) echo " <a href='expense_form.php?operation=update_expense&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra utgift'></i></a>";

            echo "</td>\n";
            
            echo "<td class='amount'>" .number_format((float)abs($bookkeeping->Amount), 2, ',', '')."</td>\n";
            
            echo "<td>$bookkeeping->AccountingDate</td>";

            echo "<td>";
            if (empty($bookkeeping->AccountingDate)) echo "<a href='economy.php?operation=delete&id=$bookkeeping->Id'><i class='fa-solid fa-trash' title='Radera'></i></a>";
            echo "</td>";
            echo "</tr>\n";
        }
        echo "</table>";
    }

    
}


?>



