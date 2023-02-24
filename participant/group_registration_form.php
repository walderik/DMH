<?php

require 'header.php';

$current_groups = $current_user->getUnregisteredGroupsForUser($current_larp);

if (empty($current_groups)) {
    header('Location: index.php?error=no_group');
    exit;
}

?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
	       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


	<div class="content">
		<h1>Anmälan av grupp till <?php echo $current_larp->Name;?></h1>
		<form action="logic/group_registration_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="insert"> 
    		<input type="hidden" id="LARPId" name="LARPId" value="<?php echo $current_larp->Id ?>">


			<p>När en grupp är anmäld till lajvet går det för karaktärer att anmäla sig som medlemmar i gruppen. <br>
			   Du som gruppansvarig, har möjlighet att ta bort någon ur gruppen om någon anmäler sig till den men inte hör till den.<br><br>
			   Efter anmälan går det inte längre att redigera gruppen.
			   </p>
				
				
			<div class="question">
				<label for="GroupId">Grupp</label><br>
				<?php selectionDropdownByArray('Group', $current_groups, false, true) ?>
			</div>
			<div class="question">
    			<label for="IntrigueType">Intrigtyper</label>
    			<div class="explanation">Vilken typ av intriger vill gruppen helst ha?  <br>
    			    <?php IntrigueType::helpBox(true); ?></div>
                <?php
    
                IntrigueType::selectionDropdown(true, false);
                
                ?>
            </div>

			
			<div class="question">
    			<label for="HousingRequest">Boende</label>
    			<div class="explanation">Hur vill gruppen helst bo? Vi kan inte garantera plats i hus. <br><?php HousingRequest::helpBox(true); ?></div>
                <?php
    
                HousingRequest::selectionDropdown(false,true);
                
                ?>
            </div>
			

			  <input type="submit" value="Anmäl">

		</form>
	</div>

</body>
</html>