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


		
    			<?php
    			
    			$resultCheck = count($current_participating_larp_array);
    			if ($resultCheck > 0) {
    			    echo "<div class='itemselector'>";
    			    echo "<div class='header'>";
    			    
    			    echo "<i class='fa-solid fa-shield-halved'></i> Pågående lajv";
	                echo "</div>";

    			    foreach ($current_participating_larp_array as $larp) {
    			        echo "<div class='itemcontainer borderbottom'>";
    			        echo "<div class='itemname'>$larp->Name</div>";
    			        
    			        echo "<form action='../includes/set_larp.php' method='POST'>";
    			        echo "<input type='hidden' value='" . $larp->Id . "' name='larp' id='larp'>\n";
    			        $startdate=date_create($larp->StartDate);
    			        $enddate=date_create($larp->EndDate);
    			        $fmt = new \IntlDateFormatter('sv_SE', NULL, NULL);
    			        $fmt->setPattern('d MMMM');
    			        // See: https://unicode-org.github.io/icu/userguide/format_parse/datetime/#datetime-format-syntax for pattern syntax
    			        
    			        echo $fmt->format($startdate) . " - " . $fmt->format($enddate)."<br>\n";
    			        echo "Kampanj: ".$larp->getCampaign()->Name."<br>\n";
    			        
            			echo "<div class='center'><button class='button-18' type='submit'>Välj</button></div>";
            			
            			echo "</form>";
            			echo "</div>";
    			    }
    			    echo "</div>";
    			}
    			

    			


    			$resultCheck = count($future_larp_array);
    			 if ($resultCheck > 0) {

    			     echo "<div class='itemselector'>";
    			     echo "<div class='header'>";
    			     
    			     echo "<i class='fa-solid fa-shield-halved'></i> Kommande lajv";
    			     echo "</div>";
    			     
			        foreach ($future_larp_array as $larp) {
			            echo "<div class='itemcontainer borderbottom'>";
			            echo "<div class='itemname'>$larp->Name</div>";
			            
			            echo "<form action='../includes/set_larp.php' method='POST'>";
			            echo "<input type='hidden' value='" . $larp->Id . "' name='larp' id='larp'>\n";
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
			                echo "Sista anmälningsdag: ".$fmt->format($lastregistration)."\n";
			            }
			            
						if (!empty(trim($larp->Description))) {
							echo "<br>";
							echo "<span class='full-description' style='display:none;'>" . nl2br(htmlspecialchars($larp->Description)) . "</span>";
							echo "<button type='button' class='read-more'>Läs mer</button>";
						}
					
			            echo "<br>";
			            echo "<div class='center'><button class='button-18' type='submit'>Välj</button></div>";
			            echo "</form>";
			            echo "</div>"; 
			        }
			        
			        echo "</div>";
    			 }
    			 
    			$resultCheck = count($past_larp_array);
    			 if ($resultCheck > 0) {
    			     ?>
     			 
		     	<div class='itemselector'>
				<div class="header">

				<i class="fa-solid fa-shield-halved"></i>
				Lajv du har varit på
				</div>
     			 
	    		<div class='itemcontainer'>
    				Välj det här om du vill fylla i vad som hände på lajvet.
				</div>   
	    		<div class='itemcontainer'>
      			<?php  
      			echo "<form action='../includes/set_larp.php' method='POST'>";
      			foreach (array_reverse($past_larp_array) as $larp) {
      			    echo "<input type='radio' id='pastlarp_$larp->Id' name='larp' value='$larp->Id'>";
      			    echo "<label for='pastlarp_$larp->Id'>$larp->Name</label><br>\n";
     			     }
    			     echo "<div class='center'><button class='button-18' type='submit'>Välj</button></div>";
    			     echo "<br><hr>";
    			 }
    			 echo "</form>";
    			 
    			 ?>
    			 </div>
    			 </div>
    			 
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
    			     echo "<div class='itemselector'>";
    			     echo "<div class='header'>";
    			     
    			     echo "<i class='fa-solid fa-shield-halved'></i> Arrangör";
    			     echo "</div>";
    			     
    			     
                     echo "<div class='itemcontainer'>";
        			 echo "Eftersom du är arrangör kan du även välja bland dessa lajv.";
        			 echo "</div>";
        			 
        			 echo "<div class='itemcontainer'>";
        			 echo "<form action='../includes/set_larp.php' method='POST'>";
        			 foreach (array_reverse($larps_organizer) as $larp) {
        			     echo "<input type='radio' id='orglarp_$larp->Id' name='larp' value='$larp->Id'>";
        			     echo "<label for='orglarp_$larp->Id'>$larp->Name</label><br>\n";
        			 }
        			 echo "<div class='center'><button class='button-18' type='submit'>Välj</button></div>";
        			 echo "</form>";
        			 echo "</div>";
        			 echo "</div>";
    			 
    			 }
    			 
    			 
    			 
    			 if (AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)) {
    			     echo "<div class='itemselector'>";
    			     echo "<div class='header'>";
    			     
    			     echo "<i class='fa-solid fa-shield-halved'></i> OM Admin";
    			     echo "</div>";
    			     
    			     
    			     echo "<div class='itemcontainer'>";
    			     echo "Eftersom du är OM admin har du tillgång till alla lajv.";
    			     echo "</div>";
    			     
    			     echo "<div class='itemcontainer'>";
    			     echo "<form action='../includes/set_larp.php' method='POST'>";
    			     $larps = LARP::all();
    			     foreach (array_reverse($larps) as $larp) {
    			         echo "<input type='radio' id='omlarp_$larp->Id' name='larp' value='$larp->Id'>";
    			         echo "<label for='omlarp_$larp->Id'>$larp->Name</label><br>\n";
    			     }
    			     echo "<div class='center'><button class='button-18' type='submit'>Välj</button></div>";
    			     echo "</form>";
    			     echo "</div>";
    			     echo "</div>";
    			     
    			 }
    			 
    			 
    			 ?>
			 </div>
			 <script>
				document.addEventListener('DOMContentLoaded', function() {
					document.querySelectorAll('.read-more').forEach(function(button) {
						button.addEventListener('click', function() {
							var fullDescription = this.previousElementSibling;
							if (fullDescription.style.display === 'none') {
								fullDescription.style.display = 'block';
								this.textContent = 'Läs mindre';
							} else {
								fullDescription.style.display = 'none';
								this.textContent = 'Läs mer';
							}
						});
					});
				});
			</script>
	</body>
</html>