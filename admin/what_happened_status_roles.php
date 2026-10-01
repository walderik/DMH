<?php
include_once 'header.php';
include 'navigation.php';


?>

<div class="content">
<h1>Vilka karaktärer har fyllt i "Vad hände"</h1>
Visar om en karaktär har fyllt i "Vad hände" eller inte. Ingen hänsyn tas till hur lång texten är.<br>
<h2>Huvudkaraktärer</h2>


<?php

$tabletxt = "<table class='small_data'>";
$tabletxt .= "<tr><th>Karaktär</th><th>Spelare</th><th>Status</th></tr>";
$roles = $current_larp->getAllMainRoles(false);
$personIdArr = array();

foreach ($roles as $role) {
    $tabletxt .= "<tr><td>".$role->getViewLink()."</td>";
    $tabletxt .= "<td>";
    $hasRegistered = $role->hasRegisteredWhatHappened($current_larp);
    $player = $role->getPerson();
    
    if (!is_null($player)) {
        $tabletxt .= $player->getViewLink();
        $tabletxt .= contactEmailIcon($player);
        if (!$hasRegistered) $personIdArr[] = $player->Id;
    }
    $tabletxt .= "</td>";
    $tabletxt .= "<td>".showStatusIcon($hasRegistered)."</td></tr>";
}

$tabletxt .= "</table>";

if (!empty($personIdArr)) echo contactSeveralEmailIcon('Skicka till spelarna som inte har fyllt i', $personIdArr, 'Deltagare', "Vad hände för din karaktär på $current_larp->Name");

echo $tabletxt;
?>

<?php 

$roles = $current_larp->getAllNotMainRoles(false);
if (!empty($roles)) {

?>

<h2>Sidokaraktärer</h2>
<table class="small_data">
<tr><th>Karaktär</th><th>Spelare</th><th>Status</th></tr>
<?php 
foreach ($roles as $role) {
    echo "<tr><td>".$role->getViewLink()."</td>";
    echo "<td>";
    $player = $role->getPerson();
    
    if (!is_null($player)) {
        echo $player->getViewLink();
        echo contactEmailIcon($player);
    }
    echo "</td>";
    echo "<td>".showStatusIcon($role->hasRegisteredWhatHappened($current_larp))."</td></tr>";
}
?>


</table>
<?php 
}
?>

