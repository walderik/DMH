<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/includes/init.php';

if (!isset($_SESSION['navigation'])) {
    header('Location: ../participant/index.php');
    exit;
}

$onlyCommon = false;
if (isset($_GET['common'])) $onlyCommon = true;

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


include $navigation;

$unsent_emails = Email::allUnsent();

if (!empty($unsent_emails)) {
    $currentPageUrl = 'http://' . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
    echo "<meta http-equiv='refresh' content='15'; URL='$currentPageUrl'>";
}


?>

<style>
th {
  cursor: pointer;
}

</style>


<link href='../css/participant_style.css' rel='stylesheet' type='text/css'>
<script src="../javascript/table_sort.js"></script>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-envelope"></i> E-post
		</div>

   		<div class='itemcontainer'>
		<div  style='display:table'>
    <?php 
        if (!empty($unsent_emails)) {
            echo "<strong>".count($unsent_emails) ."</strong> mail har ännu inte skickats iväg. <br>Sidan kommer automatiskt att laddas om tills alla har skickats. Du måste inte stanna på den här sidan, men gå gärna tillbaka hit efteråt så att du ser att alla mail verkligen har kommit iväg.";
        }
        if ($_SESSION['navigation'] == Navigation::PARTICIPANT) {
            if (isset($current_larp)) $emails = Email::allForPersonAtLarp($current_person, $current_larp);
            else $emails = Email::allForPerson($current_person);
        } elseif ($_SESSION['navigation'] == Navigation::LARP && !$onlyCommon) {
            $emails = Email::allBySelectedLARP($current_larp);
            echo "<a href='mail_admin.php?common=1'>Visa system-mail</a><br>";
        }
        else $emails = Email::allCommon();
        
        if (empty($emails)) {
            echo "Du har inte fått några meddelanden.";
        } else {
        
    	    $tableId = "mail";
            echo "<table id='$tableId' class='data'>";
            //echo "<table id='$tableId' class='participant_table' style='width:93%;padding: 6px; margin: 16px 16px 0px;'>";
            echo "<tr>";
            $col = 0;
            if ($_SESSION['navigation'] != Navigation::PARTICIPANT) echo "<th onclick='sortTable($col++, \"$tableId\");' width='30%'>Till</th>";
            echo "<th onclick='sortTable($col++, \"$tableId\")'>Ämne</th>".
    	    "<th onclick='sortTable($col++, \"$tableId\")'></th>".
    	    "<th onclick='sortTable($col++, \"$tableId\")'>Skickat av</th>".
    	    "<th onclick='sortTable($col++, \"$tableId\")'>Skickat</th>";
            if ($_SESSION['navigation'] != Navigation::PARTICIPANT) echo "<th onclick='sortTable($col++, \"$tableId\")'>Fel</th>";
            echo "</tr>\n";
        	
    
        	foreach (array_reverse($emails) as $email) {
        	    $senderName = "Omnes Mundi";
        	    if (isset($email->SenderPersonId)) {
        	        $person = Person::loadById($email->SenderPersonId);
        	        $senderName = $person->Name;
        	    }
        	    
        	    echo "<tr>";
        	    
        	    if ($_SESSION['navigation'] != Navigation::PARTICIPANT) {
            	    if (!($to_array = @unserialize($email->To))) {
            	        $to = $email->To;
            	    } elseif (!empty($to_array)) {
            	        $to = implode(", ", $to_array);
            	    }
            	    if (!empty($to)) $to = "($to)";
            	    
            	    echo "<td>$email->ToName $to</td>";
        	    }
        	    echo "<td><a href='view_email.php?id=$email->Id'>$email->Subject</a></td>";
        	    
        	    $attachements = $email->attachments();
        	    echo "<td>";
        	    if (!empty($attachements)) echo "<i class='fa-solid fa-paperclip'></i>";
                echo "</td>";
        	    echo "<td>$senderName</td>";
        	    echo "<td>$email->SentAt</td>";
        	    if ($_SESSION['navigation'] != Navigation::PARTICIPANT) {
            	    echo "<td>";
            	    if (!is_null($email->ErrorMessage)) {
            	        echo showStatusIcon(false);
            	    }
        	    }
        	    echo "</td>";
        	    //echo " <a href='mail_admin.php?operation=delete&id=" . $email->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
        	    echo "</tr>";
        	} 
        	echo "</table>";
        }
    	?>

</div>
</div>