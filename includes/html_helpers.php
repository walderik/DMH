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
    $option = ($required) ? "required class='requiredField'" : '';
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


function showStatusIcon($value, ?string $fix_url = NULL, ?string $unfix_url = NULL, ?string $fel_text = '', ?string $ok_text = '') {
    if ($value == true or $value == 1) {
        if (isset($unfix_url) && !is_null($unfix_url)) {
            return "<a href='$unfix_url'><img src='../images/ok-icon.png' alt='OK' title='$ok_text' width='20' height='20'></a>";
        }
        else return "<img src='../images/ok-icon.png' alt='OK' title='$ok_text' width='20' height='20'>";
    }
    elseif ($value == false or $value == 0) {
        if (isset($fix_url) && !is_null($fix_url)) {
            if (empty($fel_text) || $fel_text === '') $fel_text = 'Klicka för att åtgärda';
            return "<a href='$fix_url'><img src='../images/alert-icon.png' alt='Varning' title='$fel_text' width='20' height='20'></a>";
        }
        else return "<img src='../images/alert-icon.png' alt='Varning' title='$fel_text' width='20' height='20'>";
    }
}

/* Variant av showStatusIcon som använder Post-anrop istället
 Användning - echo showPostStatusIcon( false, '/fix.php', '/unfix.php', 'Klicka för att åtgärda', 'Ta bort ok',
     // POST vid FIX
    [ 'id'     => 123, 'param2' => 'fix' ],
    // POST vid UNFIX
    ['id' => 123, 'param2' => 'unfix' ]
);
 */
function showPostStatusIcon($value, ?string $fix_url = null, ?string $unfix_url = null, ?string $fel_text = '', ?string $ok_text = '', ?array $fix_post_data = [], ?array $unfix_post_data = [], ?bool $showText = false): string {      
    $value = (bool)$value;
    
    // Intern hjälpfunktion
    $postIcon = function (string $action, string $img, string $alt, string $title, array $post_data, $showText): string {
        $text = "";
        if ($showText) $text = " ".$title;
        $html = "\n<form action='$action' method='post' style='display:inline;'>\n";
        if ($post_data != null) {
            foreach ($post_data as $name => $value) {
                $html .= "<input type='hidden' name='$name' value='".htmlspecialchars($value)."'>\n";
            }
        }
        
        $html .= "<button type='submit' style='background:none;border:none;padding:0;cursor:pointer;'>
            <img src='$img' alt='$alt' title='".htmlspecialchars($title ?? '')."' width='20' height='20'>$text
        </button>
    </form>\n";
        return $html;
    };
    
    /* ===== OK ===== */
    if ($value) {
        if ($showText) $clearText = $ok_text;
        if (!empty($unfix_url)) return $postIcon($unfix_url, '../images/ok-icon.png', 'OK', $ok_text ?? '', $unfix_post_data, $showText);
        return "<img src='../images/ok-icon.png' alt='OK' title='" . htmlspecialchars($ok_text ?? '') . "'  width='20' height='20'>\n";
    }

    if (empty($fel_text)) $fel_text = 'Klicka för att åtgärda';
    if (!empty($fix_url)) return $postIcon($fix_url, '../images/alert-icon.png', 'Varning', $fel_text, $fix_post_data, $showText );
    return "<img src='../images/alert-icon.png' alt='Varning' title='" . htmlspecialchars($fel_text) . "' width='20'  height='20'>\n";
}


function showWarningIcon(?string $fel_text = '') {
    return "<img src='../images/warning.png' alt='Varning' title='$fel_text' width='20' height='20'>";
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



function print_participant_question_start(String $title, String $description, bool $isRequired, bool $isIntrigue, bool $mayCollapse) {
    echo "<div class='itemcontainer ";
    if ($isIntrigue) echo "intrigue";
    echo "'>\n";
    if ($isRequired || !$mayCollapse) {
        echo "<div class='itemname'>$title&nbsp;";
        if ($isRequired) echo "<font style='color:red'>*</font>";
        echo "</div>\n";
    } else echo "<details><summary class='itemname'>$title</summary>";
    if (!empty($description)) {
        echo $description;
        echo "<br>\n";
    };
}

function print_participant_question_end(bool $isRequired) {
    if (!$isRequired) echo "</details>";
    echo "</div>\n";
}

function print_participant_text_input(String $title, String $description, String $id, $value, String $inputParams, bool $isRequired, bool $isIntrigue) {
    print_participant_question_start($title, $description, $isRequired, $isIntrigue, empty($value));
    echo "<input ";
    if ($isIntrigue && $isRequired) echo "class='requiredIntrigueField' ";
    echo "type='text' id='$id' name='$id' value='".htmlspecialchars($value)."' $inputParams";
    if ($isRequired) echo " required";
    echo ">\n";
    print_participant_question_end($isRequired);
}

function print_participant_textarea(String $title, String $description, String $id, $value, String $inputParams, bool $isRequired, $isIntrigue, ?bool $may_collapse=true) {
    print_participant_question_start($title, $description, $isRequired, $isIntrigue, ($may_collapse && empty($value)));
    echo "<textarea ";
    if ($isIntrigue && $isRequired) echo "class='requiredIntrigueField' ";
    echo "type='text' id='$id' name='$id' $inputParams";
    if ($isRequired) echo " required";
    echo ">$value</textarea>\n";
    print_participant_question_end($isRequired);
 }


function linkify($str) {
    $url_pattern = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';
    $str= preg_replace($url_pattern, '<a href="$0" target="_blank">$0</a>', $str);
    return $str;
}

# Ikonen som visar annonser
function advertismentIcon() {
    global $current_person, $current_larp;
    
    if (!isset($current_person) || !isset($current_larp)) {
        return;
    }
    
    # Visas inte alls om det inte finns annonstyper för lajet
    if (empty(AdvertismentType::isInUse($current_larp))) return;
    
    $latest_ad = Advertisment::larpsLatest($current_larp);
    if (!empty($latest_ad)) {
        $created = new DateTime($latest_ad->CreatedAt);
        $checked = new DateTime($current_person->AdvertismentsCheckedAt);
        if (is_null($current_person->AdvertismentsCheckedAt) || $created > $checked) {
            echo  "<a href='advertisments.php'><font style='color:red'><i class='fa-solid fa-bullhorn'></i></font></a>";
            return;
        }
    }
    echo  "<a href='advertisments.php'><i class='fa-solid fa-bullhorn'></i></a>";
}

# Visar "Du har fått email"-ikonen som röd om man som deltgare har nya email att läsa
function emailIcon() {
    global $current_person, $current_larp;
    
    if (!isset($current_person) || !isset($current_larp)) {
        return;
    }
    
    if ($_SESSION['navigation'] != Navigation::PARTICIPANT) { # Om man inte jobbar som "Vanlig deltagare"
        echo "<a href='../common/mail_admin.php' class='expand_hide always_show'><i class='fa-solid fa-envelope'></i></a>";
        return;
    }
    
    if (is_null($current_person->LastMailSentAt)) {
        echo "<a href='../common/mail_admin.php' class='expand_hide always_show'><i class='fa-solid fa-envelope'></i></a>";
        return;
    }
    
    if (is_null($current_person->MailCheckedAt)) {
        echo "<a href='../common/mail_admin.php' class='expand_hide always_show'><font style='color:red'><i class='fa-solid fa-envelope'></i></font></a>";
        return;
    }
    
    $email_sent_at = new DateTime($current_person->LastMailSentAt);  
    $email_checked_at = new DateTime($current_person->MailCheckedAt);

    if ($email_sent_at > $email_checked_at) {
        echo "<a href='../common/mail_admin.php' class='expand_hide always_show'><font style='color:red'><i class='fa-solid fa-envelope'></i></font></a>";
    } else {
        echo "<a href='../common/mail_admin.php' class='expand_hide always_show'><i class='fa-solid fa-envelope'></i></a>";
    }
}



function participantPrintedIntrigue($number, $commonText, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr, $alwaysPrintWhatHappened) {
    $formattedText = "";
    if (!empty($commonText)) {
        $formattedText .= "<p>";
        if (!empty($commonTextHeader)) {
            $formattedText .= "<strong>".htmlspecialchars($commonTextHeader)."</strong><br>";
        }
        $formattedText .= nl2br(htmlspecialchars($commonText))."</p>";
    }
    
    if (!empty($intrigueTextArr)) {
        $tmpIntrigueTextArr = array();
        foreach ($intrigueTextArr as $intrigueText) {
            if (is_array($intrigueText)) {
                $tmpIntrigueTextArr[] = "<strong>".htmlspecialchars($intrigueText[0])."</strong><br>".nl2br(htmlspecialchars($intrigueText[1]));
            } else {
                $tmpIntrigueTextArr[] = nl2br(htmlspecialchars($intrigueText));
            }
        }
        $formattedText .= "<p>".join("<br><br>",$tmpIntrigueTextArr). "</p>";
        
        
        
    }
    
    if (!empty($offTextArr)) {
        $tmpOffTextArr = array();
        foreach ($offTextArr as $offText) {
            $tmpOffTextArr[] = nl2br(htmlspecialchars($offText));
        }
        $formattedText .= "<p><strong>Off-information:</strong><br><i>".join("<br><br>",$tmpOffTextArr)."</i></p>";
    }
    
    if (!empty($whatHappenedTextArr) || $alwaysPrintWhatHappened) {
        $tmpWhatHappenedTextArr = array();
        foreach ($whatHappenedTextArr as $whatHappenedText) {
            if (is_array($whatHappenedText)) {
                $tmpWhatHappenedTextArr[] = "<strong>".htmlspecialchars($whatHappenedText[0])."</strong><br>".nl2br(htmlspecialchars($whatHappenedText[1]))."<br><br>";
            } else {
                $tmpWhatHappenedTextArr[] = nl2br(htmlspecialchars($whatHappenedText));
            }
        }
        $formattedText .= "<p><strong>Vad hände med det:</strong><br>";
        if (!empty($tmpWhatHappenedTextArr)) $formattedText .= join("<br><br>",$tmpWhatHappenedTextArr);
        else $formattedText .= "Inget rapporterat";
        $formattedText .= "</p>";
    }
    
    if (!empty($formattedText)) {
        return "<h3>Intrig $number:</h3>".$formattedText;
    }
    
}

