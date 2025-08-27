<?php
$previous_larps = $role->getPreviousLarps();
if (isset($previous_larps) && count($previous_larps) > 0) {
    
    echo "<h2>Historik</h2>";
    foreach ($previous_larps as $prevoius_larp) {
        $previous_larp_role = LARP_Role::loadByIds($role->Id, $prevoius_larp->Id);
        echo "<div class='border'>";
        echo "<h3>$prevoius_larp->Name</h3>";
        if (!empty($previous_larp_role->Intrigue)) {
            echo "<strong>Intrig</strong><br>";
            echo "<p>".nl2br($previous_larp_role->Intrigue)."</p>";
        }
        
        $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $prevoius_larp->Id);
        foreach($intrigues as $intrigue) {
            $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
            if ($intrigue->isActive() && !empty($intrigueActor->IntrigueText)) {
                echo "<div class='intrigue'>";
                echo "<p><strong>Intrigspår: $intrigue->Name</strong><br>".nl2br($intrigueActor->IntrigueText)."</p>";
                
                echo "<p><strong>Vad hände med det?</strong><br>";
                if (!empty($intrigueActor->WhatHappened)) echo nl2br($intrigueActor->WhatHappened);
                else echo "Inget rapporterat";
                echo "</p>";
                echo "</div>";
            }
            
        }
        
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
                
    }
    if (!empty($role->PreviousLarps)) {
        echo "<div class='border'><h3>Tidigare</h3>";
        echo "<p>".nl2br(htmlspecialchars($role->PreviousLarps))."</p></div>";
    }
    
}
