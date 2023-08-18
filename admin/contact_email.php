<?php

include_once 'header.php';

global $current_larp;

$name = 'Stranger';
$type = "normal";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['name'])) $name = $_GET['name'];
    
    if (isset($_GET['email'])) {
        $email = $_GET['email'];
    } elseif (isset($_GET['official_type_id'])) {
        $official_type = OfficialType::loadById($_GET['official_type_id']);
        if(!isset($official_type)) {
            header('Location: index.php?error=no_email');
            exit;
        }
        $email = 'OFFICIALTYPE';
        $name = '';
    } elseif (isset($_GET['allagruppledare'])) {
        $email = 'ALLAGRUPPLEDARE';
        $name = '';
    } elseif (isset($_GET['all'])) {
        $email = 'ALLADELTAGARE';
        $name = '';
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['send_intrigues'])) {
        $type="intrigues";
        $email = 'send_intrigues';
        $name = '';
    } elseif (isset($_POST['send_housing'])) {
        $type="housing";
        $email = 'send_housing';
        $name = '';
    }
}


if (empty($email)) {
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
		
		if(isset($official_type)) {
		    echo "<h1>Skicka ett utskick till alla funktionärer som tar $official_type->Name.</h1>\n";
		    echo "Det kommer ta några minuter att skicka till alla.<br>Som mest skickas 60 mail i minuten.<br>\n";
		    echo "För att mailen ska gå iväg måste du fortsätta att använda systemet.<br>\n";
		} elseif (isset($_GET['allagruppledare'])) {
		    echo "<h1>Skicka ett utskick till alla gruppledarna.</h1>\n";
		    echo "Det kommer ta några minuter att skicka till alla.<br>Vi skickar som mest 60 mail per minut.<br>\n";
		    echo "För att mailen ska gå iväg måste du fortsätta att använda systemet.<br>\n";
		} elseif (isset($_GET['all'])) {
		    echo "<h1>Skicka ett utskick till alla deltagarna.</h1>\n";
		    echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest 60 mail i minuten.<br>\n";
		    echo "För att mailen ska gå iväg måste du fortsätta att använda systemet.<br>\n";
		} elseif (isset($_POST['send_intrigues'])) {
		    echo "<h1>Skicka ut intrigerna till alla deltagarna.</h1>\n";
		    echo "I mailet kommer karaktärsbladen till alla deras karaktärer och grupper som de är med i bifogas.<br>\n";
		    echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest 60 mail i minuten.<br>\n";
		    echo "För att mailen ska gå iväg måste du fortsätta att använda systemet.<br>\n";
		} elseif (isset($_POST['send_housing'])) {
		    echo "<h1>Skicka ut boendet till alla deltagarna.</h1>\n";
		    echo "Det kommer att skickas ett mail för varje hus/lägerplats.<br>\n";
		    echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest 60 mail i minuten.<br>\n";
		    echo "För att mailen ska gå iväg måste du fortsätta att använda systemet.<br>\n";
		} else {
		  echo "<h1>Skicka ett mail till $email";
          if ($name != '') echo " ($name)";
		}
        echo "</h1>\n";
    	?>
    	
    	
		<form action="logic/send_contact_email.php" method="post" enctype="multipart/form-data">
    		<input type="hidden" id="email" name="email" value="<?php echo $email; ?>">
    		<?php if (isset($official_type)) echo "<input type='hidden' id='official_type' name='official_type' value='$official_type->Id'>"; ?>
    		<input type="hidden" id="name" name="name" value="<?php echo $name; ?>">
    		<input type="hidden" id="type" name="type" value="<?php echo $type; ?>">
    		<input type="hidden" id="referer" name="referer" value="<?php echo $referer; ?>">
    		
    		<p><br />
    		<p><?php echo "$hej $name"; ?> !<br></p>
			<p><textarea id="text" name="text" rows="8" cols="121" maxlength="60000" required></textarea></p>
			<?php 
			if ($type=="housing") { 
			    echo "<p>";
			    echo "Du kommer att bo i/på hus/lägerplats &lt;namn&gt; tillsammans med &lt;antal&gt; andra personer.<br><br>\n";
			    echo "Beskrivning av &lt;namn&gt;: &lt;Huset/lägerplatsens beskrivning&gt;<br><br>\n";
			    echo "Vägbeskrivning: &lt;Huset/lägerplatsens plats&gt;<br>\n";
			    echo "</p>";
			    
			}?>
			Med vänliga hälsningar<br /><br />
			<b>Arrangörerna av <?php echo $current_larp->Name; ?></b><br>

			<?php if ($type == "normal") {?>
			<br><hr><br>
			Ladda upp en pdf som bilaga om du vill. Max storlek 5 MB och bara pdf:er.<br><br>
			<input type="file" name="bilaga" id="bilaga"><br>
			<?php }?>
    		<br><hr><br>
    		<input type="submit" value="Skicka">
		</form>

	</div>


</body>
</html>
