<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/init.php';

if (!isset($_SESSION['navigation'])) {
    header('Location: ../participant/index.php');
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
} elseif ($_SESSION['navigation'] == Navigation::PARTICIPANT) {
    include '../participant/header.php';
    $navigation =  '../participant/navigation.php';
} else {
    header('Location: ../participant/index.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $emailId = $_GET['id'];
    }
    else {
        header('Location: index.php?0');
        exit;
    }
}

$email = Email::loadById($emailId); 
$attachements = $email->attachments();

if ($_SESSION['navigation'] == Navigation::LARP) {
    if ($current_larp->Id != $email->LarpId && !is_null($email->LarpId)) {
        header('Location: ../participant/index.php?1'); //Emailet är inte för detta lajvet
        exit;
    }
} elseif ($_SESSION['navigation'] == Navigation::PARTICIPANT) {
    $person = $email->toPerson();
    if ($person->Id != $current_person->Id) {
        header('Location: ../participant/index.php'); //Emailet är inte för dig
        exit;
    }
} else {
    if (!is_null($email->LarpId)) {
        header('Location: ../participant/index.php'); //Emailet är för ett lajv
        exit;
    }
}

$user = User::loadById($email->SenderUserId);

include $navigation;

?>
<link href='../css/participant_style.css' rel='stylesheet' type='text/css'>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-envelope"></i> 
			<?php echo $email->Subject ?>
		</div>
		
  		<div class='itemcontainer'>
       	<div class='itemname'>Skickat av</div>
    		<?php 
    		if (isset($user)) {
			 echo "$user->Name";
			} else {
			    echo "Systemet";
			}
			?>
		</div>
		
  		<div class='itemcontainer'>
       	<div class='itemname'>Till</div>
    		<?php 
    		if (!($to_array = @unserialize($email->To))) {
    		    $to = $email->To;
    		} elseif (!empty($to_array)) {
    		    $to = implode(", ", $to_array);
    		}
    		if (!empty($to)) $to = "";
    		
    		echo "$email->ToName $to";
    		?>
		</div>

  		<div class='itemcontainer'>
       	<div class='itemname'>Ämne</div>
		<?php echo $email->Subject ?>
		</div>

  		<div class='itemcontainer'>
       	<div class='itemname'>När</div>
		<?php echo $email->SentAt ?>
		</div>

		<?php if (!empty($email->ErrorMessage) && $_SESSION['navigation'] != Navigation::PARTICIPANT) { ?>
		    <div class='itemcontainer'>
		    <div class='itemname'>Felmeddelande</div>
		    <?php echo $email->ErrorMessage ?>
			</div>
		<?php }?>

  		<div class='itemcontainer'>
       	<div class='itemname'>Meddelande</div>
		<?php echo $email->mailContent(); ?>
		</div>
		
		<?php if (!empty($attachements)) {?>
		    <div class='itemcontainer'>
		    <div class='itemname'>Bilagor</div>
    			<?php 
    			foreach ($attachements as $attachment) {
    			    echo "<a href='view_email_attachment.php?id=$attachment->Id' target='_blank'>$attachment->Filename</a><br>";
    			}
    			?>
			</div>
				
		<?php } ?>
	</div>

</body>
</html>
