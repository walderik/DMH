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

      <script type = "text/javascript">  
            function del(id) {
            	var operation = document.getElementById("operation");
        		var telegram_id = document.getElementById("Id");
  				var submit_button = document.getElementById("submit_button");
  
               	var result = confirm("Är du säker på att du vill radera telegrammet? " + id);
               	if (result == true) {
            		operation.value = "delete";
            		alert("Set " + operation.value);

            		telegram_id.value = id;
            		alert("Set " + telegram_id.value);
            		submit_button.click();
        		}
            }  

</script>
 
</head>
    <body>
    
   
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $operation = $_POST['operation'];
        if ($operation == 'insert') {
            echo "Insert";
            $telegram = Telegram::newFromArray($_POST);
            $telegram->create;
        }
        else if ($operation == 'delete'){
            echo "Delete";
        }
        else if ($operation == 'update') {
            echo "Update";
        }
        else {
            echo $operation;
        }
    
    }
    



?>
    
    <h1>Telegram</h1>
    <?php

//     $sql = "SELECT * FROM telegrams ORDER BY Deliverytime;";
//     $result = mysqli_query($conn, $sql);
//     $resultCheck = mysqli_num_rows($result);
    
//     if ($resultCheck > 0) {
//         echo  "<table id='telegrams'>";
//         echo "<tr><th>Id</td><th>Leveranstid</th><th>Avsändare</th><th>Avsändarens stad</th><th>Mottagare</th><th>Mottagarens stad</th><th>Meddelande</th><th>Anteckningar</th><th></th></tr>";
//         while ($row = mysqli_fetch_assoc($result)) {
//             echo  "<tr>";
//             echo "<td>" . $row['Id'] . "</td>";
//             echo "<td>" . $row['Deliverytime'] . "</td>";
//             echo "<td>" . $row['Sender'] . "</td>";
//             echo "<td>" . $row['SenderCity'] . "</td>";
//             echo "<td>" . $row['Reciever'] . "</td>";
//             echo "<td>" . $row['RecieverCity'] . "</td>";
//             echo "<td>" . str_replace("\n", "<br>", $row['Message']) . "</td>";
//             echo "<td>" . str_replace("\n", "<br>", $row['OrganizerNotes']) . "</td>";
//             echo "<td>" . "<img src='images/remove-icon-hi.png' width='20' alt='Radera' onclick='confirm(Test);'></td>";
//             //echo "<td>" . "<img src='images/remove-icon-hi.png' width='20' alt='Radera' onclick='delete_telegram(" . $row['Id'] .  ");'></td>";
//             echo  "</tr>";
//         }
//         echo "</table>";
//     }
        $telegram_array = Telegram::all();
        $resultCheck = count($telegram_array);
        if ($resultCheck > 0) {
            echo  "<table id='telegrams'>";
            echo "<tr><th>Id</td><th>Leveranstid</th><th>Avsändare</th><th>Avsändarens stad</th><th>Mottagare</th><th>Mottagarens stad</th><th>Meddelande</th><th>Anteckningar</th><th></th></tr>";
            foreach($telegram_array as $telegram) {
                echo  "<tr>";
                echo "<td>" . $telegram->id . "</td>";
                echo "<td>" . $telegram->deliverytime . "</td>";
                echo "<td>" . $telegram->sender . "</td>";
                echo "<td>" . $telegram->senderCity . "</td>";
                echo "<td>" . $telegram->reciever . "</td>";
                echo "<td>" . $telegram->recieverCity . "</td>";
                echo "<td>" . str_replace("\n", "<br>", $telegram->message) . "</td>";
                echo "<td>" . str_replace("\n", "<br>", $telegram->organizerNotes) . "</td>";
                echo "<td>" . "<img src='images/remove-icon-hi.png' width='20' alt='Radera' onclick='del(" . $telegram->id . ");' /></td>";
                echo  "</tr>";
            }
            echo "</table>";
        }
	?>
	
	<form method="post">
	<input type="hidden" id="operation" name="operation" value="insert">
	<input type="hidden" id="Id" name="telegram_id" value="">
	<table>
		<tr>
			<td><label for="Deliverytime">Leveranstid</label></td>
			<td><input type="datetime-local" id="Deliverytime"
       name="Deliverytime" value="1868-09-13T17:00"
       min="1868-09-13T17:00" max="1868-09-15T13:00" required></td>
		</tr>
		<tr>
			<td><label for="Sender">Avsändare</label></td>
			<td><input type="text" id="Sender" name="Sender" required></td>
		</tr>
		<tr>

			<td><label for="SenderCity">Avsändarens stad</label></td>
			<td><input type="text" id="SenderCity" name="SenderCity" required></td>
		</tr>
		<tr>

			<td><label for="Reciever">Mottagare</label></td>
			<td><input type="text" id="Reciever" name="Reciever" required></td>
		</tr>
		<tr>

			<td><label for="RecieverCity">Mottagarens stad</label></td>
			<td><input type="text" id="RecieverCity" name="RecieverCity" value="Slow River" required></td>
		</tr>
		<tr>

			<td><label for="Message">Meddelande</label></td>
			<td>
			<textarea id="Message" name="Message" rows="4" cols="50"></textarea>
			</td>
		</tr>
		<tr>

			<td><label for="OrganizerNotes">Anteckningar om telegrammet</label></td>
			<td>
			<textarea id="OrganizerNotes" name="OrganizerNotes" rows="4" cols="50"></textarea>
			</td>

		</tr>

	</table>

				  <input id="submit_button" type="submit" value="Lägg till">
		</form>
	
    </body>

</html>