<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require $root . '/includes/init.php';

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Berghems vänners anmälningssystem</title>
		<link href="../css/style.css" rel="stylesheet" type="text/css">
		<link href="../css/participant_style.css" rel="stylesheet" type="text/css">
		<link rel="icon" type="image/x-icon" href="../images/bv.ico">
		<script src="https://kit.fontawesome.com/30d6e99205.js" crossorigin="anonymous"></script>
	</head>
	<body class="loggedin">



        <nav id="navigation">
          <ul class="links">
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


		<div class="content">
			<h1>Aktivt lajv</h1>
			<label for="larp">Välj lajv:</label>
			<form action="../includes/set_larp.php" method="POST">
    			<?php
    			 $larp_array = LARP::all();
    			 $resultCheck = count($larp_array);
    			 if ($resultCheck > 0) {
    			     echo "<select name='larp' id='larp'>";
    
    			     foreach ($larp_array as $larp) {
    			         echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
    			     }
    			     echo "</select>";
    			 }
    			 else {
    			     echo "<p>Inga registrarade ännu</p>";
    			 }
    			 ?>
    			 <input type="submit" value="Välj">
			 </form>
			 </div>

	</body>
</html>