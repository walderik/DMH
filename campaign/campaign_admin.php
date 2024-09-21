<?php
 include_once 'header.php';
 
 if (!AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)  && !isset($_SESSION['admin'])) {
     exit;
 }
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'update') {
        $campaign=Campaign::loadById($_POST['Id']);
        $campaign->setValuesByArray($_POST);
        $campaign->update();
    } 
}

$campaign = $current_larp->getCampaign();
include 'navigation.php';
?>

    <div class="content">   
        <h1>Inställningar för kampanjen <?php $campaign->Name?> <a href='campaign_form.php?operation=update&id=<?php echo $campaign->Id ?>'><i class='fa-solid fa-pen'></i></a></h1>

            <table id='larp'>
               <tr><td valign="top" class="header">Namn</td><td><?php echo $campaign->Name ?></td></tr>
               <tr><td valign="top" class="header">Förkortning</td><td><?php echo $campaign->Abbreviation ?></td></tr>
               <tr><td valign="top" class="header">Ikon</td><td><img src='../images/<?php echo $campaign->Icon ?>' width='30' height='30'/><br><?php echo $campaign->Icon ?></td></tr>
               <tr><td valign="top" class="header">Hemsida</td><td><?php echo $campaign->Homepage ?></td></tr>
               <tr><td valign="top" class="header">Epost</td><td><?php echo $campaign->Email ?></td></tr>
               <tr><td valign="top" class="header">Bankkonto</td><td><?php echo $campaign->Bankaccount ?></td></tr>
               <tr><td valign="top" class="header">Minsta ålder</td><td><?php echo $campaign->MinimumAge ?></td></tr>
               <tr><td valign="top" class="header">Minsta ålder utan ansvarig vuxen</td><td><?php echo $campaign->MinimumAgeWithoutGuardian ?></td></tr>
               <tr><td valign="top" class="header">Lajv-valuta</td><td><?php echo $campaign->Currency ?></td></tr> 
            </table>
			<br>

				<?php 
                echo "<strong>Arrangörsbehörighet:</strong><br>";
                $organizers = Person::getAllWithAccessToCampaign($campaign);
                if (count($organizers) == 0) echo "Ingen utsedd än<br>";
                foreach ($organizers as $organizer) {
                    echo "$organizer->Name<br>";
                }
                ?>
         
	</div>
</body>

</html>