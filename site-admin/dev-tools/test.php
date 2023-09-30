<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

require 'random_name.php';
require 'lorem_ipsum.php';

//Ifthe user isnt admin it may not see these pages
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}

for($i=0; $i < 10; $i++) {
    echo RandomName::getName()."<br>";
}