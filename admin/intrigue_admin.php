<?php
include_once 'header.php';

include 'navigation.php';
?>

    <div class="content">
        <h1>Intriger</h1>
            <a href="intrigue_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
       <?php

       $intrigue_array = Intrigue::allByLARP($current_larp);
       if (!empty($intrigue_array)) {
            echo "<table class='data'>";
            echo "<tr><th>Nummer</td><th>Namn</th><th>Aktuell</th><th>Huvud-<br>intrig</th><th>Intrigtyper</th><th>Ansvarig</th><th></th></tr>\n";
            foreach ($intrigue_array as $intrigue) {
                echo "<tr>\n";
                echo "<td>" . $intrigue->Number . "</td>\n";
                echo "<td>" . $intrigue->Name . "</td>\n";
                echo "<td>" . ja_nej($intrigue->Active) . "</td>\n";
                echo "<td>" . ja_nej($intrigue->MainIntrigue) . "</td>\n";
                echo "<td>" . commaStringFromArrayObject($intrigue->getIntriguetypes()) . "</td>\n";
                $responsibleUser = $intrigue->getResponsibleUser();
                echo "<td>$responsibleUser->Name</td>";
                
                echo "<td>" . "<a href='view_intrigue.php?Id=" . $intrigue->Id . "'><i class='fa-solid fa-eye'></i></td>\n";
                //echo "<td>" . "<a href='intrigue_admin.php?operation=delete&id=" . $intrigue->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        else {
            echo "<p>Inga registrarade ännu</p>";
        }
        ?>
    </div>
	
</body>

</html>