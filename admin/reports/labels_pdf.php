<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_person, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/pdf/labels.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET" || !isset($_GET['type'])) {
    header('Location: ../../admin/index.php');
    exit;
}



$pdf = new LABELS_PDF();
$type = $_GET['type'];

if ($type == 'groups') $pdf->allGroups($current_larp);
elseif ($type == 'roles') $pdf->allMainRoles($current_larp);
elseif ($type == 'persons') $pdf->allPersons($current_larp);






// close and output PDF document
$pdf->Output($type.'.pdf', 'I');