<?php
include_once 'header.php';


include 'navigation.php';
include 'npc_navigation.php';
?>


    <div class="content">   
        <h1>NPC</h1>
        
        <p>
        <?php 
        $notcoming_count = NPC_assignment::numberNotComingNPCPlayers($current_larp);
        if ($notcoming_count>0) echo "<strong>$notcoming_count NPC'er är tilldelade till en deltagare som är avbokad.</strong><br>";
        
        $unassigned_count = NPC_assignment::numberUnassigned($current_larp);
        if ($unassigned_count>0) echo "<strong>$unassigned_count NPC'er som ska spelas på lajvet har ingen spelare.</strong><br>";
        
        $unreleased_count = NPC_assignment::numberUnreleased($current_larp);
        if ($unreleased_count>0) echo "<strong>$unreleased_count tilldelade NPC'er har inte blivit släpta till sin spelare.</strong><br>";

        $npcs = Role::getAllNPCToBePlayed($current_larp);
        $persons=Person::getAllInterestedNPC($current_larp);
        $numberInterestedUnassigned = NPC_assignment::numberInterestedUnassigned($current_larp);
        
        ?>
        
        


        <?php echo count($npcs) ?> NPC'er kommer att spelas på lajvet. <br>
        <?php  echo "Det finns ".count($persons)." deltagare som vill spela NPC'er"; ?>
        <?php  if ($numberInterestedUnassigned > 0) echo ", $numberInterestedUnassigned av dem har inte fått någon NPC"?>.</p>
  	</div>
</body>

</html>
  