<?php
include_once 'header.php';

//print_r($_POST);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $intrigue = Intrigue::newFromArray($_POST);
        $intrigue->create();
    } elseif ($operation == 'update') {
        $intrigue=Intrigue::loadById($_POST['Id']);
        $intrigue->setValuesByArray($_POST);
        $intrigue->deleteAllIntrigueTypes();
        $intrigue->saveAllIntrigueTypes($_POST['IntrigueTypeId']);
        $intrigue->update();
    } elseif ($operation == "add_intrigue_actor_role") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        $intrigue->addRoleActors($_POST['RoleId']);
    } elseif ($operation == "exhange_intrigue_actor_role") {
        $intrigueActor=IntrigueActor::loadById($_POST['Id']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->RoleId = $_POST['RoleId'];
        $intrigueActor->GroupId=null;
        $intrigueActor->update();
    } elseif ($operation == "exhange_intrigue_actor_group") {
        $intrigueActor=IntrigueActor::loadById($_POST['Id']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->GroupId = $_POST['GroupId'];
        $intrigueActor->RoleId=null;
        $intrigueActor->update();
        
    } elseif ($operation == "add_intrigue_actor_group") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        $intrigue->addGroupActors($_POST['GroupId']);
    } elseif ($operation == "add_intrigue_prop") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        $intrigue->addProps($_POST['PropId']);
    } elseif ($operation == "add_intrigue_npc") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        $intrigue->addNPCs($_POST['NPCId']);
    } elseif ($operation == "add_intrigue_message") {
        $intrigue=Intrigue::loadById($_POST['Id']);
        if (isset($_POST['TelegramId'])) $intrigue->addTelegrams($_POST['TelegramId']);
        if (isset($_POST['LetterId'])) $intrigue->addLetters($_POST['LetterId']);
    } elseif ($operation == "update_intrigue_actor") {
        $intrigueActor=IntrigueActor::loadById($_POST['IntrigueActorId']);
        $intrigue=$intrigueActor->getIntrigue();
        $intrigueActor->IntrigueText = $_POST['IntrigueText'];
        $intrigueActor->Offinfo = $_POST['Offinfo'];
        $intrigueActor->update();      
    } else {
        $intrigue=Intrigue::loadById($_POST['Id']);
    }

    
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['Id'])) $intrigue=Intrigue::loadById($_GET['Id']);
    $operation = "";
    if (isset($_GET['operation']))
        $operation = $_GET['operation'];
    
    if ($operation == "remove_intrigueactor") {
        IntrigueActor::delete($_GET['IntrigueActorId']);
    } elseif ($operation == "remove_prop") {
        Intrigue_Prop::delete($_GET['IntriguePropId']);
    } elseif ($operation == "remove_npc") {
        Intrigue_NPC::delete($_GET['IntrigueNPCId']);
    } elseif ($operation == "remove_letter") {
        Intrigue_Letter::delete($_GET['IntrigueLetterId']);
    } elseif ($operation == "remove_telegram") {
        Intrigue_Telegram::delete($_GET['IntrigueTelegramId']);
    }
}

function printActorIntrigue(IntrigueActor $intrgueActor, $name) {
    echo "<h2>Intrig för $name <a href='actor_intrigue_form.php?IntrigueActorId=$intrgueActor->Id&name=$name'><i class='fa-solid fa-pen'></i></a></h2>";
    echo "<table width='100%''>";
    
    echo "<tr><td width='10%'>Intrigtext</td><td>".htmlspecialchars($intrgueActor->IntrigueText)."</td></tr>";
    echo "<tr><td>Off-info</td><td>".htmlspecialchars($intrgueActor->OffInfo)."</td></tr>";
    echo "<tr><td>Ska ha vid incheck</td><td></td></tr>";
    echo "<tr><td>Rekvisita aktören känner till</td><td></td></tr>";
    echo "<tr><td>Karaktärer aktören känner till</td><td></td></tr>";
    echo "</table>";
    
}

function printKnownActor(IntrigueActor $intrigueActor) {
    if (!empty($intrigueActor->GroupId)) {
        $group=$intrigueActor->getGroup();
        echo "<li style='display:table-cell; width:19%;'>";
        echo "<div class='name'>$group->Name</div>";
        echo "<div>Grupp</div>";
        echo "</div>";
        echo "</li>";
        
    } else {
        $role = $intrigueActor->getRole();
        echo "<li style='display:table-cell; width:19%;'>";
        echo "<div class='name'>$role->Name</div>";
        $role_group = $role->getGroup();
        if (!empty($role_group)) {
            echo "<div>$role_group->Name</div>";
        }
        
        if ($role->hasImage()) {
            $image = Image::loadById($role->ImageId);
            if (!is_null($image)) {
                
                echo "<img src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
            }
        }
        echo "</li>";
    }
}

if (empty($intrigue)) {
    header('Location: intrigue_admin.php');
    exit;
    
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
        <h1>Intrig: <?php echo "$intrigue->Number. $intrigue->Name" ?> <a href="intrigue_form.php?operation=update&id=<?php echo $intrigue->Id ?>"><i class='fa-solid fa-pen'></i></a></h1>
<table width='100%'>
<tr><td width='10%'>Nummer</td><td><?php echo $intrigue->Number ?></td></tr>
<tr><td>Intrignamn</td><td><?php echo $intrigue->Name ?></td></tr>
<tr><td>Aktuell</td><td><?php echo ja_nej($intrigue->Active); ?></td></tr>
<tr><td>Huvudintrig</td><td><?php echo ja_nej($intrigue->MainIntrigue); ?></td></tr>
<tr><td>Intrigtyp</td><td><?php echo commaStringFromArrayObject($intrigue->getIntriguetypes())?></td></tr>
<tr><td>Anteckningar</td><td><?php  echo $intrigue->Notes ?></td></tr>
<tr><td>Aktörer<br>(Grupper och karaktärer som är inblandade i intrigen)</td>
<td>

<div class='container'>
<a href="choose_group.php?operation=add_intrigue_actor_group&Id=<?php echo $intrigue->Id?>"><i class='fa-solid fa-plus' title="Lägg till grupp"></i></a>
<ul class='image-gallery' style='display:table; border-spacing:5px;'>
	<?php 
	$groupActors = $intrigue->getAllGroupActors();
	$temp=0;
	foreach ($groupActors as $groupActor) {
	    $group = $groupActor->getGroup();
	    echo "<li style='display:table-cell; width:19%;'>";
	    echo "<div class='name'><a href='../admin/view_group.php?id=$group->Id'>$group->Name</a></div>";
	    echo "<div>Grupp</div>";
	    echo "<div><a href=''>Intrig xx</a></div>";
	    echo "<div align='right'>";
	    echo "<a href='choose_group.php?operation=exhange_intrigue_actor_group&Id=$groupActor->Id?'><i class='fa-solid fa-rotate' title='Byt ut'></i></a> ";
	    echo "<a href='view_intrigue.php?operation=remove_intrigueactor&IntrigueActorId=$groupActor->Id&Id=$intrigue->Id'";
	    if (!empty($groupActor->IntrigueText)) echo " onclick=:\'return confirm('Det finns en skriven intrigtext. Vill du ta bort gruppen i alla fall?')\' ";
        echo "><i class='fa-solid fa-xmark' title='Ta bort grupp'></i></a>";
	    echo "</div>";
	    echo "</li>";
	    $temp++;
	    if($temp==5)
	    {
	        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	        $temp=0;
	    }
	}
	?>
</ul>	
<a href="choose_role.php?operation=add_intrigue_actor_role&Id=<?php echo $intrigue->Id?>"><i class='fa-solid fa-plus' title="Lägg till karaktär"></i></a>
<ul class='image-gallery' style='display:table; border-spacing:5px;'>
	<?php 
	$roleActors = $intrigue->getAllRoleActors();
	$temp=0;
	foreach ($roleActors as $roleActor) {
	    $role = $roleActor->getRole();
	    echo "<li style='display:table-cell; width:19%;'>";
	    echo "<div class='name'><a href='../admin/view_role.php?id=$role->Id'>$role->Name</a></div>";
	    $role_group = $role->getGroup();
	    if (!empty($role_group)) {
	        echo "<div>$role_group->Name</div>";
	    }
	    echo "<div><a href=''>Intrig xx</a></div>";

	    if ($role->hasImage()) {
	        $image = Image::loadById($role->ImageId);
	        if (!is_null($image)) {
	            
	            echo "<img src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
	        }
	    }
	    echo "<div align='right'>";
	    echo "<a href='choose_role.php?operation=exhange_intrigue_actor_role&Id=$roleActor->Id'><i class='fa-solid fa-rotate' title='Byt ut'></i></a> ";
	    echo "<a href='view_intrigue.php?operation=remove_intrigueactor&IntrigueActorId=$roleActor->Id&Id=$intrigue->Id'";
	    if (!empty($roleActor->IntrigueText)) echo " onclick=:\'return confirm('Det finns en skriven intrigtext. Vill du ta bort karaktären i alla fall?')\' ";
	    echo "><i class='fa-solid fa-xmark' title='Ta bort karaktär'></i></a>";
	    echo "</div>";
	    echo "</li>";
	    $temp++;
	    if($temp==5)
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
<a href="choose_prop.php?operation=add_intrigue_prop&Id=<?php echo $intrigue->Id?>"><i class='fa-solid fa-plus' title="Lägg till rekvisita"></i></a>

<ul class='image-gallery' style='display:table; border-spacing:5px;'>
	<?php 
	$intrigue_props = $intrigue->getAllProps();
	$temp=0;
	foreach ($intrigue_props as $intrigue_prop) {
	    $prop = $intrigue_prop->getProp();
	    echo "<li style='display:table-cell; width:19%;'>\n";
	    echo "<div class='name'>$prop->Name</div>\n";
	    if ($prop->hasImage()) {
	        $image = Image::loadById($prop->ImageId);
	        echo "<td><img width=100 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
	    }
	    echo "<div align='right'>";
	    echo "<a href='view_intrigue.php?operation=remove_prop&IntriguePropId=$intrigue_prop->Id&Id=$intrigue->Id'>";
	    echo "<i class='fa-solid fa-xmark' title='Ta bort karaktär'></i></a>";
	    echo "</div>";
	    echo "</li>\n";
	    $temp++;
	    if($temp==5)
	    {
	        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	        $temp=0;
	    }
	}
    ?>

</ul>
</td></tr>
<tr><td>NPC</td>
<td>
<a href="choose_npc.php?operation=add_intrigue_npc&Id=<?php echo $intrigue->Id?>"><i class='fa-solid fa-plus' title="Lägg till NPC"></i></a>
<ul class='image-gallery' style='display:table; border-spacing:5px;'>
	<?php 
	$intrigue_npcs = $intrigue->getAllNPCs();
	$temp=0;
	foreach ($intrigue_npcs as $intrigue_npc) {
	    $npc = $intrigue_npc->getNPC();
	    echo "<li style='display:table-cell; width:19%;'>\n";
	    echo "<div class='name'>$npc->Name</div>\n";
	    $npc_group = $npc->getNPCGroup();
	    if (!empty($npc_group)) {
	        echo "<div>$npc_group->Name</div>";
	    }
	    if ($npc->IsAssigned()) {
	        $person = $npc->getPerson();
	        echo "<div>Spelas av $person->Name</div>"; 
	    } else {
	        echo "Spelas inte";
	    }
	    
	    
	    if ($npc->hasImage()) {
	        $image = Image::loadById($npc->ImageId);
	        echo "<td><img width=100 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
	    }
	    echo "<div align='right'>";
	    echo "<a href='view_intrigue.php?operation=remove_npc&IntrigueNPCId=$intrigue_npc->Id&Id=$intrigue->Id'>";
	    echo "<i class='fa-solid fa-xmark' title='Ta bort NPC'></i></a>";
	    echo "</div>";
	    echo "</li>\n";
	    $temp++;
	    if($temp==5)
	    {
	        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	        $temp=0;
	    }
	}
    ?>

</td></tr>
<tr><td>Telegram & Brev</td><td>
<a href="choose_telegram_letter.php?operation=add_intrigue_message&Id=<?php echo $intrigue->Id?>"><i class='fa-solid fa-plus' title="Lägg till telegram och brev"></i></a>
<br>
<?php 
$intrigue_letters = $intrigue->getAllLetters();
foreach ($intrigue_letters as $intrigue_letter) {
    $letter=$intrigue_letter->getLetter();
    echo "Från: $letter->Signature, Till: $letter->Recipient, ".mb_strimwidth(str_replace('\n', '<br>', $letter->Message), 0, 50, '...');
    echo "<a href='view_intrigue.php?operation=remove_letter&IntrigueLetterId=$intrigue_letter->Id&Id=$intrigue->Id'><i class='fa-solid fa-xmark' title='Ta bort NPC'></i></a>";
    echo "<br>"; 
}
$intrigue_telegrams = $intrigue->getAllTelegrams();
foreach ($intrigue_telegrams as $intrigue_telegram) {
    $telegram=$intrigue_telegram->getTelegram();
    echo "$telegram->Deliverytime, Från: $telegram->Sender, Till: $telegram->Reciever, ".mb_strimwidth(str_replace('\n', '<br>', $telegram->Message), 0, 50, '...');
    echo "<a href='view_intrigue.php?operation=remove_telegram&IntrigueTelegramId=$intrigue_telegram->Id&Id=$intrigue->Id'><i class='fa-solid fa-xmark' title='Ta bort NPC'></i></a>";
    echo "<br>";
}


?>
</td></tr>
<tr><td>Länkade intriger</td><td>
<a href="choose_intrigue.php?operation=add_intrigue_link&Id=<?php echo $intrigue->Id?>"><i class='fa-solid fa-plus' title="Lägg till länk till annan intrig"></i></a>

</td></tr>

</table>

<?php 

foreach ($groupActors as $groupActor) {
    $group = $groupActor->getGroup();
    printActorIntrigue($groupActor, $group->Name);

}

foreach ($roleActors as $roleActor) {
    $role = $roleActor->getRole();
    printActorIntrigue($roleActor, $role->Name);
    
}


?>
        