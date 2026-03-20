<?php
include_once '../header.php';

global $current_larp;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($current_larp->isHousingReleased()) return;
    Housing::reset($current_larp);
}
header('Location: ../housing.php?');
exit;


