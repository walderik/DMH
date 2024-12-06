<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

$future_larp_array = LARP::allFutureLARPs();
//$future_closed_larp_array = LARP::allFutureNotYetOpenLARPs();
$past_larp_array = LARP::allPastLarpsWithRegistrations($current_person);
$current_participating_larp_array = LARP::currentParticipatingLARPs($current_person);

$referer = '';
if (isset($_SERVER['HTTP_REFERER'])) $referer = $_SERVER['HTTP_REFERER'];

if (sizeof($current_participating_larp_array) == 1 AND str_contains($referer, '/regsys/index.php')) {
    header('Location: ../includes/set_larp.php?larp='.$current_participating_larp_array[0]->Id);
    exit;
}
include "navigation.php";

?>

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

		<div class="content">
			<h1>Välj lajv</h1>
			
		
    			<?php
    			
    			$resultCheck = count($current_participating_larp_array);
    			if ($resultCheck > 0) {
    			    echo "<h3>Pågående lajv</h3>";
    			    
    			    foreach ($current_participating_larp_array as $larp) {
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
    			        
    			    }
        			echo "<br>";
        			echo '<button type="submit">Välj</button>';
        			echo "</form>";
        			echo "</div>";
        			echo "<br><hr>";
    			}
    			

    			


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
      			foreach (array_reverse($past_larp_array) as $larp) {
    			         echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
    			     }
    			     echo "</select> ";
    			     echo '<input type="submit" value="Välj">';
    			     echo "<br><hr>";
    			 }
    			 echo "</form>";
    			 
    			 ?>
			 <?php 
			     $larps_organizer = array();
    			 $campaigns = Campaign::organizerForCampaigns($current_person);
    			 foreach ($campaigns as $campaign) {
    			     

    			     $larps_in_campaign=LARP::allByCampaign($campaign->Id);
    			     $larps_organizer = array_merge($larps_organizer, $larps_in_campaign);
    			 }
    			 $larps = LARP::organizerForLarps($current_person);
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
        			 
        			 foreach (array_reverse($larps_organizer) as $larp) {
        			     echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
        			 }
        			 echo "</select> ";
        			 echo '<input type="submit" value="Välj">';
        			 echo "<br><hr>";
        			 echo "</form>";
    			 
    			 }
    			 
    			 
    			 
    			 if (AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)) {
    			     echo "<h3>OM Admin</h3>";
    			     echo "<p>Eftersom du är OM admin har du tillgång till alla lajv.</p>";
    			     echo "<form action='../includes/set_larp.php' method='POST'>";
    			     echo "<label for='larp'>Välj lajv: </label>";
    			     echo "<select name='larp' id='larp'>";
    			     $larps = LARP::all();
    			     foreach (array_reverse($larps) as $larp) {
    			         echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
    			     }
    			     echo "</select> ";
    			     echo '<input type="submit" value="Välj">';
    			     echo "<br><hr>";
    			     echo "</form>";
    			     
    			 }
    			 
    			 
    			 ?>
    			     
  
			 
			 </div>

	</body>
</html>