<?php
include_once 'header.php';

global $purpose;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } elseif (isset($_POST['Id'])) {
        $id = $_POST['Id'];
    }
    
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $operation = $_GET['operation'];
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } elseif (isset($_GET['Id'])) {
        $id = $_GET['Id'];
    }
    
}

$multiple=false;

if ($operation == "add_intrigue_message") {
    $purpose = "Lägg till meddelande till intrig";
    $url = "logic/view_intrigue_logic.php";
    $multiple=true;
}


if ($multiple) {
    $type = "checkbox";
    $array="[]";
    
} else {
    $type="radio";
    $array="";
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

include 'navigation.php';
?>


    <div class="content">   
        <h1><?php echo $purpose;?></h1>
        	<h2>Brev</h2>
            <a href="letter_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt brev</a>  
    		    <form action="<?php echo $url;?>" method="post">
    		    <input type="hidden" id="operation" name="operation" value="<?php echo $operation;?>">
    			<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
     		<?php 
     		if (isset($id)) {
     		    echo "<input type='hidden' id='id' name='id' value='$id'>";
     		    echo "<input type='hidden' id='Id' name='Id' value='$id'>";
     		}
     		
     		$letters = Letter::allBySelectedLARP($current_larp);
     		if (empty($letters)) {
    		    echo "Inga registrerade brev";
    		} else {
    		    ?> 
    		    <table class='data'>
    		    <tr><th></th><th>Mottagare</th><th>Meddelande</th><th>Avsändare</th><th>Godkänt</th></tr>
    		    <?php 
    		    foreach ($letters as $letter)  {
    		        echo "<tr>\n";
    		        echo "<td><input type='$type' id='Letter$letter->Id' name='LetterId$array' value='$letter->Id'>";

    		        echo "<td>$letter->Recipient</td>\n";
    		        echo "<td>".mb_strimwidth(str_replace('\n', '<br>', $letter->Message), 0, 100, '...')."</td>\n";
    		        echo "<td>$letter->Signature</td>";
    		        echo "<td>" . showStatusIcon($letter->Approved) . "</td>\n";
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		    echo "<br>";
    		    echo "<input type='submit' value='$purpose'></form>";
    		}
    		?>
			
			<h2>Telegram</h2>
     		<a href="letter_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Skapa nytt telegram</a>
     		<?php 
     		$telegrams = Telegram::allBySelectedLARP($current_larp);
     		if (empty($telegrams)) {
    		    echo "<br><br>Inga registrerade telegram<br>";
    		} else {
    		    ?> 
    		    <table class='data'>
    		    <tr><th></th><th>Tid</th><th>Mottagare</th><th>Meddelande</th><th>Godkänt</th></tr>
    		    <?php 
    		    foreach ($telegrams as $telegram)  {
    		        echo "<tr>\n";
    		        echo "<td><input type='$type' id='Telegram$telegram->Id' name='TelegramId$array' value='$telegram->Id'>";

    		        echo "<td>$telegram->Deliverytime</td>\n";
    		        echo "<td>$telegram->Recipient</td>\n";
    		        echo "<td>$telegram->Message</td>\n";
    		        echo "<td>" . showStatusIcon($telegram->Approved) . "</td>\n";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		    echo "<br>";
    		    echo "<input type='submit' value='$purpose'></form>";
    		    
    		}
    		?>
        
        
        
	</div>
</body>

</html>
