<?php

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class User extends BaseModel{
    
    public $Id;
    public $Name;
    public $Email;
    public $Password;
    public $IsAdmin = 0;
    public $ActivationCode;
    public $EmailChangeCode;
    public $Blocked = 0;
    public $LastLogin = NULL;
    
//     public static $tableName = 'user';
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $user = static::newWithDefault();
        if (isset($post['Id'])) $user->Id = $post['Id'];
        if (isset($post['Name'])) $user->Name = $post['Name'];
        if (isset($post['Email'])) $user->Email = $post['Email'];
        if (isset($post['Password'])) $user->Password = $post['Password'];
        if (isset($post['IsAdmin'])) $user->IsAdmin = $post['IsAdmin'];
        if (isset($post['ActivationCode'])) $user->ActivationCode = $post['ActivationCode'];
        if (isset($post['EmailChangeCode'])) $user->EmailChangeCode = $post['EmailChangeCode'];
        if (isset($post['Blocked'])) $user->Blocked = $post['Blocked'];
        if (isset($post['LastLogin'])) $user->LastLogin = $post['LastLogin'];
        
        return $user;
    }
     
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    public static function loadByEmail($email) {
        if (is_null($email)) return null;
        
        $sql = "SELECT * FROM regsys_user WHERE Email = ?;";
        return static::getOneObjectQuery($sql, array($email));
    }
    
    # För att hitta användaren som vill byta lösenord
    public static function loadByEmailChangeCode($code) {
        if (is_null($code)) return null;
        
        $sql = "SELECT * FROM regsys_user WHERE EmailChangeCode = ?;";
        return static::getOneObjectQuery($sql, array($code));
    }
    
    # Update an existing group in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_user SET Name=?, Email=?, Password=?, IsAdmin=?, ActivationCode=?, EmailChangeCode=?, Blocked=?, LastLogin=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Email, $this->Password, $this->IsAdmin, $this->ActivationCode, $this->EmailChangeCode, $this->Blocked, $this->LastLogin, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new group in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_user (Name, Email, Password, IsAdmin, ActivationCode, EmailChangeCode, Blocked, LastLogin) VALUES (?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->Name, $this->Email, $this->Password, $this->IsAdmin, $this->ActivationCode, $this->EmailChangeCode, $this->Blocked, $this->LastLogin))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getPersons() {
        return Person::getPersonsForUser($this->Id);
    }

     
    public function getUnregisteredPersonsForUser($larp) {
        $persons = Person::getPersonsForUser($this->Id);
        $unregistered_persons = Array();
        foreach ($persons as $person) {
            if (!$person->isRegistered($larp) && !$person->isReserve($larp)) {
                array_push($unregistered_persons,$person);
            }
        }
        return $unregistered_persons;
    }
    
    public function isActivated() {
        if ($this->ActivationCode == 'activated') return true;
        return false;
    }
    
    public function setEmailChangeCode() {
        $code = bin2hex(random_bytes(20));
        $this->EmailChangeCode = $code;
        $this->update();
        return $code;
    }
    
    public function isComing(Larp $larp) {
        if (is_null($larp)) return null;
        $sql = "SELECT COUNT(*) AS Num FROM regsys_person, regsys_registration WHERE ".
            "regsys_person.UserId =? AND ".
            "regsys_person.Id = regsys_registration.PersonId AND ".
            "regsys_registration.SpotAtLARP = 1 AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_registration.LarpId=?;";
        return static::existsQuery($sql, array($this->Id, $larp->Id));
        
    }
    
    public static function allWithoutPersons() {
        $sql = "SELECT * from regsys_user WHERE Id NOT IN (".
            "SELECT UserId FROM regsys_person) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, null);
    }
    
    
 }
