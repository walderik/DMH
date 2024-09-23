<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $campaignId = $_GET['CampaignId'];
    $campaign = Campaign::loadById($campaignId);
    
}

include 'navigation.php';
?>


    <div class="content"> 
    <h1>Sätt huvudarrangör för <?php echo $campaign->Name ?></h1>
	<form autocomplete="off" action="permissions.php" method="post">
		<input type="hidden" id="operation" name="operation" value="main_organizer"> 
		<input type="hidden" id="CampaignId" name="CampaignId" value="<?php echo $campaignId ?>">

		<?php  autocomplete_person_id('60%', true); ?> 
	</form>
	</div>

