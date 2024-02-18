<?php


class Reserve_Registration extends BaseModel{
    
    
    public $Id;
    public $LARPId;
    public $PersonId;
    public $RegisteredAt;
    public $NPCDesire;
    public $HousingRequestId;
    public $LarpHousingComment;
    public $TentType;
    public $TentSize;
    public $TentHousing;
    public $TentPlace;
    public $GuardianId;
    public $TypeOfFoodId;
    
    
    public static $orderListBy = 'RegisteredAt';
    
    
    public static function newFromArray($post){
        $registration = static::newWithDefault();
        $registration->setValuesByArray($post);
        return $registration;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['LARPId'])) $this->LARPId = $arr['LARPId'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['RegisteredAt'])) $this->RegisteredAt = $arr['RegisteredAt'];
        if (isset($arr['NPCDesire'])) $this->NPCDesire = $arr['NPCDesire'];
        if (isset($arr['HousingRequestId'])) $this->HousingRequestId = $arr['HousingRequestId'];
        if (isset($arr['LarpHousingComment'])) $this->LarpHousingComment = $arr['LarpHousingComment'];
        if (isset($arr['TentType'])) $this->TentType = $arr['TentType'];
        if (isset($arr['TentSize'])) $this->TentSize = $arr['TentSize'];
        if (isset($arr['TentHousing'])) $this->TentHousing = $arr['TentHousing'];
        if (isset($arr['TentPlace'])) $this->TentPlace = $arr['TentPlace'];
        if (isset($arr['GuardianId'])) $this->GuardianId = $arr['GuardianId'];
        if (isset($arr['TypeOfFoodId'])) $this->TypeOfFoodId = $arr['TypeOfFoodId'];
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    public static function allBySelectedLARP(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_reserve_registration WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function count(LARP $larp) {
        if (is_null($larp)) return 0;
        $sql = "SELECT COUNT(*) as Num FROM regsys_reserve_registration WHERE LARPid = ?";
        return static::countQuery($sql, array($larp->Id));
    }
    
    
    public static function isInUse(LARP $larp) {
        if (is_null($larp)) return false;
        $count = static::count($larp);
        if ($count == 0) return false;
        return true;
    }
    
    
    
    # Update an existing registration in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_reserve_registration SET LARPId=?, PersonId=?, 
                RegisteredAt=?, NPCDesire=?, HousingRequestId=?, 
                LarpHousingComment=?, TentType=?, TentSize=?, TentHousing=?, TentPlace=?,
                GuardianId=?, TypeOfFoodId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId, 
            $this->RegisteredAt,$this->NPCDesire, $this->HousingRequestId,
            $this->LarpHousingComment, $this->TentType, $this->TentSize, $this->TentHousing, $this->TentPlace,
            $this->GuardianId, $this->TypeOfFoodId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    
    
    # Create a new registration in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_reserve_registration (LARPId, PersonId, RegisteredAt,
            NPCDesire, HousingRequestId, LarpHousingComment, TentType, TentSize, TentHousing, TentPlace, GuardianId, TypeOfFoodId) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId, $this->RegisteredAt, 
            $this->NPCDesire, $this->HousingRequestId, 
            $this->LarpHousingComment, $this->TentType, $this->TentSize, $this->TentHousing, $this->TentPlace,
            $this->GuardianId, $this->TypeOfFoodId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public static function loadByIds($personId, $larpId)
    {
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $sql = "SELECT * FROM regsys_reserve_registration WHERE PersonId = ? AND LARPId = ?";
        return static::getOneObjectQuery($sql, array($personId, $larpId));
    }
    
    public function getPerson() {
        return Person::loadById($this->PersonId);
    }
    
    public function getGuardian() {
        return Person::loadById($this->GuardianId);
    }
    
    public function getLARP() {
        return LARP::loadById($this->LARPId);
    }
    
    public function getTypeOfFood() {
        if (is_null($this->TypeOfFoodId)) return null;
        return TypeOfFood::loadById($this->TypeOfFoodId);
    }
    
    
    
    public function getOfficialTypes() {
        if (is_null($this->Id)) return array();
        
        $stmt = $this->connect()->prepare("SELECT * FROM ".
            "regsys_officialtype_reserve where Reserve_RegistrationId = ? ORDER BY OfficialTypeId;");
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = OfficialType::loadById($row['OfficialTypeId']);
        }
        $stmt = null;
        return $resultArray;
    }
    
    
    
    # Spara den här relationen
    public function saveAllOfficialTypes($officialTypeIds) {
        if (!isset($officialTypeIds)) {
            return;
        }
        foreach($officialTypeIds as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                "regsys_officialtype_reserve (OfficialTypeId, Reserve_RegistrationId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    
    public function deleteAllOfficialTypes() {
        $stmt = $this->connect()->prepare("DELETE FROM ".
            "regsys_officialtype_reserve WHERE Reserve_RegistrationId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function getSelectedOfficialTypeIds() {
        if (is_null($this->Id)) return array();
        
        $stmt = $this->connect()->prepare("SELECT OfficialTypeId FROM ".
            "regsys_officialtype_reserve where Reserve_RegistrationId = ? ORDER BY OfficialTypeId;");
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = $row['OfficialTypeId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
    
    

    public function turnIntoRegistration() {
        
        //Skapa en registration
        $registration = Registration::newWithDefault();
        $registration->LARPId = $this->LARPId;
        $registration->PersonId = $this->PersonId;
        $now = new Datetime();
        $registration->RegisteredAt = date_format($now,"Y-m-d H:i:s");
        $registration->NPCDesire = $this->NPCDesire;
        $registration->HousingRequestId = $this->HousingRequestId;
        $registration->LarpHousingComment = $this->LarpHousingComment;
        $registration->TentType = $this->TentType;
        $registration->TentSize = $this->TentSize;
        $registration->TentHousing = $this->TentHousing;
        $registration->TentPlace = $this->TentPlace;
        $registration->GuardianId = $this->GuardianId;
        $registration->TypeOfFoodId = $this->TypeOfFoodId;
        
        //Räkna ut hur mycket man ska betala + refnr
        $person = Person::loadById($registration->PersonId);
        $larp = LARP::loadById($this->LARPId);
        $age = $person->getAgeAtLarp($larp);
        $registration->AmountToPay = PaymentInformation::getPrice(date("Y-m-d"), $age, $larp, $registration->FoodChoice);
        
        $registration->PaymentReference = $registration->createPaymentReference();

        
        $registration->create();

        //Official types
        $officialTypeIds = $this->getSelectedOfficialTypeIds();
        $registration->saveAllOfficialTypes($officialTypeIds);
        
        //Gör LARP_Role av alla Reserve_Larp_Role
        $reserve_larp_roles = Reserve_LARP_Role::getReserveRolesForPerson($this->LARPId, $this->PersonId);
        foreach($reserve_larp_roles as $reserve_larp_role) {
            $larp_role = LARP_Role::newWithDefault();
            $larp_role->LARPId = $reserve_larp_role->LARPId;
            $larp_role->RoleId = $reserve_larp_role->RoleId;
            $larp_role->IsMainRole = $reserve_larp_role->IsMainRole;
            $larp_role->create();
        }

        //Skicka anmälan mail
        BerghemMailer::send_registration_mail($registration);
        
        //Ta bort allt kring reservationen
        $this->deleteAllOfficialTypes();
        Reserve_Registration::delete($this->Id);
        foreach($reserve_larp_roles as $reserve_larp_role) {
            Reserve_LARP_Role::deleteByIds($reserve_larp_role->LARPId, $reserve_larp_role->RoleId);
        }
        
        
    }
    
}