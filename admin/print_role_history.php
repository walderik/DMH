<?php
$previous_larps = $role->getPreviousLarps($current_larp);
if (isset($previous_larps) && count($previous_larps) > 0) {
    $first = true;
    echo "<h2>Historik</h2>";
    foreach ($previous_larps as $prevoius_larp) {
        $previous_larp_role = LARP_Role::loadByIds($role->Id, $prevoius_larp->Id);
        $previous_assignment = NPC_assignment::getAssignment($role, $prevoius_larp);
        echo "<div class='border'>";
        echo "<h3>$prevoius_larp->Name</h3>";
        if (isset($previous_assignment)) {
            $person = $previous_assignment->getPerson();
            if (!empty($person)) echo "Spelad av $person->Name<br>"; 
        }
        if (isset($previous_larp_role) && !empty($previous_larp_role->Intrigue)) {
            $first = false;
            echo "<strong>Intrig</strong><br>";
            echo "<p>".nl2br($previous_larp_role->Intrigue)."</p>";
        }
        
        
        $intrigues = $role->getAllIntriguesIncludingSubdivisionsSorted($current_larp);
        $subdivisions = Subdivision::allForRole($role, $current_larp);
        
        $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $prevoius_larp->Id);
        foreach($intrigues as $intrigue) {
            $commonTextHeader = "";
            $intrigueTextArr = array();
            $offTextArr = array();
            $whatHappenedTextArr = array();
            
            $intrigue->findAllInfoForRoleInIntrigue($role, $subdivisions, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr, true);
            
            $formattedText = "";

            
            if (!empty($intrigue->CommonText)) {
                if (!empty($commonTextHeader)) {
                    $formattedText .= "<p><i>Gemensam text:</i><br><strong>$commonTextHeader</strong><br>".nl2br(htmlspecialchars($intrigue->CommonText))."</p>";
                } else $formattedText .= "<p><i>Gemensam text:</i><br>".nl2br(htmlspecialchars($intrigue->CommonText))."</p>";
            }
            
            if (!empty($intrigueTextArr)) {
                $tmpIntrigueTextArr = array();
                foreach ($intrigueTextArr as $intrigueText) {
                    if (is_array($intrigueText) && $intrigueText[2]) {
                        $tmpIntrigueTextArr[] = "<strong>".htmlspecialchars($intrigueText[0])."</strong><br>".nl2br(htmlspecialchars($intrigueText[1]));
                    } elseif (is_array($intrigueText)) {
                        $tmpIntrigueTextArr[] = "<i>".htmlspecialchars($intrigueText[0])."</i><br>".nl2br(htmlspecialchars($intrigueText[1]));
                    } else {
                        $tmpIntrigueTextArr[] = nl2br(htmlspecialchars($intrigueText));
                    }
                }
                $formattedText .=  "<p>".join("<br><br>",$tmpIntrigueTextArr). "</p>";
            }
            
            if (!empty($offTextArr)) {
                $tmpOffTextArr = array();
                foreach ($offTextArr as $offText) {
                    $tmpOffTextArr[] = nl2br(htmlspecialchars($offText));
                }
                $formattedText .= "<p><strong>Off-information:</strong><br><i>".join("<br><br>",$tmpOffTextArr)."</i></p>";
            }
            
            
            //Vad hände
            $tmpWhatHappenedTextArr = array();
            foreach ($whatHappenedTextArr as $whatHappenedText) {
                if (is_array($whatHappenedText) && $whatHappenedText[2]) {
                    $tmpWhatHappenedTextArr[] = "<strong>".htmlspecialchars($whatHappenedText[0])."</strong><br>".nl2br(htmlspecialchars($whatHappenedText[1]));
                } elseif (is_array($whatHappenedText)) {
                    $tmpWhatHappenedTextArr[] = "<i>".htmlspecialchars($whatHappenedText[0])."</i><br>".nl2br(htmlspecialchars($whatHappenedText[1]));
                } else {
                    $tmpWhatHappenedTextArr[] = nl2br(htmlspecialchars($whatHappenedText));
                }
            }
            $formattedText .= "<p><strong>Vad hände med det:</strong><br>";
            if (!empty($tmpWhatHappenedTextArr)) $formattedText .= join("<br><br>",$tmpWhatHappenedTextArr);
            else $formattedText .= "Inget rapporterat";
            $formattedText .= "</p>";
            
            
            
            if (!empty($formattedText)) {
                if ($first) $first = false;
                else echo "<hr>";
                echo "<h4>Intrigspår $intrigue->Number, $intrigue->Name</h4>".$formattedText;
            }
        }
        if ($first) $first = false;
        else echo "<hr>";
        

        if (isset($previous_larp_role)) {
            echo "<br><strong>Vad hände för $role->Name?</strong><br>";
            if (isset($previous_larp_role->WhatHappened) && $previous_larp_role->WhatHappened != "")
                echo nl2br(htmlspecialchars($previous_larp_role->WhatHappened));
            else echo "Inget rapporterat";
            echo "<br><strong>Vad hände för andra?</strong><br>";
            if (isset($previous_larp_role->WhatHappendToOthers) && $previous_larp_role->WhatHappendToOthers != "")
                echo nl2br(htmlspecialchars($previous_larp_role->WhatHappendToOthers));
            else echo "Inget rapporterat";
            echo "<br><strong>Vad händer fram till nästa lajv?</strong><br>";
            if (isset($previous_larp_role->WhatHappensAfterLarp) && $previous_larp_role->WhatHappensAfterLarp != "")
                echo nl2br(htmlspecialchars($previous_larp_role->WhatHappensAfterLarp));
            else echo "Inget rapporterat";
            echo "</div>";
        } elseif (isset($previous_assignment)) {
            echo "<br><strong>Vad hände för $role->Name?</strong><br>";
            if (isset($previous_assignment->WhatHappened) && $previous_assignment->WhatHappened != "")
                echo nl2br(htmlspecialchars($previous_assignment->WhatHappened));
            else echo "Inget rapporterat";
            echo "<br><strong>Vad hände för andra?</strong><br>";
            if (isset($previous_assignment->WhatHappendToOthers) && $previous_assignment->WhatHappendToOthers != "")
                echo nl2br(htmlspecialchars($previous_assignment->WhatHappendToOthers));
            else echo "Inget rapporterat";
        }
        
    }
    if (!empty($role->PreviousLarps)) {
        echo "<div class='border'><h3>Tidigare</h3>";
        echo "<p>".nl2br(htmlspecialchars($role->PreviousLarps))."</p></div>";
    }
    
}
