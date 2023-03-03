<?php
include_once 'header_subpage.php';
?>

<div class="content">
    <h1>Mat</h1>

    Just nu är det <?php echo count(Registration::allBySelectedLARP()); ?> anmälda deltagare.<br>
    
    <?php 
    $count = TypeOfFood::countByType($current_larp);
    foreach($count as $item) {
        echo $item['Name'].": ".$item['Num']." st<br>";
    }
    
    
    ?>
</div>

