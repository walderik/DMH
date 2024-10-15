		<?php 
		$currency = $current_larp->getCampaign()->Currency;
		$previous_larps = $group->getPreviousLarps();
		if (isset($previous_larps) && count($previous_larps) > 0) {
		    
		    echo "<h2>Historik</h2>";
		    foreach ($previous_larps as $prevoius_larp) {
		        $previous_larp_group = LARP_Group::loadByIds($group->Id, $prevoius_larp->Id);
		        echo "<div class='border'>";
		        echo "<h3>$prevoius_larp->Name</h3>";
		        if (isset($previous_larp_group->StartingMoney)) {
		            echo "Började med $previous_larp_group->StartingMoney $currency";
		            if (isset($previous_larp_group->EndingMoney)) echo ", slutade med $previous_larp_group->EndingMoney $currency.";
		            echo "<br>";
		        }
		        
		        
		        if (!empty($previous_larp_group->Intrigue)) {
    		        echo "<strong>Intrig</strong><br>";
    		        echo "<p>".nl2br($previous_larp_group->Intrigue)."</p>";
		        }
		        
		        $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $prevoius_larp->Id);
		        foreach($intrigues as $intrigue) {
		            
		            $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $group);
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
		        
		        echo "<br><strong>Vad hände för $group->Name?</strong><br>";
		        if (isset($previous_larp_group->WhatHappened) && $previous_larp_group->WhatHappened != "")
		            echo nl2br(htmlspecialchars($previous_larp_group->WhatHappened));
		            else echo "Inget rapporterat";
	            echo "<br><strong>Vad hände för andra?</strong><br>";
	            if (isset($previous_larp_group->WhatHappendToOthers) && $previous_larp_group->WhatHappendToOthers != "")
	                echo nl2br(htmlspecialchars($previous_larp_group->WhatHappendToOthers));
	                else echo "Inget rapporterat";
	            echo "</div>";
		                
		    }
		}
			    
