<?php
require 'header.php';

$_SESSION['navigation'] = Navigation::PARTICIPANT;
$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));

$campaign = $current_larp->getCampaign();
$registration = $current_person->getRegistration($current_larp);

if ($isMob) echo "<br><button  onclick='doSwish()'><img style='padding:2px'  width='20' src='../images/Swish Logo.png'><span style='vertical-align: top'> Betala med swish </span></button>";
    else echo "<br><img width='200' src='../includes/display_image.php?Swish=1&RegistrationId=$registration->Id&CampaignId=$campaign->Id'/>\n";
?>

<script>

function doSwish() {
<!-- Deep link URL for existing users with app already installed on their device -->
window.location = '<?php echo Swish::getSwishLink($registration, $campaign)?>';

<!-- Download URL (TUNE link) for new users to download the app -->
setTimeout("window.location = 'index.php?error=swishNotInstalled';", 1000);
}

</script>