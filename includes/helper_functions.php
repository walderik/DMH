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