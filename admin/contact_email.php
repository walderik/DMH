<?php

include_once 'header.php';

global $current_larp;


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}

$name = 'Stranger';
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
} elseif (isset($_GET['all'])) {
    $email = 'ALLADELTAGARE';
    $name = '';
} else {
    header('Location: index.php?error=no_email');
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
		} elseif (!isset($_GET['all'])) {
		    echo "<h1>Skicka ett utskick till alla deltagarna.</h1>\n";
		    echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest 60 mail i minuten.<br>\n";
		} else {
		  echo "<h1>Skicka ett mail till $email";
          if ($name != '') echo " ($name)";
		}
        echo "</h1>\n";
    	?>
		<form action="logic/send_contact_email.php" method="post">
    		<input type="hidden" id="email" name="email" value="<?php echo $email; ?>">
    		<?php if (isset($official_type)) echo "<input type='hidden' id='official_type' name='official_type' value='$official_type->Id'>"; ?>
    		<input type="hidden" id="name" name="name" value="<?php echo $name; ?>">
    		<input type="hidden" id="referer" name="referer" value="<?php echo $referer; ?>">
    		
    		<p><br />
    		<p><?php echo "$hej $name"; ?> !<br></p>
			<p><textarea id="text" name="text" rows="8" cols="121" maxlength="60000" required></textarea></p>
			Med vänliga hälsningar<br /><br />
			<b>Arrangörerna av <?php echo $current_larp->Name; ?></b><br>

	
    		<br />
    		<input type="submit" value="Skicka">
		</form>

	</div>


</body>
</html>
