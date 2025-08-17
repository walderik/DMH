<?php
include_once 'header.php';

$mode = "encrypt";
$text = "";
$result = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['mode'])) {
        $mode = $_GET['mode'];
    }
    if (isset($_GET['text'])) {
        $text = $_GET['text'];
    }
    if (isset($_GET['result'])) {
        $result = $_GET['result'];
    }
}


include 'navigation.php';
?>


    <div class="content">   
        <h1>Krypto</h1>

		
    		<form action="logic/encrypt_decrypt.php" method="post">
    			<textarea id='text' name ='text' rows="3" cols ="100"><?php echo $text;?></textarea>
    			<br><br>
    			<select name="mode" id="mode">
                  <option value="encrypt" <?php if ($mode == 'encrypt') echo "selected"; ?>>Kryptera</option>
                  <option value="decrypt" <?php if ($mode == 'decrypt') echo "selected"; ?>>Avkryptera</option>
                </select>
				<br><br>
				<input type="submit" value="Kör">
			</form>
			<br><br>
			<?php if (!empty($result)) {
			    echo "$text<br>";
			    echo "→<br>";
			    echo $result;
			    
			}
			?>

        
        
        
	</div>
</body>

</html>
