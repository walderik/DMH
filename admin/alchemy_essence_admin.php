<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Alchemy_Essence::delete($_GET['Id']);
    }
}


include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Essenser <a href="alchemy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till magi"></i></a></h1>

            <a href="alchemy_essence_form.php?operation=insert"><i class="fa-solid fa-file-circle-plus"></i>Lägg till essens</a>  
       <?php
    
       $essences = Alchemy_Essence::allByCampaign($current_larp);
       if (!empty($essences)) {
           $tableId = "essences";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Element</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Motsatt essens</th>".
               "<th></th>";
           
           foreach ($essences as $essence) {
                echo "<tr>\n";
                echo "<td><a href ='alchemy_essence_form.php?operation=update&id=$essence->Id'>$essence->Name</a></td>\n";
                echo "<td>$essence->Element</td>\n";
                echo "<td>";
                if ($essence->hasOppositeEssence()) {
                    $opposite = $essence->getOppositeEssence();
                    echo $opposite->Name;
                }

                echo "</td>\n";
                echo "<td>";
                if ($essence->mayDelete()) {
                    echo "<a href='alchemy_essence_admin.php?operation=delete&Id=" . $essence->Id . "'><i class='fa-solid fa-trash'></i>";
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