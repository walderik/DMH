<?php
require 'header.php';

$years = array_reverse(LARP::getAllYears());
$choosen_year = date('Y');
$choosenLarps = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['year'])) $choosen_year = $_POST['year'];
    if (isset($_POST['choosenLarps'])) $choosenLarps = $_POST['choosenLarps'];
}

$larps = LARP::getAllForYear(0, $choosen_year);

include "navigation.php";
?>
<h1>Rapportering till Sensus</h1>


<form method="POST">
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
<br>

<?php 
foreach ($larps as $larp) {
    $persons = Person::getAllRegistered($larp, false);
    if (!$larp->LarpHasEnded) continue;
    $participant_count = count($persons);
    if ($participant_count == 0) {
        echo "$larp->Name har inga deltagare.<br>";
        continue;
    }
    $count_women = 0;
    foreach ($persons as $person) {
        if ((strlen($person->SocialSecurityNumber) > 12) && (substr($person->SocialSecurityNumber, 11,1) % 2 == 0)) $count_women++;
    }
    $percent_women = round(100*$count_women / $participant_count,0);
    
    $formatter = new IntlDateFormatter(
        'sv-SE',
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        'Europe/Stockholm',
        IntlDateFormatter::GREGORIAN,
        'EEEE d MMMM'
        );
    
    $begin = new DateTime(substr($larp->StartDate,0,10));
    $end   = new DateTime(substr($larp->EndDate,0,10));
    $dateArray = array();
    for($i = $begin; $i <= $end; $i->modify('+1 day')){
        $dateArray[] = $i->format("Y-m-d");
    }
    $dates = implode(", ", $dateArray);
    
    echo "<h2>$larp->Name</h2>";

    echo "Datum: $dates<br>";
    echo "Antal deltagare: $participant_count<br>";
    echo "$percent_women % kvinnor"; 
}






