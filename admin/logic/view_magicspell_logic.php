<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $spell = Magic_Spell::newFromArray($_POST);
        $spell->create();
    } elseif ($operation == 'delete') {
        Magic_Spell::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $spell=Magic_Spell::loadById($_POST['Id']);
        $spell->setValuesByArray($_POST);
        $spell->update();
    } elseif ($operation == "add_spell_school") {
        $spell=Magic_Spell::loadById($_POST['Id']);
        if (isset($_POST['SchoolId'])) $spell->addSchools($_POST['SchoolId']);
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['Id'])) $spell=Magic_Spell::loadById($_GET['Id']);
    $operation = "";
    if (isset($_GET['operation'])) $operation = $_GET['operation'];
    
    if ($operation == "remove_school") {
        $school=Magic_School::loadById($_GET['SchoolId']);
        $school->removeSpell($spell->Id);
    }
}


$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : $_SERVER['HTTP_REFERER'];
if (empty($referer)) $referer = "../magic_schools_admin.php";
header('Location: ' . $referer);

