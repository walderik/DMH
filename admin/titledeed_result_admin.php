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
        <h1>Resultat för verksamheter <a href="commerce.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
        <p>Ikoner:<br>
        <i class='fa-solid fa-money-bill-wave'></i> - Kan inte säljas<br>
        <i class='fa-solid fa-house'></i> - Handelsstation
        </p>
       <?php
    
       $titledeed_array = Titledeed::allByCampaign($current_larp, true);
       $currency = $current_larp->getCampaign()->Currency;
        if (!empty($titledeed_array)) {
            $tableId = "titledeeds";
            echo "<table id='$tableId' class='data'>";
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
                "<th onclick='sortTable(1, \"$tableId\")'>Rapporterat<br>resultat</th>".
                "<th onclick='sortTable(2, \"$tableId\")'>Klarade behov</th>".
                "<th onclick='sortTable(3, \"$tableId\")'>Pengar<br>i resultat</th>".
                "<th onclick='sortTable(4, \"$tableId\")'>Ägare</th>".
                "</tr>\n";
            foreach ($titledeed_array as $titledeed) {
                if (!$titledeed->isInUse()) continue;
                $titledeedresult = $titledeed->getResult($current_larp);
                echo "<tr>\n";
                echo "<td><a href='titledeed_result_form.php?id=" . $titledeed->Id . "'>$titledeed->Name</a>";
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
                
                if (isset($titledeedresult)) {
                    echo "<td>";
                    echo showStatusIcon(true);
                    echo "</td>\n";
                    echo "<td>";
                    echo showStatusIcon($titledeedresult->NeedsMet);
                    echo "</td>\n";
                    echo "<td>";
                    echo $titledeedresult->Money;
                    echo "</td>\n";
                    
                } else {
                    echo "<td>".showStatusIcon(false)."</td>";
                    echo "<td></td>";
                    echo "<td></td>";
                }
                
                echo "<td>";
                
                echo "<a href='choose_group.php?operation=add_titledeed_owner_group&Id=$titledeed->Id'><i class='fa-solid fa-plus' title='Lägg till grupp'></i><i class='fa-solid fa-users' title='Lägg till grupp'></i></a><br>";
                $owner_groups = $titledeed->getGroupOwners();
                foreach ($owner_groups as $owner_group) {
                    echo $owner_group->getViewLink() . "<a href='titledeed_admin.php?operation=delete_owner_group&titledeeId=$titledeed->Id&groupId=$owner_group->Id'><i class='fa-solid fa-trash'></i></a><br>";
                    
                }
                
                echo "<a href='choose_role.php?operation=add_titledeed_owner_role&Id=$titledeed->Id'><i class='fa-solid fa-plus' title='Lägg till karaktär'></i><i class='fa-solid fa-user' title='Lägg till karaktär'></i></a><br>";
                
                $owner_roles = $titledeed->getRoleOwners();
                foreach ($owner_roles as $owner_role) {
                    echo $owner_role->getViewLink();
                    echo " <a href='titledeed_admin.php?operation=delete_owner_role&titledeeId=$titledeed->Id&roleId=$owner_role->Id'><i class='fa-solid fa-trash'></i></a><br>";
                    
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