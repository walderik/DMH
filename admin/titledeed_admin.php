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

    <div class="content">
        <h1>Verksamheter <a href="commerce.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
        <p>Verksamheter går bara att ta bort om det inte finns några ägare eller inställningar för produktion/behov. 
        Detta för att man inte ska råka ta bort verksamheter som är i spel av misstag.</p>
        <p>Ikoner:<br>
        <i class='fa-solid fa-money-bill-wave'></i> - Kan inte säljas<br>
        <i class='fa-solid fa-house'></i> - Handelsstation
        </p>
        <p>
        <a href="titledeed_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a><br>
        <a href="logic/all_titledeeds_dmh_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera ägarbevis till verksamheterna (Maskinskrivna)</a><br>
        <a href="logic/all_titledeeds_doh_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera ägarbevis till verksamheterna (Kalligrafi / Runor)</a><br>
        <a href="logic/all_resources_pdf.php?type=<?php echo RESOURCE_PDF::Handwriting?>" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera resurskort till verksamheterna (Handskrift / Maskinskrift)</a><br> 
        <a href="logic/all_resources_pdf.php?type=<?php echo RESOURCE_PDF::Calligraphy?>" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera resurskort till verksamheterna (Kalligrafi)</a> <br>
        <br>
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
                "<th onclick='sortTable(2, \"$tableId\")'>Plats</th>".
                "<th onclick='sortTable(3, \"$tableId\")'>Ägare</th>".
                "<th onclick='sortTable(4, \"$tableId\")'>I spel</th>".
                "<th onclick='sortTable(5, \"$tableId\")'>Normal<br>Tillgång/Behov</th>".
                "<th onclick='sortTable(6, \"$tableId\")'>Nu<br>Tillgång/Behov</th>".
                "<th onclick='sortTable(7, \"$tableId\")'>Resultat</th>".
                "<th></th></tr>\n";
            foreach ($titledeed_array as $titledeed) {
                echo "<tr>\n";
                echo "<td><a href='titledeed_form.php?operation=update&id=" . $titledeed->Id . "'>$titledeed->Name</a>";
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
                if (isset($titledeed->TitledeedPlaceId)) {
                    echo $titledeed->getTitledeedPlaceName();
                    if (!empty($titledeed->Location)) echo "<br>($titledeed->Location)";
                } else echo $titledeed->Location;
                echo "</td>\n";
                
                echo "<td>";
                echo "<a href='choose_group.php?operation=add_titledeed_owner_group&Id=$titledeed->Id'><i class='fa-solid fa-plus' title='Lägg till grupp'></i><i class='fa-solid fa-users' title='Lägg till grupp'></i></a><br>";
                $owner_groups = $titledeed->getGroupOwners();
                foreach ($owner_groups as $owner_group) {
                    echo "<a href='../admin/view_group.php?id=$owner_group->Id'>$owner_group->Name</a>  <a href='titledeed_admin.php?operation=delete_owner_group&titledeeId=$titledeed->Id&groupId=$owner_group->Id'><i class='fa-solid fa-trash'></i></a><br>";
                    
                }
                
                echo "<a href='choose_role.php?operation=add_titledeed_owner_role&Id=$titledeed->Id'><i class='fa-solid fa-plus' title='Lägg till karaktär'></i><i class='fa-solid fa-user' title='Lägg till karaktär'></i></a><br>";
                
                $owner_roles = $titledeed->getRoleOwners();
                foreach ($owner_roles as $owner_role) {
                    echo "<a href='../admin/view_role.php?id=$owner_role->Id'>$owner_role->Name</a>  <a href='titledeed_admin.php?operation=delete_owner_role&titledeeId=$titledeed->Id&roleId=$owner_role->Id'><i class='fa-solid fa-trash'></i></a><br>";
                    
                }
                echo "</td>\n";
 
                echo "<td>";
                echo showStatusIcon($titledeed->isInUse());
                
                echo "</td>";
                echo "<td>";
                $produces_normally = $titledeed->ProducesNormally();
                if (!empty($produces_normally)) echo "Tillgångar: ". commaStringFromArrayObject($produces_normally) . "<br>\n";
                $requires_normally = $titledeed->RequiresNormally();
                if (!empty($requires_normally)) echo "Behöver: " . commaStringFromArrayObject($requires_normally)."\n";
                echo "</td>\n";
                echo "<td>";
                echo "<a href='resource_titledeed_form.php?Id=$titledeed->Id'><i class='fa-solid fa-pen' title='Ändra'></i></a><br>";
                
                echo "Tillgångar: ";
                echo $titledeed->ProducesString()."<br>";
                
                echo "Behöver: ";
                echo $titledeed->RequiresString();
                echo "<br>";
                echo "För uppgradering: ";
                echo $titledeed->RequiresForUpgradeString();
                echo "</td>\n";
                echo "</td>";
                echo "<td>".$titledeed->calculateResult()." $currency</td>";
                
                echo "<td>";
                if ($titledeed->mayRemove())
                    echo "<a href='titledeed_admin.php?operation=delete&id=" . $titledeed->Id . "'><i class='fa-solid fa-trash'></i>";
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