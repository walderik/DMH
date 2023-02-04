<?php

# En selector där man kan välja i en array
function selectionDropdownByArray(String $name_in, Array $selectionDatas, ?bool $multiple=false, ?bool $required=true, $selected=null) {
//     $name = ($multiple) ? (static::class . "Id[]") : static::class."Id";
    if (str_ends_with($name_in,"]")) {
        $name = ($multiple) ? ($name_in . "[]") : $name_in;
    }
    else {
        $name = ($multiple) ? ($name_in . "Id[]") : $name_in."Id";
    }
    
    
    # TODO Hantera required för checkboxes när det behövs - Det går med Javascripts   (behövs inte idag)
    //     https://tutorialdeep.com/knowhow/make-checkbox-field-required-form-html/
    //     https://stackoverflow.com/questions/11787665/making-sure-at-least-one-checkbox-is-checked
    //     Men enklast är nog att göra en kontroll när man sparar formuläret och ger ett felmeddelande om värdet saknas.
    $option = ($required) ? 'required' : '';
    $type = ($multiple) ? "checkbox" : "radio";
    
    echo "<div class='selectionDropdown'>\n";
    
    if (empty($selectionDatas)) {
        echo "</div>\n";
        return;
    }
    
    if (count($selectionDatas)==1){
        echo $selectionDatas[0]->Name . "<br>\n";
        echo "<input type='hidden' id='" .$name_in.$selectionDatas[0]->Id . "' name='" . $name . "' value=" .  $selectionDatas[0]->Id . ">";
        echo "</div>\n";
        return;
    }
    print_r($selected);
    echo "<br>";
    
    // lägg till tomt val om det inte är multiple eller required
    if (!$multiple && !$required){
        $empty_object = clone $selectionDatas[0];
        $empty_object->Id = "null";
        $empty_object->Name = "[ Ingen / Inget ]";
        array_unshift($selectionDatas , $empty_object);
    }
    
    foreach ($selectionDatas as $selectionData) {
        $row_option = $option;
        # Kolla om något är selected
        if(!$multiple) {
            if ((!is_null($selected) && $selected == $selectionData->Id) || (is_null($selected) && 'null' == $selectionData->Id))
                $row_option = $row_option.' checked="checked"';
        } else {
            if (!is_null($selected) && !empty($selected) && in_array($selectionData->Id, $selected))
                $row_option = $row_option.' checked="checked"';
        }
        
        echo "<input type='" . $type . "' id='" .$name_in.$selectionData->Id . "' name='" . $name . "' value='" . $selectionData->Id . "' " . $row_option . ">\n";
        echo "<label for='" .$name_in.$selectionData->Id . "'>" .  $selectionData->Name . "</label><br>\n";
    }
    echo "</div>\n";
}


function commaStringFromArrayObject($objectArray) {
    $output="";
    
    foreach($objectArray as $object) {
        $output += $object->Name . ", ";
    }
    if (strlen($output) > 2) {
        $output = substr($output, 0, -2);
    }
    return $output;
}


