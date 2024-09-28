<?php
require 'header.php';

include "navigation.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
     $operation = "";
    if (isset($_POST['operation'])) $operation = $_POST['operation'];
    if ($operation == 'main_organizer') {
        $campaign = Campaign::loadById($_POST['CampaignId']);
        $person_id = $_POST['person_id'];
        if ($person_id == 0) {
            $error_message = "Du kan bara välja bland de föreslagna personerna.";
        } else {
            $campaign->MainOrganizerPersonId = $person_id;
            $campaign->update();
            if (!AccessControl::hasAccessCampaign(User::loadById($campaign->MainOrganizerPersonId)->Id, $campaign->Id)) AccessControl::grantCampaign($campaign->MainOrganizerPersonId, $campaign->Id);
        }
    } elseif ($operation == 'remove_main_organizer') {
        $campaign = Campaign::loadById($_POST['CampaignId']);
        $campaign->MainOrganizerPersonId = null;
        $campaign->update();
    } elseif ($operation = 'permission_person') {
        $person_id = $_POST['person_id'];
        if ($person_id == 0) {
            $error_message = "Du kan bara välja bland de föreslagna personerna.";
        } else {
                
            if (isset($_POST['Permission'])) $new_permissions = $_POST['Permission'];
            else $new_permissions = array();
            $all_permissions = AccessControl::ACCESS_TYPES;
            AccessControl::revokeAllOther($person_id);
            foreach ($all_permissions as $key => $permission) {
                if (in_array($key, $new_permissions)) {
                    AccessControl::grantOther($person_id, $key);
                }
            }
        }
    }
    if (!isset($error_message)) {
        header('Location: ' . 'permissions.php');
        exit;
    }
    
}






if (isset($error_message) && strlen($error_message)>0) {
    echo '<div class="error">'.$error_message.'</div>';
}
if (isset($message_message) && strlen($message_message)>0) {
    echo '<div class="message">'.$message_message.'</div>';
}


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
    if (!empty($mainOrg)) {
        echo $mainOrg->Name; 
        $text = "Byt huvudarrangör";
    } else {
        $text = "Sätt huvudarrangör";
    }
    echo " <a href = 'change_main_organizer.php?CampaignId=$campaign->Id'><i class='fa-solid fa-pen' title='$text'></i></a> ";
    if (!empty($mainOrg)) {
        echo "<form action='permissions.php' method='post' style='display:inline-block'>";
        echo "<input type='hidden' name='CampaignId' value='$campaign->Id'>\n";
        echo "<input type='hidden' name='operation' value='remove_main_organizer'>\n";
        echo " <button class='invisible' type ='submit'><i class='fa-solid fa-trash-can' title='Ta bort huvudarrangör'></i></button>\n";
        echo "</form>\n";
     }

    echo "</td>";
}
echo "</table>";
?>


<h2>Övriga behörigheter</h2>
<?php 
$personsWithPermissions = Person::getAllWithOtherAccess();
if (!empty($personsWithPermissions)) {
    echo "<table class='data'>";
    echo "<tr><th>Person</th><th>Behörighet</th>";
    foreach($personsWithPermissions as $person) {
        echo "<tr>";
        echo "<td>$person->Name</td>";
        echo "<td>";
        $access = $person->getOtherAccess();
        $accessTexts = array();
        foreach ($access as $item) $accessTexts[] = AccessControl::ACCESS_TYPES[$item];
        echo implode(", ", $accessTexts);
        echo " <a href = 'permission_person.php?PersonId=$person->Id'><i class='fa-solid fa-pen' title='Ändra behörigheter'></i></a>";
        echo "</td>";
        echo "</tr>";
        
    }
    echo "</table>";
}
echo "<a href = 'permission_person.php'>Lägg till personer med behörighet</a>";

?>