<?php
include_once 'header.php';
require_once $root . '/pdf/resource_pdf.php';

include 'navigation.php';
?>

    <div class="content">
        <h1>Handel</h1>
        <div class='linklist'>
        <a href="resource_admin.php">Resurser</a><br>
        <a href="titledeed_admin.php">Verksamheter</a><br>
        <a href="selection_data_admin.php?type=titledeedplace">Platser för verksamheter</a><br>
        <a href="titledeed_economy_overview.php">Översikt ekonomi för verksamheter</a><br>        
        <a href="titledeed_economy_DOH_rules.php">Regler för nivåer på verksamheter för DÖH</a><br>        
        <br>
        <a href="resource_titledeed_overview_normal.php">Resursfördelning - normala resurser</a><br>
        <a href="resource_titledeed_overview_rare.php">Resursfördelning - ovanliga resurser</a><br>
        <a href="resource_titledeed_overview_place.php">Resursöversikt - per plats</a><br>
        <br>
        <a href="commerce_roles.php">Karaktärer med handel</a><br>
        <a href="commerce_groups.php">Grupper med handel</a><br>
        <br>
        <a href="roles_money.php">Pengar till karaktärer i början på lajvet</a><br>
        <a href="groups_money.php">Pengar till grupper i början på lajvet</a><br>
        <br>

        <a href="logic/all_titledeeds_dmh_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera ägarbevis till verksamheterna (Maskinskrivna)</a><br>
        <a href="logic/all_titledeeds_doh_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera ägarbevis till verksamheterna (Kalligrafi / Runor)</a><br>
        <a href="logic/all_resources_pdf.php?type=<?php echo RESOURCE_PDF::Handwriting?>" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera resurskort till verksamheterna (Handskrift / Maskinskrift)</a><br> 
        <a href="logic/all_resources_pdf.php?type=<?php echo RESOURCE_PDF::Calligraphy?>" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera resurskort till verksamheterna (Kalligrafi)</a> <br>
        <br>

        <a href="reports/titledeeds_info_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Lista med verksamheter (för notarie)</a><br>  
        <a href="reports/titledeeds_info_pdf.php?all_info=1" target="_blank"><i class="fa-solid fa-file-pdf"></i>Lista med verksamheter (för handelsansvarig)</a><br><br>  
        <a href="reports/resources_info_pdf.php?all_info=1" target="_blank"><i class="fa-solid fa-file-pdf"></i>Prislista</a><br>  
        <br>
        <a href="titledeed_result_admin.php">Resultat efter lajvet</a><br>
        </div>

</body>
</html>