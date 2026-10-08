<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $GroupId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}
$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));

if($isMob){
    $columns=2;
    $type="Mobile";
    //echo 'Using Mobile Device...';
}else{
    $columns=5;
    $type="Computer";
    //echo 'Using Desktop...';
}
$temp=0;


$group = Group::loadById($GroupId); 

if (!$current_person->isMemberGroup($group) && !$current_person->isGroupLeader($group) && !$current_person->hasNPCInGroup($group, $current_larp)) {
    header('Location: index.php?error=no_member'); //Inte medlem i gruppen
    exit;
}



include 'navigation.php';
?>
		    <div class='itemselector'>
		    <div class="header">
		    	<i class="fa-solid fa-landmark"></i> Historik för <?php echo $group->getViewLink() ?>
		    </div>
		    <div class='itemcontainer'>
		    
		   <?php 
		   $previous_larps = $group->getPreviousLarps($current_larp);
		    foreach ($previous_larps as $prevoius_larp) {
		        $previous_larp_group = LARP_Group::loadByIds($group->Id, $prevoius_larp->Id);
		        echo "<div class='borderbottom'>";
		        echo "<h3>$prevoius_larp->Name</h3>";
		        if (!empty($previous_larp_group->Intrigue)) {
		            echo "<strong>Intrig</strong><br>";
		            echo "<p>".nl2br($previous_larp_group->Intrigue)."</p>";
		        }
		        
		        $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $prevoius_larp->Id);
		        foreach($intrigues as $intrigue) {
		            $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $group);
		            if ($intrigue->isActive() && !empty($intrigueActor->IntrigueText)) {
		                echo "<p><strong>Intrig $intrigue->Number</strong><br>".nl2br($intrigueActor->IntrigueText)."</p>";
		                
		                echo "<p><strong>Vad hände med det?</strong><br>";
		                if (!empty($intrigueActor->WhatHappened)) echo nl2br($intrigueActor->WhatHappened);
		                else echo "Inget rapporterat";
		                echo "</p>";
		            }
		        }
		        
		        echo "<p><strong>Vad hände för $group->Name?</strong><br>";
		        if (isset($previous_larp_group->WhatHappened) && $previous_larp_group->WhatHappened != "")
		            echo nl2br(htmlspecialchars($previous_larp_group->WhatHappened));
	            else echo "Inget rapporterat";
	            echo "</p>";
	            echo "<p><strong>Vad hände för andra?</strong><br>";
	            if (isset($previous_larp_group->WhatHappendToOthers) && $previous_larp_group->WhatHappendToOthers != "")
	                echo nl2br(htmlspecialchars($previous_larp_group->WhatHappendToOthers));
                else echo "Inget rapporterat";
                echo "</p>";
                echo "<p><strong>Vad händer fram till nästa lajv?</strong><br>";
                if (isset($previous_larp_group->WhatHappensAfterLarp) && $previous_larp_group->WhatHappensAfterLarp != "")
                    echo nl2br(htmlspecialchars($previous_larp_group->WhatHappensAfterLarp));
                else echo "Inget rapporterat";
                echo "</p>";
                echo "</div>";
		                
		    }

			    
			
			
		?>
			
			
			
		</div>

	</div>


</body>
</html>
