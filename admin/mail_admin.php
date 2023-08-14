<?php
include_once 'header.php';

include 'navigation.php';
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
	    $tableId = "mail";
        echo "<table id='$tableId' class='data'>";
        echo "<tr><th onclick='sortTable(0, \"$tableId\");' width='30%'>Till</th>".
	    "<th onclick='sortTable(1, \"$tableId\")'>Ämne</th>".
	    "<th onclick='sortTable(2, \"$tableId\")'></th>".
	    "<th onclick='sortTable(3, \"$tableId\")'>Skickat av</th>".
	    "<th onclick='sortTable(4, \"$tableId\")'>Skickat</th>".
	    "<th onclick='sortTable(5, \"$tableId\")'>Fel</th>".
        "</tr>\n";
    	
    	$emails = Email::allBySelectedLARP($current_larp);
    	foreach (array_reverse($emails) as $email) {
    	    $user = User::loadById($email->SenderUserId);
    	    
    	    if (!($to_array = @unserialize($email->To))) {
    	        $to = $email->To;
    	    } elseif (!empty($to_array)) {
    	        $to = implode(", ", $to_array);
    	    }
    	    
    	    echo "<tr>";
    	    echo "<td>$email->ToName ($to)</td>";
    	    echo "<td><a href='view_email.php?id=$email->Id'>$email->Subject</a></td>";
    	    
    	    $attachements = $email->attachments();
    	    echo "<td>";
    	    if (!empty($attachements)) echo "<i class='fa-solid fa-paperclip'></i>";
            echo "</td>";
    	    echo "<td>$user->Name</td>";
    	    echo "<td>$email->SentAt</td>";
    	    echo "<td>";
    	    if (!empty($email->ErrorMessage)) {
    	        echo showStatusIcon(false);
    	    }
    	    echo "</td>";
    	    //echo " <a href='mail_admin.php?operation=delete&id=" . $email->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
    	    echo "</tr>";
    	}  	
    	
    	?>
	</table>
