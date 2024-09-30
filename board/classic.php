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
<h1>Berghems klassikern</h1>


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
<br>

<?php 
foreach ($larps as $larp) {
    echo "<input type='checkbox' name='choosenLarps[]' name='larp$larp->Id' value='$larp->Id'>";
    echo "<label for='larp$larp->Id'> $larp->Name</label><br>";
}


?>
<br>
<input type='submit' value='Visa'>



<?php 
if (!empty($choosenLarps)) {
    $participants = Person::allParticipants($choosenLarps);
    if (!empty($participants)) {
        echo "<h2>Alla som har deltagit på lajven</h2>";
        echo sizeof($participants)." personer.";
        echo "<table class='small_data'>";
        foreach($participants as $person) {
            echo "<tr>";
            echo "<td>$person->Name</td>";
            echo "</tr>";
        
        }
        echo "</table>";
        
    }
}