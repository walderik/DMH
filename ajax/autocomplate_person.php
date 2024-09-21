<?php
include_once 'header.php';


error_reporting(E_ALL);
ini_set('display_errors', 1);

// get the parameters from URL
// $search = $_REQUEST["search"];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $search = $data['search'] ?? '';
    
    $persons = Person::searchPersons($search);
    
    $results = array();
    
    foreach ($persons as $person) {
        $results[] = array($person->Id, "$person->Name, ". $person->getAgeNow().' år');
    }
        
    // return $results;
    echo json_encode($results);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

?>