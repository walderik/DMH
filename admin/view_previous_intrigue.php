<?php
include_once 'header.php';

$cols = 5;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['Id'])) {
        $intrigue=Intrigue::loadById($_GET['Id']);
    } else {
        header("Location: index.php");
        exit;
    }
    $larp = $intrigue->getLarp();
    
    if ($current_larp->Id == $intrigue->LarpId) {
        header("Location: view_intrigue.php?Id=$intrigue->Id");
        exit;
    } else if ($larp->CampaignId != $current_larp->CampaignId) {
        header("Location: index.php");
        exit;
    }
}



function printActorIntrigue(IntrigueActor $intrgueActor, $name) {
    echo "<h2>Intrig för $name</h2>\n";
    echo "<table width='100%''>\n";
    
    echo "<tr><td width='10%'>Intrigtext</td><td>";
    echo nl2br(htmlspecialchars($intrgueActor->IntrigueText));
    echo "</td></tr>\n";
    echo "<tr><td>Off-info<br>till deltagaren</td><td>".nl2br(htmlspecialchars($intrgueActor->OffInfo))."</td></tr>\n";
    echo "<tr><td>Ska ha vid incheck</td>\n";
    echo "<td>";
    $checkinProps = $intrgueActor->getAllPropsForCheckin();
    printAllProps($checkinProps, $intrgueActor, true);
    $checkinLetters = $intrgueActor->getAllLettersForCheckin();
    printAllLetters($checkinLetters, $intrgueActor);
    $checkinTelegrams = $intrgueActor->getAllTelegramsForCheckin();
    printAllTelegrams($checkinTelegrams, $intrgueActor);
    
    echo "</td></tr>\n";
    echo "<tr><td>Rekvisita och PDF aktören känner till</td><td>\n";
    $knownProps = $intrgueActor->getAllPropsThatAreKnown();
    printAllProps($knownProps, $intrgueActor, false);
    $knownPdfs = $intrgueActor->getAllPdfsThatAreKnown();
    printAllPdfs($knownPdfs, $intrgueActor);
    echo "</td></tr>\n";
    echo "<tr><td>Karaktärer aktören känner till</td><td>\n";
    $knownActors = $intrgueActor->getAllKnownActors();
    printAllKnownActors($knownActors, $intrgueActor);
    echo "</td></tr>\n";
    if (!empty($intrgueActor->WhatHappened)) {
        echo "<tr><td>Vad hände</td>\n";
        echo "<td>".nl2br($intrgueActor->WhatHappened)."</td></tr>\n";
    }
    echo "</table>\n";
    
}



function printAllProps($props, $intrigueActor, $isCheckin) {
    global $cols;
    if (empty($props)) return;
    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
    $temp=0;
    foreach ($props as $prop) {
        echo "\n";
        echo "<li style='display:table-cell; width:19%;'>\n";
        echo "<div class='name'>$prop->Name</div>\n";
        if ($prop->hasImage()) {
            echo "<img width=100 src='../includes/display_image.php?id=$prop->ImageId'/>\n";
        }
        echo "</li>\n";
        $temp++;
        if($temp==$cols)
        {
            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
            $temp=0;
        }
    }


	echo "</ul>";
}

function printAllPdfs($intrigue_pdfs, $intrigueActor) {
    foreach($intrigue_pdfs as $intrigue_pdf) {
        echo "<a href='view_intrigue_pdf.php?id=$intrigue_pdf->Id' target='_blank'>$intrigue_pdf->Filename</a>";
        echo "<br>";
    }
    
}

function printAllLetters($letters, $intrigueActor) {
    foreach($letters as $letter) {
        echo "Från: $letter->Signature, Till: $letter->Recipient, ".mb_strimwidth(str_replace('\n', '<br>', $letter->Message), 0, 50, '...');
        echo "<br>";
    }
    
}


function printAllTelegrams($telegrams, $intrigueActor) {
    foreach($telegrams as $telegram) {
        echo "$telegram->Deliverytime, Från: $telegram->Sender, Till: $telegram->Reciever, ".mb_strimwidth(str_replace('\n', '<br>', $telegram->Message), 0, 50, '...');
        echo "<br>";
    }
    
}

function printKnownActor(IntrigueActor $knownIntrigueActor, $intrigueActor) {
    if (!empty($knownIntrigueActor->GroupId)) {
        $group=$knownIntrigueActor->getGroup();
        echo "<li style='display:table-cell; width:19%;'>";
        echo "<div class='name'>$group->Name</div>";
        echo "<div>Grupp</div>";
        echo "</div>";
        echo "<div align='right'>";
        echo "</div>";
        echo "</li>";
        
    } else {
        $role = $knownIntrigueActor->getRole();
        echo "<li style='display:table-cell; width:19%;'>";
        echo "<div class='name'>$role->Name</div>";
        $role_group = $role->getGroup();
        if (!empty($role_group)) {
            echo "<div>$role_group->Name</div>";
        }
        
        if ($role->hasImage()) {
            echo "<img src='../includes/display_image.php?id=$role->ImageId'/>\n";
        }
        echo "<div align='right'>";
        echo "</div>";
    }
}

function printAllKnownActors($known_actors, $intrigue_actor) {
    global $cols;
    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
    $temp=0;
    foreach($known_actors as $known_actor) {
        $ka = $known_actor->getKnownIntrigueActor();
        printKnownActor($ka, $intrigue_actor);
        $temp++;
        if($temp==$cols)
        {
            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
            $temp=0;
        }
    }
    echo "</ul>";
}




include 'navigation.php';
?>
<style>
th, td {
  border-style:solid;
  border-color: #d4d4d4;
}
</style>

    <div class="content">
        <h1>Intrigspår: <?php echo "$intrigue->Number. $intrigue->Name" ?> på <?php echo $intrigue->getLarp()->Name?></h1>
<table width='100%'>
<tr><td width="10%">Aktuell</td><td><?php echo ja_nej($intrigue->isActive()); ?></td></tr>
<tr><td>Huvudintrig</td><td><?php echo ja_nej($intrigue->MainIntrigue); ?></td></tr>
<tr><td>Intrigtyp</td><td><?php echo commaStringFromArrayObject($intrigue->getIntriguetypes())?></td></tr>
<tr><td>Ansvarig</td><td>
	<?php 
	$responsiblePerson = $intrigue->getResponsiblePerson();
	if (isset($responsiblePerson)) echo $responsiblePerson->Name;
?></td></tr>
<tr><td>Text till alla aktörer</td><td><?php  echo nl2br($intrigue->CommonText); ?></td></tr>
<tr><td>Anteckningar</td><td><?php  echo nl2br($intrigue->Notes); ?></td></tr>
<tr><td>Aktörer<br>(Grupper och karaktärer som är inblandade i intrigen)</td>
<td>

<div class='container'>
<ul class='image-gallery' style='display:table; border-spacing:5px;'>
	<?php 
	$groupActors = $intrigue->getAllGroupActors();
	$temp=0;
	foreach ($groupActors as $groupActor) {
	    $group = $groupActor->getGroup();
	    echo "<li style='display:table-cell; width:19%;'>";
	    echo "<div class='name'>$group->Name</div>";
	    echo "<div>Grupp</div>";
	    $actor_intrigues = $groupActor->getAllIntrigues();
	    foreach ($actor_intrigues as $actor_intrigue) {
	        if ($actor_intrigue->Id != $intrigue->Id) {
	            echo "<div><a href='view_previous_intrigue.php?Id=$actor_intrigue->Id'>$actor_intrigue->Number. $actor_intrigue->Name</a></div>";
	        }
	    }
	    if ($group->hasImage()) {
	        echo "<img src='../includes/display_image.php?id=$group->ImageId'/>\n";
	    }
	    echo "<div align='right'>";
	    if (!$groupActor->isAtLARP()) echo "<i class='fa-solid fa-bed' title='Inte anmäld till lajvet'></i> ";
	    echo "</div>";
	    echo "</li>";
	    $temp++;
	    if($temp==$cols)
	    {
	        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	        $temp=0;
	    }
	}
	?>
</ul>	
<ul class='image-gallery' style='display:table; border-spacing:5px;'>
	<?php 
	$roleActors = $intrigue->getAllRoleActors();
	$temp=0;
	foreach ($roleActors as $roleActor) {
	    $role = $roleActor->getRole();
	    echo "<li style='display:table-cell; width:19%;'>";
	    echo "<div class='name'>$role->Name</div>";
	    $role_group = $role->getGroup();
	    if (!empty($role_group)) {
	        echo "<div>$role_group->Name</div>";
	    }
	    $actor_intrigues = $roleActor->getAllIntrigues();
	    foreach ($actor_intrigues as $actor_intrigue) {
	        if ($actor_intrigue->Id != $intrigue->Id) {
	           echo "<div><a href='view_previous_intrigue.php?Id=$actor_intrigue->Id'>$actor_intrigue->Number. $actor_intrigue->Name</a></div>";
	        }
	    }
	    if ($role->hasImage()) {
	        echo "<img src='../includes/display_image.php?id=$role->ImageId'/>\n";
	    }
	    echo "<div align='right'>";
	    if (!$roleActor->isAtLARP()) echo "<i class='fa-solid fa-bed' title='Inte anmäld till lajvet'></i> ";
	    echo "</div>";
	    echo "</li>";
	    $temp++;
	    if($temp==$cols)
	    {
	        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	        $temp=0;
	    }
	}
	?>


</ul>
	</td>
	</tr>
<tr><td>Rekvisita</td><td>
<ul class='image-gallery' style='display:table; border-spacing:5px;'>
	<?php 
	$intrigue_props = $intrigue->getAllProps();
	$temp=0;
	foreach ($intrigue_props as $intrigue_prop) {
	    $prop = $intrigue_prop->getProp();
	    echo "<li style='display:table-cell; width:19%;'>\n";
	    echo "<div class='name'>$prop->Name</div>\n";
	    $prop_intrigues = $intrigue_prop->getAllIntrigues();
	    foreach ($prop_intrigues as $prop_intrigue) {
	        if ($prop_intrigue->Id != $intrigue->Id) {
	            echo "<div><a href='view_previous_intrigue.php?Id=$prop_intrigue->Id'>Intrig: $prop_intrigue->Number. $prop_intrigue->Name</a></div>";
	        }
	    }
	    if ($prop->hasImage()) {
	        echo "<img width='100' src='../includes/display_image.php?id=$prop->ImageId'/>\n";
	    }
	    echo "</li>\n";
	    $temp++;
	    if($temp==$cols)
	    {
	        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	        $temp=0;
	    }
	}
    ?>

</ul>
</td></tr>
<?php if ($larp->hasTelegrams() || $larp->hasLetters()) {?>

<tr><td>Meddelanden</td><td>
<?php 
$intrigue_letters = $intrigue->getAllLetters();
foreach ($intrigue_letters as $intrigue_letter) {
    $letter=$intrigue_letter->getLetter();
    echo "Från: $letter->Signature, Till: $letter->Recipient, ".str_replace('\n', '<br>', $letter->Message);

    $letter_intrigues = $intrigue_letter->getAllIntrigues();
    foreach ($letter_intrigues as $letter_intrigue) {
        if ($letter_intrigue->Id != $intrigue->Id) {
            echo "<br>&nbsp;&nbsp;&nbsp;<a href='view_previous_intrigue.php?Id=$letter_intrigue->Id'>Intrig: $letter_intrigue->Number. $letter_intrigue->Name</a>";
        }
    }
    
    echo "<br><br>"; 
}
$intrigue_telegrams = $intrigue->getAllTelegrams();
foreach ($intrigue_telegrams as $intrigue_telegram) {
    $telegram=$intrigue_telegram->getTelegram();
    echo "$telegram->Deliverytime, Från: $telegram->Sender, Till: $telegram->Reciever, ".str_replace('\n', '<br>', $telegram->Message);
    $telegram_intrigues = $intrigue_telegram->getAllIntrigues();
    foreach ($telegram_intrigues as $telegram_intrigue) {
        if ($telegram_intrigue->Id != $intrigue->Id) {
            echo "<br>&nbsp;&nbsp;&nbsp;<a href='view_previous_intrigue.php?Id=$telegram_intrigue->Id'>Intrig: $telegram_intrigue->Number. $telegram_intrigue->Name</a>";
        }
    }
    echo "<br><br>";
}


?>
</td></tr>
<?php }?>

<tr><td>PDF</td><td>
<?php 
$intrigue_pdfs = $intrigue->getAllPdf();
foreach ($intrigue_pdfs as $intrigue_pdf) {
    echo "<a href='view_intrigue_pdf.php?id=$intrigue_pdf->Id' target='_blank'>$intrigue_pdf->Filename</a>";
    echo "<br>"; 
}
?>
</td></tr>

<tr><td>Länkade intriger</td><td>
<?php 
$intrigue_relations = $intrigue->getAllIntrigueRelations();
foreach ($intrigue_relations as $intrigue_relation) {
    echo "<a href='view_previous_intrigue.php?Id=$intrigue_relation->Id'>Intrig: $intrigue_relation->Number. $intrigue_relation->Name</a>";
    if (!$intrigue_relation->isActive()) echo " (inte aktuell)";
    echo "<br>";
}
?>
</td></tr>
<tr><td>Händelser</td><td>
<?php 
$timeline_array = $intrigue->getTimeline();
foreach ($timeline_array as $timeline) {
    echo substr($timeline->When,0,16)." $timeline->Description";
    echo "<br>";
}
?>
</td></tr>

<?php if ($larp->hasRumours()) {?>
<tr><td>Rykten</td><td>
<?php 
$rumour_array = $intrigue->getRumours();
foreach ($rumour_array as $rumour) {
    $knows_rumour = $rumour->getKnows();
    $knows_rumour_array = array();
    foreach($knows_rumour as $knows) $knows_rumour_array[] = $knows->getName();
    $knows_txt = "Vet om: ". implode(", ", $knows_rumour_array);
    
    
    
    echo $rumour->Text;
    echo "<br>";
    echo $knows_txt."<br><br>";
}
?>
</td></tr>
<?php }?>
</table>

<?php 

foreach ($groupActors as $groupActor) {
    $group = $groupActor->getGroup();
    if ($groupActor->isAtLARP()) printActorIntrigue($groupActor, $group->Name);

}

foreach ($roleActors as $roleActor) {
    $role = $roleActor->getRole();
    if ($roleActor->isAtLARP()) printActorIntrigue($roleActor, $role->Name);
    
}


?>

<script src="../javascript/saveIntrigueText_ajax.js"></script>
       