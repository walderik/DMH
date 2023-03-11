<?php require 'header.php'; ?>

<nav id="navigation">
    <a href="#" class="logo"><img src="../images/<?php echo $current_larp->getCampaign()->Icon; ?>" width="30" height="30"/> <?php echo $current_larp->Name;?></a>
    <ul class="links">
        <li><a href="index.php"><i class="fa-solid fa-house"></i>Hem</a></li>
        <li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
    </ul>
</nav>