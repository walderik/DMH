<?php
include_once 'header.php';

include 'navigation.php';
?>

<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>

    <div class="content">
        <h1>Översikt över verksamheternas ekonomi <a href="commerce.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
        <p>Tabellen visar enbart de verksamheter som är i spel.</p>
        <p>Ikoner:<br>
        <i class='fa-solid fa-money-bill-wave'></i> - Kan inte säljas<br>
        <i class='fa-solid fa-house'></i> - Handelsstation
        </p>
        <p>
        <a href="resource_titledeed_overview_normal.php">Översikt - normala resurser</a> &nbsp; 
        <a href="resource_titledeed_overview_rare.php">Översikt - ovanliga resurser</a>
        
        </p>
       <?php
    
       $titledeed_array = Titledeed::allByCampaign($current_larp, true);
       $currency = $current_larp->getCampaign()->Currency;
        if (!empty($titledeed_array)) {
            $tableId = "titledeeds";
            echo "<table id='$tableId' class='data'>";
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
                "<th onclick='sortTable(1, \"$tableId\")'>Nivå</th>".
                "<th onclick='sortTable(2, \"$tableId\")'>Tillgångar<br>$currency</th>".
                "<th onclick='sortTable(3, \"$tableId\")'>Behöver<br>$currency</th>".
                "<th onclick='sortTable(4, \"$tableId\")'>Uppgradering<br>$currency</th>".
                "</tr>\n";
            foreach ($titledeed_array as $titledeed) {
                if ($titledeed->isInUse()) {
                    echo "<tr>\n";
                    
                    echo "<td>";
                    echo "<a href='resource_titledeed_form.php?Id=$titledeed->Id'>$titledeed->Name</a><br>";
                    if ($titledeed->Tradeable == 0) {
                        echo " <i class='fa-solid fa-money-bill-wave'></i>";
                    }
                    if ($titledeed->IsTradingPost == 1) {
                        echo " <i class='fa-solid fa-house'></i>";
                    }
                    echo "<td>$titledeed->Size</td>\n";
                    
                    echo "<td>" . $titledeed->calculateProduces() ." $currency</td>";
                    echo "<td>" . $titledeed->calculateProduces() ." $currency</td>";
                    echo "<td>" . $titledeed->calculateUpgrade() ." $currency</td>";
                    echo "</tr>\n";
                }
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