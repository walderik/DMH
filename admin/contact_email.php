<?php

include_once 'header.php';

global $current_larp;

$name = '';
$type = "one";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['send_intrigues'])) {
        $type="intrigues";
        $name = '';
        $subject = "Intrigutskick för $current_larp->Name";
    } elseif (isset($_POST['send_housing'])) {
        $type="housing";
        $name = '';
        $subject = "Boende på $current_larp->Name";
    } elseif (isset($_POST['send_one'])) {
        $type="one";
        $personId = $_POST['personId'];
        $person = Person::loadById($personId);
        $name = $person->Name;
        $email = $person->Email;
    } elseif (isset($_POST['send_all'])) {
        $type="all";
        $name = '';
    } elseif (isset($_POST['send_several'])) {
        $type="several";
        $subject = $_POST['subject'];
        $personId = $_POST['personId'];
        $name = $_POST['name'];
    }
}


if (empty($type)) {
    header('Location: index.php');
    exit;
}

$referer = '';
if (isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER'];

$campaign = $current_larp->getCampaign();

$hej = $campaign->hej();

include 'navigation.php';
?>

	<div class="content">
		
		<?php 
		switch ($type) {
		    case "intrigues":
		        echo "<h1>Skicka ut intrigerna till alla deltagarna.</h1>\n";
		        echo "I mailet kommer karaktärsbladen till alla deras karaktärer och grupper som de är med i bifogas.<br>\n";
		        echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest ett mail var 15 sekund.<br>\n";
		        echo "För att mailen ska gå iväg måste du fortsätta att använda systemet, eller låta sidan med skickad epost vara öppen.<br>\n";
		        break;
		    case "housing":
		        echo "<h1>Skicka ut boendet till alla deltagarna.</h1>\n";
		        echo "Det kommer att skickas ett mail för varje hus/lägerplats.<br>\n";
		        echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest ett mail var 15 sekund till max 15 mottagare.<br>\n";
		        echo "För att mailen ska gå iväg måste du fortsätta att använda systemet, eller låta sidan med skickad epost vara öppen.<br>\n";
		        break;
		    case "one":
		        echo "<h1>Skicka ett mail till $name ($email)</h1>";
		        break;
		    case "all":
		        echo "<h1>Skicka ett utskick till alla deltagarna.</h1>\n";
		        echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest ett mail var 15 sekund till max 15 mottagare.<br>\n";
		        echo "För att mailen ska gå iväg måste du fortsätta att använda systemet, eller låta sidan med skickad epost vara öppen.<br>\n";
		        break;
		    case "several":
		        echo "<h1>Skicka ett utskick till alla $name</h1>\n";
		        echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest ett mail var 15 sekund till max 15 mottagare.<br>\n";
		        echo "För att mailen ska gå iväg måste du fortsätta att använda systemet, eller låta sidan med skickad epost vara öppen.<br>\n";
		        break;
		}
		
		?>
    	
    	
		<form action="logic/send_contact_email.php" method="post" enctype="multipart/form-data">
		<?php 
		if (isset($personId)) {
		    if (is_array($personId)) {
		        foreach ($personId as $id)  {
    		        echo "<input type='hidden' name='personId[]' value='$id'>\n";
    		    }
    		    
    		} else {
    		    echo "<input type='hidden' id='email' name='personId' value='$personId'>";
    		}
		}
		
        if (!isset($subject)) $subject = "Meddelande från $campaign->Name";    
	    echo "Ärende: <input id='subject' size = '70' name='subject' value='$subject' required>";
		    

		
		?>

    		<input type="hidden" id="type" name="type" value="<?php echo $type; ?>">
    		<input type="hidden" id="referer" name="referer" value="<?php echo $referer; ?>">
    		
    		<p><br />
    		<p>
    		<?php 

		      $greeting= "$hej $name!";
    		  echo "<input id='greeting' size = '50' name='greeting' value='$greeting' required>";
    		
    		?>
 
    		
    		 <br></p>
			<p><textarea id="text" name="text" rows="8" cols="121" maxlength="60000" required></textarea></p>
			<?php 
			if ($type=="housing") { 
			    echo "<p>";
			    echo "Du kommer att bo i/på hus/lägerplats &lt;namn&gt; tillsammans med &lt;antal&gt; andra personer.<br><br>\n";
			    echo "Beskrivning av &lt;namn&gt;: &lt;Huset/lägerplatsens beskrivning&gt;<br><br>\n";
			    echo "Vägbeskrivning: &lt;Huset/lägerplatsens plats&gt;<br><br>\n";
			    echo "Om du vill veta mer om ditt hus kan du titta på http://main.berghemsvanner.se/husen-i-byn/ eller logga in i Omnes Mundi https://www.berghemsvanner.se/regsys/.\n";
			    echo "</p>";
			    
			}?>
			Med vänliga hälsningar<br /><br />
			<b>
			
			<?php 
			$senderText1 = "$current_user->Name för arrangörsgruppen av $current_larp->Name";
			$senderText2 = "Arrangörerna av $current_larp->Name";
			
			echo "<select name='senderText' id='senderText'>";
		    echo "<option value=\"$senderText1\">$senderText1</option>";
		    echo "<option value=\"$senderText2\">$senderText2</option>";
		    echo "</select>";
			
			?>
			
			
			</b><br>

			<?php if ($type == "one" || $type == "all" || $type == "several") {?>
			<br><hr><br>
			Ladda upp en pdf som bilaga om du vill. Max storlek 5 MB och bara pdf:er.<br><br>
			<input type="file" name="bilaga" id="bilaga"><br>
			<?php }?>
			<br>
			(Tryck inte på "Skicka" innan den valda filen laddats upp ordentligt. Det tar ett litet tag.)
    		<br>
    		<br>
    		<hr><br>
    		<input type="submit" value="Skicka">
		</form>

	</div>


</body>
</html>
