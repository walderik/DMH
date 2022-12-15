<?php
include_once 'includes/db.inc.php';
require 'telegram.php';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Administration av telegram</title>
<link rel="stylesheet" href="includes/admin_system.css">

<script type="text/javascript">  
    function del(id) {
    	var operation = document.getElementById("operation");
		var telegram_id = document.getElementById("Id");
		var sender = document.getElementById("Sender");        		
 		var reciever = document.getElementById("Reciever");       		
		var message = document.getElementById("Message");        		
		
		var submit_button = document.getElementById("submit_button");

       	var result = confirm("Är du säker på att du vill radera telegrammet?");
       	if (result == true) {
    		operation.value = "delete";
    		telegram_id.value = id;
    		sender.value = " ";
    		reciever.value = " ";
    		message.value = " ";
    		submit_button.click();
		}
    }  
    
    
 //   function edit(id); //, sendertxt, sendercitytxt, recievertxt, recievercitytxt, messagetxt) {
   // 	alert("Edit");
    	
    	/*
    	var operation = document.getElementById("operation");
		var telegram_id = document.getElementById("Id");
		var sender = document.getElementById("Sender");        		
		var sendercity = document.getElementById("SenderCity");        		
 		var reciever = document.getElementById("Reciever");       		
 		var recievercity = document.getElementById("RecieverCity");       		
		var message = document.getElementById("Message");        		
		var notes = document.getElementById("OrganizerNotes");        		
		
		var submit_button = document.getElementById("submit_button");

		operation.value = "update";
		telegram_id.value = id;
		sender.value = sendertxt;
		sendercity.value = sendercitytxt;
		reciever.value = recievertxt;
		recievercity.value = recievercitytxt;
		message.value = messagetxt.replace("<br>", "\n");
		//notes.value = notestxt.replace("<br>", "\n");
		submit_button.value = "Uppdatera";
		*/
  //  }  
    

</script>

</head>
<body>
    
   
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
    if ($operation == 'insert') {
        $telegram = Telegram::newFromArray($_POST);
        $telegram->create();
    } else if ($operation == 'delete') {
        Telegram::delete($_POST['Id']);
    } else if ($operation == 'update') {
        echo "Update" . $_POST['Id'];
    } else {
        echo $operation;
    }
}

?>
    
    <h1>Telegram</h1>
    <?php

    $telegram_array = Telegram::all();
    $resultCheck = count($telegram_array);
    if ($resultCheck > 0) {
        echo "<table id='telegrams'>";
        echo "<tr><th>Id</td><th>Leveranstid</th><th>Avsändare</th><th>Avsändarens stad</th><th>Mottagare</th><th>Mottagarens stad</th><th>Meddelande</th><th>Anteckningar</th><th></th><th></th></tr>\n";
        foreach ($telegram_array as $telegram) {
            echo "<tr>\n";
            echo "<td>" . $telegram->id . "</td>\n";
            echo "<td>" . $telegram->deliverytime . "</td>\n";
            echo "<td>" . $telegram->sender . "</td>\n";
            echo "<td>" . $telegram->senderCity . "</td>\n";
            echo "<td>" . $telegram->reciever . "</td>\n";
            echo "<td>" . $telegram->recieverCity . "</td>\n";
            echo "<td>" . str_replace("\n", "<br>", $telegram->message) . "</td>\n";
            echo "<td>" . str_replace("\n", "<br>", $telegram->organizerNotes) . "</td>\n";
            
            //"," . str_replace("\n", "<br>", $telegram->organizerNotes) . 
            //echo "<td>" . "<img src='images/icons8-pencil-20.png' width='20' alt='Redigera' onclick='edit(" . $telegram->id . "," . $telegram->deliverytime . "," . $telegram->sender . "," . $telegram->senderCity . "," . $telegram->reciever . "," . $telegram->recieverCity . "," . str_replace("\n", "<br>", $telegram->message) . ");' /></td>\n";
            //echo "<td>" . "<img src='images/icons8-pencil-20.png' width='20' alt='Redigera' onclick='edit(" . $telegram->id . "," . $telegram->deliverytime . "," . $telegram->sender . "," . $telegram->senderCity . "," . $telegram->reciever . "," . $telegram->recieverCity . "," . str_replace("\n", "<br>", $telegram->message) . ");' /></td>\n";
            echo "<td>" . "<img src='images/icons8-pencil-20.png' width='20' alt='Redigera' onclick='del(" . $telegram->id . ");' /></td>\n";
            echo "<td>" . "<img src='images/icons8-trash-20.png' width='20' alt='Radera' onclick='del(" . $telegram->id . ");' /></td>\n";
            echo "</tr>\n";
        }
        echo "</table>";
    }
    ?>
    <form action="telegram_pdf.php" method="post">
		<input id="submit_button" type="submit" value="Skapa pdf">
	</form>    
	
	<form method="post">
		<input type="hidden" id="operation" name="operation" value="insert"> <input
			type="hidden" id="Id" name="Id" value="-1">
		<table>
			<tr>
				<td><label for="Deliverytime">Leveranstid</label></td>
				<td><input type="datetime-local" id="Deliverytime"
					name="Deliverytime" value="1868-09-13T17:00" min="1868-09-13T17:00"
					max="1868-09-15T13:00" required></td>
			</tr>
			<tr>
				<td><label for="Sender">Avsändare</label></td>
				<td><input type="text" id="Sender" name="Sender" required></td>
			</tr>
			<tr>

				<td><label for="SenderCity">Avsändarens stad</label></td>
				<td><input type="text" id="SenderCity" name="SenderCity"
					value="Junk City" required></td>
			</tr>
			<tr>

				<td><label for="Reciever">Mottagare</label></td>
				<td><input type="text" id="Reciever" name="Reciever" required></td>
			</tr>
			<tr>

				<td><label for="RecieverCity">Mottagarens stad</label></td>
				<td><input type="text" id="RecieverCity" name="RecieverCity"
					value="Slow River" required></td>
			</tr>
			<tr>

				<td><label for="Message">Meddelande</label></td>
				<td><textarea id="Message" name="Message" rows="4" cols="50"
						required></textarea></td>
			</tr>
			<tr>

				<td><label for="OrganizerNotes">Anteckningar om telegrammet</label></td>
				<td><textarea id="OrganizerNotes" name="OrganizerNotes" rows="4"
						cols="50"></textarea></td>

			</tr>

		</table>

		<input id="submit_button" type="submit" value="Lägg till">
	</form>

</body>

</html>