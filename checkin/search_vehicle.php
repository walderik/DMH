<?php
include_once 'header.php';

$type = "checkin";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['licencePlate'])) $licencePlate = $_POST['licencePlate'];
}

if (isset($licencePlate)) {
    $licencePlate = strtoupper($licencePlate);
    $registrations = Registration::getByLicencePlate($licencePlate, $current_larp);
}



include 'navigation.php';
?>
	<div class="header">
		<i class="fa-solid fa-user"></i>
		Sök fordon
	</div>
	<div class='itemcontainer'>
		Skriv gärna in bara de tre första bokstäverna eftersom alla inte kommer ihåg hela numret vid inchecknng.<br><br>
    	<form autocomplete="off" method="post">
    		<label for="licencePlate">Registreringsnummer:</label>
    		<input type="text" id="licencePlate" name="licencePlate">
  			<input type="submit" value="Sök">
    	</form>
	</div>
	
	<?php 
	if (isset($licencePlate)) {
	    echo "<div class='itemcontainer'>";
	    echo "Sökning gjord för <b>$licencePlate</b><br><br>";
	    if (empty($registrations)) echo "Ingen hittad.";
	    else {
	        foreach ($registrations as $registration) {
	            $person = $registration->getPerson();
	            echo "<p>";
	            echo "<b>$registration->VehicleLicencePlate</b><br>";
                echo "<a href='checkout_person.php?id=$person->Id'><b>$person->Name</b></a><br>";
	            if ($registration->isCheckedIn()) echo "Incheckad $registration->CheckinTime";
	            else echo "Inte incheckad";
	            echo "<br>";
	            if ($registration->isCheckedOut()) echo "Utcheckad $registration->CheckoutTime";
	            else echo "Inte utcheckad";
	            echo "</p>";
	            //echo "<br<br><br>";
	        }
	    }
	    echo "</div>";
	    
	}
	
	?>