<?php
include_once 'header.php';




if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $intrigueActor = IntrigueActor::loadById($_GET['IntrigueActorId']);
    $intrigue=$intrigueActor->getIntrigue();
}

if (isset($_GET['section'])) $section = $_GET['section'];
else $section = "";


include 'navigation.php';
?>


    <div class="content">   
        <h1>Välj grupper/karaktärer som aktören ska känna till</h1>
	    <form action="logic/view_intrigue_logic.php" method="post">
	    <input type="hidden" id="operation" name="operation" value="choose_intrigue_knownactors">
	    <input type='hidden' id='IntrigueActorId' name='IntrigueActorId' value='<?php echo $intrigueActor->Id?>'>
		<input type="hidden" id="Section" name="Section" value="<?php echo $section;?>">
        <h2>Grupper</h2>
     		<?php 
     		$intrigue_group_actors = $intrigue->getAllGroupActors();
     		if (empty($intrigue_group_actors)) {
     		    echo "Inga registrerade grupper";
     		} else {
     		    ?>
    		    <table class='data'>
    		    <tr><th>Namn</th></tr>
    		    <?php 
    		    foreach ($intrigue_group_actors as $intrigue_group_actor)  {
    		        $group=$intrigue_group_actor->getGroup();
    		        echo "<tr>\n";
    		        echo "<td><input type='checkbox' id='IntrigueActor$group->Id' name='KnownIntrigueActorId[]' value='$intrigue_group_actor->Id'>";

    		        echo "<label for='IntrigueActor$group->Id'>$group->Name</label></td>\n";

    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
    		<br>
			<input type="submit" value="Lägg till">
			
        <h2>Karaktärer</h2>
     		<?php 
     		$intrigue_role_actors = $intrigue->getAllRoleActors();
     		if (empty($intrigue_role_actors)) {
     		    echo "Inga registrerade karaktärer";
     		} else {
     		    ?>
    		    <table class='data'>
    		    <tr><th>Namn</th><th>Grupp</th></tr>
    		    <?php 
    		    foreach ($intrigue_role_actors as $intrigue_role_actor)  {
    		        $role=$intrigue_role_actor->getRole();
    		        echo "<tr>\n";
    		        echo "<td><input type='checkbox' id='IntrigueActorRole$intrigue_role_actor->Id' name='KnownIntrigueActorId[]' value='$intrigue_role_actor->Id'>";

    		        echo "<label for='IntrigueActorRole$intrigue_role_actor->Id'>$role->Name</label></td>\n";
    		        $group = $role->getGroup();
    		        if (is_null($group)) {
    		            echo "<td>&nbsp;</td>\n";
    		        } else {
    		            echo "<td>$group->Name</td>\n";
    		        }
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
    		<br>
			<input type="submit" value="Lägg till">
			
			</form>
        
     		
	</div>
</body>

</html>
