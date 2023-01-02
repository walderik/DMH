<?php

class Dbh {
    protected function connect() {
        try {
            $dbServername = "192.168.0.20";
            $dbUsername = "root";
            $dbPassword = "";
            $dbName = "dmh_registrationsystem";
            
            $dbh = new PDO('mysql:host='.$dbServername.';dbname='.$dbName, $dbUsername, $dbPassword);
            return $dbh;
        }
        catch (PDOException $e) {
            print "Error: ". $e->getMessage() . "<br>";
            die();
        }
    }
}