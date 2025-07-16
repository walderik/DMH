<?php
include_once 'header.php';

include 'navigation.php';
include 'intrigue_navigation.php';


function print_intrigue(Intrigue $intrigue, $responsiblePerson) {
    echo "<tr>\n";
    
    
    echo "<td>" . $intrigue->Number . "</td>\n";
    echo "<td><a href='view_intrigue.php?Id=" . $intrigue->Id . "'>$intrigue->Name</a></td>\n";
    echo "<td>" . Intrigue::STATUS_TYPES[$intrigue->Status] . "</td>\n";
    echo "<td>" . ja_nej($intrigue->isActive()) . "</td>\n";
    echo "<td>" . ja_nej($intrigue->MainIntrigue) . "</td>\n";
    echo "<td>" . commaStringFromArrayObject($intrigue->getIntriguetypes()) . "</td>\n";
    if (isset($responsiblePerson)) echo "<td>$responsiblePerson->Name</td>";
    else echo "<td></td>";
    
    echo "<td>";
    if ($intrigue->mayDelete()) echo "<a href='logic/view_intrigue_logic.php?operation=delete&id=" . $intrigue->Id . "'><i class='fa-solid fa-trash' title='Ta bort'></i>";
    echo "</td>\n";
    
    echo "</tr>\n";
    
}

?>


    <div class="content">
       <h1>Intrigspår</h1>
       <a href="intrigue_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>&nbsp; &nbsp; 
       <a href="intrigue_continue.php"><i class="fa-solid fa-file-circle-plus"></i>Fortsätt på intrigspår från tidigare lajv</a><br> 
       <br>
       <a href="reports/intrigues_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Alla intrigspår</a>&nbsp; &nbsp;
       <a href="intrigue_pdfs.php"><i class="fa-solid fa-file-pdf"></i>Alla pdf'er som används i något intrigspår</a>  
               <p>Intrigspår går bara att ta bort om det inte finns några aktörer eller kopplade rykten/meddelande eller annat. 
        Detta för att man inte ska råka ta bort intrigspår som är i spel av misstag.</p>
       
       <?php if ($current_larp->isEnded()) { ?>
       		&nbsp; &nbsp;<a href="reports/intrigues_what_happened_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Vad hände</a>&nbsp; &nbsp;
       <?php
        }
       $intrigue_array = Intrigue::allByLARP($current_larp);
       if (!empty($intrigue_array)) {
           echo "<h2>Intrigspår skapade av $current_person->Name</h2>";
           echo "<table class='data'>";
            
            
            echo "<tr><th>Nummer</td><th>Namn</th><th>Status</th><th>Aktuell</th><th>Huvud-<br>intrig</th><th>Intrigtyper</th><th></th><th></th></tr>\n";
            foreach ($intrigue_array as $intrigue) {
                $responsiblePerson = $intrigue->getResponsiblePerson();
                
                if (isset($responsiblePerson) && $current_person->Id == $responsiblePerson->Id ) {
                    print_intrigue($intrigue, null);
                }
             }
            echo "</table>";

            echo "<h2>Intrigspår skapade av andra</h2>";
            echo "<table class='data'>";
            
            
            echo "<tr><th>Nummer</td><th>Namn</th><th>Status</th><th>Aktuell</th><th>Huvud-<br>intrig</th><th>Intrigtyper</th><th>Ansvarig</th><th></th></tr>\n";
            foreach ($intrigue_array as $intrigue) {
                $responsiblePerson = $intrigue->getResponsiblePerson();
                
                if (!isset($responsiblePerson) || $current_person->Id != $responsiblePerson->Id ) {
                    print_intrigue($intrigue, $responsiblePerson);
                }
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