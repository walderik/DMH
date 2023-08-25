<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];

    
        if ($operation == 'insert') {
            $timeline = Timeline::newFromArray($_POST);
            $timeline->create();
        } elseif ($operation == 'delete') {
            Timeline::delete($_POST['Id']);
        } elseif ($operation == 'update') {
            $timeline=Timeline::loadById($_POST['Id']);
            $timeline->setValuesByArray($_POST);
            $timeline->update();
        }
    }
    if (isset($_POST['Referer'])) header('Location: ' . $_POST['Referer']);
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Timeline::delete($_GET['id']);
        if (isset($_GET['gotoreferer'])) header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
}
$timeline_array = Timeline::getAll($current_larp);

function whatHappens($starttime) {
    global $timeline_array;
    $endtime = clone $starttime;
    $endtime->modify("+1 hour");
    $res = array();
    foreach ($timeline_array as $timeline) {
        $time = new DateTime($timeline->When);
        if ($time >= $starttime && $time < $endtime) {
           $res[] = $timeline; 
        }
    }
    return $res;
}


$formatter = new IntlDateFormatter(
    'sv-SE',
    IntlDateFormatter::FULL,
    IntlDateFormatter::FULL,
    'Europe/Stockholm',
    IntlDateFormatter::GREGORIAN,
    'EEEE d MMMM'
    );





include 'navigation.php';
?>


    <div class="content">
        <h1>Körschema</h1>
            <a href="timeline_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  &nbsp; &nbsp;
        
        <a href="reports/print_timeline_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera pdf</a> 
    
        <?php 
        
        $startdate = new DateTime(substr($current_larp->StartDate, 0, 10));
        $enddate   = new DateTime(substr($current_larp->EndDate, 0, 10));
        
        $starthour = date('H', strtotime($current_larp->StartDate));
        $endHour = date('H',strtotime($current_larp->EndDate));
        
        $interval = DateInterval::createFromDateString('1 day');
        $enddate->modify('+1 minute');
        $period = new DatePeriod($startdate, $interval, $enddate);
        $enddate->modify('-1 minute');
        echo "<table border='0'>";
        foreach ($period as $dt) {
            echo "<tr><td colspan='2'><h2>".ucfirst($formatter->format($dt))."</h2></td></tr>";
            $morning = 0;
            $evening = 23;
            if ($dt == $startdate) $morning = $starthour;
            if ($dt == $enddate) $evening = $endHour;
            for ($i = $morning;$i <= $evening; $i++) {
                echo "<tr><td valign='top'><strong>";
                echo $i;
                echo "</strong></td><td valign='top'>";
                if ($dt == $startdate && $i == $starthour) echo "Lajvstart<br>";
                $this_hour = clone $dt;
                $this_hour->modify("+$i hours");
                //Vad händer den här timmen
                $happens = whatHappens($this_hour);
                foreach ($happens as $timeline) {
                    echo substr($timeline->When,11,5)." $timeline->Description";
                    if (isset($timeline->IntrigueId)) {
                        $intrigue = $timeline->getIntrigue();
                        echo " <a href='view_intrigue.php?Id=$intrigue->Id'>";
                        echo "$intrigue->Number. $intrigue->Name"; 
                        echo "</a>";
                    }
                    echo " <a href='timeline_form.php?operation=update&id=" . $timeline->Id . "'><i class='fa-solid fa-pen'></i></a>";
                    echo " <a href='timeline_admin.php?operation=delete&id=" . $timeline->Id . "'><i class='fa-solid fa-trash'></i></a>";
                    echo "<br>";
                }
                
                if ($dt == $enddate && $i == $endHour) echo "Lajvslut<br>";
                echo "</td></tr>";
            }
        }
        echo "</table>";

        ?>
    </div>
	
</body>

</html>