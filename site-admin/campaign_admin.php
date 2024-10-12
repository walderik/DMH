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
    } elseif ($operation == "remove_campaign_organizer") {
        AccessControl::revokeCampaign($_POST['personId'], $_POST['campaignId']);
    } elseif ($operation == "add_campaign_organizer" && isset($_POST['person_id'])) {
        $person_id = $_POST['person_id'];
        if ($person_id == 0) {
            $error_message = "Du kan bara välja bland de föreslagna personerna.";
        } else {
            AccessControl::grantCampaign($person_id, $_POST['campaignId']);
        }
    }
}



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Campaign::delete($_GET['id']);
    }
}

include "navigation.php";


if (isset($error_message) && strlen($error_message)>0) {
    echo '<div class="error">'.$error_message.'</div>';
}
if (isset($message_message) && strlen($message_message)>0) {
    echo '<div class="message">'.$message_message.'</div>';
}

?>

    <div class="content">   
        <h1>Kampanjer</h1>
            <p>Alla arrangörer får tillgång till alla tidigare lajv i kampanjen eftersom det är en historia och då behöver man kunna se vad som har hänt.</p>
        
            <a href="campaign_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
        <?php
        
        $campaign_array = Campaign::all();
        $resultCheck = count($campaign_array);
        if ($resultCheck > 0) {
            echo "<table id='larp' class='data'>";
            echo "<tr><th>Namn</th><th>Förkortning</th><th>Icon</th><th>Hemsida</th><th>Epost</th><th>Bankkonto</th><th>Swishnummer</th><th>Minimiålder</th><th>Minimiålder<br>utan ansvarig vuxen</th><th>Lajv-<br>valuta</th><th></th><th></th></tr>\n";
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
                echo "<td>" . $campaign->SwishNumber . "</td>\n";
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
                $organizers = Person::getAllWithAccessToCampaign($campaign);
                if (count($organizers) == 0) echo "Ingen utsedd än<br>";
                foreach ($organizers as $organizer) {
                    echo "$organizer->Name ";
                    echo "<form method='post' style='display:inline-block'>";
                    echo "<input type='hidden' name='campaignId' value='$campaign->Id'>\n";
                    echo "<input type='hidden' name='personId' value='$organizer->Id'>\n";
                    echo "<input type='hidden' name='operation' value='remove_campaign_organizer'>\n";
                    echo " <button class='invisible' type ='submit'><i class='fa-solid fa-trash-can' title='Ta bort ur kampanjarrangörsgruppen'></i></button>\n";
                    echo "</form>\n";
                    echo "<br>";
                }
                echo "<form method='post'  autocomplete='off' style='display: inline;'>";
                echo "<input type='hidden' name='campaignId' value='$campaign->Id'>\n";
                echo "<input type='hidden' name='operation' value='add_campaign_organizer'>\n";
                echo "Lägg till kampanjarrangör ";
                autocomplete_person_id('40%', true); 
			    echo "</form>";		
                echo "</td>";
                echo "</tr>";
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