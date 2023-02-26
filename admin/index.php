<?php
require 'header.php';
include_once '../includes/error_handling.php';

?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
              <li class="dropdown"><a href="#" class="trigger-drop">Admin<i class="arrow"></i></a>
              <ul class="drop">
                <li><a href="registered_persons.php">Deltagare</a></li>
                <li><a href="payment_information_admin.php">Avgift</a></li>
              </ul>
            </li>
              <li class="dropdown"><a href="#" class="trigger-drop">Intriger<i class="arrow"></i></a>
              <ul class="drop">
                <li><a href="roles.php">Karaktärer</a></li>
                <li><a href="telegram_admin.php">Telegram</a></li>
              </ul>
            </li>
              <li class="dropdown"><a href="#" class="trigger-drop">Köket<i class="arrow"></i></a>
              <ul class="drop">
                <li><a href="food.php">Matönskemål</a></li>
                <li><a href="allergies.php">Allergier</a></li>
              </ul>
            </li>
              <li class="dropdown"><a href="#" class="trigger-drop">Funktionärer<i class="arrow"></i></a>
              <ul class="drop">
                <li><a href="officials.php">Funktionärer</a></li>
                <li><a href="npc.php">NPC'er</a></li>
              </ul>
            </li>
        	<li><a href="super_admin.php" style="color: red"><i class="fa-solid fa-lock"></i>Super admin</a></li>  
            <li><a href="../participant/" style="color: #99bbff"><i class="fa-solid fa-unlock"></i>Deltagare</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


		<div class="content">
			<h1>Administration av <?php echo $current_larp->Name;?></h1>
	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }?>
	  <?php if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }?>
			<p>
				Just nu är det <?php echo Registration::numberOff(); ?> anmälda deltagare.<br> 
			</p>
		</div>
	</body>
</html>