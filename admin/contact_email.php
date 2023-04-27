<?php

include_once 'header.php';

global $current_larp;


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}

if (isset($_GET['email'])) {
    $email = $_GET['email'];
} else {
    header('Location: index.php?error=no_email');
    exit;
}

$name = 'Stranger';
if (isset($_GET['name'])) $name = $_GET['name'];

$referer = '';
if (isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER'];

$campaign = $current_larp->getCampaign();

$hej = $campaign->hej();

include 'navigation_subpage.php';
?>

	<div class="content">
		
		<h1>Skicka ett mail till <?php echo $email; ?> <?php if ($name!='') echo "($name)"; ?></h1>
		<p>En kopia av mailet kommer skickas till <?php echo $current_larp->getCampaign()->Email ?>.<br /></p>
		<form action="logic/send_contact_email.php" method="post">
    		<input type="hidden" id="email" name="email" value="<?php echo $email; ?>">
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
