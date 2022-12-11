<?php
include_once 'includes/db.inc.php';
include 'telegram.php';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Administration av telegram</title>
<link rel="stylesheet" href="includes/admin_system.css">

</head>
    <body>
    
<script>
        var operation = document.getElementById("operation");
        var telegram_id = document.getElementById("telegram_id");
  		var submit_button = document.getElementById("submit_button");
  
        function delete_telegram(id) {
        	var result = confirm("Är du säker på att du vill radera telegrammet?);
        	if (result) {
            	operation.value = 'delete';
            	telegram_id.value = id;
            	submit_button.submit();
        	}
        }
</script>
    
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $operation = $_POST['operation'];
        if ($operation == 'insert') {
            echo "Insert";
            $telegram = Telegram.newFromArray($_POST)
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

    $sql = "SELECT * FROM telegrams ORDER BY Deliverytime;";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    
    if ($resultCheck > 0) {
        echo  "<table id='telegrams'>";
        echo "<tr><th>Id</td><th>Leveranstid</th><th>Avsändare</th><th>Avsändarens stad</th><th>Mottagare</th><th>Mottagarens stad</th><th>Meddelande</th><th>Anteckningar</th><th></th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo  "<tr>";
            echo "<td>" . $row['Id'] . "</td>";
            echo "<td>" . $row['Deliverytime'] . "</td>";
            echo "<td>" . $row['Sender'] . "</td>";
            echo "<td>" . $row['SenderCity'] . "</td>";
            echo "<td>" . $row['Reciever'] . "</td>";
            echo "<td>" . $row['RecieverCity'] . "</td>";
            echo "<td>" . str_replace("\n", "<br>", $row['Message']) . "</td>";
            echo "<td>" . str_replace("\n", "<br>", $row['OrganizerNotes']) . "</td>";
            echo "<td>" . "<img src='images/remove-icon-hi.png' width='20' alt='Radera' onclick='confirm(Test);'></td>";
            //echo "<td>" . "<img src='images/remove-icon-hi.png' width='20' alt='Radera' onclick='delete_telegram(" . $row['Id'] .  ");'></td>";
            echo  "</tr>";
        }
        echo "</table>";
    }

    
	?>
	
	<form method="post">
	<input type="hidden" id="operation" name="operation" value="insert">
	<input type="hidden" id="telegram_id" name="telegram_id" value="">
	<table>
		<tr>
			<td><label for="delivery_time">Leveranstid</label></td>
			<td><input type="datetime-local" id="delivery_time"
       name="delivery_time" value="1868-09-13T17:00"
       min="1868-09-13T17:00" max="1868-09-15T13:00" required></td>
		</tr>
		<tr>
			<td><label for="sender">Avsändare</label></td>
			<td><input type="text" id="sender" name="sender" required></td>
		</tr>
		<tr>

			<td><label for="sender_city">Avsändarens stad</label></td>
			<td><input type="text" id="sender_city" name="sender_city" required></td>
		</tr>
		<tr>

			<td><label for="reciever">Mottagare</label></td>
			<td><input type="text" id="reciever" name="reciever" required></td>
		</tr>
		<tr>

			<td><label for="reciever_city">Mottagarens stad</label></td>
			<td><input type="text" id="reciever_city" name="reciever_city" value="Slow River" required></td>
		</tr>
		<tr>

			<td><label for="message">Meddelande</label></td>
			<td>
			<textarea id="message" name="message" rows="4" cols="50"></textarea>
			</td>
		</tr>
		<tr>

			<td><label for="notes">Anteckningar om telegrammet</label></td>
			<td>
			<textarea id="notes" name="notes" rows="4" cols="50"></textarea>
			</td>

		</tr>

	</table>

				  <input id="submit_button" type="submit" value="Lägg till">
		</form>
	
    </body>

</html>