<?php
include_once 'header.php';
require_once $root . '/pdf/resource_pdf.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
        if ($operation == 'delete') {
            Titledeed::delete($_GET['id']);
        }
        elseif ($operation == 'delete_owner_role') {
            $titledeeId=$_GET['titledeeId'];
            $roleId=$_GET['roleId'];
            $titledeed = Titledeed::loadById($titledeeId);
            $titledeed->deleteRoleOwner($roleId);
        }
        elseif ($operation == 'delete_owner_group') {
            $titledeeId=$_GET['titledeeId'];
            $groupId=$_GET['groupId'];
            $titledeed = Titledeed::loadById($titledeeId);
            $titledeed->deleteGroupOwner($groupId);
        }
        header('Location: titledeed_admin.php');
        exit;
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
<script src="../javascript/show_hide_rows.js"></script>

    <div class="content">
        <h1>Utbetalningar för verksamheter <a href="commerce.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
        		    <?php 
	        echo "<p>Verksamheterna är filtrerade på om de är i spel eller inte.<br>";
	        echo '<button id="btn_show" onclick="show_hide();">Visa alla</button>';
	        echo "</p>";
		    ?>
       <?php
    
       $titledeed_array = Titledeed::allByCampaign($current_larp, true);
       $currency = $current_larp->getCampaign()->Currency;
        if (!empty($titledeed_array)) {
            $tableId = "titledeeds";
            echo "<table id='$tableId' class='data'>";
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
                "<th onclick='sortTable(1, \"$tableId\")'>Nivå</th>".
                "<th onclick='sortTable(2, \"$tableId\")'>Utdelning</th>".
                "<th onclick='sortTable(3, \"$tableId\")'>I spel</th>".
                "</tr>\n";
            foreach ($titledeed_array as $titledeed) {
                if ($titledeed->isInUse()) echo "<tr>\n";
                else echo "<tr class='show_hide hidden'>\n";
                echo "<td><a href='view_titledeed.php?id=$titledeed->Id'>$titledeed->Name</a> <a href='titledeed_form.php?operation=update&id=$titledeed->Id'><i class='fa-solid fa-pen'></i></a>";
                if ($titledeed->Tradeable == 0) {
                    echo " <i class='fa-solid fa-money-bill-wave'></i>";
                }
                if ($titledeed->IsTradingPost == 1) {
                    echo " <i class='fa-solid fa-house'></i>";
                }
                echo "<br><br><small>";
                if (!empty($titledeed->Type)) echo "$titledeed->Type ";
                if (!empty($titledeed->Size)) echo "($titledeed->Size)";
                "</small></td>\n";
                echo "<td>$titledeed->Level</td>\n";
                
                echo "<td>";
                echo $titledeed->Dividend;
                echo "</td>\n";
                
 
                echo "<td>";
                echo showStatusIcon($titledeed->isInUse());
                
                echo "</td>";
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