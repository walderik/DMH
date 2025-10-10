<?php
include_once 'header.php';

$mode = "encrypt";
$text = "";
$result = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['mode'])) {
        $mode = $_POST['mode'];
    }
    if (isset($_POST['text'])) {
        $text = $_POST['text'];
    }
    if (!empty($text)) {
        if ($mode == 'encrypt') {
            $result = encodeText(trim($text));
            
        } elseif ($mode == 'decrypt') {
            $result = decodeText(trim($text));
        }
    }
    
}


function encodeText($text) {
    $result = "";
    
    
    //C,J,Q W, X & Z saknar kryptering och ersätts S,I,K,V,KS & S.
    $tobeencoded = $text;
    $tobeencoded = str_replace("C", "S", $tobeencoded);
    $tobeencoded = str_replace("c", "s", $tobeencoded);
    $tobeencoded = str_replace("J", "I", $tobeencoded);
    $tobeencoded = str_replace("j", "J", $tobeencoded);
    $tobeencoded = str_replace("Q", "K", $tobeencoded);
    $tobeencoded = str_replace("q", "k", $tobeencoded);
    
    $tobeencoded = str_replace("W", "V", $tobeencoded);
    $tobeencoded = str_replace("w", "v", $tobeencoded);
    $tobeencoded = str_replace("X", "KS", $tobeencoded);
    $tobeencoded = str_replace("x", "ks", $tobeencoded);
    $tobeencoded = str_replace("Z", "S", $tobeencoded);
    $tobeencoded = str_replace("z", "s", $tobeencoded);
    
    foreach (mb_str_split($tobeencoded) as $char) {
        $result .= encode($char);
    }
    
    return $result;
}

function encode($char) {
    switch (strtolower($char)) {
        case "a":
        case "å":
        case "ä":
            $res = "b";
            break;
        case "d":
            $res = "r";
            break;
        case "e":
            $res = "t";
            break;
        case "f":
            $res = "a";
            break;
        case "g":
            $res = "e";
            break;
        case "h":
            $res = "i";
            break;
        case "i":
            $res = "o";
            break;
        case "k":
            $res = "u";
            break;
        case "l":
            $res = "y";
            break;
        case "m":
            $res = "f";
            break;
        case "n":
            $res = " ";
            break;
        case "o":
        case "ö":
            $res = "d";
            break;
        case "p":
            $res = "g";
            break;
        case "r":
            $res = "f";
            break;
        case "s":
            $res = rand(0,9); //Siffra 0-9
            break;
        case "t":
            $res = "v";
            break;
        case "u":
            $res = "!";
            break;
        case "v":
            $res = "p";
            break;
        case "y":
            $res = "ii";
            break;
        case " ":
            $res = "X";
            break;
        default:
            $res = $char;
    }
    if (ctype_upper($char)) return strtoupper($res);
    return $res;
    
}


function decodeText($text) {
    $result = "";
    
    //Skriver någon C, J, Q eller W i ett krypto ska dessa bara ignoreras.
    $tobedecoded = $text;
    $tobedecoded = str_replace("C", "", $tobedecoded);
    $tobedecoded = str_replace("c", "", $tobedecoded);
    $tobedecoded = str_replace("J", "", $tobedecoded);
    $tobedecoded = str_replace("j", "", $tobedecoded);
    $tobedecoded = str_replace("Q", "", $tobedecoded);
    $tobedecoded = str_replace("q", "", $tobedecoded);
    $tobedecoded = str_replace("W", "", $tobedecoded);
    $tobedecoded = str_replace("w", "", $tobedecoded);
    
    //Hantering av dubbeltecken
    //II = Y, sätter den till C för att hantera att Y = L
    $tobedecoded = str_replace("II", "C", $tobedecoded);
    $tobedecoded = str_replace("ii", "c", $tobedecoded);
    
    
    
    foreach (mb_str_split($text) as $char) {
        $result .= decode($char);
    }
    return $result;
}




function decode($char) {
    switch (strtolower($char)) {
        case "b":
            $res = "a";
            break;
        case "r":
            $res = "d";
            break;
        case "t":
            $res = "e";
            break;
        case "a":
            $res = "f";
            break;
        case "e":
            $res = "g";
            break;
        case "i":
            $res = "h";
            break;
        case "o":
            $res = "i";
            break;
        case "u":
            $res = "k";
            break;
        case "y":
            $res = "l";
            break;
        case "f":
            $res = "m";
            break;
        case " ":
            $res = "n";
            break;
        case "d":
            $res = "o";
            break;
        case "g":
            $res = "p";
            break;
        case "f":
            $res = "r";
            break;
        case "v":
            $res = "t";
            break;
        case "!":
            $res = "u";
            break;
        case "p":
            $res = "v";
            break;
        case "c":
            $res = "Y";
            break;
        case "x":
            $res = " ";
            break;
        default:
            $res = $char;
    }
    if (ctype_upper($char)) return strtoupper($res);
    return $res;
    
}





include 'navigation.php';
?>


    <div class="content">   
        <h1>Krypto</h1>

		
    		<form method="post">
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
			    echo nl2br(htmlspecialchars_decode($text))."<br>";
			    echo "→<br>";
			    echo nl2br(htmlspecialchars_decode($result));
			    
			}
			?>

        
        
        
	</div>
</body>

</html>
