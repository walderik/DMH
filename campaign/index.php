<?php
require 'header.php';
$_SESSION['navigation'] = Navigation::CAMPAIGN;
$campaign = $current_larp->getCampaign();
include "navigation.php";
?>
<h1><?php echo $campaign->Name?></h1>
Huvudarrangör för kampanjen är 
<?php

$mainOrganizer = $campaign->getMainOrganizer();
if (isset($mainOrganizer)) echo $mainOrganizer->Name;
else echo "inte utsedd.";

?>



