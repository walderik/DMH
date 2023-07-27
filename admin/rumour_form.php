<?php
include_once 'header.php';

$rumour = Rumour::newWithDefault();;
$rumour->Approved = 1;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'new') {
        if (isset($_GET['IntrigueId'])) $rumour->IntrigueId = $_GET['IntrigueId'];
    } elseif ($operation == 'update') {
        $rumour = Rumour::loadById($_GET['id']);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = "new";
    $referer = $_POST['Referer'];
    if (isset($_POST['operation'])) $operation = $_POST['operation'];
    $rumour = Rumour::loadById($_POST['id']);
    
    if ($operation=='delete_concern') {
        Rumour_concerns::delete($_POST['concernId']);
        $operation = 'update';
    } elseif ($operation == 'delete_knows') {
        Rumour_knows::delete($_POST['knowsId']);
        $operation = 'update';
    } elseif ($operation == 'add_concerns_group') {
        if (isset($_POST['GroupId'])) $rumour->addGroupConcerns($_POST['GroupId']);
        $operation = 'update';
    } elseif ($operation == 'add_concerns_role') {
        if (isset($_POST['RoleId'])) $rumour->addRoleConcerns($_POST['RoleId']);
        $operation = 'update';
    } elseif ($operation == 'add_knows_group') {
        if (isset($_POST['GroupId'])) $rumour->addGroupKnows($_POST['GroupId']);
        $operation = 'update';
    } elseif ($operation == 'add_knows_role') {
        if (isset($_POST['RoleId'])) $rumour->addRoleKnows($_POST['RoleId']);
        $operation = 'update';
    }
    
}



function default_value($field) {
    GLOBAL $rumour;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($rumour->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "id":
            $output = $rumour->Id;
            break;
        case "action":
            if (is_null($rumour->Id)) {
                $output = "Lägg till";
                break;
            }
            $output = "Uppdatera";
            break;
    }
    
    echo $output;
}

if (isset($_POST['2ndReferer'])) {
    $referer = $_POST['2ndReferer'];
} elseif (isset($_POST['Referer'])) {
    $referer = $_POST['Referer'];
} elseif (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
} else {
    $referer = "rumour_admin.php";
}

$intrigue_array = Intrigue::allByLARP($current_larp);

include 'navigation.php';
?>
    

    <div class="content"> 
    <h1><?php echo default_value('action');?> rykte <a href="<?php echo $referer?>"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
	<form id="main" action="rumour_admin.php" method="post"></form>
		<input form='main' type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input form='main' type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
		<input form='main' type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr>

				<td><label for="Text">Text</label></td>
				<td><textarea form='main' id="Text" name="Text" rows="4" cols="100" maxlength="2000"
					 required><?php echo htmlspecialchars($rumour->Text); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="Notes">Anteckningar<br>(om ryktet inte är godkänt kan<br>deltagaren som skapade ryktet<br>se anteckningarna)</label></td>
				<td><textarea form='main' id="Notes" name="Notes" rows="4" cols="100" maxlength="2000"
					 ><?php echo htmlspecialchars($rumour->Notes); ?></textarea></td>
			</tr>
			<tr>

				<td><label for="Approved">Godkänt</label></td>
				<td>
				<input form='main' type="radio" id="Approved_yes" name="Approved" value="1" <?php if ($rumour->Approved == 1) echo 'checked="checked"'?>> 
    			<label for="Approved_yes">Ja</label><br> 
    			<input form='main' type="radio" id="Approved_no" name="Approved" value="0" <?php if ($rumour->Approved == 0) echo 'checked="checked"'?>> 
    			<label for="Approved_no">Nej</label>
				</td>
			</tr>
			<tr>

				<td><label for="IntrigueId">Kopplad intrig</label></td>
				
				<td><?php  selectionDropDownByArray("IntrigueId", $intrigue_array, false, $rumour->IntrigueId, "form='main'");?></td>
			</tr>
			<tr>

				<td><label for="Text">Vem/vilka handlar<br>ryktet om?</label></td>
				<td>
					
					<?php if ($operation=='update') {
					    echo "<form id='concerns_group' action='choose_group.php' method='post'></form>";
					    echo "<input form='concerns_group' type='hidden' id='id' name='id' value='$rumour->Id'>";
					    echo "<input form='concerns_group' type='hidden' id='2ndReferer' name='2ndReferer' value='$referer'>";
					    echo "<input form='concerns_group' type='hidden' id='operation' name='operation' value='add_concerns_group'>";
					    echo "<button form='concerns_group' class='invisible' type='submit'><i class='fa-solid fa-plus' title='Lägg till grupp(er) ryktet handlar om'></i><i class='fa-solid fa-users' title='Lägg till grupp(er) ryktet handlar om'></i></button>";
					    echo "<br>";
					    
					    echo "<form id='concerns_role' action='choose_role.php' method='post'></form>";
					    echo "<input form='concerns_role' type='hidden' id='id' name='id' value='$rumour->Id'>";
					    echo "<input form='concerns_role' type='hidden' id='2ndReferer' name='2ndReferer' value='$referer'>";
					    echo "<input form='concerns_role' type='hidden' id='operation' name='operation' value='add_concerns_role'>";
					    echo "<button form='concerns_role' class='invisible' type='submit'><i class='fa-solid fa-plus' title='Lägg till karaktär(er) ryktet handlar om'></i><i class='fa-solid fa-user' title='Lägg till karaktär(er) ryktet handlar om'></i></button>";
					    
					} else {
					  echo "<strong>När ryktet är skapat, lägga in vilka det handlar om.</strong>";
					}?>
					
					<?php 
					$concerns_array = $rumour->getConcerns();
					foreach ($concerns_array as $concern) {
					    echo "<form id='delete_concern_$concern->Id' action='rumour_form.php' method='post'>";
					    echo $concern->getViewLink();
					    echo " ";
					    echo "<input form='delete_concern_$concern->Id' type='hidden' id='operation' name='operation' value='delete_concern'>";
					    echo "<input form='delete_concern_$concern->Id' type='hidden' id='id' name='id' value='$rumour->Id'>";
					    echo "<input form='delete_concern_$concern->Id' type='hidden' id='Referer' name='Referer' value='$referer'>";
					    echo "<input form='delete_concern_$concern->Id' type='hidden' id='concernId' name='concernId' value='$concern->Id'>";
					    echo "<button form='delete_concern_$concern->Id' class='invisible' type='submit'><i class='fa-solid fa-trash' title='Ta bort från rykte'></i></button>";
					    echo "</form>";
					}
					?>
				</td>
				</tr>
				<tr>
				<td><label for="Text">Vem/vilka vet<br>om ryktet?</label></td>
				<td>
					//TODO slumpmässig fördelning av ryktet
					<?php if ($operation=='update') {
					    echo "<form id='knows_group' action='choose_group.php' method='post'></form>";
					    echo "<input form='knows_group' type='hidden' id='id' name='id' value='$rumour->Id'>";
					    echo "<input form='knows_group' type='hidden' id='2ndReferer' name='2ndReferer' value='$referer'>";
					    echo "<input form='knows_group' type='hidden' id='operation' name='operation' value='add_knows_group'>";
					    echo "<button form='knows_group' class='invisible' type='submit'><i class='fa-solid fa-plus' title='Lägg till grupp(er) som känner till ryktet'></i><i class='fa-solid fa-users' title='Lägg till grupp(er) som känner till ryktet'></i></button>";
					    echo "<br>";
					    
					    echo "<form id='knows_role' action='choose_role.php' method='post'></form>";
					    echo "<input form='knows_role' type='hidden' id='id' name='id' value='$rumour->Id'>";
					    echo "<input form='knows_role' type='hidden' id='2ndReferer' name='2ndReferer' value='$referer'>";
					    echo "<input form='knows_role' type='hidden' id='operation' name='operation' value='add_knows_role'>";
					    echo "<button form='knows_role' class='invisible' type='submit'><i class='fa-solid fa-plus' title='Lägg till karaktär(er) som känner till ryktet'></i><i class='fa-solid fa-user' title='Lägg till karaktär(er) som känner till ryktet'></i></button>";
					    
					    
					} else {
					  echo "<strong>När ryktet är skapat, kan du sprida det.</strong>";
					}?>
					
					<?php 
					$knows_array = $rumour->getKnows();
					foreach ($knows_array as $knows) {
					    echo "<form id='delete_knows_$knows->Id' action='rumour_form.php' method='post'>";
					    echo $knows->getViewLink();
					    echo " ";
					    echo "<input form='delete_knows_$knows->Id' type='hidden' id='operation' name='operation' value='delete_knows'>";
					    echo "<input form='delete_knows_$knows->Id' type='hidden' id='id' name='id' value='$rumour->Id'>";
					    echo "<input form='delete_knows_$knows->Id' type='hidden' id='Referer' name='Referer' value='$referer'>";
					    echo "<input form='delete_knows_$knows->Id' type='hidden' id='knowsId' name='knowsId' value='$knows->Id'>";
					    echo "<button form='delete_knows_$knows->Id' class='invisible' type='submit'><i class='fa-solid fa-trash' title='Ta bort från rykte'></i></button>";
					    echo "</form>";
					}
					?>
				</td>
			</tr>
 
 
 
		</table>
		<input form='main' id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</div>
    </body>

</html>