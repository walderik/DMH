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

$url = $_SERVER['REQUEST_URI'];

if (str_contains($url, "/participant/")) $participant_selected = "selected='selected'";
elseif (str_contains($url, "/admin/")) $admin_selected = "selected='selected'";
elseif (str_contains($url, "/campaign/")) $campaign_selected = "selected='selected'";
elseif (str_contains($url, "/board/")) $board_selected = "selected='selected'";
elseif (str_contains($url, "/houses/")) $houses_selected = "selected='selected'";
elseif (str_contains($url, "/site-admin/")) $site_admin_selected = "selected='selected'";


if (AccessControl::isMoreThanParticipant($current_user, $current_larp)) {

	  
    echo "<select name='part' id='part' onchange='changePart()'>\n";
  	echo "<option value='../participant/' $participant_selected>Deltagare</option>\n";
    if (AccessControl::hasAccessLarp($current_user, $current_larp)) 
        echo "<option value='../admin/' $admin_selected>Arrangör</option>\n";
    if (AccessControl::hasAccessLarp($current_user, $current_larp))
        echo "<option value='../campaign/' $campaign_selected>Kampanj</option>\n";
    if (AccessControl::hasAccessOther($current_user->Id, AccessControl::BOARD)) 
        echo "<option value='../board/' $board_selected>Styrelse</option>\n";
    if (AccessControl::hasAccessOther($current_user->Id, AccessControl::HOUSES)) 
        echo "<option value='../houses/' $houses_selected>Hus & Läger</option>\n";
    if (AccessControl::hasAccessOther($current_user->Id, AccessControl::ADMIN)) 
        echo "<option value='../site-admin/' $site_admin_selected>OM Admin</option>\n";

	 echo "</select>\n";
 }
