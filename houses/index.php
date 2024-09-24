<?php
require 'header.php';
$_SESSION['navigation'] = Navigation::HOUSES;
include "navigation.php";
?>
<h1>Hus & Läger</h1>
<?php 
$housesWithoutMapPoint = House::countHousesWithoutMapPoint();
$campsWithoutMapPoint = House::countCampsWithoutMapPoint();

if (($housesWithoutMapPoint > 0) || ($campsWithoutMapPoint > 0)) {
?>

<h2>Saknar kartpunkter</h2>
<table>
<tr><th>Typ</th><th>Antal</th></tr>
<tr><td>Hus</td><td><?php echo $housesWithoutMapPoint?></td></tr>
<tr><td>Lägerplats</td><td><?php echo $campsWithoutMapPoint?></td></tr>


</table>
<?php }?>

