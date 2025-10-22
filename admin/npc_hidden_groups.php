<?php
include_once 'header.php';





include 'navigation.php';
include 'npc_navigation.php';

?>

<script src="../javascript/table_sort.js"></script>

    <div class="content">   
        <h1>Gömda grupper</h1>

			<?php 
			$tableId = "groups";
			$colnum=0;
			echo "<table id='$tableId' class='data'>";
			echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
			 			"<th>&nbsp; &nbsp; </th>";
			$colnum++;
			echo "<th onclick='sortTableNumbers(". $colnum++ .", \"$tableId\")'>Synlighet</th>";
			echo "<th onclick='sortTableNumbers(". $colnum++ .", \"$tableId\")'>Antal medlemmar</th>";
			if ($current_larp->hasCommerce()) echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Har verksamhet</th>";
			echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")' colspan='2'>Intrig</th></tr>\n";
			
			
			$hidden_groups = Group::getAllHiddenGroups($current_larp->CampaignId);
			foreach ($hidden_groups as $group) {
			    echo "<tr>\n";
			    echo "<td>";
			    echo $group->getViewLink();
			    if (!empty($group->PersonId)) echo "<br>Gruppen har en gruppledare " . showStatusIcon(false);
			    if ($group->userMayEdit($current_larp)) echo "<br>Gruppledaren får ändra gruppen " . showStatusIcon(false);
			    echo "</td>\n";
			    echo "<td>";
			    echo $group->getEditLinkPen(true);
			    if ($group->mayDelete()) echo "&nbsp;<a href='logic/delete_group.php?id=" . $group->Id . "'><i class='fa-solid fa-trash' title='Ta bort grupp'></i></a>";
			    
			    echo "\n";
			    echo "<a href='group_sheet.php?id=" . $group->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Gruppblad för $group->Name'></i></a>\n";
			    echo "</td>\n";
			    echo "<td>";
			    if ($group->Visibility == Group::VISIBILITY_FULL) echo "Full";
			    elseif ($group->Visibility == Group::VISIBILITY_NOT_CHOOSE) echo "Synlig NPC-grupp";
			    elseif ($group->Visibility == Group::VISIBILITY_INVISIBLE) echo "Osynlig NPC-grupp";
			    echo "</td>";
			    echo "<td>" . $group->countAllRolesInGroup($current_larp) . "</td>\n";
			    if ($current_larp->hasCommerce())
			    {
			        $titledeeds = Titledeed::getAllForGroup($group);
			        $hasTitleDeed = empty($titledeeds) ? 'Nej' : 'Ja';
			        echo "<td>" . $hasTitleDeed . "</td>\n";
			    }
			    echo "<td>" . showStatusIcon($group->hasIntrigue($current_larp));
			    $intrigueWords = $group->intrigueWords($current_larp);
			    
			    if (!empty($intrigueWords)) echo "<br>$intrigueWords ord";
			    $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);
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
			    echo "</tr>\n";
			}
			echo "</table>";
			
			?>
			
  	</div>
</body>

</html>
  