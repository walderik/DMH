<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';
require_once $root . '/pdf/character_sheet_pdf.php';


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $roleId = $_GET['id'];
    $role = Role::loadById($roleId);
}

if (empty($role)) {
    header('Location: index.php'); // Karaktären finns inte
    exit;
}

# Kolla behörigheten
$person = $role->getPerson();
if ($current_user->IsAdmin==0 && $person->UserId != $current_user->Id) {
    header("Location: index.php"); # Inte din karaktär
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // Karaktären är inte anmäld
    exit;
}


$pdf = new CharacterSheet_PDF();
$title = 'Karaktärsblad '.$role->Name;
$pdf->SetTitle(utf8_decode($title));
$pdf->SetAuthor(utf8_decode($current_larp->Name));
$pdf->SetCreator('Omnes Mundos');
$pdf->AddFont('Helvetica','');
$subject = $role->Name;
$pdf->SetSubject(utf8_decode($subject));

$pdf->new_character_sheet($role, $current_larp);

$pdf->Output();