<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $school = Magic_School::newFromArray($_POST);
        $school->create();
    } elseif ($operation == 'delete') {
        Magic_School::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $school=Magic_School::loadById($_POST['Id']);
        $school->setValuesByArray($_POST);
        $school->update();
    } elseif ($operation == "add_school_spell") {
        $school=Magic_School::loadById($_POST['Id']);
        if (isset($_POST['SpellId'])) $school->addSpells($_POST['SpellId']);
    }
}


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['Id'])) $school=Magic_School::loadById($_GET['Id']);
    $operation = "";
    if (isset($_GET['operation'])) $operation = $_GET['operation'];
    
    if ($operation == "remove_spell") {
        $school->removeSpell($_GET['SpellId']);
    }
}



$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : $_SERVER['HTTP_REFERER'];
if (empty($referer)) $referer = "../magic_schools_admin.php";
header('Location: ' . $referer);
exit;

