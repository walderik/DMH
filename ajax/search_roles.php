<?php
include_once 'header.php';
include_once '../includes/selection_data_control.php';


// get the parameters from URL
$operation = $_REQUEST["operation"];

if ($operation == "search") {
    $larpId = $_REQUEST["larpId"];
    $larp = LARP::loadById($larpId);
    $type = $_REQUEST["type"];
    $value = $_REQUEST["value"];
    
    $roles = Role::getAllWithTypeValue($larpId, $type, $value);
    $typeTexts = getAllTypesForRoles($larp);
    
    
    $valueText = call_user_func($type . '::loadById', $value)->Name;
    
    $resSearch = "Sökning på alla karaktärer med värde $valueText för ".$typeTexts[$type]."<br>";
    
    $emailArr = array();
    $resTable = "<table class='data'><tr><th>Namn</th><th>Yrke</th><th>Grupp</th>";
    
    if ($type == "Religion") $resTable .="<th>Kommentar till religion</th>";
    if ($type == "LarperType") $resTable .="<th>Kommentar till typ av lajvare</th>";
    if ($type == "IntrigueType") $resTable .="<th>Intrigidéer</th>";
    if ($type == "Race") $resTable .="<th>Kommentar till ras</th>";
    if ($type == "Ability") $resTable .="<th>Kommentar till förmåga</th>";
    if ($type == "Council") $resTable .="<th>Kommentar till byråd</th>";
    if ($type == "RoleFunction") $resTable .="<th>Kommentar till funktion</th>";
    
    $resTable .= "</tr>";

    foreach ($roles as $role) {
        $resTable .= "<tr><td>" . $role->getViewLink() . "</td><td>$role->Profession</td><td>";

        $group = $role->getGroup();
        if (isset($group)) $resTable .= $group->getViewLink();
        $resTable .= "</td>";
        
        if ($type == "Religion") $resTable .="<td>$role->Religion</td>";
        if ($type == "LarperType") $resTable .="<td>$role->TypeOfLarperComment</td>";
        if ($type == "IntrigueType") $resTable .="<td>$role->IntrigueSuggestions</td>";
        if ($type == "Race") $resTable .="<td>$role->RaceComment</td>";
        if ($type == "Ability") $resTable .="<td>$role->AbilityComment</td>";
        if ($type == "Council") $resTable .="<td>$role->Council</td>";
        if ($type == "RoleFunction") $resTable .="<td>$role->RoleFunctionComment</td>";
        $resTable .= "</tr>";
        $personIdArr[] = $role->getPerson()->Id;
    }
    $resTable .="</table>";
    
    $resMail = "";
    if (!empty($personIdArr)) $resMail = contactSeveralEmailIcon('Skicka till deltagarna med dessa roller', $personIdArr, $valueText, "Meddelande från $current_larp->Name")."<br>";
    
    
    echo $resSearch . $resMail. $resTable;
} elseif ($operation="values") {
    $larpId = $_REQUEST["larpId"];
    $type = $_REQUEST["type"];
    $data_array = call_user_func($type . '::allForLarp', $current_larp);
    
    $res = array();
    foreach ($data_array as $data) {
        $res[] = $data->Id.":".$data->Name;
    }
    
    echo implode(";",$res);
    
} else {
    echo "Okänd operation.";
}


