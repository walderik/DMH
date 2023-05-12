<?php
include_once 'header.php';


echo "<br><br><br><br>";
print_r($_POST);

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $prop = Prop::loadById($_GET['id']);

}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $prop = Prop::loadById($_POST['id']);

    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];
        echo "<br>Operation: $operation<br>";
        if ($operation == "remove_owner") {
            $prop->RoleId = null;
            $prop->GroupId = null;
            $prop->update();
        } else if ($operation == "set_prop_owner_role") {
            echo "Sätter ägare till ".$_POST['RoleId'];
            $prop->RoleId = $_POST['RoleId'];
            $prop->GroupId = null;
            $prop->update();
        } else if ($operation == "set_prop_owner_group") {
            $prop->RoleId = null;
            $prop->GroupId = $_POST['GroupId'];
            $prop->update();
        }
    
    }
}



$owner_set = false;
$owner = "";
if (isset($prop->GroupId)) {
    $group = Group::loadById($prop->GroupId);
    $owner = $group->Name;
    $owner_set = true;
}
elseif (isset($prop->RoleId)) {
    $role = Role::loadById($prop->RoleId);
    $owner = $role->Name;
    $owner_set = true;
}

include 'navigation_subpage.php';
?>
    
<style>

img {
  float: right;
}
</style>


    <div class="content"> 
    <h1>Ändra ägare av rekvisita</h1>
    
   
        <?php 
            if ($prop->hasImage()) {
                $image = Image::loadById($prop->ImageId);
                echo "<td>";
                echo '<img src="data:image/jpeg;base64,'.base64_encode($image->file_data).'"/>';
                if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
                echo "</td>";
            }
            ?>
		
		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><?php echo $prop->Name; ?></td>

			</tr>
			<tr>

				<td><label for="Description">Beskrivning</label></td>
				<td><?php echo $prop->Description; ?></td>
			</tr>
			<tr>

				<td><label for="StorageLocation">Lagerplats</label></td>
				<td><?php echo $prop->StorageLocation; ?></td>
			</tr>
			<tr>
				<td><label for="Marking">Märkning</label></td>
				<td><?php echo $prop->Marking; ?></td>

			</tr>
			<tr>
				<td><label for="Properties">In-lajv egenskaper</label></td>
				<td><?php echo $prop->Properties; ?></td>

			</tr>
			<tr>
				<td><label for="Owner">Ägare</label></td>
				<td>
				<?php 
				if ($owner_set) {
				    //Det finns en ägare
				    echo $owner;
				    echo "</td><td>";
				    echo "<form action='prop_owner_form.php' method='post'>";
				    echo "<input type='hidden' id='operation' name='operation' value='remove_owner'>";
				    echo "<input type='hidden' id='id' name='id' value='$prop->Id'>";
				    echo "<input id='submit_button' type='submit' value='Ta bort ägare'>";
				    echo "</form>";
				}
				else {
				    //Det finns ingen ägare
				    echo "<form action='choose_role.php' method='post'>";
				    echo "<input type='hidden' id='operation' name='operation' value='set_prop_owner_role'>";				    
				    echo "<input type='hidden' id='id' name='id' value='$prop->Id'>";
				    echo "<input id='submit_button' type='submit' value='Välj en karaktär som ägare'>";
				    echo "</form>";
				    echo "</td><td>";
				    echo "<form action='choose_group.php' method='post'>";
				    echo "<input type='hidden' id='operation' name='operation' value='set_prop_owner_group'>";
				    echo "<input type='hidden' id='id' name='id' value='$prop->Id'>";
				    echo "<input id='submit_button' type='submit' value='Välj en grupp som ägare'>";
				    echo "</form>";
				    
				    
				}
				
				?>
				</td>

			</tr>
		</table>

	</div>
    </body>

</html>