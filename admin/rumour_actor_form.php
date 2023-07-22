<?php
include_once 'header.php';

print_r($_POST);

$operation = "";
$type = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $rumour = Rumour::loadById($_GET['id']);        
    }
    if (isset($_GET['type'])) {
        $type = $_GET['type'];
    }
        
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $operation = "";
    $referer = $_POST['Referer'];
    $rumour = Rumour::loadById($_POST['id']);
    if (isset($_POST['type'])) {
        $type = $_POST['type'];
    }
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];
    }
    if ($operation == 'add_group') {
        if ($type == 'concerns') $rumour->addConcernedGroup($_POST['GroupId']);
        elseif ($type == 'knows') $rumour->addKnowsGroup($_POST['GroupId']);
    } elseif ($operation == 'add_role') {
        if ($type == 'concerns') $rumour->addConcernedRole($_POST['RoleId']);
        elseif ($type == 'knows') $rumour->addKnowsRole($_POST['RoleId']);
    }  elseif ($operation=='delete') {
        if ($type == 'concerns') Rumour_concerns::delete($_POST['itemId']);
        elseif ($type == 'knows') Rumour_knows::delete($_POST['itemId']);
    }
    
}

if (empty($rumour)) {
    header('Location: rumour_admin.php');
    exit;
}
    
if ($type == 'concerns') {
    $headline = "Ändra vilka ryktet handlar om";
    $question = "Vem/vilka handlar<br>ryktet om?";
} elseif ($type == 'knows') {
   $headline = "Ändra vilka som känner till ryktet"; 
   $question = "Vem/vilka känner<br>till ryktet?";
}

include 'navigation.php';
?>
    

    <div class="content"> 
		<form action="rumour_form.php" method="post">
		<input type="hidden" id="operation" name="operation" value="update"> 
		<input type="hidden" id="id" name="id" value="<?php echo $rumour->Id; ?>">
		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<h1><?php echo $headline?> <button class='invisible'><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></button></h1>
		</form>
		<table>
			<tr>

				<td><label for="Text">Text</label></td>
				<td><?php echo nl2br(htmlspecialchars($rumour->Text)); ?></td>
			</tr>
			<tr>

				<td><label for="Text"><?php echo $question?></label></td>
				<td>
					<form action="rumour_actor_form.php" method="post">
				
					<input type="hidden" id="operation" name="operation" value="add_group"> 
					<input type="hidden" id="id" name="id" value="<?php echo $rumour->Id; ?>">
					<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
					<input type="hidden" id="type" name="type" value="<?php echo $type;?>">
					
					<?php 
					    
					    $groups = Group::getAllRegistered($current_larp);
					    selectionDropDownByArray("GroupId", $groups);
					    echo "<input id='submit_button' type='submit' value='Lägg till'>";
					    echo "<br><br>";
					    ?>
					</form>	
									    
					<form action="rumour_actor_form.php" method="post">
					<input type="hidden" id="operation" name="operation" value="add_role"> 
					<input type="hidden" id="id" name="id" value="<?php echo $rumour->Id; ?>">
					<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
					<input type="hidden" id="type" name="type" value="<?php echo $type;?>">
					    
					    
					    <?php 
					    $roles = Role::getAllRoles($current_larp);
					    selectionDropDownByArray("RoleId", $roles);
					    echo "<input id='submit_button' type='submit' value='Lägg till'>";
					    echo "<br><br>";
					    
                    ?>
					</form>
					
					<?php 
					if ($type =='concerns') $item_array = $rumour->getConcerns();
					if ($type =='knows') $item_array = $rumour->getKnows();
					foreach ($item_array as $item) {
					    echo "<form action='rumour_actor_form.php' method='post'>";
					    
					    echo $item->getViewLink();
					    echo " ";
					    echo "<input type='hidden' id='operation' name='operation' value='delete'>";
					    echo "<input type='hidden' id='id' name='id' value='$rumour->Id'>";
					    echo "<input type='hidden' id='Referer' name='Referer' value='$referer'>";
					    echo "<input type='hidden' id='type' name='type' value='$type'>";
					    echo "<input type='hidden' id='itemId' name='itemId' value='$item->Id'>";
					    echo "<button class='invisible' type='submit'><i class='fa-solid fa-trash' title='Ta bort från rykte'></i></button>";
					    echo "</form>";
					    //echo " <a href='rumour_actor_form.php?Referer=$referer&operation=delete&id=$rumour->Id&itemId=$item->Id&type=$type'><i class='fa-solid fa-trash' title='Ta bort från rykte'></i></a>";
					    echo "<br>";
					}
					?>
				</td>
			</tr>
 
 
 
		</table>

	</div>
    </body>

</html>