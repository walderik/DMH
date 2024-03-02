<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $emailId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$email = Email::loadById($emailId); 
$attachements = $email->attachments();

if ($current_larp->Id != $email->LarpId && !is_null($email->LarpId)) {
    header('Location: index.php'); //Emailet är inte för detta lajvet
    exit;
}

$user = User::loadById($email->SenderUserId);

include 'navigation.php';
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
			?>
			<tr><td>Till</td><td><?php echo "$email->ToName ($to)"; ?></td></tr>
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
