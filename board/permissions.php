<?php
require 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = "";
    if (isset($_POST['operation'])) $operation = $_POST['operation'];
    if ($operation == 'main_organizer') {
        $campaign = Campaign::loadById($_POST['CampaignId']);
        if ($_POST['MainOrganizerUserId'] == 'null') $campaign->MainOrganizerUserId=NULL;
        else $campaign->MainOrganizerUserId = $_POST['MainOrganizerUserId'];
        $campaign->update();
    } elseif ($operation = 'permission_user') {
        $userId = $_POST['UserId'];
        if (isset($_POST['Permission'])) $new_permissions = $_POST['Permission'];
        else $new_permissions = array();
        $all_permissions = AccessControl::ACCESS_TYPES;
        AccessControl::revokeAllOther($userId);
        foreach ($all_permissions as $key => $permission) {
            if (in_array($key, $new_permissions)) {
                AccessControl::grantOther($userId, $key);
            }
        }
    }
    header('Location: ' . 'permissions.php');
    exit;
    
}




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
    echo " <a href = 'change_main_organizer.php?CampaignId=$campaign->Id'><i class='fa-solid fa-pen' title='Byt huvudarrangör'></i></a>";
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
        echo " <a href = 'permission_user.php?UserId=$user->Id'><i class='fa-solid fa-pen' title='Ändra behörigheter'></i></a>";
        echo "</td>";
        echo "</tr>";
        
    }
    echo "</table>";
}
echo "<a href = 'permission_user.php'>Lägg till användare med behörighet</a>";

?>