<?php
require 'header.php';

include "navigation.php";
?>
<h1>Behörigheter</h1>
<h2>Huvudarrangörer</h2>
<?php 
$campaigns = Campaign::all();
echo "<table class='data'>";
echo "<tr><th>Kampanj</th><th>Huvudarrangör</th>";
foreach($campaigns as $campaign) {
    echo "<tr>";
    echo "<td>$campaign->Name</td>";
    echo "<td>";
    $mainOrg = $campaign->getMainOrganizer();
    if (!empty($mainOrg)) echo $mainOrg->Name; 
    
    echo "</td>";
}
echo "</table>";
?>


<h2>Övriga behörigheter</h2>
<?php 
$usersWithPermissions = User::getAllWithOtherAccess();
if (!empty($usersWithPermissions)) {
    echo "<table class='data'>";
    echo "<tr><th>Person</th><th>Behörighet</th>";
    foreach($usersWithPermissions as $user) {
        echo "<tr>";
        echo "<td>$user->Name</td>";
        echo "<td>";
        $access = $user->getOtherAccess();
        $accessTexts = array();
        foreach ($access as $item) $accessTexts[] = AccessControl::ACCESS_TYPES[$item];
        echo implode(", ", $accessTexts);
        echo "</td>";
        echo "</tr>";
        
    }
    echo "</table>";
}

?>