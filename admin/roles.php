<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>


<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>

    <div class="content">   
        <h1>Karaktärer</h1>
        <a href="role_list.php"><i class='fa-solid fa-eye' title='Se alla karaktärer samlat på en sida'></i> En sida med alla</a> &nbsp; &nbsp; &nbsp;
        <a href='character_sheet.php?all_info=true' target='_blank'><i class='fa-solid fa-file-pdf' title='Allt om alla karaktärer som en stor PDF (tar tid att generera)'></i> Allt om alla</a>
         &nbsp; &nbsp; &nbsp;
        <a href='character_sheet.php?all_info=true&children=true' target='_blank'><i class='fa-solid fa-file-pdf' title='Allt om alla barns karaktärer som en stor PDF (tar tid att generera)'></i> Allt om alla barn (upp till 15 år)</a>
         &nbsp; &nbsp; &nbsp;
        <a href='character_sheet.php?bara_intrig=japp' target='_blank'><i class='fa-solid fa-file-pdf' title='Alla intriger (tar tid att generera)'></i> Alla intriger</a>
         &nbsp; &nbsp; &nbsp;
        <a href='character_sheet.php?' target='_blank'><i class='fa-solid fa-file-pdf' title='Alla karaktärer så som de ser ut för deltagarna(tar tid att generera)'></i> Alla karaktärer så som de ser ut för deltagarna</a>
        <h2>Huvudkaraktärer</h2>
     		<?php 
     		$roles = $current_larp->getAllMainRoles(true);
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    $tableId = "main_roles";
    		    $colnum = 0;
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
        		    "<th>&nbsp; &nbsp; </th>";
    		    $colnum++;
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Plats<br>på lajvet</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")' width='10%'>Yrke</th>";
    		    if (LarperType::isInUse($current_larp)) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Typ av lajvare</th>";    		    
    		    if (IntrigueType::isInUse($current_larp)) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Intrigtyper</th>";
    		    if (Wealth::isInUse($current_larp)) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Rikedom</th>";
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Gruppering</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")' colspan='2'>Intrig</th></tr>\n";
    		    foreach ($roles as $role)  {
    		        //Sidan visar bara anmälda karaktärer, aldrig npc.
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
    		                echo "<td nowrap>" . $role->getViewLink() . "\n";
    		                echo "</td>";
    		                echo "<td colspan='7'><strong>Karaktären har en intrig som behöver hanteras.</td>";
    		                echo "<td><a href='edit_intrigue.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		            }
    		            else {
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                if (LarperType::isInUse($current_larp)) echo "<td></td>";
    		                if (IntrigueType::isInUse($current_larp)) echo "<td></td>";
    		                if (Wealth::isInUse($current_larp)) echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		            }
    		        }
    		        else {
        		        echo $role->getViewLink();
        		        if ($role->userMayEdit()) echo "<br>Deltagaren får ändra karaktären " . showStatusIcon(false);
        		        echo "</td>\n";
        		        echo "<td nowrap>";
        		        echo $role->getEditLinkPen(true) . "\n";
        		        echo "<a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a>\n";
        		        echo "</td>\n";
        		        echo "<td align='center'>".showStatusIcon($registration->hasSpotAtLarp())."</td>\n";
        		        echo "<td>$role->Profession</td>\n";
        		        if ($role->isMysLajvare()) {
        		            if (LarperType::isInUse($current_larp)) echo "<td></td>";
        		            if (IntrigueType::isInUse($current_larp)) echo "<td></td>";
        		            if (Wealth::isInUse($current_larp)) echo "<td></td>";
        		        }
        		        else {
        		            if (LarperType::isInUse($current_larp))  {
                		        echo "<td>";
                		        $larpertype = $role->getLarperType();
                		        if (!empty($larpertype)) echo $larpertype->Name;
                		        echo "</td>\n";
        		            }
        		            if (IntrigueType::isInUse($current_larp)) {
                		        echo "<td>";
                		        echo commaStringFromArrayObject($role->getIntrigueTypes());
                		        echo "</td>\n";
        		            }
        		            if (Wealth::isInUse($current_larp)) {
                		        $wealth = $role->getWealth();
                		        echo "<td>";
                		        if (!empty($wealth)) echo $wealth->Name;
                		        echo "</td>\n";
        		            }
        		        }
        		        $hasOtherIntrigues = false;
       		           $group = $role->getGroup();
        		        if (is_null($group)) {
        		            echo "<td>&nbsp;</td>\n";
        		        } else {
        		            echo "<td>";
							echo $group->getViewLink();
        		            $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);
        		            echo "<br>";
        		            if (!empty($intrigues)) echo "Intrig: ";
        		            foreach ($intrigues as $intrigue) {
        		                echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
        		                if ($intrigue->isActive()) {
        		                    echo $intrigue->Number;
        		                    $hasOtherIntrigues = true;
        		                }
        		                else echo "<s>$intrigue->Number</s>";
        		                echo "</a>";
        		                echo " ";
        		            }
        		            
        		            
        		            echo "</td>\n";
        		        }
        		        
        		        $subdivisions = Subdivision::allForRole($role, $current_larp);
        		        if (empty($subdivisions)) {
        		            echo "<td>&nbsp;</td>\n";
        		        } else {
        		            echo "<td>";
        		            
        		            foreach ($subdivisions as $subdivision) {
        		                echo $subdivision->getViewLink();
        		                $intrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $current_larp->Id);
        		                echo "<br>";
        		                if (!empty($intrigues)) echo "Intrig: ";
        		                foreach ($intrigues as $intrigue) {
        		                    echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
        		                    if ($intrigue->isActive()) {
        		                        echo $intrigue->Number;
        		                        $hasOtherIntrigues = true;
        		                    }
        		                    else echo "<s>$intrigue->Number</s>";
        		                    echo "</a>";
        		                    echo " ";
        		                }
        		                echo "<br><br>";
          		            }
        		            
        		            
        		            echo "</td>\n";
        		        }

        		        
        		        
        		        if ($role->isMysLajvare()) {
        		            echo "<td colspan=2>N/A</td>\n";
        		        } else {
        		            echo "<td>"; 
        		            if ($role->hasIntrigue($current_larp)) echo showStatusIcon(true);
        		            elseif ($hasOtherIntrigues) echo showWarningIcon();
        		            else echo showStatusIcon(false);
        		            $intrigueWords = $role->intrigueWords($current_larp);
        		            
        		            if (!empty($intrigueWords)) echo "<br>$intrigueWords ord";
        		            $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
        		            echo "<br>";
        		            if (!empty($intrigues)) echo "Intrig: ";
        		            foreach ($intrigues as $intrigue) {
        		                echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
        		                if ($intrigue->isActive()) echo $intrigue->Number;
        		                else echo "<s>$intrigue->Number</s>";
        		                echo "</a>";
        		                echo " ";
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

      		<?php 
     		$roles = $current_larp->getAllNotMainRoles(true);
    		if (!empty($roles)) {
            ?>
                <h2>Övriga karaktärer</h2>
                <p>Antal ord på intrig och vilka intriger de är med i visas inte för nedanstående karaktärer.</p>
     		<?php 

    		    $tableId = "other_roles";
    		    $colnum = 0;
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
        		    "<th>&nbsp; &nbsp; </th>";
    		    $colnum++;
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Plats<br>på lajvet</th>".
    		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")' width='10%'>Yrke</th>";
    		    if (LarperType::isInUse($current_larp)) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Typ av lajvare</th>";
    		    if (IntrigueType::isInUse($current_larp)) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Intrigtyper</th>";
    		    if (Wealth::isInUse($current_larp)) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Rikedom</th>";
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Grupp</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\")' colspan='2'>Intrig</th></tr>\n";
    		    foreach ($roles as $role)  {
    		        //Sidan visar bara anmälda karaktärer, aldrig npc.
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
    		                echo "<td nowrap>" . $role->getViewLink() . "\n";
    		                echo "</td>";
    		                echo "<td colspan='7'><strong>Karaktären har en intrig som behöver hanteras.</td>";
    		                echo "<td><a href='edit_intrigue.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		            }
    		            else {
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		                if (LarperType::isInUse($current_larp)) echo "<td></td>";
    		                if (IntrigueType::isInUse($current_larp)) echo "<td></td>";
    		                if (Wealth::isInUse($current_larp)) echo "<td></td>";
    		                echo "<td></td>";
    		                echo "<td></td>";
    		            }
    		        }
    		        else {
        		            
        		        echo "<td>" . $role->getViewLink() . "\n";
        		        echo "<td>";
        		        echo $role->getEditLinkPen(true) . "\n";
        		        echo "<a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad'></i></a>\n";
        		        echo "</td>\n";
        		        echo "<td align='center'>".showStatusIcon($registration->hasSpotAtLarp())."</td>\n";
        		        
        		        echo "<td>" . $role->Profession . "</td>\n";
        		        if ($role->isMysLajvare()) {
        		            if (LarperType::isInUse($current_larp)) echo "<td></td>";
        		            if (IntrigueType::isInUse($current_larp)) echo "<td></td>";
        		            if (Wealth::isInUse($current_larp)) echo "<td></td>";
        		        }
        		        else {
            		            
        		            if (LarperType::isInUse($current_larp))  {
        		                echo "<td>";
        		                $larpertype = $role->getLarperType();
        		                if (!empty($larpertype)) echo $larpertype->Name;
        		                echo "</td>\n";
        		            }
        		            if (IntrigueType::isInUse($current_larp)) {
        		                echo "<td>";
        		                echo commaStringFromArrayObject($role->getIntrigueTypes());
        		                echo "</td>\n";
        		            }
        		            if (Wealth::isInUse($current_larp)) {
        		                $wealth = $role->getWealth();
        		                echo "<td>";
        		                if (!empty($wealth)) echo $wealth->Name;
        		                echo "</td>\n";
        		            }
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

</html>
