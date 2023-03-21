<?php
global $tbl_prefix;
$tbl_prefix = "regsys_";

class Dbh {
    public static $dbServername = "192.168.0.20";
    public static $dbUsername = "root";
    public static $dbPassword = "";
    public static $dbName = "berghemsvanner_";
    
//     public static $dbServername = "berghemsvanner.se.mysql.service.one.com";
//     public static $dbUsername = "berghemsvanner_";
//     public static $dbPassword = "Y2K0U1!";
//     public static $dbName = "berghemsvanner_";
    
    
    
    protected function connect() {
        global $tbl_prefix;
        try {
            $dbh = new PDO('mysql:host='.self::$dbServername.';dbname='.self::$dbName, self::$dbUsername, self::$dbPassword);
            return $dbh;
        }
        catch (PDOException $e) {
            print "Error: ". $e->getMessage() . "<br>";
            die();
        }
        $tbl_prefix = "regsys_";
    }
    
    protected static function connectStatic() {
        global $tbl_prefix;
        try {

            $dbh = new PDO('mysql:host='.self::$dbServername.';dbname='.self::$dbName, self::$dbUsername, self::$dbPassword);
            return $dbh;
        }
        catch (PDOException $e) {
            print "Error: ". $e->getMessage() . "<br>";
            die();
        }
        $tbl_prefix = "regsys_";
    }
    
}