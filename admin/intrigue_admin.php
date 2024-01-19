<?php
include_once 'header.php';

include 'navigation.php';


?>
<script src="../javascript/show_hide_rows.js"></script>

    <div class="content">
        <h1>Intrigspår</h1>
            <a href="intrigue_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>&nbsp;  
            <a href="intrigue_continue.php"><i class="fa-solid fa-file-circle-plus"></i>Fortsätt på intrigspår från tidigare lajv</a><br>  
            <a href="reports/intrigues_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Alla intrigspår</a>&nbsp;
            <a href="intrigue_pdfs.php"><i class="fa-solid fa-file-pdf"></i>Alla pdf'er som används i intrigspår</a>  
       
       <?php
       echo "<br>";
       echo "<br>";
       echo "Intrigspår filtrerade på ansvarig (endast akuella).<br>";
       echo '<button id="btn_show" onclick="show_hide();">Visa alla</button>';
       echo "<br>";
       echo "<br>";
       
       $intrigue_array = Intrigue::allByLARP($current_larp);
       if (!empty($intrigue_array)) {
            echo "<table class='data'>";
            
            
            echo "<tr><th>Nummer</td><th>Namn</th><th>Aktuell</th><th>Huvud-<br>intrig</th><th>Intrigtyper</th><th>Ansvarig</th></tr>\n";
            foreach ($intrigue_array as $intrigue) {
                $show = true;
                if ($current_user->Id != $intrigue->ResponsibleUserId || !$intrigue->isActive()) {
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