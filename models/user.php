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

    public function getUnregisteredGroups($larp) {
        //TODO returnera grupper som inte ännu är anmälda och som användaren har en deltagare som är gruppledare för
    }
    
    
}
