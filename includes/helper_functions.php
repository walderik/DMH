<?php


function getAge($date, $at_date) {
    return intval(date('Y', strtotime($at_date) - strtotime($date))) - 1970;
}

function startsWithNumber($string) {
    return strlen($string) > 0 && ctype_digit(substr($string, 0, 1));
}

function displayEmailQueue() {
    
}


function scrub($filename) {
    $scrubbed_filename = str_replace(array("'",'´', '"', '`'), '',$filename);
    $scrubbed_filename = mb_convert_encoding($scrubbed_filename, "ASCII");
    return $scrubbed_filename;
}

# Kolla om man just nu är OM-admin för närvarande
function isOmAdmin(?String $url = NULL) {
    global $current_user;
    if (!isset(AccessControl::hasAccessOther($current_user->Id, AccessControl::ADMIN))) return false;
    if (is_null($url)) $url = $_SERVER['REQUEST_URI'];
    return (strpos($url, "/site-admin/")!=false);
}


function encode_utf_to_iso($string) {
    return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
}