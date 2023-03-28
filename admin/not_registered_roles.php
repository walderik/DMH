<?php
include_once 'header.php';

include 'navigation_subpage.php';
?>


    <div class="content">   
        <h1>Roller i kampanjen som inte är anmälda (än) i år</h1>

     		<?php 
    		$roles = Role::getAllUnregisteredRoles($current_larp);
    		if (empty($roles)) {
    		    echo "Inga anmälda roller";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th>Yrke</th><th>Grupp</th><th>Spelare</th><th>Senast spelad</th></tr>\n";
    		    foreach ($roles as $role)  {
    		        echo "<tr>\n";
    		        echo "<td>" . $role->Name;
    		        if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
    		        echo "</td>\n";
    		        echo "<td>$role->Profession</td>\n";
    		        $group = $role->getGroup();
    		        if (is_null($group)) {
    		            echo "<td>&nbsp;</td>\n";
    		        } else {
    		            echo "<td>$group->Name</td>\n";
    		        }
    		        echo "<td>".$role->getPerson()->Name."</td>";
    		        //TODO leta rätt på när rollen senast var anmäld
    		        echo "<td></td>";
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
	</div>
</body>

</html>
