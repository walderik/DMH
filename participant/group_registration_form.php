<?php

require 'header.php';

$current_groups = $current_user->getUnregisteredGroups($current_larp);

if (empty($current_groups)) {
    header('Location: index.php&error=no_group');
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
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="LarpId" name="Id" value="<?php echo $current_larp->Id ?>">


			<p>När en grupp är anmäld till lajvet går det för karaktärer att anmäla sig som medlemmar i gruppen. <br>
			   Du som gruppledare, har möjlighet att ta bort någon ur gruppen om någon anmäler sig till den men inte hör till den.<br><br>
			   Efter anmälan går det inte längre att redigera gruppen.
			   </p>
				
				
			<div class="question">
				<label for="GroupId">Grupp</label><br>
				<?php selectionDropdownByArray('GroupId', $current_groups, false, true) ?>
			</div>
			<div class="question">
				<label for="WantIntrigue">Vill gruppen ha en arrangörsskriven intrig?</label><br>
				<div class="explanation">Även om du svara 'nej' här förbehåller vi oss rätten att ge er en intrig i alla fall, eller låta er förekomma i andras intriger. </div>
				<input type="radio" id="WantIntrigue_yes" name="WantIntrigue" value="1" checked="checked">
                <label for="WantIntrigue_yes">Ja</label><br>
                <input type="radio" id="WantIntrigue_no" name="WantIntrigue" value="0">
                <label for="WantIntrigue_no">Nej</label><br>
			</div>

			<div class="question">
    			<label for="IntrigueType">Intrigtyper</label>
    			<div class="explanation">Vilken typ av intriger vill du helst ha?  <br>
    			    <?php IntrigueType::helpBox(true); ?></div>
                <?php
    
                IntrigueType::selectionDropdown(tru);
                
                ?>
            </div>

			
			<div class="question">
    			<label for="HousingRequest">Boende</label>
    			<div class="explanation">Hur vill gruppen helst bo? Vi kan inte garantera plats i hus. <br><?php HousingRequest::helpBox(true); ?></div>
                <?php
    
                HousingRequest::selectionDropdown(false,true);
                
                ?>
            </div>
			
			<div class="question">
			  <input type="submit" value="Anmäl">
			</div>
		</form>
	</div>

</body>
</html>