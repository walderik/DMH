<?php
include_once 'header.php';
include_once '../classes/statistics.php';

include 'navigation.php';


/*

 * 
 * Statistik under admin där man presenterar till exempel 
 * antal anmälda deltagare, 
 * max antal deltagare, 
 * hur många av de olika funktionärerna, 
 * förväntad och faktisk inkomst, 
 * antal grupper och storleksfördelning på grupperna osv. 
 * I princip samla all statistik på ett ställe

 */

?>

<div class="content">
	<h1>Statistik</h1>
	<h2>Personer på lajvet</h2>
		Just nu är det <?php echo Registration::countAllNonOfficials($current_larp); ?> anmälda deltagare och <?php echo Registration::countAllOfficials($current_larp); ?> funktionärer.<br> 
		Max antal deltagare är <?php echo $current_larp->MaxParticipants ?>.<br><br>
		Nedanstående statisktik räknar både deltagare och funktionärer.<br><br>
		Den yngsta kommer att vara <?php echo Statistics::youngest($current_larp)?> år.<br>
		Den äldsta kommer att vara <?php echo Statistics::oldest($current_larp)?> år.<br>
		<br>
        Antal som har betalat: <?php echo Statistics::countHasPayed($current_larp) ?> st<br>
        Antal som är medlemmar: <?php echo Statistics::countIsMember($current_larp)?> st<br>
        Antal som är helt klara: <?php echo Statistics::countHasSpot($current_larp)?> st <br>
Det är <?php echo Statistics::countParticipantHasSpot($current_larp)?> helt klara deltagare och <?php echo Statistics::countOfficialHasSpot($current_larp)?> helt klara funktionärer.



		<h3>Typ av mat</h3>
    <?php 
    $count = TypeOfFood::countByType($current_larp);
    foreach($count as $item) {
        echo $item['Name'].": ".$item['Num']." st<br>";
    }
    
    
    ?>
    
<!--  Vanliga alleriger -->  

  
    	<h3>Erfarenhet som lajvare</h3>
    <?php 
    $count = Experience::countByType($current_larp);
    foreach($count as $item) {
        echo $item['Name'].": ".$item['Num']." st<br>";
    }
    
    
    ?>

    	<h3>Boendeönskemål</h3>
    <?php 
    $count = HousingRequest::countByType($current_larp);
    foreach($count as $item) {
        echo $item['Name'].": ".$item['Num']." st<br>";
    }
    
    
    ?>

<h2>Karaktärer</h2>
	Antal huvudkaraktärer <?php echo count($current_larp->getAllMainRoles(false))?><br>
	Antal huvudkaraktärer utan grupp <?php echo count(Role::getAllMainRolesWithoutGroup($current_larp))?><br>
	Antal sidokaraktärer <?php echo count(Role::getAllNotMainRoles($current_larp, false))?><br>

	
	<br>
	I nedanstående tabell finns huvudkaraktärer i första kolumnen och sidokaraktärer i andra. Bakgrundslajvare är inte medräknade.
    	
	<table>

<?php 
function printRoleStatistics($type, $headline, $larp) {
    if (!call_user_func($type."::isInUse", $larp)) return;
    
    echo "<tr><th colspan='2'><h3>$headline</h3></th></tr>";
    echo "<tr>";
    echo "<td>";
    
    $count = call_user_func($type."::countByTypeOnRoles",$larp, true);
    foreach($count as $item) {
        echo $item['Name'].": ".$item['Num']." st<br>";
    }
    echo "</td><td>";
    $count = call_user_func($type."::countByTypeOnRoles",$larp, false);
    foreach($count as $item) {
        echo $item['Name'].": ".$item['Num']." st<br>";
    }
    echo "</td></tr>";
    
}




	$types = getAllTypesForRoles($current_larp);
	foreach ($types as $key => $type) {	
	    printRoleStatistics($key, $type, $current_larp);
	}
		    
?>
     
    </table>
     
    
    


<!--  åldersfördelning -->
   <h2>Grupper</h2>
   	Just nu är <?php echo count(Group::getAllRegistered($current_larp))?> grupper anmälda.<br>
 <!-- storleksfördelning på grupperna osv. --> 
  
  <?php 
function printGroupStatistics($type, $headline, $larp) {
    if (!call_user_func($type."::isInUse", $larp)) return;
    
    echo "<h3>$headline</h3>";
    $count = call_user_func($type."::countByTypeOnGroups",$larp);
    foreach($count as $item) {
        echo $item['Name'].": ".$item['Num']." st<br>";
    }
}




	$types = getAllTypesForGroups($current_larp);
	foreach ($types as $key => $type) {	
	    printGroupStatistics($key, $type, $current_larp);
	}
		    
    ?>
  


  <h2>Funktionärer</h2>
  	<h3>Tillsatta funktionärer</h3>
  	<?php 
  	$officialTypes = OfficialType::allActive($current_larp);
  	
  	foreach ($officialTypes as $officialType) {
  	    $persons = Person::getAllOfficialsByType($officialType, $current_larp);
  	    echo "$officialType->Name: ".count($persons)." st<br>";
  	}
  	
  	?>
  
  		<h3>Antal som kan tänka sig att ställa upp som funktionär</h3>
   	<?php 
  	$officialTypes = OfficialType::allActive($current_larp);
  	
  	foreach ($officialTypes as $officialType) {
  	    $persons = Person::getAllWhoWantToBeOfficialsByType($officialType, $current_larp);
  	    echo "$officialType->Name: ".count($persons)." st<br>";
  	}
  	
  	?>
  
  
  
  
  <h2>Ekomoni</h2>
		Förväntade intäkter: <?php echo Registration::totalIncomeToBe($current_larp);?> SEK<br>
	    Faktiskta intäkter: <?php echo Registration::totalIncomeToday($current_larp)?> SEK
  