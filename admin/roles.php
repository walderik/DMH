<?php
 include_once 'header.php';
 
 include 'navigation_subpage.php';
?>


    <div class="content">   
        <h1>Karaktärer</h1>
        <a href="role_list.php"><i class='fa-solid fa-eye' title='Se alla karaktärer samlat på en sida'> En sida med alla</i></a> &nbsp; &nbsp; &nbsp;
        <a href='character_sheet.php?' target='_blank'><i class='fa-solid fa-file-pdf' title='Allt om alla karkatärer som en stor PDF (tar tid att generera)'> allt om alla</i></a>
        <h2>Huvudkaraktärer</h2>
     		<?php 
     		$roles = $current_larp->getAllMainRoles();
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th>&nbsp; &nbsp; </th><th>Godkänd</th><th>Yrke</th><th>Group</th><th colspan='2'>Intrig</th></tr>\n";
    		    foreach ($roles as $role)  {
    		        $person = $role->getPerson();
    		        echo "<tr>\n";
    		        echo "<td>" . $role->Name;
    		        if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
    		        if ($role->userMayEdit($current_larp)) echo "<br>Deltagaren får ändra karaktären " . showStatusIcon(false);
    		        echo "</td>\n";
    		        echo "<td nowrap>" . "<a href='view_role.php?id=" . $role->Id . "'><i class='fa-solid fa-eye' title='Se karaktären'></i></a>\n";
    		        echo "<a href='edit_role.php?id=" . $role->Id . "'><i class='fa-solid fa-pen' title='Redigera karaktären'></i></a>\n";
    		        echo "<a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a>\n";
    		        echo "</td>\n";
    		        echo "<td align='center'>".showStatusIcon($person->isApprovedCharacters($current_larp))."</td>\n";
    		        echo "<td>$role->Profession</td>\n";
   		        $group = $role->getGroup();
    		        if (is_null($group)) {
    		            echo "<td>&nbsp;</td>\n";
    		        } else {
    		            echo "<td>$group->Name</td>\n";
    		        }
    		        if ($role->isMysLajvare()) {
    		            echo "<td colspan=2>N/A</td>\n";
    		        } else {
        		        echo "<td>" . showStatusIcon($role->hasIntrigue($current_larp)) . "</td>\n";
        		        echo "<td><a href='edit_intrigue.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        }
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
                <h2>Övriga karaktärer</h2>
     		<?php 
     		$roles = $current_larp->getAllNotMainRoles();
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th>&nbsp; &nbsp; </th><th>Godkänd</th><th>Yrke</th><th>Group</th><th colspan='2'>Intrig</th></tr>\n";
    		    foreach ($roles as $role)  {
    		        echo "<tr>\n";
    		        echo "<td>" . $role->Name . "</td>\n";
    		        echo "<td>" . "<a href='view_role.php?id=" . $role->Id . "'><i class='fa-solid fa-eye' title='Se karaktären'></i></a>\n";
    		        echo "<a href='edit_role.php?id=" . $role->Id . "'><i class='fa-solid fa-pen' title='Redigera karaktären'></i></a>\n";
    		        echo "<a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad'></i></a>\n";
    		        echo "</td>\n";
    		        echo "<td align='center'>".showStatusIcon($person->isApprovedCharacters($current_larp))."</td>\n";
    		        
    		        echo "<td>" . $role->Profession . "</td>\n";
    		        $group = $role->getGroup();
    		        if (is_null($group)) {
    		            echo "<td>&nbsp;</td>\n";
    		        } else {
    		            echo "<td>$group->Name</td>\n";
    		        }
    		        if ($role->isMysLajvare()) {
    		            echo "<td>N/A</td>\n";
    		        } else {
    		            echo "<td>" . showStatusIcon($role->hasIntrigue($current_larp)) . "</td>\n";
    		            echo "<td><a href='edit_intrigue.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        }
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
        
	</div>
</body>

</html>
