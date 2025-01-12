<?php
include_once 'header.php';

global $purpose;



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $intrigueActor = IntrigueActor::loadById($_GET['IntrigueActorId']);
    $intrigue=$intrigueActor->getIntrigue();
}

if (isset($_GET['section'])) $section = $_GET['section'];
else $section = "";


include 'navigation.php';
?>


    <div class="content">   
        <h1>Välj föremål som aktören ska ha vid incheckning</h1>
	    <form action="logic/view_intrigue_logic.php" method="post">
	    <input type="hidden" id="operation" name="operation" value="choose_intrigue_checkin">
	    <input type='hidden' id='IntrigueActorId' name='IntrigueActorId' value='<?php echo $intrigueActor->Id?>'>
		<input type="hidden" id="Section" name="Section" value="<?php echo $section;?>">
        <h2>Rekvisita</h2>
     		<?php 
     		$intrigue_props = $intrigue->getAllProps();
     		if (empty($intrigue_props)) {
     		    echo "Ingen registrerad rekvisita i intrigspåret";
     		} else {
     		    ?>
    		    <table class='data'>
    		    <tr><th>Namn</th></tr>
    		    <?php 
    		    foreach ($intrigue_props as $intrigue_prop)  {
    		        $prop=$intrigue_prop->getProp();
    		        echo "<tr>\n";
    		        echo "<td><input type='checkbox' id='Intrigue_Prop$prop->Id' name='Intrigue_PropId[]' value='$intrigue_prop->Id'>";

    		        echo "<label for='Prop$intrigue_prop->Id'>$prop->Name</label></td>\n";

    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		    echo "<br>";
    		    echo "<input type='submit' value='Lägg till'>";
     		}
    		?>
			
		<?php if ($current_larp->hasLetters()) {?>
        <h2>Brev</h2>
     		<?php 
     		$intrigue_letters = $intrigue->getAllLetters();
     		if (empty($intrigue_letters)) {
     		    echo "Inga registrerade brev i intrigspåret";
     		} else {
     		    ?>
    		    <table class='data'>
    		    <tr><th></th><th>Mottagare</th><th>Meddelande</th><th>Avsändare</th><th>Godkänt</th></tr>
    		    <?php 
    		    foreach ($intrigue_letters as $intrigue_letter)  {
    		        $letter=$intrigue_letter->getLetter();
    		        echo "<tr>\n";
    		        echo "<td><input type='checkbox' id='Intrigue_Letter$intrigue_letter->Id' name='Intrigue_LetterId[]' value='$intrigue_letter->Id'>";

    		        echo "<td>$letter->Recipient</td>\n";
    		        echo "<td>".mb_strimwidth(str_replace('\n', '<br>', $letter->Message), 0, 100, '...')."</td>\n";
    		        echo "<td>$letter->Signature</td>";
    		        echo "<td>" . showStatusIcon($letter->Approved) . "</td>\n";
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		    echo "<br>";
    		    echo "<input type='submit' value='Lägg till'>";
    		    
    		}
    		?>
		<?php } ?>
			
		<?php if ($current_larp->hasTelegrams()) {?>
        <h2>Telegram</h2>
     		<?php 
     		$intrigue_telegrams = $intrigue->getAllTelegrams();
     		if (empty($intrigue_telegrams)) {
     		    echo "Inga registrerade telegram i intrigspåret";
     		} else {
     		    ?>
    		    <table class='data'>
    		    <tr><th></th><th>Tid</th><th>Mottagare</th><th>Meddelande</th><th>Godkänt</th></tr>
    		    <?php 
    		    foreach ($intrigue_telegrams as $intrigue_telegram)  {
    		        $telegram=$intrigue_telegram->getTelegram();
    		        echo "<tr>\n";
    		        echo "<td><input type='checkbox' id='Intrigue_Telegram$intrigue_telegram->Id' name='Intrigue_TelegramId[]' value='$intrigue_telegram->Id'>";

    		        echo "<td>$telegram->Deliverytime</td>\n";
    		        echo "<td>$telegram->Reciever</td>\n";
    		        echo "<td>$telegram->Message</td>\n";
    		        echo "<td>" . showStatusIcon($telegram->Approved) . "</td>\n";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		    echo "<br>";
    		    echo "<input type='submit' value='Lägg till'>";
     		}
     		?>
			
			<?php  } ?>
			
			
			
			</form>
        
     		
	</div>
</body>

</html>
