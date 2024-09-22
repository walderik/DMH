<?php
$referer = '';
if (isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER'];
else {
    header('Location: ../participant/index.php');
    exit;
}

$isLarp = false;

if (str_contains($referer, "/admin/")) {
    include '../admin/header.php';
    $navigation = '../admin/navigation.php';
    $isLarp = true;
} elseif (str_contains($referer, "/campaign/")) {
    include '../campaign/header.php';
    $navigation =  '../campaign/navigation.php';
} elseif (str_contains($referer, "/board/")) {
    include '../board/header.php';
    $navigation =  '../board/navigation.php';
} elseif (str_contains($referer, "/houses/")) {
    include '../houses/header.php';
    $navigation =  '../houses/navigation.php';
} elseif (str_contains($referer, "/site-admin/")) {
    include '../site-admin/header.php';
    $navigation =  '../site-admin/navigation.php';
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

<script src="../javascript/table_sort.js"></script>

<div class="content">
    <h1>E-post</h1>
    <?php 
        if (!empty($unsent_emails)) {
            echo "<strong>".count($unsent_emails) ."</strong> mail har ännu inte skickats iväg. <br>Sidan kommer automatiskt att laddas om tills alla har skickats. Du måste inte stanna på den här sidan, men gå gärna tillbaka hit efteråt så att du ser att alla mail verkligen har kommit iväg.";
        }
        
	    $tableId = "mail";
        echo "<table id='$tableId' class='data'>";
        echo "<tr><th onclick='sortTable(0, \"$tableId\");' width='30%'>Till</th>".
	    "<th onclick='sortTable(1, \"$tableId\")'>Ämne</th>".
	    "<th onclick='sortTable(2, \"$tableId\")'></th>".
	    "<th onclick='sortTable(3, \"$tableId\")'>Skickat av</th>".
	    "<th onclick='sortTable(4, \"$tableId\")'>Skickat</th>".
	    "<th onclick='sortTable(5, \"$tableId\")'>Fel</th>".
        "</tr>\n";
    	
        if ($isLarp) $emails = Email::allBySelectedLARPAndCommon($current_larp);
        else $emails = Email::allCommon();

    	foreach (array_reverse($emails) as $email) {
    	    $sendUserName = "";
    	    if (isset($email->SenderUserId)) {
    	       $user = User::loadById($email->SenderUserId);
    	       $sendUserName = $user->Name;
    	    }
    	    
    	    if (!($to_array = @unserialize($email->To))) {
    	        $to = $email->To;
    	    } elseif (!empty($to_array)) {
    	        $to = implode(", ", $to_array);
    	    }
    	    if (!empty($to)) $to = "($to)";
    	    
    	    echo "<tr>";
    	    echo "<td>$email->ToName $to</td>";
    	    echo "<td><a href='view_email.php?id=$email->Id'>$email->Subject</a></td>";
    	    
    	    $attachements = $email->attachments();
    	    echo "<td>";
    	    if (!empty($attachements)) echo "<i class='fa-solid fa-paperclip'></i>";
            echo "</td>";
    	    echo "<td>$sendUserName</td>";
    	    echo "<td>$email->SentAt</td>";
    	    echo "<td>";
    	    if (!is_null($email->ErrorMessage)) {
    	        echo showStatusIcon(false);
    	    }
    	    echo "</td>";
    	    //echo " <a href='mail_admin.php?operation=delete&id=" . $email->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
    	    echo "</tr>";
    	}  	
    	
    	?>
	</table>
