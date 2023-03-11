<?php require 'header.php'; ?>

<nav id="navigation">
    <a href="<?php echo $current_larp->getCampaign()->Homepage ?>" class="logo" target="_blank">
    	<img src="../images/<?php echo $current_larp->getCampaign()->Icon; ?>" width="30" height="30"/>
    </a>
    <a href="choose_larp.php" class="logo"><?php echo $current_larp->Name;?></a>
    <ul class="links">
        <li><a href="index.php"><i class="fa-solid fa-house"></i>Hem</a></li>
        <li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
    </ul>
</nav>
<?php $current_larp->getCampaign()->Homepage ?>