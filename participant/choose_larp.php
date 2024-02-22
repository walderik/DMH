<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

$future_larp_array = LARP::allFutureLARPs();
//$future_closed_larp_array = LARP::allFutureNotYetOpenLARPs();
$past_larp_array = LARP::allPastLarpsWithRegistrations($current_user);

?>
<!DOCTYPE html>
<html>
<head>
<script>
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <script src="https://kit.fontawesome.com/30d6e99205.js" crossorigin="anonymous"></script>
    <link href="../css/navigation_participant.css" rel="stylesheet" type="text/css">
	<link href="../css/style.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="../images/bv.ico">
	<title>Omnes Mundi, Berghems vänners anmälningssystem</title>
	
</head>
<body>
<style>
.content > p {
    margin: 5px;
    margin-left:20px;
    padding:0px;
} 


div.border
{
    padding: 10px;
    border: 2px solid #000;
    border-radius: 15px;
    -moz-border-radius: 15px;
}


</style>
<div class="topnav"  id="myTopnav">
    <div id="right">

	  
	  <div id="placeholder" class="dropdown">&nbsp;<br>&nbsp;
	    <button class="dropbtn">   
	    </button>
	  </div> 


	  <a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut&nbsp;&nbsp;&nbsp;&nbsp;</a>
	  <a href="javascript:void(0);" style="font-size:15px;" class="icon" onclick="myFunction()">&#9776;</a>
	  </div>
    
    </div>

		<div class="content">
			<h1>Välj lajv</h1>
    			<?php

    			$resultCheck = count($future_larp_array);
    			 if ($resultCheck > 0) {

			        echo "<h3>Kommande lajv</h3>";
 
			        foreach ($future_larp_array as $larp) {
			            echo "<div class='border'>";
			            echo "<form action='../includes/set_larp.php' method='POST'>";
			            echo "<input type='hidden' value='" . $larp->Id . "' name='larp' id='larp'>\n";
			            echo "<strong>$larp->Name</strong><br>\n";
			            $startdate=date_create($larp->StartDate);
			            $enddate=date_create($larp->EndDate);
			            $fmt = new \IntlDateFormatter('sv_SE', NULL, NULL);
			            $fmt->setPattern('d MMMM');
			            // See: https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax for pattern syntax
			            
			            echo $fmt->format($startdate) . " - " . $fmt->format($enddate)."<br>\n";
			            echo "Kampanj: ".$larp->getCampaign()->Name."<br>\n";
			            if ($larp->mayRegister()) {
			                echo "Anmälan är öppen.<br>\n";
			                $lastregistration=date_create($larp->LatestRegistrationDate);
			                echo "Sista anmälningsdag: ".$fmt->format($lastregistration)."<br>\n";
			            }
			            if (isset($larp->Description)) {
			                echo "<br>";
			                echo nl2br(htmlspecialchars($larp->Description));
			                echo "<br>";
			            }
			            echo "<br>";
			            echo '<button type="submit">Välj</button>';
			            echo "</form>";
			            echo "</div>";
			            
			        }
			        
    			     echo "<br><hr>";

    			 }
    			 
    			$resultCheck = count($past_larp_array);
    			 if ($resultCheck > 0) {
    			     ?>
     			 
    			 <h3>Lajv du har varit på</h3> 
    			 <p>Välj det här om du vill fylla i vad som hände på lajvet.</p>   
      			<?php  
      			echo "<form action='../includes/set_larp.php' method='POST'>";
      			echo "<label for='larp'>Välj lajv: </label>";
      			echo "<select name='larp' id='larp'>";
      			foreach ($past_larp_array as $larp) {
    			         echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
    			     }
    			     echo "</select>";
    			     echo '<input type="submit" value="Välj">';
    			     echo "<br><hr>";
    			 }
    			 echo "</form>";
    			 
    			 ?>
			 <?php 
			     $larps_organizer = array();
    			 $campaigns = Campaign::organizerForCampaigns($current_user);
    			 foreach ($campaigns as $campaign) {
    			     

    			     $larps_in_campaign=LARP::allByCampaign($campaign->Id);
    			     $larps_organizer = array_merge($larps_organizer, $larps_in_campaign);
    			 }
    			 $larps = LARP::organizerForLarps($current_user);
    			 $larps_organizer = array_merge($larps_organizer, $larps);
    			 
 
    			 $larps_organizer = array_udiff($larps_organizer, array_merge($future_larp_array, $past_larp_array),
    			     function ($objOne, $objTwo) {
    			         return $objOne->Id - $objTwo->Id;
    			     });


    			 
    			 if (!empty($larps_organizer)) {
        			 echo "<h3>Arrangör</h3>";
        			 echo "<p>Eftersom du är arrangör kan du även välja bland dessa lajv.</p>";
        			 echo "<form action='../includes/set_larp.php' method='POST'>";
        			 echo "<label for='larp'>Välj lajv: </label>";
        			 echo "<select name='larp' id='larp'>";
        			 
        			 foreach ($larps_organizer as $larp) {
        			     echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
        			 }
        			 echo "</select>";
        			 echo '<input type="submit" value="Välj">';
        			 echo "<br><hr>";
        			 echo "</form>";
    			 
    			 }
    			 
    			 
    			 
    			 if (isset($_SESSION['admin'])) {
    			     echo "<h3>OM Admin</h3>";
    			     echo "<p>Eftersom du är OM admin har du tillgång till alla lajv.</p>";
    			     echo "<form action='../includes/set_larp.php' method='POST'>";
    			     echo "<label for='larp'>Välj lajv: </label>";
    			     echo "<select name='larp' id='larp'>";
    			     $larps = LARP::all();
    			     foreach ($larps as $larp) {
    			         echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
    			     }
    			     echo "</select>";
    			     echo '<input type="submit" value="Välj">';
    			     echo "<br><hr>";
    			     echo "</form>";
    			     
    			 }
    			 
    			 
    			 ?>
    			     
  
			 
			 </div>

	</body>
</html>