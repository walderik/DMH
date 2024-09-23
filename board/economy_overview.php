<?php
require 'header.php';

$campaigns = Campaign::all();
$years = array_reverse(LARP::getAllYears());

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
    
    $larps = LARP::getAllForYear($choosen_campaignId, $choosen_year);
    
    if (empty($larps)) {
        echo "<br>Kampanjen har inget lajv $choosen_year";
        exit;
    }
    
    foreach ($larps as $larp) {
        echo "<h2>Resultat för $larp->Name</h3>";
        echo "<table>";
        economy_overview($larp);
        echo "</table>";
    }
    
}



?>
