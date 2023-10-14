<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/init.php';

AccessControl::accessControlLarp();
 