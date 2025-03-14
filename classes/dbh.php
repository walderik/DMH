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
        $dbConnectionData = Environment::getDbConnectionData();
        self::$dbServername = $dbConnectionData->ServerName;
        self::$dbUsername = $dbConnectionData->UserName;
        self::$dbPassword = $dbConnectionData->Password;
        self::$dbName = $dbConnectionData->DBName;
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
    
    public static function connectStatic() {
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