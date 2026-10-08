<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RoleId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}




$role = Role::loadById($RoleId);
$isPc = $role->isPC($current_larp);

if ($isPc) $person = $role->getPerson();

if ($isPc && $person->Id != $current_person->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

$group = $role->getGroup();

$isAssignedToMe = false;
if ($role->isNPC($current_larp)) {
    $assignment = NPC_assignment::getAssignment($role, $current_larp);
    if (!empty($assignment) && ($assignment->PersonId == $current_person->Id)) {
        $isAssignedToMe = true;
    } elseif (!empty($group) && ($current_person->isMemberGroup($group) || $current_person->hasNPCInGroup($group, $current_larp))) {
        //Ok, din grupp
    } else {
        header('Location: index.php'); //NPC som inte är din och inte är med i din grupp
        exit;
    }
}

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);

if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
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

$campaign = $current_larp->getCampaign();


include 'navigation.php';
?>


		    <div class='itemselector'>
		    <div class="header">
		    
		    <i class="fa-solid fa-landmark"></i> Historik för <?php  echo $role->getViewLink()?>
		    </div>
		    <div class='itemcontainer' style='padding-top:0px;'>		

		    

		   <?php 
		   $previous_larps = $role->getPreviousLarps($current_larp);
		    foreach ($previous_larps as $prevoius_larp) {
		        $previous_larp_role = LARP_Role::loadByIds($role->Id, $prevoius_larp->Id);
		        echo "<h2 style='border-top:none;'>$prevoius_larp->Name</h2>";
		        echo "<div class='borderbottom'>";
		        
		        if (isset($previous_larp_role) && !empty($previous_larp_role->Intrigue)) {
		            echo "<p>".nl2br(htmlspecialchars($previous_larp_role->Intrigue))."</p>";
		        }
		        
		        $intrigues = $role->getAllIntriguesIncludingSubdivisionsSorted($prevoius_larp);
		        $subdivisions = Subdivision::allForRole($role, $prevoius_larp);
		        
		        foreach ($intrigues as $intrigue) {
		            if ($intrigue->isActive()) {
		                		         
		                $hasCommonText = false;
		                $commonTextHeader = "";
		                $intrigueTextArr = array();
		                $offTextArr = array();
		                $whatHappenedTextArr = array();
		                
		                $intrigue->findAllInfoForRoleInIntrigue($role, $subdivisions, $hasCommonText, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr);
		                //Visa alltid rubriken "Vad hände"
		                echo participantPrintedIntrigue($intrigue->Number, $intrigue->CommonText, $hasCommonText, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr, false);
		            }
		        }
		        
		        $previous_assignment = NPC_assignment::getAssignment($role, $prevoius_larp);
		        if (isset($previous_larp_role)) {
    		        echo "<p><strong>Vad hände för $role->Name?</strong><br>";
    		        if (isset($previous_larp_role->WhatHappened) && $previous_larp_role->WhatHappened != "")
    		            echo nl2br(htmlspecialchars($previous_larp_role->WhatHappened));
    	            else echo "Inget rapporterat";
    	            echo "</p>";
    	            echo "<p><strong>Vad hände för andra?</strong><br>";
    	            if (isset($previous_larp_role->WhatHappendToOthers) && $previous_larp_role->WhatHappendToOthers != "")
    	                echo nl2br(htmlspecialchars($previous_larp_role->WhatHappendToOthers));
                    else echo "Inget rapporterat";
                    echo "</p>";
                    echo "<p><strong>Vad händer efter lajvet?</strong><br>";
                    if (isset($previous_larp_role->WhatHappensAfterLarp) && $previous_larp_role->WhatHappensAfterLarp != "")
                        echo nl2br(htmlspecialchars($previous_larp_role->WhatHappensAfterLarp));
                    else echo "Inget rapporterat";
                    echo "</p>";
                    echo "</div>";
		        } elseif (isset($previous_assignment)) {
		            echo "<p><strong>Vad hände för $role->Name?</strong><br>";
		            if (isset($previous_assignment->WhatHappened) && $previous_assignment->WhatHappened != "") {
		                echo nl2br(htmlspecialchars($previous_assignment->WhatHappened));
		            } else echo "Inget rapporterat";
	                echo "</p>";
	                echo "<p><strong>Vad hände för andra?</strong><br>";
	                if (isset($previous_assignment->WhatHappendToOthers) && $previous_assignment->WhatHappendToOthers != "") {
	                    echo nl2br(htmlspecialchars($previous_assignment->WhatHappendToOthers));
	                } else echo "Inget rapporterat";
                    echo "</p>";
                    echo "</div>";
		        }
		                

		    }
		    
		    if (!empty($role->PreviousLarps)) {
		        echo "<div class='border'><h3>Tidigare</h3>";
		        echo "<p>".nl2br(htmlspecialchars($role->PreviousLarps))."</p></div>";
		    }
		    echo "</div>";
			    
			
			
		?>


</body>
</html>
