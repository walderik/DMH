<?php

include_once 'header.php';

global $current_larp;

$name = '';
$type = "one";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['send_one'])) {
        $type="one";
        $email = $_POST['email'];
        $name = $_POST['name'];
    } elseif (isset($_POST['send_several'])) {
        $type="several";
        $subject = $_POST['subject'];
        $email = $_POST['email'];
        $name = $_POST['name'];
    }
}


if (empty($type)) {
    header('Location: index.php');
    exit;
}

$referer = '';
if (isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER'];

$hej = 'Hej';

include 'navigation.php';
?>

	<div class="content">
		
		<?php 
		switch ($type) {
		    case "one":
		        echo "<h1>Skicka ett mail till $name ($email)</h1>";
		        break;
		    case "several":
		        echo "<h1>Skicka ett utskick till alla $name</h1>\n";
		        echo "Det kommer ta några minuter att skicka till alla.<br>Det går iväg som mest ett mail var 15 sekund till max 15 mottagare.<br>\n";
		        echo "För att mailen ska gå iväg måste du fortsätta att använda systemet, eller låta sidan med skickad epost vara öppen.<br>\n";
		        break;
		}
		
		?>
    	
    	
		<form action="logic/send_contact_email.php" method="post" enctype="multipart/form-data">
		<?php 
		if (isset($email)) {
    		if (is_array($email)) {
    		    foreach ($email as $emailStr)  {
    		        echo "<input type='hidden' name='email[]' value='$emailStr'>\n";
    		    }
    		    
    		} else {
    		    echo "<input type='hidden' id='email' name='email' value='$email'>";
    		}
		}
		
		if (isset($subject)) {
		    echo "<input type='hidden' id='subject' name='subject' value='$subject'>";
		    
		}
		
		?>

    		<input type="hidden" id="type" name="type" value="<?php echo $type; ?>">
    		<input type="hidden" id="referer" name="referer" value="<?php echo $referer; ?>">
    		
    		<p><br />
    		<p>
    		<?php 
    		if($type=="several") {
    		    echo "$hej ";
    		    echo "<input id='name' name='name' value='$name'>";
    		} else {
    		    echo "<input type='hidden' id='name' name='name' value='$name;'>";
    		    echo "$hej $name"; 
    		}
    		echo "!";
    		?>
    		
    		 <br></p>
			<p><textarea id="text" name="text" rows="8" cols="121" maxlength="60000" required></textarea></p>
			Med vänliga hälsningar<br /><br />
			<b>Berghems vänner</b><br>

			<br><hr><br>
			Ladda upp en pdf som bilaga om du vill. Max storlek 5 MB och bara pdf:er.<br><br>
			<input type="file" name="bilaga" id="bilaga"><br>

			<br>
			(Tryck inte på "Skicka" innan den valda filen laddats upp ordentligt. Det tar ett litet tag.)
    		<br>
    		<br>
    		<hr><br>
    		<input type="submit" value="Skicka">
		</form>

	</div>


</body>
</html>
