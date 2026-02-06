<?php

require 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['RoleId'])) {
        $role = Role::loadById($_GET['RoleId']);
    } else {
        header('Location: ../index.php');
        exit;
    }
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}


include 'navigation.php';
include 'npc_navigation.php';

?>

	<div class="content">

		<h1>Gör om <?php  echo $role->Name?> till spelarkaraktär</h1>
		<p>Detta kommer att göra om karaktären till en spelare så att den inte längre är NPC.</p>
		<form action="logic/turn_into_pc_save.php" method="post">
    		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">

		
		<table>
 			<tr><td valign="top" class="header">Namn</td>
 			<td><?php echo $role->Name ?></td>
		<?php 
		if ($role->hasImage()) {
		    
		    $image = Image::loadById($role->ImageId);
		    echo "<td rowspan='20' valign='top'>";
		    echo "<img width='300' src='../includes/display_image.php?id=$role->ImageId'/>\n";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    echo "</td>";
		}
		?>
 			
 			</tr>
			<tr><td valign="top" class="header">Vem ska få <?php echo $role->Name?>?</td>
			    <td>
			    <?php 
			        $persons=Person::getAllRegistered($current_larp, false);
			        echo selectionDropDownByArray("PersonId", $persons, true);
			    ?>
			    </td></tr>



		</table>		
			<input type="submit" value="Gör om <?php  echo $role->Name?> till spelarkaraktär">

			</form>

	</div>


</body>
</html>
