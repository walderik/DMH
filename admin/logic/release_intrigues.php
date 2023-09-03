<?php
include_once '../header.php';

$current_larp->DisplayIntrigues = 1;
$current_larp->update();

header('Location: ../settings.php');

