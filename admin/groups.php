<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>


    <div class="content">   
        <h1>Grupper</h1>
            <a href="create_group.php"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  

     		<?php 
    		$groups = Group::getAllRegistered($current_larp);
    		if (empty($groups)) {
    		    echo "Inga anmälda grupper";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th></th><th>Gruppledare</th><th>Rikedom</th><th colspan='2'>Intrig</th></tr>\n";
    		    foreach ($groups as $group)  {
    		        echo "<tr>\n";
    		        echo "<td>" . $group->Name;
    		        if ($group->IsDead ==1) echo " <i class='fa-solid fa-skull-crossbones' title='Död'></i>";
    		        if ($group->userMayEdit($current_larp)) echo "<br>Gruppledaren får ändra gruppen " . showStatusIcon(false);
    		        
    		        echo "</td>\n";
    		        echo "<td>" . "<a href='view_group.php?id=" . $group->Id . "'><i class='fa-solid fa-eye'></i></a>\n";
    		        echo "<a href='edit_group.php?id=" . $group->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        $person = $group->getPerson();
    		        echo "<td>" . $person->Name . " " . contactEmailIcon($person->Name,$person->Email) . "</td>\n";
    		        echo "<td>" . $group->getWealth()->Name . "</td>\n";
    		        $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    		        echo "<td>" . showStatusIcon($larp_group->hasIntrigue());

    		        if ($larp_group->hasIntrigue()) echo "<br>".str_word_count($larp_group->Intrigue)." ord";
    		        
    		        echo "</td>\n";
    		        echo "<td><a href='edit_group_intrigue.php?id=" . $group->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
         
	</div>
</body>

</html>
