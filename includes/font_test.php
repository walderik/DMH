<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require_once $root . '/pdf/our_fonts_pdf.php';
//print_r(OurFonts::fontArray());
//print_r(OurFonts::fontsToLoad());

our_fonts_pdf::print_test();

