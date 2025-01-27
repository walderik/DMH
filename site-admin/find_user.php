<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['person_id'])) {
        $person_id = $_POST['person_id'];
        if ($person_id == 0) {
            $error_message = "Du kan bara välja bland de föreslagna personerna.";
        } else {
            $person = Person::loadById($person_id);
            $user = $person->getUser();
         }
    }
}

include 'navigation.php';

if (isset($error_message) && strlen($error_message)>0) {
    echo '<div class="error">'.$error_message.'</div>';
}
if (isset($message_message) && strlen($message_message)>0) {
    echo '<div class="message">'.$message_message.'</div>';
}

?>
<h1>Hitta användare för person</h1>

     <form method="post"  autocomplete="off" style="display: inline;">
     	Sök användare 
     	<?php autocomplete_person_id('40%', true); ?>
	 </form>	
	 
	 <?php if (isset($user)) { ?>	
	<br>	
	 Användare: <?php echo $user->Name?><br>
	 Epost: <?php echo $user->Email ?><br>
	 Senast inloggad: <?php echo $user->LastLogin?><br>
	 <?php 
	 $persons = $user->getPersons();
	 if (empty($persons)) {
	   echo "Har inga registrerade personer.";
	   exit;
	 }
	 echo "<br>";
	 foreach ($persons as $person) {
	   echo "Id: $person->Id, $person->Name, $person->SocialSecurityNumber<br>";
	 }
	 
	 ?>
	 
	 
	 <?php }?>