<?php


function getAge($date, $at_date) {
    return intval(date('Y', strtotime($at_date) - strtotime($date))) - 1970;
}

function startsWithNumber($string) {
    return strlen($string) > 0 && ctype_digit(substr($string, 0, 1));
}