<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

$future_open_larp_array = LARP::allFutureOpenLARPs();
$future_closed_larp_array = LARP::allFutureNotYetOpenLARPs();
$past_larp_array = LARP::allPastLarpsWithRegistrations($current_user);

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
			<h1>Vilket lajv?</h1>
    			<?php

    			$resultCheck = count($future_open_larp_array);
    			 if ($resultCheck > 0) {
    			     ?>
    			     <h3>Kommande lajv</h3>
					<form action="../includes/set_larp.php" method="POST">
					<label for="larp">Välj lajv:</label>
    			     
    			     <select name='larp' id='larp'>
    			<?php
    
    			     foreach ($future_open_larp_array as $larp) {
    			         echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
    			     }
    			     echo "</select>";
    			     echo '<input type="submit" value="Välj">';    			 }
    			 
    			 ?>

			 </form>
			 <br>
    			<?php

    			$resultCheck = count($future_closed_larp_array);
    			 if ($resultCheck > 0) {
    			     ?>
    			     <h3>Kommande lajv (anmälan är inte öppen än)</h3>

					<form action="../includes/set_larp.php" method="POST">
 					<label for="larp">Välj lajv: </label>
    			    <select name='larp' id='larp'>
    
    			<?php
    			     foreach ($future_closed_larp_array as $larp) {
    			         echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
    			     }
    			     echo "</select>";
    			     echo '<input type="submit" value="Välj">';
    			 }
    			 
    			 ?>
    			 
			 </form>
			 <br>
    			<?php

    			$resultCheck = count($past_larp_array);
    			 if ($resultCheck > 0) {
    			     ?>
    			 <h3>Tidigare lajv</h3>    
				<form action="../includes/set_larp.php" method="POST">
				<label for="larp">Välj lajv:</label>
			    <select name='larp' id='larp'>
      			<?php  
    			     foreach ($past_larp_array as $larp) {
    			         echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
    			     }
    			     echo "</select>";
    			     echo '<input type="submit" value="Välj">';
    			 }
    			 
    			 ?>
			 </form>
			 </div>

	</body>
</html>