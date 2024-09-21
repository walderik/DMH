<?php

require '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {


    $personId = $_GET['personId'];

    AccessControl::revokeLarp($personId, $current_larp->Id);
}
header('Location: ../settings.php');

