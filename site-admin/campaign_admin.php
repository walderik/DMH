<?php
 include_once 'header.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $campaign = Campaign::newFromArray($_POST);
        $campaign->create();
    } elseif ($operation == 'delete') {
        Campaign::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $campaign=Campaign::loadById($_POST['Id']);
        $campaign->setValuesByArray($_POST);
        $campaign->update();
    } 
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Campaign::delete($_GET['id']);
    }
}

include "navigation.php";
?>

    <div class="content">   
        <h1>Kampanjer</h1>
            <a href="campaign_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
        <?php
        
        $campaign_array = Campaign::all();
        $resultCheck = count($campaign_array);
        if ($resultCheck > 0) {
            echo "<table id='larp' class='data'>";
            echo "<tr><th>Namn</th><th>Förkortning</th><th>Icon</th><th>Hemsida</th><th>Epost</th><th>Bankkonto</th><th>Minimiålder</th><th>Minimiålder<br>utan ansvarig vuxen</th><th>Lajv-<br>valuta</th><th></th><th></th></tr>\n";
            foreach ($campaign_array as $campaign) {
                echo "<tr>\n";
        
                //echo "<td>" . $campaign->Id . "</td>\n";
                echo "<td>" . $campaign->Name . "</td>\n";
                echo "<td>" . $campaign->Abbreviation . "</td>\n";
                //echo "<td>" . $campaign->Description . "</td>\n";
                echo "<td><img src='../images/$campaign->Icon' width='30' height='30'/><br>$campaign->Icon</td>\n";
                echo "<td>" . $campaign->Homepage . "</td>\n";
                echo "<td>" . $campaign->Email . "</td>\n";
                echo "<td>" . $campaign->Bankaccount . "</td>\n";
                echo "<td>" . $campaign->MinimumAge . "</td>\n";
                echo "<td>" . $campaign->MinimumAgeWithoutGuardian . "</td>\n";
                echo "<td>" . $campaign->Currency . "</td>\n";
                
                echo "<td>" . "<a href='campaign_form.php?operation=update&id=" . $campaign->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                if (!$campaign->hasLarps()) {
                    echo "<td>" . "<a href='campaign_admin.php?operation=delete&id=" . $campaign->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                }
                else {
                    echo "<td></td>";
                }
                echo "</tr>\n";
                
                echo "<tr>";
                echo "<td colspan='10'>";
                echo "Arrangörsbehörighet:<br>";
                $organizers = User::getAllWithAccessToCampaign($campaign);
                if (count($organizers) == 0) echo "Ingen utsedd än<br>";
                foreach ($organizers as $organizer) {
                    echo "$organizer->Name ";
                    echo "<a href='logic/remove_organizer.php?campaignId=$campaign->Id&userId=$organizer->Id' onclick=\"return confirm('Är du säker på att du vill ta bort $organizer->Name från arrangörsgruppen?');\">";
                    echo "<i class='fa-solid fa-trash-can'></i></a><br>";
                }
                echo "<a href='choose_users.php?campaignId=$campaign->Id&operation=organizer'>Lägg till arrangör</a>";
                echo "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        else {
            echo "<p>Inga registrarade ännu</p>";
        }
        ?>
        
	</div>
</body>

</html>