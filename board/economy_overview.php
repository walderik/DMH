<?php
require 'header.php';

$campaigns = Campaign::all();
$years = array_reverse(LARP::getAllYears());
$choosen_year = date('Y');
$choosen_campaignId = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['campaignId'])) $choosen_campaignId = $_POST['campaignId'];
    if (isset($_POST['year'])) $choosen_year = $_POST['year'];
}



include "navigation.php";
?>
<h1>Ekonomisk översikt</h1>

<form method="POST">
<select name="campaignId" id="campaignId">
  <option value='0'>Alla kampanjer</option>
<?php 
foreach ($campaigns as $campaign) {
  echo "<option value='$campaign->Id'";
  if (isset($choosen_campaignId) && $campaign->Id == $choosen_campaignId) echo " selected ";
  echo ">$campaign->Name</option>";   
}
?>
</select>

<select name="year" id="year">
<?php 
foreach ($years as $year) {
  echo "<option value='$year'";
  if (isset($choosen_year) && $year == $choosen_year) echo " selected ";
  echo ">$year</option>";   
}
?>
</select>

<input type='submit' value='Visa'>

</form>
<?php 
if (isset($choosen_campaignId) && isset($choosen_year)) {
    
    if ($choosen_campaignId == 0) {
        //Alla kampanjer
        $campaigns = Campaign::all();
    } else $campaigns[] = Campaign::loadById($choosen_campaignId);
    
    foreach ($campaigns as $campaign) {
        echo "<h2>$campaign->Name</h2>";
        echo "<table>";
        economy_overview_campaign($campaign, $choosen_year);
        echo "</table>";
        
    }
}



?>
