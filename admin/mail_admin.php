<?php
include_once 'header.php';

include 'navigation.php';
?>

<div class="content">
    <h1>E-post</h1>
    <?php 
	    $tableId = "mail";
        echo "<table id='$tableId' class='data'>";
        echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Till</th>".
	    "<th onclick='sortTable(1, \"$tableId\")'>Ämne</th>".
	    "<th onclick='sortTable(2, \"$tableId\")'>Skickat av</th>".
	    "<th onclick='sortTable(4, \"$tableId\")'>Skickat</th>".
	    "<th onclick='sortTable(5, \"$tableId\")'>Fel</th>".
        "<th></th>".
        "</tr>\n";
    	
    	$emails = Email::allBySelectedLARP($current_larp);
    	foreach (array_reverse($emails) as $email) {
    	    $user = User::loadById($email->SenderUserId);
    	    echo "<tr>";
    	    echo "<td>$email->ToName ($email->To)</td>";
    	    echo "<td>$email->Subject</td>";
    	    echo "<td>$user->Name</td>";
    	    echo "<td>$email->SentAt</td>";
    	    echo "<td>";
    	    if (!empty($email->ErrorMessage)) {
    	        echo showStatusIcon(false);
    	    }
    	    echo "</td>";
    	    echo "<td><a href='view_email.php?id=$email->Id'><i class='fa-solid fa-eye'></i></a>\n";
    	    //echo " <a href='mail_admin.php?operation=delete&id=" . $email->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
    	    echo "</tr>";
    	}  	
    	
    	?>
	</table>
	<?php 
include_once '../javascript/table_sort.js';
?>