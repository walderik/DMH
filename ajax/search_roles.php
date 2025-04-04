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
    $tableId = "roles";
    $colnum = 0;
    $resTable = "<table id='$tableId'  class='data'><tr>".
        "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
        "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Yrke</th>".
        "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Grupp</th>";
    
    if ($type == "Religion") $resTable .="<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Kommentar till religion</th>";
    if ($type == "LarperType") $resTable .="<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Kommentar till typ av lajvare</th>";
    if ($type == "IntrigueType") $resTable .="<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Intrigidéer</th>";
    if ($type == "Race") $resTable .="<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Kommentar till ras</th>";
    if ($type == "Ability") $resTable .="<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Kommentar till förmåga</th>";
    if ($type == "RoleFunction") $resTable .="<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Kommentar till funktion</th>";
    
    $resTable .= "</tr>";

    foreach ($roles as $role) {
        $resTable .= "<tr><td>" . $role->getViewLink();
        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
        if (!empty($larp_role) && $larp_role->IsMainRole!=1) $resTable .= " (sidokaraktär)";
        $resTable .= "</td><td>$role->Profession</td><td>";

        $group = $role->getGroup();
        if (isset($group)) $resTable .= $group->getViewLink();
        $resTable .= "</td>";
        
        if ($type == "Religion") $resTable .="<td>$role->Religion</td>";
        if ($type == "LarperType") $resTable .="<td>$role->TypeOfLarperComment</td>";
        if ($type == "IntrigueType") $resTable .="<td>$role->IntrigueSuggestions</td>";
        if ($type == "Race") $resTable .="<td>$role->RaceComment</td>";
        if ($type == "Ability") $resTable .="<td>$role->AbilityComment</td>";
        if ($type == "RoleFunction") $resTable .="<td>$role->RoleFunctionComment</td>";
        $resTable .= "</tr>";
        $person = $role->getPerson();
        if (!is_null($person)) $personIdArr[] = $person->Id;
    }
    $resTable .="</table>";
    
    $resMail = "";
    if (!empty($personIdArr)) $resMail = contactSeveralEmailIcon('Skicka till deltagarna med dessa roller', $personIdArr, $valueText, "Meddelande från $current_larp->Name")."<br>";
    
    
    $resSubdivision = "Lägg till alla karaktärerna i grupperingen <form action='subdivision_form.php' method='post' style='display: inline-block'>";
    $resSubdivision .= "<input type='hidden' name='operation' value='add_subdivision_member'>";

    foreach ($roles as $role) {
        $resSubdivision .= "<input type='hidden' name='RoleId[]' value=$role->Id>";
    }
    
    
    $subdivisions = Subdivision::allByCampaign($current_larp);
    $resSubdivision .= "<select name='id' id='id'>";
    foreach ($subdivisions as $subdivision) {
        $resSubdivision .= "<option value='$subdivision->Id'>$subdivision->Name</option>";
    }
    $resSubdivision .= "</select>";
    
    $resSubdivision .= " <input type ='submit' value='Lägg till'>";
    $resSubdivision .= "</form>";

    echo $resSearch . $resMail . $resTable . $resSubdivision;
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


