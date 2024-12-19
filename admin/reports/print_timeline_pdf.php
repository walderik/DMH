<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}


$formatter = new IntlDateFormatter(
    'sv-SE',
    IntlDateFormatter::FULL,
    IntlDateFormatter::FULL,
    'Europe/Stockholm',
    IntlDateFormatter::GREGORIAN,
    'EEEE d MMMM'
    );

$name = 'Körschema';

$pdf = new Report_TCP_PDF();

$pdf->init($current_person->Name, $name, $current_larp->Name, false);


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}


$timeline_array = Timeline::getAll($current_larp);


$startdate = new DateTime(substr($current_larp->StartDate, 0, 10));
$enddate   = new DateTime(substr($current_larp->EndDate, 0, 10));

$starttime = date('H:i', strtotime($current_larp->StartDate));
$endtime = date('H:i',strtotime($current_larp->EndDate));

$interval = DateInterval::createFromDateString('1 day');
$enddate->modify('+1 minute');
$period = new DatePeriod($startdate, $interval, $enddate);
$enddate->modify('-1 minute');

foreach ($period as $dt) {
    $headline = ucfirst($formatter->format($dt));
    $rows = array();
    $header = array("Tid", "Händelse");
    if ($dt == $startdate) $rows[] = array($starttime, "Lajvstart");
    $this_day = clone $dt;

    //Vad händer den här dagen
    $happens = whatHappensDay($this_day);
    foreach ($happens as $timeline) {
        $text = $timeline->Description;
        if (isset($timeline->IntrigueId)) {
            $intrigue = $timeline->getIntrigue();
            $text = $text . ", Intrig $intrigue->Number. $intrigue->Name";
        }
        $rows[] = array(substr($timeline->When,11,5), $text);
    }
    
    if ($dt == $enddate) $rows[] = array($endtime, "Lajvslut");
    
    // add a page
    $pdf->AddPage();
    // print table
    $pdf->Table($headline, $header, $rows);
    
}




// close and output PDF document
$pdf->Output($name.'.pdf', 'I');


function whatHappensDay($starttime) {
    global $timeline_array;
    $endtime = clone $starttime;
    $endtime->modify("+1 day");
    $res = array();
    foreach ($timeline_array as $timeline) {
        $time = new DateTime($timeline->When);
        if ($time >= $starttime && $time < $endtime) {
            $res[] = $timeline;
        }
    }
    return $res;
}

