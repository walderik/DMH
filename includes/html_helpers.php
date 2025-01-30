<?php

# En selector där man kan välja objekt i en array
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
    
    if (empty($selectionDatas)) {
        return;
    }
    
    echo "<table class='selectionDropdown'>\n";
    
    //Om det bara finns en och man måste välja så väljs den.
    if (count($selectionDatas)==1 && $required){
        $first_key = array_key_first($selectionDatas);
        echo "<tr><td>";
        echo htmlspecialchars($selectionDatas[$first_key]->Name) . "<br>\n";
        echo "<input type='hidden' id='" .$name_in.$selectionDatas[$first_key]->Id . "' name='" . $name . "' value=" .  $selectionDatas[$first_key]->Id . ">";
        echo "</td>";
        if (($name_in!='Group') && isset($selectionDatas[$first_key]->Description)) echo "<td>".nl2br(htmlspecialchars($selectionDatas[$first_key]->Description))."</td>";
        echo "</tr>";
        echo "</table>\n";
        return;
    }
    
    // lägg till tomt val om det inte är multiple eller required
    if (!$multiple && !$required){
        $empty_object = clone $selectionDatas[0];
        $empty_object->Id = "null";
        $empty_object->Name = "[ Ingen / Inget ]";
        if (isset($empty_object->Description)) $empty_object->Description = "";
        array_unshift($selectionDatas , $empty_object);
    }
    
    foreach ($selectionDatas as $selectionData) {
        $row_option = $option;
        
        # Kolla om något är selected
        if($multiple) {
            if (!is_null($selected) && !empty($selected) && in_array($selectionData->Id, $selected))
                $row_option = $row_option.' checked="checked"';
        } else {  
            if ((!is_null($selected) && $selected == $selectionData->Id) 
                || (is_null($selected) && 'null' == $selectionData->Id))
                $row_option = $row_option.' checked="checked"';
        }
        echo "<tr>";
        echo "<td  style='white-space: nowrap;'>";
        echo "<input type='" . $type . "' id='" .$name_in.$selectionData->Id . "' name='" . $name . "' value='" . $selectionData->Id . "' " . $row_option . ">\n";
        echo "<label for='" .$name_in.$selectionData->Id . "'>" .  htmlspecialchars($selectionData->Name) . "</label><br>\n";
        echo "</td>";
        if (($name_in!='Group') && isset($selectionData->Description)) echo "<td>".nl2br(htmlspecialchars($selectionData->Description))."</td>";
        
        echo "</tr>";
    }
    echo "</table>\n";
}

# En ikon som man hovrar över och som då visar hjälptexten. Går att klicka på mobiltelefon.
function help_icon($help_text) {
    $formatted_text = nl2br($help_text);
    echo "&nbsp; <label data-tooltip='&nbsp; $formatted_text' data-toggle='tooltip'><i class='fa-solid fa-circle-info'></i></label>";
//     echo "<script>
//     function showHelpText(element) {
//         var tooltip = element.getAttribute('data-tooltip');
//         alert(tooltip);
//     }
//     </script>";
}

// function help_icon($text) {
//     global $help_icon_count;
    
//     if (isset($help_icon_count)) $help_icon_count++;
//     else $help_icon_count=1;
    
//     $formatted_text = nl2br($text);
//     echo "&nbsp; <label onclick='showHelpText(this)' data-tooltip=\"$formatted_text\" data-toggle='tooltip'><i class='fa-solid fa-circle-info'></i></label>";
    
//     if ($help_icon_count == 1)
//         echo "<script>
//         function showHelpText(element) {
//             var tooltip = element.getAttribute('data-tooltip');
//             var helpDiv = document.createElement('div');
//             helpDiv.innerHTML = tooltip;
//             helpDiv.style.position = 'absolute';
//             helpDiv.style.backgroundColor = '#fff';
//             helpDiv.style.border = '1px solid #ccc';
//             helpDiv.style.padding = '10px';
//             helpDiv.style.boxShadow = '0 4px 8px rgba(0,0,0,0.1)';
//             document.body.appendChild(helpDiv);
            
//             var rect = element.getBoundingClientRect();
//             helpDiv.style.top = rect.bottom + 'px';
//             helpDiv.style.left = rect.left + 'px';
            
//             helpDiv.onclick = function() {
//                 document.body.removeChild(helpDiv);
//             };
//         }
//         </script>";
// }

# En selector där man kan välja i en array av objekt
function selectionDropDownByArray(String $name, Array $selectionDatas, $required=true, $selected=null, ?String $selectedValue = null, ?String $formIdentifier="") {
    // lägg till tomt val om det inte är required
    if (!$required){
        if (!empty($selectionDatas)) $empty_object = clone $selectionDatas[0];
        else $empty_object = Religion::newWithDefault();
        $empty_object->Id = "null";
        $empty_object->Name = "[ Ingen / Inget ]";
        array_unshift($selectionDatas , $empty_object);
    }
    
   $selectedFound = false;
   if (empty($selected)) $selectedFound = true;
   echo "<select $formIdentifier name='$name' id='$name'>\n";
    foreach ($selectionDatas as $selectionData) {
        $row_option = '';
        if (!empty($selected) && $selectionData->Id == $selected) {
            $row_option = 'selected';
            $selectedFound = true;
        }
            
     echo "   <option value='$selectionData->Id' $row_option>".htmlspecialchars($selectionData->Name)."</option>\n";
    }
    
    if (!$selectedFound && !empty($selectedValue)) echo "   <option value='$selected' selected>".htmlspecialchars($selectedValue)."</option>\n";
    echo "</select>\n";
    
}

# En selector där man kan välja i en array
function selectionDropDownBySimpleArray(String $name, Array $alternatives, $selected=null, ?String $formIdentifier="") {
    
    echo "<select $formIdentifier name='$name' id='$name'>\n";
    foreach ($alternatives as $key => $alternative) {
        $row_option = '';
        if (!empty($selected) && $key == $selected)
            $row_option = 'selected';
            
            echo "   <option value='$key' $row_option>".htmlspecialchars($alternative)."</option>\n";
    }
    echo "</select>\n";
    
}


# Tar en array av object och gör en komma-separerad lista av deras namn
function commaStringFromArrayObject($objectArray) {
    $outputs = array();
    
    foreach($objectArray as $object) {
        $outputs[] = htmlspecialchars($object->Name);
    }
    return implode(", ", $outputs);
}

function ja_nej($val) {
    if ($val == 1 or $val==true) return "Ja";
    if ($val == 0 or $val==false) return "Nej";
}


function showStatusIcon($value, ?string $fix_url = NULL, ?string $unfix_url = NULL) {
    if ($value == true or $value == 1) {
        if (isset($unfix_url) && !is_null($unfix_url)) {
            return "<a href='$unfix_url'><img src='../images/ok-icon.png' alt='OK' width='20' height='20'></a>";
        }
        else return '<img src="../images/ok-icon.png" alt="OK" width="20" height="20">';
    }
    if ($value == false or $value == 0) {
        if (isset($fix_url) && !is_null($fix_url)) {
            return "<a href='$fix_url'><img src='../images/alert-icon.png' alt='Varning' width='20' height='20'></a>";
        }
        else return '<img src="../images/alert-icon.png" alt="Varning" width="20" height="20">';
    }
}

function showQuestionmarkIcon() {
    return '<img src="../images/questionmark.jpeg" alt="?" width="20" height="20">';
}


function showParticipantStatusIcon($value, $message) {
    if ($value == true or $value == 1) {
        return '<img src="../images/ok-icon.png" alt="OK" width="20" height="20">';
    }
    if ($value == false or $value == 0) {
        return "<div class='errorbutton'><img src='../images/alert-icon.png' alt='$message' width='20' height='20'><div> $message</div></div>";
    }
}

function contactEmailIcon(Person $person, ?int $sender=BerghemMailer::LARP) {
    $param = date_format(new Datetime(),"suv");
    $res = "<form action='../common/contact_email.php'  class='fabutton' method='post'>\n";
    $res .= "<input type=hidden name='sender' value='$sender'>";
    $res .= "<input type=hidden name='send_one' value=$param>\n".
        "<input type=hidden name='personId' value=$person->Id>\n".
        "<button type='submit' class='invisible' title='Skicka mail till $person->Name'>".
        "  <i class='fa-solid fa-envelope-open-text'></i>".
        "</button>\n".
        "</form>\n";
    return $res;
}


function contactAllEmailIcon(){
    $param = date_format(new Datetime(),"suv");
    return "<form action='../common/contact_email.php'  class='fabutton' method='post'>".
        "<input type=hidden name='isLarp' value='1'>".
        "<input type=hidden name='send_all' value=$param>".
        "<button type='submit' class='invisible' title='Skicka ett utskick till alla deltagare i lajvet'>".
        "  <i class='fa-solid fa-envelope-open-text'></i>".
        "</button>".
        "</form>";
}


function contactSeveralEmailIcon($txt, $personIdArr, $greeting, $subject, ?int $sender=BerghemMailer::LARP){
    $param = date_format(new Datetime(),"suv");
    $retrunStr = "<form action='../common/contact_email.php'  class='fabutton' method='post'>\n".
        "<input type=hidden name='send_several' value=$param>\n";
    $retrunStr .= "<input type=hidden name='sender' value='$sender'>";
    
    foreach ($personIdArr as $personId)  {
        $retrunStr .= "<input type='hidden' name='personId[]' value='$personId'>\n";
    }
    $retrunStr .= "<input type=hidden name='name' value='$greeting'>\n".
        "<input type=hidden name='subject' value='$subject'>\n".
        "<button type='submit' class='invisible' title='$txt'>".
        "  <i class='fa-solid fa-envelope-open-text' title='Skicka epost till alla'></i>  ".$txt.
        "</button>\n".
    "</form>\n";
    return $retrunStr;
}


function formatDateTimeForInput($dateTime) {
    //2024-05-12 15:25:36
    $dateTimeSplit = explode(' ',$dateTime);
    $date = $dateTimeSplit[0];
    $fullTime = $dateTimeSplit[1];
    return $date."T".substr($fullTime, 0, 5);
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
    }
    echo "</select>\n";
    echo "</div>\n";
}

# Inamtning för hitta personer enkelt
# Man kan ange bredden på inmatningsfältet som argument
function autocomplete_person_id($width="100%", $render_sumbit_button=false) {
    global $autocomplete_count;
    
    if (isset($autocomplete_count)) $autocomplete_count++;
    else $autocomplete_count=1;
    if ($autocomplete_count == 1)
        echo "
        <style>
            .suggestions div {
                cursor: pointer;
                padding: 5px;
                border: 1px solid #ccc;
            }
            .suggestions div:hover {
                background-color: #f0f0f0;
            }
        </style>
       <script src='../javascript/autocomplete_person.js'></script>";

    echo "<input type='text' id='autocomplete_person$autocomplete_count' placeholder='Ange ett namn eller personnummer' title='Ange ett namn eller personnummer' style='width:$width;display:inline;' oninput='autocomplete_person(event, this, $autocomplete_count)'>";
    if ($render_sumbit_button) echo " &nbsp; <input id='autocomplete_person_submit_button$autocomplete_count' type='submit' value='Välj en person' style='display:inline;'>	";
	echo "<input type='hidden' id='person_id$autocomplete_count'  name='person_id' value='0'>
	<div id='suggestions$autocomplete_count' class='suggestions'></div>";
}

function economy_overview(Larp $larp) {

    $income = Registration::totalIncomeToday($larp) + Bookkeeping::sumRegisteredIncomes($larp);
    $refund = 0 - Registration::totalFeesReturned($larp);
    $expense = Bookkeeping::sumRegisteredExpenses($larp);
    $sum = $income + $refund + $expense;

    echo "<tr><td style='font-weight: normal'>Faktiskta intäkter:<br>registrerade betalningar och andra registrerade intäkter</td>\n";
    echo "<td align='right'>".number_format((float)($income), 2, ',', '')." SEK</td></tr>\n";
    echo "<tr><td style='font-weight: normal'>Avdrag återbetalningar:<br>alla registrerade återbetalningar</td>\n";
    echo "<td align='right'>". number_format((float)($refund), 2, ',', '')." SEK</td></tr>\n";
    echo "<tr><td style='font-weight: normal'>Övriga utgifter:<br>andra registrerade utgifter<br></td>\n";
    echo "<td align='right'>".number_format((float)$expense, 2, ',', '')." SEK</td></tr>\n";
    echo "<tr><td style='font-weight: normal'>Balans:<br>faktiska inkomster och utgifter</td>\n";
    echo "<td align='right'>".number_format((float)$sum, 2, ',', '')."  SEK</td></tr>\n";
    return $sum;
}

function economy_overview_campaign(Campaign $campaign, $year) {
    
    $income = Bookkeeping::sumRegisteredIncomesCampaign($campaign, $year);
    $expense = Bookkeeping::sumRegisteredExpensesCampaign($campaign, $year);
    $sum = $income + $expense;
    
    echo "<tr><td style='font-weight: normal'>Intäkter:</td>\n";
    echo "<td align='right'>".number_format((float)($income), 2, ',', '')." SEK</td></tr>\n";
    echo "<tr><td style='font-weight: normal'>Utgifter:<br>andra registrerade utgifter<br></td>\n";
    echo "<td align='right'>".number_format((float)$expense, 2, ',', '')." SEK</td></tr>\n";
    
    $larps = LARP::getAllForYear($campaign->Id, $year);
    if (!empty($larps)) {
        foreach ($larps as $larp) {
            echo "<tr><td style='font-weight: normal'>";
            
            echo "<details><summary>$larp->Name</summary> ";
            echo substr($larp->StartDate, 0, 10) .' - '.substr($larp->EndDate, 0, 10)."<br><br>\n";
            echo "<table>";
            $larpsum = economy_overview($larp);
            echo "</table><br></details><br>\n";
            
            echo "</td>\n";
            echo "<td align='right'>".number_format((float)$larpsum, 2, ',', '')." SEK</td></tr>\n";
            $sum += $larpsum;
            
        }
    }
    echo "<tr><td style='font-weight: normal'>Balans:</td>\n";
    echo "<td align='right'>".number_format((float)$sum, 2, ',', '')."  SEK</td></tr>\n";
}

# Ta bort någon som husförvaltare för ett hus
function remove_housecaretaker(Person $person, House $house) {
    $txt = '"Är du säker '.$person->Name.' inte ska vara husförvaltare för '.$house->Name.'?"';
    $confirm = "onclick='return confirm($txt)'";
    $i = "<i class='fa-solid fa-trash' title='Ta bort $person->Name som husförvaltare för $house->Name'></i>";
    return " <a href='logic/remove_caretaker.php?person_id=$person->Id&houseId=$house->Id' $confirm>$i</a>";
}
