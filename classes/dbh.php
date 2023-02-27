<?php

global $tbl_prefix;

class Dbh {
    protected function connect() {
        global $tbl_prefix;
        try {
            $dbServername = "192.168.0.20";
            $dbUsername = "root";
            $dbPassword = "";
            $dbName = "berghemsvanner_";
            
            $dbh = new PDO('mysql:host='.$dbServername.';dbname='.$dbName, $dbUsername, $dbPassword);
            return $dbh;
        }
        catch (PDOException $e) {
            print "Error: ". $e->getMessage() . "<br>";
            die();
        }
        $tbl_prefix = "";
    }
    
    protected static function connectStatic() {
        global $tbl_prefix;
        try {
            $dbServername = "192.168.0.20";
            $dbUsername = "root";
            $dbPassword = "";
            $dbName = "berghemsvanner_";
            
            $dbh = new PDO('mysql:host='.$dbServername.';dbname='.$dbName, $dbUsername, $dbPassword);
            return $dbh;
        }
        catch (PDOException $e) {
            print "Error: ". $e->getMessage() . "<br>";
            die();
        }
        $tbl_prefix = "";
    }
    
}