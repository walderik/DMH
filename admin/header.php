<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/includes/init.php';

AccessControl::accessControlLarp();
 