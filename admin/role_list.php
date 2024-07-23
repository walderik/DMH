<?php

include_once 'header.php';


$roles = $current_larp->getAllMainRoles(false);

include 'navigation.php';
?>


	<div class="content">
	
		<h1>Alla karaktärer</h1>
	
		<?php foreach ($roles as $role) {
		    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
		    
		    
		    if (isset($role->GroupId)) {
		        $group=Group::loadById($role->GroupId);
		    }
		    
		    
		    ?>
		<h2>
			<?php echo $role->Name;?>&nbsp;
			<a href='edit_role.php?id=<?php echo $role->Id;?>'><i class='fa-solid fa-pen'></i></a>

			<?php echo $role->Name;?>&nbsp;
			<?php echo $role->getEditLinkPen(false);?>

			<a href='character_sheet.php?id=<?php echo $role->Id;?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad'></i></a>
		</h2>
		<?php include 'print_role.php';?>
	<?php }?>
	</div>


</body>
</html>
