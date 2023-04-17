<?php

# En selector där man kan välja i en array
function selectionByArray(String $name_in, Array $selectionDatas, ?bool $multiple=false, ?bool $required=true, $selected=null) {
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
    
    //Om det bara finns en och man måste välja så väljs den.
    if (count($selectionDatas)==1 && $required){
        $first_key = array_key_first($selectionDatas);
        echo $selectionDatas[$first_key]->Name . "<br>\n";
        echo "<input type='hidden' id='" .$name_in.$selectionDatas[$first_key]->Id . "' name='" . $name . "' value=" .  $selectionDatas[$first_key]->Id . ">";
        echo "</div>\n";
        return;
    }
    
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
        if($multiple) {
            if (!is_null($selected) && !empty($selected) && in_array($selectionData->Id, $selected))
                $row_option = $row_option.' checked="checked"';
        } else {    
            if ((!is_null($selected) && $selected == $selectionData->Id) || (is_null($selected) && 'null' == $selectionData->Id))
                $row_option = $row_option.' checked="checked"';
        }
        
        echo "<input type='" . $type . "' id='" .$name_in.$selectionData->Id . "' name='" . $name . "' value='" . $selectionData->Id . "' " . $row_option . ">\n";
        echo "<label for='" .$name_in.$selectionData->Id . "'>" .  $selectionData->Name . "</label><br>\n";
    }
    echo "</div>\n";
}




# En selector där man kan välja i en array
function selectionDropDownByArray(String $name, Array $selectionDatas, $selected=null) {
   echo "<select name='$name' id='$name'>\n";
    foreach ($selectionDatas as $selectionData) {
     echo "   <option value='$selectionData->Id'>$selectionData->Name</option>\n";
    }
    echo "</select>\n";
    
}

# Tar en array av object och gör en komma-separerad lista av deras namn
function commaStringFromArrayObject($objectArray) {
    $output="";
    
    foreach($objectArray as $object) {
        $output = $output . $object->Name . ", ";
    }
    if (strlen($output) > 2) {
        $output = substr($output, 0, -2);
    }
    return $output;
}

function ja_nej($val) {
    if ($val == 1 or $val==true) return "Ja";
    if ($val == 0 or $val==false) return "Nej";
}


function showStatusIcon($value, ?string $fix_url = NULL) {
    if ($value == true or $value == 1) {
        return '<img src="../images/ok-icon.png" alt="OK" width="20" height="20">';
    }
    if ($value == false or $value == 0) {
        if (isset($fix_url) && !is_null($fix_url)) {
            return "<a href='$fix_url'><img src='../images/alert-icon.png' alt='Varning' width='20' height='20'></a>";
        }
        return '<img src="../images/alert-icon.png" alt="Varning" width="20" height="20">';
    }
}

function contactEmailIcon($name,$email) {
    return "<a href='contact_email.php?email=$email&name=$name'><i class='fa-solid fa-envelope-open-text'></i></a>";
}

function startsWithNumber($string) {
    return strlen($string) > 0 && ctype_digit(substr($string, 0, 1));
}

# En HTML-selector för fonter
function fontDropDown(String $name, ?String $selected=null) {
    echo "<div class='fontDropdown'>\n";
    echo "<select name='$name' id='$name' required>";
    foreach (OurFonts::fontArray() as $font) {
        //$row_option = 'required';
        $row_option = "";
        # Kolla om något är selected
        if (!is_null($selected) && $selected == $font) {
            //$row_option = $row_option.' checked="checked"';
            $row_option="selected";
        }
        

        echo "<option value='$font' $row_option>$font</option>\n"; 
        //echo "<input type='radio' id='" .$font . "' name='" . $name . "' value='" . $font . "' " . $row_option . ">\n";
        //echo "<label for='" .$font . "'>" .  $font . "</label><br>\n";
    }
    echo "</select>\n";
    echo "</div>\n";
}
