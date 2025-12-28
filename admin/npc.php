<?php
include_once 'header.php';


include 'navigation.php';
include 'npc_navigation.php';
?>


    <div class="content">   
        <h1>NPC</h1>
        En NPC är en roll som inte ägs och kontrolleras av en deltagare. Den kan tilldelas en deltagare och blir då en "karaktär" som den deltagare får hantera precis som sina andra karaktärer. <br>
        Tills dess en NPC har en deltagare kontrolleras den av arrangörerna. <br>
        <br>
        En NPC kan tillfälligt under ett lajv tilldelas att spelas av en deltagare i ett NPC-Uppdrag. Det är då ett tillfälligt inhopp av någon deltagare bara på det lajvet och vanligtivs med tydliga instruktioner av arrangörerna.<br>
        Om en NPC skall spelas under ett lajv, måste man först skapa ett Uppdrag som man sedan kan tilldelas en deltagare som har anmält att de vill spela NPC.<br>
        <br>
        En NPC kan tillhöra en vanlig grupp, precis som vilken annan roll som helst, men den kan också tillhöra grupper utan deltagare. Dessa grupper kan vara synliga eller dolda för övriga deltagare.<br>
        <br>
        Vanliga deltagare kan skapa NPC:er till sina grupper. Dessa måste då också godkännas av arrangörerna innan de tillhör fiktionen.
        <p>
        <?php 
        $notcoming_count = NPC_assignment::numberNotComingNPCPlayers($current_larp);
        if ($notcoming_count>0) echo "<strong>$notcoming_count NPC'er är tilldelade till en deltagare som är avbokad.</strong><br><br>";
        
        $unassigned_count = NPC_assignment::numberUnassigned($current_larp);
        if ($unassigned_count>0) echo "<strong>$unassigned_count NPC'er som ska spelas på lajvet har ingen spelare.</strong><br><br>";
        
        $unreleased_count = NPC_assignment::numberUnreleased($current_larp);
        if ($unreleased_count>0) echo "<strong>$unreleased_count tilldelade NPC'er har inte blivit släppta till sin spelare.</strong><br><br>";

        $npcs = Role::getAllNPCToBePlayed($current_larp);
        $persons=Person::getAllInterestedNPC($current_larp);
        $numberInterestedUnassigned = NPC_assignment::numberInterestedUnassigned($current_larp);
        
        ?>

        <?php echo count($npcs) ?> NPC'er kommer att spelas på lajvet. <br><br>
        <?php echo "Det finns ".count($persons)." deltagare som vill spela NPC'er"; ?>
        <?php if ($numberInterestedUnassigned > 0) echo ", $numberInterestedUnassigned av dem har inte fått någon NPC" ?>.</p>
  	</div>
</body>

</html>
  