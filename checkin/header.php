<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/includes/init.php';

AccessControl::hasAccessCheckin($current_person, $current_larp);
 