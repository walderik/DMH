<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $code = $_GET['code'];
    $ssn = base64_decode($code);
    $person = Person::findPersonBySSN($ssn);
    
    header("Location: ../checkin_person.php?id=$person->Id");
    exit;
}
