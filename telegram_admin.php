<?php
include_once 'includes/db.inc.php';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Administration av telegram</title>
</head>
    <body>
    
<?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // collect value of input field
    $name = $_POST['delivery_time'];
    echo "Lägger till telegram";
    
    $stmt = $conn->prepare("INSERT INTO telegrams (Deliverytime, Sender, SenderCity, Reciever, RecieverCity, Message, OrganizerNotes) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $deliverytime, $sender, $sendercity, $reciever, $recievercity, $message, $notes);
    
    // set parameters and execute
    $deliverytime = $_POST['delivery_time'];
    $sender = $_POST['sender'];
    $sendercity = $_POST['sender_city'];
    $reciever = $_POST['reciever'];
    $recievercity = $_POST['reciever_city'];
    $message = $_POST['message'];
    $notes = $_POST['notes'];
    $stmt->execute();
    
    echo $name;
    }
    



?>
    
    <h1>Telegram</h1>
    <?php
    echo  "<table>";
    $sql = "SELECT * FROM telegrams;";
    $result = mysqli_query($conn, $sql);
    $resultCheck = mysqli_num_rows($result);
    
    if ($resultCheck > 0) {
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo  "<tr>";
            echo "<td>" . $row['Id'] . "</td>";
            echo "<td>" . $row['Deliverytime'] . "</td>";
            echo "<td>" . $row['Sender'] . "</td>";
            echo "<td>" . $row['SenderCity'] . "</td>";
            echo "<td>" . $row['Reciever'] . "</td>";
            echo "<td>" . $row['RecieverCity'] . "</td>";
            echo "<td>" . $row['Message'] . "</td>";
            echo "<td>" . $row['OrganizerNotes'] . "</td>";
            
            echo  "</tr>";
        }
    }
    echo "</table>";
    
	?>
	
	<form method="post">
	<table>
		<tr>
			<td><label for="delivery_time">Leveranstid</label></td>
			<td><input type="text" id="delivery_time" name="delivery_time" required></td>
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

				  <input type="submit" value="Lägg till">
		</form>
	
    </body>

</html>