 <?php

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class User extends BaseModel{
    
    public $Id;
    public $Email;
    public $Password;
    public $IsAdmin = false;
    public $ActivationCode;
    public $EmailChangeCode;
    
//     public static $tableName = 'user';
    public static $orderListBy = 'Email';
    
    public static function newFromArray($post){
        $user = static::newWithDefault();
        if (isset($post['Id'])) $user->Id = $post['Id'];
        if (isset($post['Email'])) $user->Email = $post['Email'];
        if (isset($post['Password'])) $user->Password = $post['Password'];
        if (isset($post['IsAdmin'])) $user->IsAdmin = $post['IsAdmin'];
        if (isset($post['ActivationCode'])) $user->ActivationCode = $post['ActivationCode'];
        if (isset($post['EmailChangeCode'])) $user->EmailChangeCode = $post['EmailChangeCode'];
        
        return $user;
    }
     
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    public static function loadByEmail($email) {
        if (is_null($email)) return null;
        
        $stmt = static::connectStatic()->prepare("SELECT * FROM `user` WHERE Email = ?;");
        
        if (!$stmt->execute(array($email))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return null;
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = $rows[0];
        $stmt = null;
        
        return static::newFromArray($row);
    }
    
    # För att hitta användaren som vill byta lösenord
    public static function loadByEmailChangeCode($code) {
        if (is_null($code)) return null;
        
        $stmt = static::connectStatic()->prepare("SELECT * FROM `user` WHERE EmailChangeCode = ?;");
        
        if (!$stmt->execute(array($code))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return null;
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $row = $rows[0];
        $stmt = null;
        
        return static::newFromArray($row);
    }
    
    # Update an existing group in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE user SET Email=?, Password=?, IsAdmin=?, ActivationCode=?, EmailChangeCode=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Email, $this->Password, $this->IsAdmin, $this->ActivationCode, $this->EmailChangeCode, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new group in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO user (Email, Password, IsAdmin, ActivationCode, EmailChangeCode) VALUES (?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->Email, $this->Password, $this->IsAdmin, $this->ActivationCode, $this->EmailChangeCode))) {
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

    public function getUnregisteredGroupsForUser($larp) {
        $persons = Person::getPersonsForUser($this->Id);
        $unregistered_groups = Array();
        foreach ($persons as $person) {
            $groups = Group::getGroupsForPerson($person->Id);
            foreach ($groups as $group) {
                if (!$group->isRegistered($larp)) {
                    array_push($unregistered_groups,$group);
                }
            }
        }
        return $unregistered_groups;
    }
 
    
    public function getUnregisteredPersonsForUser($larp) {
        $persons = Person::getPersonsForUser($this->Id);
        $unregistered_persons = Array();
        foreach ($persons as $person) {
            if (!$person->isRegistered($larp)) {
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
    }
}
