<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

include_once 'header.php';

require_once $root . '/pdf/character_sheet_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$bara_intrig = false;
$role = null;
$only_main = false;
$no_history = false;
$double_sided = false;


if (isset($_GET['id'])) {
    $roleId = $_GET['id'];
    $role = Role::loadById($roleId);
    if (empty($role)) {
        header('Location: index.php'); // Karaktären finns inte
        exit;
    } elseif ($role->isPC() && !$role->isRegistered($current_larp)) {
        header('Location: index.php'); // Karaktären är inte anmäld
        exit;
    }
} else {
    if (isset($_GET['bara_intrig'])) $bara_intrig = true;
    if (isset($_GET['main'])) $only_main = true;
    if (isset($_GET['no_history'])) $no_history = true;
    if (isset($_GET['double_sided'])) $double_sided = true;
}


$pdf = new CharacterSheet_PDF();
$title = (empty($role)) ? 'Alla Karaktärer' : ('Karaktärsblad '.$role->Name) ;

$pdf->SetTitle(encode_utf_to_iso($title));
$pdf->SetAuthor(encode_utf_to_iso($current_larp->Name));
$pdf->SetCreator('Omnes Mundi');
$pdf->AddFont('Helvetica','');
$subject = (empty($role)) ? 'ALLA' : $role->Name;
$pdf->SetSubject(encode_utf_to_iso($subject));

$all_info = (isset($_GET['all_info'])) ? true : false;
$only_children = (isset($_GET['children'])) ? true : false;

if (empty($role)) {
    if (empty($only_children)) {
        $pdf->all_character_sheets($current_larp, $bara_intrig, $all_info, $only_main, $no_history, $double_sided);
    }
    else {
        $children = array();
        $roles = Role::getAllMainRoles($current_larp, false);
        foreach ($roles as $role) {
            $person = $role->getPerson();
            if (!is_null($person)) if ($person->getAgeAtLarp($current_larp) < 16) $children[] = $role;
        }
        $pdf->selected_character_sheets($children, $current_larp, $bara_intrig, $all_info, $double_sided);
    }
} else {
    $pdf->new_character_sheet($role, $current_larp, $all_info, false);
}

$pdf->Output();


