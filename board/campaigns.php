<?php
require 'header.php';

include "navigation.php";
?>
<h1>Kampanjer</h1>

<?php 
$campaigns = Campaign::all();


foreach($campaigns as $campaign) {
    echo "<h2>$campaign->Name</h2>\n";
    echo "<table class='small_data'>";
    echo "<tr>";
    echo "<td>Huvudarrangör</td>";
    echo "<td>";
    $mainOrg = $campaign->getMainOrganizer();
    if (!empty($mainOrg)) echo $mainOrg->Name;
    else echo "Saknas";
    echo "</td>\n";
    echo "</tr>";

    echo "<tr>";
    echo "<td>Arrangörsgrupp</td>";
    echo "<td>";
 
    $organizers = Person::getAllWithAccessToCampaign($campaign);
    if (count($organizers) == 0) echo "Ingen utsedd än<br>";
    foreach ($organizers as $organizer) {
        echo "$organizer->Name ";
        echo "<br>";
    }
    
    echo "</td>\n";
    echo "</tr>";
    
    
    echo "<tr>";
    echo "<td>Hemsida</td>";
    echo "<td>" . $campaign->Homepage . "</td>\n";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Epost</td>";
    echo "<td>" . $campaign->Email . "</td>\n";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Bankkonto</td>";
    echo "<td>" . $campaign->Bankaccount . "</td>\n";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Swish</td>";
    echo "<td>" . $campaign->SwishNumber . "</td>\n";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Minimiålder</td>";
    echo "<td>" . $campaign->MinimumAge . "</td>\n";
    echo "</tr>";
    echo "<tr>";
    echo "<td>Minimiålder<br>utan ansvarig vuxen</td>";
    echo "<td>" . $campaign->MinimumAgeWithoutGuardian . "</td>\n";
    echo "</tr>";
    echo "<tr>";
    echo "</table>";
    
    
}