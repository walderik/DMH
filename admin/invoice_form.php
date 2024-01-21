<?php
include_once 'header.php';

    $invoice = Invoice::newWithDefault();;
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $invoice = Invoice::loadById($_GET['id']);            
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $invoice;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($invoice->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $invoice->Id;
                break;
            case "action":
                if (is_null($invoice->Id)) {
                    $output = "Lägg till";
                    break;
                }
                $output = "Uppdatera";
                break;
        }

        echo $output;
    }
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
    }
    else {
        $referer = "";
    }
    
    include 'navigation.php';
    ?>
    
<style>

img {
  float: right;
}
</style>


    <div class="content"> 
    <h1><?php echo default_value('action');?> faktura <a href="prop_admin.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
    
   
	<form action="logic/prop_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
    			<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		
		<table>
			<tr>
				<td><label for="Name">Till</label></td>
				<td><input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($invoice->Name); ?>" size="100" maxlength="250" required></td>

			</tr>
			<tr>

				<td><label for="Description">Beskrivning</label></td>
				<td><input type="text" id="Description" name="Description"
					 value="<?php echo htmlspecialchars($invoice->Description); ?>" size="100" maxlength="250" ></td>
			</tr>
			<tr>

				<td><label for="StorageLocation">Betalningsdatum</label></td>
				<td><input type="date" id="DueDate" name="DueDate" value="<?php echo $invoice->DueDate ?>" required></td>
			</tr>
			<tr>
				<td>
					<label for="Text">Kontaktperson</label>
				</td>
				<td>
					
					<?php if ($operation=='update') {
					    echo "<form id='concerns_role' action='choose_role.php' method='post'></form>";
					    echo "<input form='concerns_role' type='hidden' id='id' name='id' value='$rumour->Id'>";
					    echo "<input form='concerns_role' type='hidden' id='2ndReferer' name='2ndReferer' value='$referer'>";
					    echo "<input form='concerns_role' type='hidden' id='operation' name='operation' value='add_concerns_role'>";
					    echo "<button form='concerns_role' class='invisible' type='submit'><i class='fa-solid fa-plus' title='Lägg till karaktär(er) ryktet handlar om'></i><i class='fa-solid fa-user' title='Lägg till karaktär(er) ryktet handlar om'></i></button>";
					    
					} elseif (isset($roleId)) {
					    $role = Role::loadById($roleId);
					    echo "<input form='main' type='hidden' id='RoleId' name='RoleId' value='$roleId'>";					    
					    echo $role->Name;
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
			
			
			
		</table>

		<input id="submit_button" type="submit" value="<?php default_value('action'); ?>">
	</form>
	</div>
    </body>

</html>