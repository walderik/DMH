<?php
 include_once 'header.php';
 
 if (!AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) {
     exit;
 }
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $larp = LARP::newFromArray($_POST);
        $larp->CampaignId = $current_larp->CampaignId;
        $larp->create();
    } elseif ($operation == 'delete') {
        LARP::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $larp=LARP::loadById($_POST['Id']);
        $larp->setValuesByArray($_POST);
        $larp->update();
    } 
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        LARP::delete($_GET['id']);
    }
}

include 'navigation.php';
?>

    <div class="content">   
        <h1>Lajv</h1>
            <a href="larp_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
        <?php
        
        $larp_array = LARP::allByCampaign($current_larp->CampaignId);
        $resultCheck = count($larp_array);
        if ($resultCheck > 0) {
            echo "<table id='larp' class='data'>";
            echo "<tr><th>Namn</th><th>Startdatum</th><th>Slutdatum</th><th>Max deltagare</th>".
            "<th>Sista anmälningsdag</th><th>Start lajvtid</th><th>Slut lajvtid</th><th>Prefix på<br>betalningsreferens</th><th>Antal dagar<br>för betalning</th><th></th><th></th></tr>\n";
            foreach ($larp_array as $larp) {
                echo "<tr>\n";
                //echo "<td>" . $larp->Id . "</td>\n";
                echo "<td>" . $larp->Name . "</td>\n";
                //echo "<td>" . $larp->getCampaign()->Name . "</td>\n";
                //echo "<td>" . $larp->TagLine . "</td>\n";
                echo "<td>" . $larp->StartDate . "</td>\n";
                echo "<td>" . $larp->EndDate . "</td>\n";
                echo "<td>" . $larp->MaxParticipants . "</td>\n";
                echo "<td>" . $larp->LatestRegistrationDate . "</td>\n";
                echo "<td>" . $larp->StartTimeLARPTime . "</td>\n";
                echo "<td>" . $larp->EndTimeLARPTime . "</td>\n";
                echo "<td>" . $larp->PaymentReferencePrefix . "</td>\n";
                echo "<td>" . $larp->NetDays . "</td>\n";
                
                echo "<td>" . "<a href='larp_form.php?operation=update&id=" . $larp->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                if (!$larp->hasRegistrations()) {
                    echo "<td>" . "<a href='larp_admin.php?operation=delete&id=" . $larp->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                }
                else {
                    echo "<td></td>";
                }
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