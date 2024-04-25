<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$name = 'Alkemiingredienser';

$pdf = new Report_PDF();

$pdf->SetTitle(encode_utf_to_iso($name));
$pdf->SetAuthor(encode_utf_to_iso($current_user->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$pdf->SetSubject(encode_utf_to_iso($name));


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

$ingredients = Alchemy_Ingredient::allByCampaign($current_larp);
usort($ingredients, "cmp");
$rows = array();
$rows[] = array("Namn", "Nivå", "Essenser", "Antal");
foreach ($ingredients as $ingredient) {
    
    $rows[] = array($ingredient->Name, $ingredient->Level, $ingredient->getEssenceNames(), $ingredient->countAtLarp($current_larp));
}
$pdf->new_report($current_larp, $name, $rows);



$pdf->Output();
