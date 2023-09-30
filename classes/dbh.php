<?php


class Dbh {
    public static $dbServername = "";
    public static $dbUsername = "";
    public static $dbPassword = "";
    public static $dbName = "";
    
//     public static $dbServername = "berghemsvanner.se.mysql.service.one.com";
//     public static $dbUsername = "berghemsvanner_";
//     public static $dbPassword = "Y2K0U1!";
//     public static $dbName = "berghemsvanner_";
    
    
    private static function setUpConnection() {
        if (str_contains($_SERVER['HTTP_HOST'], 'localhost') || str_contains($_SERVER['HTTP_HOST'], '192.168.0.19') || str_contains($_SERVER['HTTP_HOST'], '155.4.119.71')) {
            echo "local";
            self::$dbServername = "192.168.0.19";
            self::$dbUsername = "root";
            self::$dbPassword = "";
            self::$dbName = "berghemsvanner_";
        } else {
            self::$dbServername = "berghemsvanner.se.mysql.service.one.com";
            self::$dbUsername = "berghemsvanner_";
            self::$dbPassword = "Y2K0U1!";
            self::$dbName = "berghemsvanner_";
            
        }
    }
    
 # Om vi behöver längre timout :   
//     $DBH = new PDO(
//         "mysql:host=$host;dbname=$dbname",
//         $username,
//         $password,
//         array(
//             PDO::ATTR_TIMEOUT => 5, // in seconds
//             PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
//         )
//         );
    
    protected function connect() {
        Dbh::setUpConnection();
        try {
            $dbh = new PDO('mysql:host='.self::$dbServername.';dbname='.self::$dbName, self::$dbUsername, self::$dbPassword);
            return $dbh;
        }
        catch (PDOException $e) {
            print "Error: ". $e->getMessage() . "<br>";
            die();
        }

    }
    
    protected static function connectStatic() {
        Dbh::setUpConnection();
        try {

            $dbh = new PDO('mysql:host='.self::$dbServername.';dbname='.self::$dbName, self::$dbUsername, self::$dbPassword);
            return $dbh;
        }
        catch (PDOException $e) {
            print "Error: ". $e->getMessage() . "<br>";
            die();
        }

    }
    
}