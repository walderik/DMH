<?php
include_once 'header.php';
include 'navigation.php';


?>

<div class="content">
<h1>Vilka grupper har fyllt i "Vad hände"</h1>
Visar om en grupp har fyllt i "Vad hände" eller inte. Ingen hänsyn tas till hur lång texten är.<br>

<?php 

$tabletxt = "<table class='small_data'>";
$tabletxt .= "<tr><th>Grupp</th><th>Ansvarig</th><th>Status</th></tr>";


$groups = Group::getAllRegistered($current_larp);
$personIdArr = array();

foreach ($groups as $group) {
    $tabletxt .= "<tr><td>".$group->getViewLink()."</td>";
    $tabletxt .= "<td>";
    $groupleader = $group->getPerson();
    $hasRegistered = $group->hasRegisteredWhatHappened($current_larp);
    
    if (!is_null($groupleader)) {
        $tabletxt .= $groupleader->getViewLink();
        $tabletxt .= contactEmailIcon($groupleader);
        if (!$hasRegistered) $personIdArr[] = $groupleader->Id;
    }
    $tabletxt .= "</td>";
    $tabletxt .= "<td>".showStatusIcon($hasRegistered)."</td></tr>";
}
$tabletxt .= "</table>";

if (!empty($personIdArr)) echo contactSeveralEmailIcon('Skicka till gruppledarna som inte har fyllt i', $personIdArr, 'Gruppledare', "Vad hände för din grupp på $current_larp->Name");

echo $tabletxt;
?>



