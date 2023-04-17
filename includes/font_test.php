<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require_once $root . '/pdf/our_fonts_pdf.php';
our_fonts_pdf::print_test();

