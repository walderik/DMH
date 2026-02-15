<?php 
global $root;

include_once $root . '/includes/navigation_control.php';
?>

<script>
function changePart() {
  	var part_selector = document.getElementById("part");
	if (part_selector.value.length != 0) {
		window.location.href = part_selector.value;
	}
}
</script>

<?php
$participant_selected = "";
$admin_selected = "";
$campaign_selected = "";
$board_selected = "";
$houses_selected = "";
$site_admin_selected = "";
$checkin_selected = "";

if (!isset($_SESSION['navigation'])) $_SESSION['navigation'] = Navigation::PARTICIPANT;
switch ($_SESSION['navigation']) {
    case Navigation::PARTICIPANT: {
        $participant_selected = "selected='selected'";
        break;
    }
    case Navigation::LARP: {
        $admin_selected = "selected='selected'";
        break;
    }
    case Navigation::CAMPAIGN: {
        $campaign_selected = "selected='selected'";
        break;
    }
    case Navigation::BOARD: {
        $board_selected = "selected='selected'";
        break;
    }
    case Navigation::HOUSES: {
        $houses_selected = "selected='selected'";
        break;
    }
    case Navigation::OM_ADMIN: {
        $site_admin_selected = "selected='selected'";
        break;
    }
    case Navigation::CHECKIN: {
        $checkin_selected = "selected='selected'";
        break;
    }
}


if (isset($current_larp) && isset($current_person) && AccessControl::isMoreThanParticipant($current_person, $current_larp)) {

	  
    echo "<select name='part' id='part' onchange='changePart()'>\n";
  	echo "<option value='../participant/' $participant_selected>Deltagare</option>\n";
  	if (AccessControl::hasAccessLarp($current_person, $current_larp) || (AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)))  
    echo "<option value='../admin/' $admin_selected>Arrangör</option>\n";
    if (AccessControl::hasAccessCheckin($current_person, $current_larp) || (AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)))
        echo "<option value='../checkin/' $checkin_selected>In / Utcheckning</option>\n";
        if (AccessControl::hasAccessCampaign($current_person, $current_larp->CampaignId) || (AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)))
        echo "<option value='../campaign/' $campaign_selected>Kampanj</option>\n";
        if (AccessControl::hasAccessOther($current_person, AccessControl::BOARD) || (AccessControl::hasAccessOther($current_person, AccessControl::ADMIN))) 
        echo "<option value='../board/' $board_selected>Styrelse</option>\n";
        if (AccessControl::hasAccessOther($current_person, AccessControl::HOUSES) || (AccessControl::hasAccessOther($current_person, AccessControl::ADMIN))) 
        echo "<option value='../houses/' $houses_selected>Hus & Läger</option>\n";
        if (AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)) 
        echo "<option value='../site-admin/' $site_admin_selected>OM Admin</option>\n";

	 echo "</select>\n";
 }
