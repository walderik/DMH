<?php
global $root, $current_user, $current_larp, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/includes/all_includes.php';
use chillerlan\QRCode\{QRCode, QROptions};

$link = "https://$_SERVER[HTTP_HOST]/checkin/person.php?id=".base64_encode($current_person->SocialSecurityNumber);
//$data   = 'otpauth://totp/test?secret=B3JX4VCVJDVNXNZ5&issuer=chillerlan.net';
$qrcode = (new QRCode)->render($link);

// default output is a base64 encoded data URI
printf('<img width=300px" src="%s" alt="QR Code" />', $qrcode);