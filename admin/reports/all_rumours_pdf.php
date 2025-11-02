<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$name = 'Alla rykten';

$pdf = new Report_TCP_PDF();

$pdf->init($current_person->Name, $name, $current_larp->Name, false);


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

$rumours = Rumour::allApprovedBySelectedLARP($current_larp);

$rows = array();
$header = array("Text", "Gäller", "Känner till");
foreach ($rumours as $rumour) {
    $concerns = $rumour->getConcerns();
    $concerns_names = array();
    foreach ($concerns as $concern) {
        $concerns_names[] = $concern->getName();
        
    }
    $knows = $rumour->getKnows();
    $knows_names = array();
    foreach ($knows as $know) {
        $knows_names[] = $know->getName();
        
    }
    
    $rows[] = array($rumour->Text, 
        implode(", ", $concerns_names), 
        implode(", ", $knows_names));
}
// add a page
$pdf->AddPage('L');
// print table
$pdf->Table($name, $header, $rows);



// close and output PDF document
$pdf->Output($name.'.pdf', 'I');