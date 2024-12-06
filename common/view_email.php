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


	<div class="content">
		<h1><?php echo $email->Subject ?></h1>
	
		<div>
		<table>
    		<?php 
    		if (!($to_array = @unserialize($email->To))) {
    		    $to = $email->To;
    		} elseif (!empty($to_array)) {
    		    $to = implode(", ", $to_array);
    		}
    		?>
    		<?php 
    		if (isset($user)) {
			 echo "<tr><td>Skickat av</td><td>$user->Name</td></tr>";
			} else {
			    echo "<tr><td>Skickat av</td><td>Systemet</td></tr>";
			}
			
			if (!empty($to)) $to = "($to)";
			?>
			
			<tr><td>Till</td><td><?php echo "$email->ToName $to"; ?></td></tr>
			<tr><td>Ämne</td><td><?php echo $email->Subject ?></td></tr>
			<tr><td>När</td><td><?php echo $email->SentAt ?></td></tr>
			<?php 
			if (!empty($email->ErrorMessage)) {
			    echo "<tr><td>Felmeddelande</td><td><b>$email->ErrorMessage</b></td></tr>";
			}
			?>
			<tr><td colspan = '2' style='font-weight: normal'>
			<h2>Meddelande</h2>
			<?php echo $email->mailContent(); ?>
			</td>
			</tr>
			<?php if (!empty($attachements)) {?>
    			<tr>
    			<tr><td colspan = '2' style='font-weight: normal'>
    			<h2>Bilagor</h2>
    			
    			<?php 
    			foreach ($attachements as $attachment) {
    			    echo "<a href='view_email_attachment.php?id=$attachment->Id' target='_blank'>$attachment->Filename</a><br>";
    			}
    			?>
    			</td>
    			</tr>
			<?php } ?>
		</table>		
		</div>

</body>
</html>
