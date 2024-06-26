<?php
include_once '../header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'update') {
        $magician=Magic_Magician::loadById($_POST['Id']);
        $magician->setValuesByArray($_POST);
        $magician->update();
    } elseif ($operation == "add_magician_spell") {
        $magician=Magic_Magician::loadById($_POST['Id']);
        if (isset($_POST['SpellId'])) $magician->addSpells($_POST['SpellId'], $current_larp);
    } elseif ($operation == "add_magician") {
        if (isset($_POST['RoleId'])) Magic_Magician::createMagicians($_POST['RoleId'], $current_larp);
    } elseif ($operation == "set_master") {
        $magician=Magic_Magician::loadById($_POST['Id']);
        if (isset($_POST['MagicianId'])) {
            $magician->MasterMagicianId = $_POST['MagicianId'];
            $magician->update();
        }

    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['Id'])) $magician=Magic_Magician::loadById($_GET['Id']);
    $operation = "";
    if (isset($_GET['operation'])) $operation = $_GET['operation'];
    
    if ($operation == "remove_spell") {
        $magician->removeSpell($_GET['SpellId']);
    } elseif ($operation == 'delete') {
        Magic_Magician::delete($_GET['Id']);
    }
}



$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : $_SERVER['HTTP_REFERER'];
if (empty($referer)) $referer = "../magic_magician_admin.php";
header('Location: ' . $referer);

