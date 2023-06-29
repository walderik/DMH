<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>


    <div class="content">   
        <h1>Karaktärer</h1>
        <a href="role_list.php"><i class='fa-solid fa-eye' title='Se alla karaktärer samlat på en sida'></i> En sida med alla</a> &nbsp; &nbsp; &nbsp;
        <a href='character_sheet.php?' target='_blank'><i class='fa-solid fa-file-pdf' title='Allt om alla karaktärer som en stor PDF (tar tid att generera)'></i> Allt om alla</a>
        <h2>Huvudkaraktärer</h2>
     		<?php 
     		$roles = $current_larp->getAllMainRoles(true);
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    $tableId = "main_roles";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
        		    "<th>&nbsp; &nbsp; </th>".
        		    "<th onclick='sortTable(2, \"$tableId\")'>Godkänd</th>".
        		    "<th onclick='sortTable(3, \"$tableId\")'>Yrke</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")'>Typ av lajvare</th>".
        		    "<th onclick='sortTable(5, \"$tableId\")'>Intrigtyper</th>".
        		    "<th onclick='sortTable(6, \"$tableId\")'>Rikedom</th>".
        		    "<th onclick='sortTable(7, \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(8, \"$tableId\")' colspan='2'>Intrig</th></tr>\n";
    		    foreach ($roles as $role)  {
    		        $person = $role->getPerson();
    		        $registration=$person->getRegistration($current_larp);
    		        echo "<tr>\n";
    		        echo "<td>";
    		        if ($registration->isNotComing()) {
    		            echo "<s>";
    		            echo $role->Name;
    		            echo "</s>";
    		            echo "</td>";

    		        
    		            if ($role->hasIntrigue($current_larp)) {
    		                $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    		                echo "<td nowrap>" . "<a href='view_role.php?id=" . $role->Id . "'><i class='fa-solid fa-eye' title='Se karaktären'></i></a>\n";
    		                echo "</td>";
    		                echo "<td colspan='7'><strong>Karaktären har en intrig som behöver hanteras.</td>";
    		                echo "<td><a href='edit_intrigue.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		            }
    		            else {
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		            }
    		        }
    		        else {
        		        echo $role->Name;
        		        if ($role->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
        		        if ($role->userMayEdit($current_larp)) echo "<br>Deltagaren får ändra karaktären " . showStatusIcon(false);
        		        echo "</td>\n";
        		        echo "<td nowrap>" . "<a href='view_role.php?id=" . $role->Id . "'><i class='fa-solid fa-eye' title='Se karaktären'></i></a>\n";
        		        echo "<a href='edit_role.php?id=" . $role->Id . "'><i class='fa-solid fa-pen' title='Redigera karaktären'></i></a>\n";
        		        echo "<a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a>\n";
        		        echo "</td>\n";
        		        echo "<td align='center'>".showStatusIcon($person->isApprovedCharacters($current_larp))."</td>\n";
        		        echo "<td>$role->Profession</td>\n";
        		        if ($role->isMysLajvare()) {
        		            echo "<td></td>";
        		            echo "<td></td>";
        		            echo "<td></td>";
        		        }
        		        else {
            		        echo "<td>";
            		        $larpertype = $role->getLarperType();
            		        if (!empty($larpertype)) echo $larpertype->Name;
            		        echo "</td>\n";
            		        echo "<td>";
            		        echo commaStringFromArrayObject($role->getIntrigueTypes());
            		        echo "</td>\n";
            		        
            		        $wealth = $role->getWealth();
            		        echo "<td>";
            		        if (!empty($wealth)) echo $wealth->Name;
            		        echo "</td>\n";
        		        }
       		           $group = $role->getGroup();
        		        if (is_null($group)) {
        		            echo "<td>&nbsp;</td>\n";
        		        } else {
        		            echo "<td><a href='view_group.php?id=$group->Id'>$group->Name</></td>\n";
        		        }
        		        if ($role->isMysLajvare()) {
        		            echo "<td colspan=2>N/A</td>\n";
        		        } else {
        		            echo "<td>" . showStatusIcon($role->hasIntrigue($current_larp));
        		            $intrigueWords = $role->intrigueWords($current_larp);
        		            
        		            if (!empty($intrigueWords)) echo "<br>$intrigueWords ord";
        		            $intrigues = Intrigue::getAllIntriguesForRole($role->Id);
        		            echo "<br>";
        		            foreach ($intrigues as $intrigue) {
        		                echo "<a href='view_intrigue.php?Id=$intrigue->Id'>Intrig: $intrigue->Number. $intrigue->Name</a>";
	                            if ($intrigue->Active == 0) echo " (inte aktuell)";
	                            echo "<br>";
        		            }
        		            
        		            echo "</td>\n";
        		            echo "<td><a href='edit_intrigue.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
        		        }
    		        }
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
                <h2>Övriga karaktärer</h2>
                <p>Antal ord på intrig och vilka intriger de är med i visas inte för nedanstående karaktärer.</p>
     		<?php 
     		$roles = $current_larp->getAllNotMainRoles(true);
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    $tableId = "other_roles";
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
        		    "<th>&nbsp; &nbsp; </th>".
        		    "<th onclick='sortTable(2, \"$tableId\")'>Godkänd</th>".
        		    "<th onclick='sortTable(3, \"$tableId\")'>Yrke</th>".
        		    "<th onclick='sortTable(4, \"$tableId\")'>Typ av lajvare</th>".
        		    "<th onclick='sortTable(5, \"$tableId\")'>Intrigtyper</th>".
        		    "<th onclick='sortTable(6, \"$tableId\")'>Rikedom</th>".
        		    "<th onclick='sortTable(7, \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(8, \"$tableId\")' colspan='2'>Intrig</th></tr>\n";
    		    foreach ($roles as $role)  {
    		        $person = $role->getPerson();
    		        $registration=$person->getRegistration($current_larp);
    		        echo "<tr>\n";
    		        if ($registration->isNotComing()) {
    		            echo "<td><s>";
    		            echo $role->Name;
    		            echo "</s>";
    		            echo "</td>";
    		            
    		            
    		            if ($role->hasIntrigue($current_larp)) {
    		                $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    		                echo "<td nowrap>" . "<a href='view_role.php?id=" . $role->Id . "'><i class='fa-solid fa-eye' title='Se karaktären'></i></a>\n";
    		                echo "</td>";
    		                echo "<td colspan='7'><strong>Karaktären har en intrig som behöver hanteras.</td>";
    		                echo "<td><a href='edit_intrigue.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		            }
    		            else {
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		            }
    		        }
    		        else {
        		            
        		        echo "<td>" . $role->Name . "</td>\n";
        		        echo "<td>" . "<a href='view_role.php?id=" . $role->Id . "'><i class='fa-solid fa-eye' title='Se karaktären'></i></a>\n";
        		        echo "<a href='edit_role.php?id=" . $role->Id . "'><i class='fa-solid fa-pen' title='Redigera karaktären'></i></a>\n";
        		        echo "<a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad'></i></a>\n";
        		        echo "</td>\n";
        		        echo "<td align='center'>".showStatusIcon($person->isApprovedCharacters($current_larp))."</td>\n";
        		        
        		        echo "<td>" . $role->Profession . "</td>\n";
        		        if ($role->isMysLajvare()) {
        		            echo "<td></td>";
        		            echo "<td></td>";
        		            echo "<td></td>";
        		        }
        		        else {
            		            
            		        echo "<td>";
            		        $larpertype = $role->getLarperType();
            		        if (!empty($larpertype)) echo $larpertype->Name;
            		        echo "</td>\n";
            		        echo "<td>";
            		        echo commaStringFromArrayObject($role->getIntrigueTypes());
            		        echo "</td>\n";
        
            		        echo "<td>" . $role->getWealth()->Name . "</td>\n";
        		        }
        		        $group = $role->getGroup();
        		        if (is_null($group)) {
        		            echo "<td>&nbsp;</td>\n";
        		        } else {
        		            echo "<td>$group->Name</td>\n";
        		        }
        		        if ($role->isMysLajvare()) {
        		            echo "<td>N/A</td>\n";
        		        } else {
        		            $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
        		            echo "<td>" . showStatusIcon($role->hasIntrigue($current_larp));
        		            echo "</td>\n";
        		            echo "<td><a href='edit_intrigue.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
        		        }
        		        echo "</tr>\n";
        		    }
    		    }
    		    echo "</table>";
    		}
    		?>
        
	</div>
</body>
<?php 
include_once '../javascript/table_sort.js';
?>
</html>
