<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['person_id'])) {
        $person_id = $_POST['person_id'];
        if ($person_id == 0) {
            $error_message = "Du kan bara välja bland de föreslagna personerna.";
        } else {
            $searched_person = Person::loadById($person_id);
            if (isset($_POST['new_userId'])) {
                $newUser = User::loadById($_POST['new_userId']);
                $searched_person->changeUser($newUser); 
            } 
            $user = $searched_person->getUser();
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
	 
	 <table border='0'>
	 <tr><td>
	 <h2>Person</h2>
	 Namn: <?php echo $searched_person->Name?><br>
	 Epost: <?php echo $searched_person->Email ?><br>
	 <br>

     <form method="post"  autocomplete="off" style="display: inline;">
     	Sök ny användare för  <?php echo $searched_person->Name?>&nbsp;
		<input type="hidden" id="person_id" name="person_id" value="<?php echo $searched_person->Id ?>">
		<input type="Email" id="Email" name="Email" value="<?php echo htmlspecialchars($searched_person->Email); ?>" maxlength="100" required>
		 <input type="submit"  value="Sök">
	 </form>	

	 
	 	
	<br>
	<h2>Användare</h2>	
	 Namn: <?php echo $user->Name?><br>
	 Epost: <?php echo $user->Email ?><br>
	 Senast inloggad: <?php echo $user->LastLogin?><br>
	 <h3>Har följande personer</h3>
	 <?php 
	 $persons = $user->getPersons();
	 if (empty($persons)) {
	   echo "Har inga registrerade personer.";
	   exit;
	 }

	 foreach ($persons as $person) {
	   echo "$person->Name, $person->SocialSecurityNumber<br>";
	 }
	 
	 ?>
	 </td>
	 
	 <?php 
	 if (isset($_POST['Email'])) {
	     $searched_user = User::loadByEmail($_POST['Email']);
	     ?>
	     <td>
	     	<h2>Sökt användare</h2>	
	     Namn: <?php echo $searched_user->Name?><br>
    	 Epost: <?php echo $searched_user->Email ?><br>
    	 Senast inloggad: <?php echo $searched_user->LastLogin?><br>
    	 <h3>Har följande personer</h3>
    	 <?php 
    	 $persons = $searched_user->getPersons();
    	 if (empty($persons)) {
    	   echo "Har inga registrerade personer.";
    	   exit;
    	 }
    
    	 foreach ($persons as $person) {
    	   echo "$person->Name, $person->SocialSecurityNumber<br>";
    	 }
	 
    	 ?>
		<br>

     <form method="post"  autocomplete="off" style="display: inline;">
		<input type="hidden" id="person_id" name="person_id" value="<?php echo $searched_person->Id ?>">
		<input type="hidden" id="new_userId" name="new_userId" value="<?php echo $searched_user->Id ?>" >
		 <input type="submit"  value="Flytta <?php echo $searched_person->Name ?> till denna användare">
	 </form>	

    	 
    	 </td>
	     
	 
    	 <?php }?>
	 
	 </tr>
	 </table>
	 
	 <?php }?>