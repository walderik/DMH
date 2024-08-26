<?php
include_once 'header.php';


$titledeed = Titledeed::loadById($_GET['id']);
    
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referer = $_SERVER['HTTP_REFERER'];
    }
    else {
        $referer = "";
    }
    $referer = (isset($referer)) ? $referer : '../titledeed_admin.php';
    
    $currency = $current_larp->getCampaign()->Currency;
    
    include 'navigation.php';
    ?>
    

    <div class="content"> 
    <h1><?php echo $titledeed->Name ?> <a href='titledeed_form.php?operation=update&id= <?php echo $titledeed->Id?>'><i class='fa-solid fa-pen'></i></a></h1>
		<table>
			<tr>
				<td><label for="Name">Namn</label></td>
				<td><?php echo htmlspecialchars($titledeed->Name); ?></td>
			</tr>
			<tr>
				<td><label for="Type">Typ</label> <small>(vad är det för något)</small></td>
				<td><?php echo htmlspecialchars($titledeed->Type); ?></td>
			</tr>
			<tr>
				<td><label for="Size">Storlek (fritext)</label></td>
				<td><?php echo htmlspecialchars($titledeed->Size); ?></td>
			</tr>
			<tr>
				<td><label for="Size">Nivå</label></td>
				<td><?php echo $titledeed->Level; ?></td>
			</tr>
			<tr>

				<td><label for="Location">Plats fritext</label></td>
				<td><?php echo htmlspecialchars($titledeed->Location); ?></td>
			</tr>
			<?php if (TitledeedPlace::isInUse($current_larp)) {?>
			<tr>
				<td><label for="Location">Plats</label></td>
				<td>
	                <?php echo $titledeed->getTitledeedPlaceName(); ?>
				</td>
			</tr>
			<?php } ?>	

			<tr>

				<td><label for="Tradeable">Kan säljas</label></td>
    			<td><?php echo ja_nej($titledeed->Tradeable == 1); ?></td>
			</tr>
			</tr>
			<tr>

				<td><label for="IsTradingPost">Handelsstation</label></td>
    			<td><?php echo ja_nej($titledeed->IsTradingPost == 1); ?></td>
			</tr>
			<tr>

				<td><label for="IsTradingPost">Är i spel</label></td>
    			<td><?php echo ja_nej($titledeed->IsInUse()); ?></td>
			</tr>
			<tr>
				<td><label for="PublicNotes">Anteckingar om verksamheten<br>som visas på verksamheten</label></td>
				<td><?php echo nl2br(htmlspecialchars($titledeed->PublicNotes));?></td>
			</tr>
			<tr>
				<td><label for="OrganizerNotes">Anteckningar om verksamheten<br>för arrangörer</label></td>
				<td><?php echo nl2br(htmlspecialchars($titledeed->OrganizerNotes)); ?></td>
			</tr>
			
			<tr>
				<td>Ägare</td>
				<td>
				<?php
                echo "<a href='choose_group.php?operation=add_titledeed_owner_group&Id=$titledeed->Id'><i class='fa-solid fa-plus' title='Lägg till grupp'></i><i class='fa-solid fa-users' title='Lägg till grupp'></i></a><br>";
                $owner_groups = $titledeed->getGroupOwners();
                foreach ($owner_groups as $owner_group) {
                    echo $owner_group->getViewLink() . "<a href='titledeed_admin.php?operation=delete_owner_group&titledeeId=$titledeed->Id&groupId=$owner_group->Id'><i class='fa-solid fa-trash'></i></a><br>";
                }
                
                echo "<a href='choose_role.php?operation=add_titledeed_owner_role&Id=$titledeed->Id'><i class='fa-solid fa-plus' title='Lägg till karaktär'></i><i class='fa-solid fa-user' title='Lägg till karaktär'></i></a><br>";
                
                $owner_roles = $titledeed->getRoleOwners();
                foreach ($owner_roles as $owner_role) {
                    echo $owner_role->getViewLink();
                    echo "<a href='titledeed_admin.php?operation=delete_owner_role&titledeeId=$titledeed->Id&roleId=$owner_role->Id'><i class='fa-solid fa-trash'></i></a><br>";
                    
                }
                ?>
                </td>
            </tr>
                
			<tr>

				<td><label for="Produces">Tillgångar (normalt)</label></td>
				<td>
				<?php echo commaStringFromArrayObject($titledeed->ProducesNormally()); ?>
				</td>
			</tr>
			<tr>

				<td><label for="Requires">Behöver (normalt)</label></td>
				<td><?php echo commaStringFromArrayObject($titledeed->RequiresNormally()); ?></td>
			</tr>
			<tr>

				<td><label for="Produces">Tillgångar</label></td>
				<td>
				<?php echo $titledeed->ProducesString(); ?>
				</td>
			</tr>
			<tr>

				<td><label for="Requires">Behöver</label></td>
				<td><?php echo $titledeed->RequiresString(); ?></td>
			</tr>
			<tr>

				<td><label for="Requires">Behöver för uppgradering</label></td>
				<td><?php echo $titledeed->RequiresForUpgradeString(); ?></td>
			</tr>
			<tr>

				<td><label for="Requires">Resultat</label></td>
				<td><?php echo "<td>".$titledeed->calculateResult()." $currency</td>"; ?></td>
			</tr>

                
 
		</table>
	<?php 
	include 'print_titledeed_results.php';
	?>
	
	</div>
    </body>

</html>