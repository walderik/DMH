<?php
include_once 'header.php';


    $subdivision = Subdivision::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $subdivision = Subdivision::loadById($_GET['id']);
        } else {
        }
    }
      
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $operation = "insert";
        if (isset( $_POST['Referer'])) $referer = $_POST['Referer'];
        if (isset($_POST['operation'])) $operation = $_POST['operation'];
        $subdivision = Subdivision::loadById($_POST['id']);
        
        if ($operation == 'delete_member') {
            $subdivision->removeMember($_POST['memberId']);
            if (isset($_POST['ReturnTo'])) {
                header('Location: '.$_POST['ReturnTo']);
                exit;
                
            }
            $operation = 'update';
        } elseif ($operation == 'add_subdivision_member') {
            if (isset($_POST['RoleId'])) $subdivision->addMembers($_POST['RoleId']);
            if (isset($_POST['ReturnTo'])) {
                header('Location: '.$_POST['ReturnTo']);
                exit;
                
            }
            $operation = 'update';
        }
        
    }
    
    
    
    function default_value($field) {
        GLOBAL $subdivision;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($subdivision->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;
            case "id":
                $output = $subdivision->Id;
                break;
            case "action":
                if (is_null($subdivision->Id)) {
                    $output = "Lägg till";
                    break;
                }
                $output = "Uppdatera";
                break;
        }

        echo $output;
    }
    
    if (isset($_POST['2ndReferer'])) {
        $referer = $_POST['2ndReferer'];
    } elseif (isset($_POST['Referer'])) {
        $referer = $_POST['Referer'];
    } elseif (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
    } else {
        $referer = "subdivision_admin.php";
    }
    
    include 'navigation.php';
    
    ?>
    <div class="content"> 
    	<h1><?php echo default_value('action');?> gruppering</h1>
    	<form id="main" action="subdivision_admin.php" method="post"></form>
    		<input form='main' type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input form='main' type="hidden" id="Id" name="Id" value="<?php default_value('id'); ?>">
     		<input form='main' type="hidden" id="CampaignId" name="CampaignId" value="<?php echo $subdivision->CampaignId?>">
    		<table>
    			<tr>
    				<td><label for="Name">Namn</label></td>
    				<td><input form='main' type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($subdivision->Name); ?>" size="100" maxlength="250" required></td>
    			</tr>
    			<tr>
    				<td><label for="Description">Beskrivning</label><br>Om grupperingen är synlig för medlemmar så kommer de att få se namnet på grupperingen och den här beskrivningen.</td>
    				<td><textarea form='main' id="Description" name="Description" rows="6" cols="100" maxlength="60000" ><?php echo htmlspecialchars($subdivision->Description); ?></textarea></td>
    			</tr>
    			<tr>
    				<td><label for=IsVisibleToParticipants>Synlig för medlemmar</label><br>Här bestämmer du om de karaktärer som är med i grupperingen ska se om de är med i grupperingen eller inte.</td>
    				<td>
						<input form='main' type="radio" id="IsVisibleToParticipants_yes" name="IsVisibleToParticipants" value="1" <?php if ($subdivision->isVisibleToParticipants()) echo 'checked="checked"'?>> 
            			<label for="IsVisibleToParticipants_yes">Ja</label><br> 
            			<input form='main' type="radio" id="IsVisibleToParticipants_no" name="IsVisibleToParticipants" value="0" <?php if (!$subdivision->isVisibleToParticipants()) echo 'checked="checked"'?>> 
            			<label for="IsVisibleToParticipants_no">Nej</label>
					</td>
    			</tr>
    			<tr>
    				<td><label for=CanSeeOtherParticipants>Kan se vilkan andra som är medlemmar</label><br>Här bestämmer du om de karaktärer som är med i grupperingen ska se vilka andra som är med i grupperingen. Om grupperingen inte är synlig för medlemmar spelar den här ingen roll.</td>
    				<td>
						<input form='main' type="radio" id="CanSeeOtherParticipants_yes" name="CanSeeOtherParticipants" value="1" <?php if ($subdivision->canSeeOtherParticipants()) echo 'checked="checked"'?>> 
            			<label for="CanSeeOtherParticipants_yes">Ja</label><br> 
            			<input form='main' type="radio" id="CanSeeOtherParticipants_no" name="CanSeeOtherParticipants" value="0" <?php if (!$subdivision->canSeeOtherParticipants()) echo 'checked="checked"'?>> 
            			<label for="CanSeeOtherParticipants_no">Nej</label>
					</td>
    			</tr>
    			
    							<tr>
				<td><label>Medlemmar i grupperingen</label></td>
				<td>
					<?php if ($operation=='update') {
					    echo "<form id='add_member' action='choose_role.php' method='post'></form>";
					    echo "<input form='add_member' type='hidden' id='id' name='id' value='$subdivision->Id'>";
					    echo "<input form='add_member' type='hidden' id='2ndReferer' name='2ndReferer' value='$referer'>";
					    echo "<input form='add_member' type='hidden' id='operation' name='operation' value='add_subdivision_member'>";
					    echo "<button form='add_member' class='invisible' type='submit'><i class='fa-solid fa-plus' title='Lägg till karaktär(er) som är med i grupperingen'></i><i class='fa-solid fa-user' title='Lägg till karaktär(er) som är med i grupperingen'></i></button>";
					} else {
					  echo "<strong>När grupperingen är skapad, kan du lägga till medlemmar i den.</strong>";
					}?>
					
					<?php 
					$members = $subdivision->getAllMembers();
					foreach ($members as $member) {
					    echo "<form id='delete_member_$member->Id' action='subdivision_form.php' method='post'>";
					    echo $member->getViewLink();
					    echo " ";
					    echo "<input form='delete_member_$member->Id' type='hidden' id='operation' name='operation' value='delete_member'>";
					    echo "<input form='delete_member_$member->Id' type='hidden' id='id' name='id' value='$subdivision->Id'>";
					    echo "<input form='delete_member_$member->Id' type='hidden' id='Referer' name='Referer' value='$referer'>";
					    echo "<input form='delete_member_$member->Id' type='hidden' id='memberId' name='memberId' value='$member->Id'>";
					    echo "<button form='delete_member_$member->Id' class='invisible' type='submit'><i class='fa-solid fa-trash' title='Ta bort från grupperingen'></i></button>";
					    echo "</form>";
					}
					?>
				</td>
			</tr>
    			
    			
    			<tr>
    
    				<td><label for="OrganizerNotes">Anteckningar<br>för arrangörer</label></td>
    				<td><textarea form='main' id="OrganizerNotes" name="OrganizerNotes" rows="4" maxlength="60000"
    						cols="100"><?php echo htmlspecialchars($subdivision->OrganizerNotes); ?></textarea></td>
    
    			</tr>
    		</table>
    
    		<input form='main' id="submit_button" type="submit" value="<?php default_value('action'); ?>">

    	</div>
    </body>

</html>