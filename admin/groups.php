 <?php
 include_once 'header_subpage.php';
 

?>


    <div class="content">   
        <h1>Grupper</h1>
            <a href="edit_group.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  

     		<?php 
    		$groups = Group::getAllRegistered($current_larp);
    		if (empty($groups)) {
    		    echo "Inga anmälda grupper";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th></th><th>Gruppledare</th><th colspan='2'>Intrig</th></tr>\n";
    		    foreach ($groups as $group)  {
    		        echo "<tr>\n";
    		        echo "<td>" . $group->Name . "</td>\n";
    		        echo "<td>" . "<a href='view_group.php?id=" . $group->Id . "'><i class='fa-solid fa-eye'></i></a>\n";
    		        echo "<a href='edit_group.php?id=" . $group->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        echo "<td>" . $group->getPerson()->Name . "</td>\n";
    		        echo "<td>" . showStatusIcon($group->hasIntrigue($current_larp)) . "</td>\n";
    		        echo "<td><a href='edit_group_intrigue.php?id=" . $group->Id . "'><i class='fa-solid fa-pen'></i></a></td>\n";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>

        
         
	</div>
</body>

</html>
