<?php
include_once 'header.php';

include 'navigation.php';

include_once '../javascript/show_hide_rows.js';

?>

    <div class="content">
        <h1>Intriger</h1>
            <a href="intrigue_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
       
       <?php
       echo "<br>";
       echo "<br>";
       echo "Intriger filtrerade på ansvarig.<br>";
       echo '<button id="btn_show" onclick="show_hide();">Visa alla</button>';
       echo "<br>";
       echo "<br>";
       
       $intrigue_array = Intrigue::allByLARP($current_larp);
       if (!empty($intrigue_array)) {
            echo "<table class='data'>";
            
            
            echo "<tr><th>Nummer</td><th>Namn</th><th>Aktuell</th><th>Huvud-<br>intrig</th><th>Intrigtyper</th><th>Ansvarig</th></tr>\n";
            foreach ($intrigue_array as $intrigue) {
                $show = true;
                if ($current_user->Id != $intrigue->ResponsibleUserId) {
                    $show = false;
                }
                if ($show) echo "<tr>\n";
                else echo "<tr class='show_hide hidden'>\n";
                
                echo "<td>" . $intrigue->Number . "</td>\n";
                echo "<td><a href='view_intrigue.php?Id=" . $intrigue->Id . "'>$intrigue->Name</a></td>\n";
                echo "<td>" . ja_nej($intrigue->isActive()) . "</td>\n";
                echo "<td>" . ja_nej($intrigue->MainIntrigue) . "</td>\n";
                echo "<td>" . commaStringFromArrayObject($intrigue->getIntriguetypes()) . "</td>\n";
                $responsibleUser = $intrigue->getResponsibleUser();
                echo "<td>$responsibleUser->Name</td>";
                
                echo "</tr>\n";
            }
            echo "</table>";
        }
        else {
            echo "<p>Inga registrerade ännu</p>";
        }
        ?>
    </div>
	
</body>

</html>