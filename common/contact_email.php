<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/includes/init.php';


if (!isset($_SESSION['navigation'])) {
    header('Location: ../participant/index.php?11');
    exit;
}


if ($_SESSION['navigation'] == Navigation::LARP) {
    include '../admin/header.php';
    $navigation = '../admin/navigation.php';
} elseif ($_SESSION['navigation'] == Navigation::CAMPAIGN) {
    include '../campaign/header.php';
    $navigation =  '../campaign/navigation.php';
} elseif ($_SESSION['navigation'] == Navigation::BOARD) {
    include '../board/header.php';
    $navigation =  '../board/navigation.php';
} elseif ($_SESSION['navigation'] == Navigation::HOUSES) {
    include '../houses/header.php';
    $navigation =  '../houses/navigation.php';
} elseif ($_SESSION['navigation'] == Navigation::OM_ADMIN) {
    include '../site-admin/header.php';
    $navigation =  '../site-admin/navigation.php';
} else {
    header('Location: ../participant/index.php');
    exit;
}


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
        if (isset($_POST['subject'])) $subject = $_POST['subject'];
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


if (isset($_POST['sender'])) $sender = $_POST['sender'];
else $sender = BerghemMailer::LARP;

if ($sender == BerghemMailer::LARP) {
    $campaign = $current_larp->getCampaign();
    $hej = $campaign->hej();
    
    $senderTexts = ["$current_person->Name för arrangörsgruppen av $current_larp->Name", "Arrangörerna av $current_larp->Name"]; 
    if (!isset($subject)) $subject = "Meddelande från $campaign->Name";
} elseif ($sender == BerghemMailer::CAMPAIGN) {
    $campaign = $current_larp->getCampaign();
    $hej = $campaign->hej();
    
    $senderTexts = ["$current_person->Name för arrangörsgruppen för $campaign->Name", "Arrangörerna av $campaign->Name"];
    if (!isset($subject)) $subject = "Meddelande från $campaign->Name";   
} else {
    $hej = "Hej";
    $senderTexts = ["$current_person->Name för Berghems Vänner", "Berghems Vänner"];
    if (!isset($subject)) $subject = "Meddelande från Berghems Vänner";
}


$referer = $_SERVER['HTTP_REFERER'];


include $navigation;
?>

	<div class="content">
		
		<?php 
		switch ($type) {
		    case "intrigues":
		        echo "<h1>Skicka ut intrigerna till alla deltagarna.</h1>\n";
		        echo "I mailet kommer karaktärsbladen till alla deras karaktärer och grupper som de är med i bifogas.<br>\n";
		        if ($campaign->is_kir()) echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest ett mail var 15 sekund.<br>\n";
		        else echo "Eftersom karaktärsbladen är komplicerade att generera kommer de att skickas ut i omgångar för att inte belasta servern för mycket.<br>\n";
		        echo "För att mailen ska gå iväg måste du fortsätta att använda systemet, eller låta sidan med skickad epost vara öppen.<br><br>\n";
		        break;
		    case "housing":
		        echo "<h1>Skicka ut boendet till alla deltagarna.</h1>\n";
		        echo "Det kommer att skickas ett mail för varje hus/lägerplats.<br>\n";
		        if ($campaign->is_kir()) {
		            echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest ett mail var 15 sekund till max 15 mottagare.<br>\n";
		            echo "För att mailen ska gå iväg måste du fortsätta att använda systemet, eller låta sidan med skickad epost vara öppen.<br><br>\n";
		        }
		        break;
		    case "one":
		        echo "<h1>Skicka ett mail till $name ($email)</h1>";
		        break;
		    case "all":
		        echo "<h1>Skicka ett utskick till alla deltagarna.</h1>\n";
		        if ($campaign->is_kir()) {
		            echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest ett mail var 15 sekund till max 15 mottagare.<br>\n";
		            echo "För att mailen ska gå iväg måste du fortsätta att använda systemet, eller låta sidan med skickad epost vara öppen.<br><br>\n";
		        }
		        break;
		    case "several":
		        echo "<h1>Skicka ett utskick till alla $name</h1>\n";
		        if ($campaign->is_kir()) {
		            echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest ett mail var 15 sekund till max 15 mottagare.<br>\n";
		            echo "För att mailen ska gå iväg måste du fortsätta att använda systemet, eller låta sidan med skickad epost vara öppen.<br><br>\n";
		        }
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
		

	    echo "Ärende: <input id='subject' size = '70' name='subject' value='$subject' required>";
		    

		
		?>

    		<input type="hidden" id="type" name="type" value="<?php echo $type; ?>">
    		<input type="hidden" id="referer" name="referer" value="<?php echo $referer; ?>">
    		<input type="hidden" id="sender" name="sender" value="<?php  echo $sender; ?>">
    		
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
			    echo "Om du vill veta mer om ditt hus kan du titta på http://main.berghemsvanner.se/husen-i-byn/ eller logga in i Omnes Mundi https://anmalan.berghemsvanner.se/.\n";
			    echo "</p>";
			    
			}?>
			Med vänliga hälsningar<br /><br />
			<b>
			
			<?php 
			
			echo "<select name='senderText' id='senderText'>";
		    foreach ($senderTexts as $senderText) {
		        echo "<option value=\"$senderText\">$senderText</option>";
		    }
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
