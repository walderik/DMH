<?php

class LARP extends BaseModel{

    public  $Id; 
    public  $Name;
    public  $TagLine; 
    public  $StartDate; 
    public  $EndDate;
    public  $MaxParticipants; 
    public  $LatestRegistrationDate;
    public  $StartTimeLARPTime;
    public  $EndTimeLARPTime;
    public  $DisplayIntrigues = 0;
    public  $DisplayHousing = 0;
    public  $CampaignId;
    public  $VisibleToParticipants;
    public  $RegistrationOpen = 0;
    public  $PaymentReferencePrefix = "";
    public  $NetDays = 0;
    public  $LastPaymentDate;
    public  $HasTelegrams = 0;
    public  $HasLetters = 1;
    public  $HasRumours = 1;
    public  $HasAlchemy = 0;
    public  $LastDayAlchemy;
    public  $LastDayAlchemySupplier;
    public  $HasMagic = 0;
    public  $HasVisions = 0;
    public  $HasCommerce = 0;
    public  $ChooseParticipationDates = 0;
    public  $Description;
    public  $ContentDescription;
    public  $EvaluationOpenDate;
    public  $EvaluationLink;

    
//     public static $tableName = 'larp';
    public static $orderListBy = 'StartDate';
    
    public static function newFromArray($post){
        $larp = static::newWithDefault();
        $larp->setValuesByArray($post);
        return $larp;
    }
     
     
    public function setValuesByArray($arr) {
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['TagLine'])) $this->TagLine = $arr['TagLine'];
        if (isset($arr['StartDate'])) $this->StartDate = $arr['StartDate'];
        if (isset($arr['EndDate'])) $this->EndDate = $arr['EndDate'];
        if (isset($arr['MaxParticipants'])) $this->MaxParticipants = $arr['MaxParticipants'];
        if (isset($arr['LatestRegistrationDate'])) $this->LatestRegistrationDate = $arr['LatestRegistrationDate'];
        if (isset($arr['StartTimeLARPTime'])) $this->StartTimeLARPTime = $arr['StartTimeLARPTime'];
        if (isset($arr['EndTimeLARPTime'])) $this->EndTimeLARPTime = $arr['EndTimeLARPTime'];
        if (isset($arr['DisplayIntrigues'])) $this->DisplayIntrigues = $arr['DisplayIntrigues'];
        if (isset($arr['DisplayHousing'])) $this->DisplayHousing = $arr['DisplayHousing'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['VisibleToParticipants'])) $this->VisibleToParticipants = $arr['VisibleToParticipants'];        
        if (isset($arr['RegistrationOpen'])) $this->RegistrationOpen = $arr['RegistrationOpen'];
        if (isset($arr['PaymentReferencePrefix'])) $this->PaymentReferencePrefix = $arr['PaymentReferencePrefix'];
        if (isset($arr['NetDays'])) $this->NetDays = $arr['NetDays'];
        if (isset($arr['LastPaymentDate'])) $this->LastPaymentDate = $arr['LastPaymentDate'];
        if (isset($arr['HasTelegrams'])) $this->HasTelegrams = $arr['HasTelegrams'];
        if (isset($arr['HasLetters'])) $this->HasLetters = $arr['HasLetters'];
        if (isset($arr['HasRumours'])) $this->HasRumours = $arr['HasRumours'];
        if (isset($arr['HasAlchemy'])) $this->HasAlchemy = $arr['HasAlchemy'];
        if (isset($arr['LastDayAlchemy'])) $this->LastDayAlchemy = $arr['LastDayAlchemy'];
        if (isset($arr['LastDayAlchemySupplier'])) $this->LastDayAlchemySupplier = $arr['LastDayAlchemySupplier'];
        if (isset($arr['HasMagic'])) $this->HasMagic = $arr['HasMagic'];
        if (isset($arr['HasVisions'])) $this->HasVisions = $arr['HasVisions'];
        if (isset($arr['HasCommerce'])) $this->HasCommerce = $arr['HasCommerce'];
        if (isset($arr['ChooseParticipationDates'])) $this->ChooseParticipationDates = $arr['ChooseParticipationDates'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['ContentDescription'])) $this->ContentDescription = $arr['ContentDescription'];
        if (isset($arr['EvaluationOpenDate'])) $this->EvaluationOpenDate = $arr['EvaluationOpenDate'];
        if (isset($arr['EvaluationLink'])) $this->EvaluationLink = $arr['EvaluationLink'];
        
    
        if ($this->EvaluationOpenDate == '') $this->EvaluationOpenDate = null;
        if ($this->StartTimeLARPTime == '') $this->StartTimeLARPTime = null;
        if ($this->EndTimeLARPTime == '') $this->EndTimeLARPTime = null;
        if ($this->LastDayAlchemy == '') $this->LastDayAlchemy = null;
        if ($this->LastDayAlchemySupplier == '') $this->LastDayAlchemySupplier = null;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        $larp = new self();
        $larp->NetDays = 7;
        $larp->VisibleToParticipants = true;
        return $larp;
    }
    
    # Update an existing larp in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_larp SET Name=?, TagLine=?, StartDate=?, EndDate=?, ".
                 "MaxParticipants=?, LatestRegistrationDate=?, StartTimeLARPTime=?, EndTimeLARPTime=?, ".
                 "DisplayIntrigues=?, DisplayHousing=?, CampaignId=?, VisibleToParticipants=?, RegistrationOpen=?, PaymentReferencePrefix=?, NetDays=?, ".
                 "LastPaymentDate=?, HasTelegrams=?, HasLetters=?, HasRumours=?, 
                    HasAlchemy=?, LastDayAlchemy=?, LastDayAlchemySupplier=?, HasMagic=?, HasVisions=?, HasCommerce=?,
                    ChooseParticipationDates=?, Description=?, ContentDescription=?, EvaluationOpenDate=?, EvaluationLink=?  WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->TagLine,
            $this->StartDate, $this->EndDate, $this->MaxParticipants, $this->LatestRegistrationDate, 
            $this->StartTimeLARPTime, $this->EndTimeLARPTime, $this->DisplayIntrigues, $this->DisplayHousing, $this->CampaignId, 
            $this->VisibleToParticipants, $this->RegistrationOpen, $this->PaymentReferencePrefix, $this->NetDays, 
            $this->LastPaymentDate, $this->HasTelegrams, $this->HasLetters, $this->HasRumours, 
            $this->HasAlchemy, $this->LastDayAlchemy, $this->LastDayAlchemySupplier, $this->HasMagic, $this->HasVisions, $this->HasCommerce,
            $this->ChooseParticipationDates,
            $this->Description, $this->ContentDescription, $this->EvaluationOpenDate, $this->EvaluationLink, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    # Create a new larp in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_larp (Name, TagLine, StartDate, EndDate, MaxParticipants, 
            LatestRegistrationDate, StartTimeLARPTime, EndTimeLARPTime, DisplayIntrigues, DisplayHousing, CampaignId, 
            VisibleToParticipants, RegistrationOpen, PaymentReferencePrefix, NetDays, LastPaymentDate, HasTelegrams, 
            HasLetters, HasRumours, 
            HasAlchemy, LastDayAlchemy, LastDayAlchemySupplier, HasMagic, HasVisions, HasCommerce, 
            ChooseParticipationDates, Description, ContentDescription,EvaluationOpenDate,EvaluationLink) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->Name, $this->TagLine,
            $this->StartDate, $this->EndDate, $this->MaxParticipants, $this->LatestRegistrationDate,
            $this->StartTimeLARPTime, $this->EndTimeLARPTime, $this->DisplayIntrigues, $this->DisplayHousing, $this->CampaignId, 
            $this->VisibleToParticipants, $this->RegistrationOpen, $this->PaymentReferencePrefix, $this->NetDays, $this->LastPaymentDate, $this->HasTelegrams, 
            $this->HasLetters, $this->HasRumours, 
            $this->HasAlchemy, $this->LastDayAlchemy, $this->LastDayAlchemySupplier, $this->HasMagic, $this->HasVisions, $this->HasCommerce,
            $this->ChooseParticipationDates, $this->Description, $this->ContentDescription, $this->EvaluationOpenDate, $this->EvaluationLink))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
        }
        
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getCampaign() {
        return Campaign::loadById($this->CampaignId);
    }
    
    public function getAllRoles() {
        return Role::getAllRoles($this);
    }
    
    public function getAllMainRoles($includeNotComing) {
        return Role::getAllMainRoles($this, $includeNotComing);
    }
    
    public function getAllNotMainRoles($includeNotComing) {
        return Role::getAllNotMainRoles($this, $includeNotComing);
    }
    
    public function isVisibleToParticipants() {
        if ($this->VisibleToParticipants == 1) return true;
        return false;
    }
    
    public function isPastLatestRegistrationDate() {
        $today = date("Y-m-d");
        if ($today <= $this->LatestRegistrationDate) return false;
        return true;
    }
    
    public function isEnded() {
        $now = date("Y-m-d H:i:s");
        if ($now < $this->EndDate) return false;
        return true;
    }
    
    public function isEvaluationOpen() {
        if (!$this->isEnded()) return false;
        if (empty($this->EvaluationOpenDate)) return true;
        $now = date("Y-m-d");
        if ($now < $this->EvaluationOpenDate) return false;
        return true;
    }
    
    public function isAlchemyInputOpen() {
        if (empty($this->getLastDayAlchemy())) return true;
        $now = date("Y-m-d");
        if ($now < $this->LastDayAlchemy) return true;
        if ($now > $this->EndDate) return true;
        return false;
    }
    
    public function isAlchemySupplierInputOpen() {
        if (empty($this->getLastDayAlchemySupplier())) return true;
        $now = date("Y-m-d");
        if ($now > $this->LastDayAlchemySupplier) return false;
        return true;
    }
    
    public function getLastDayAlchemy() {
        if (empty($this->LastDayAlchemy)) return null;
        if ($this->LastDayAlchemy == '0000-00-00') return null;
        return $this->LastDayAlchemy;
    }
    
    public function getLastDayAlchemySupplier() {
        if (empty($this->LastDayAlchemySupplier)) return null;
        if ($this->LastDayAlchemySupplier == '0000-00-00') return null;
        return $this->LastDayAlchemySupplier;
    }
    
    public function useInternalEvaluation() {
        if (empty($this->EvaluationLink)) return true;
        return false;
    }
    
    public function isFull() {
        $number = Registration::countAllNonOfficials($this);
        if ($number >= $this->MaxParticipants) return true;
        else return false;
    }
    
    # Har intrigerna släppts för det här lajvet
    public function isIntriguesReleased() {
        return $this->DisplayIntrigues == 1;
    }
    
    public function isHousingReleased() {
        return $this->DisplayHousing == 1;
    }
    
    # Kör det här lajvet med telegram?
    public function hasTelegrams() {
        return $this->HasTelegrams == 1;
    }
    
    # Kör det här lajvet med brev?
    public function hasLetters() {
        return $this->HasLetters == 1;
    }
    
    # Kör det här lajvet med rykten?
    public function hasRumours() {
        return $this->HasRumours == 1;
    }
    
    public function hasAlchemy() {
        return $this->HasAlchemy == 1;
    }
    
    public function hasMagic() {
        return $this->HasMagic == 1;
    }
    
    public function hasVisions() {
        return $this->HasVisions == 1;
    }
    
    public function hasCommerce() {
        return $this->HasCommerce == 1;
    }
    
    public function chooseParticipationDates() {
        return $this->ChooseParticipationDates == 1;
    }
    
    
    public function mayRegister() {
        if ($this->RegistrationOpen == 0) return false;
        return true;
    }
    
    public function hasRegistrations() {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE LarpId=?;";
        
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return false;
            
        }
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = null;
        
        
        if ($res[0]['Num'] == 0) return false;
        return true;
        
    }
    
    public static function getAllForYear($campaignId, $year) {
        if (empty($campaignId)) {
            $sql = "SELECT * FROM regsys_larp WHERE StartDate LIKE ? ORDER BY CampaignId, ".static::$orderListBy.";";
            $var_array = array($year."%");
        } else { 
            $sql = "SELECT * FROM regsys_larp WHERE CampaignId=? AND StartDate LIKE ? ORDER BY ".static::$orderListBy.";";
            $var_array = array($campaignId,$year."%");

        }
        return static::getSeveralObjectsqQuery($sql, $var_array);

    }
    
    

    public static function allFutureLARPs() {
        $sql = "SELECT * FROM regsys_larp WHERE StartDate >= NOW() AND VisibleToParticipants=1 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, null);
    }
    
    public static function currentParticipatingLARPs(Person $person) {
        $sql = "SELECT * FROM regsys_larp WHERE StartDate <= NOW() AND EndDate >= NOW() AND Id IN ".
            "(SELECT regsys_registration.LarpId FROM regsys_registration WHERE ".
            "regsys_registration.PersonId = ?) ".
        "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id));
    }
    
    
    public static function allFutureOpenLARPs() {
        $sql = "SELECT * FROM regsys_larp WHERE StartDate >= NOW() AND RegistrationOpen=1 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, null);
    }
    
    public static function allFutureNotYetOpenLARPs() {
        $sql = "SELECT * FROM regsys_larp WHERE StartDate >= NOW() AND RegistrationOpen=0 ".
            "AND LatestRegistrationDate >= NOW() ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, null);
    }
    
    public static function allPastLarpsWithRegistrations(Person $person) {
        $sql = "SELECT * FROM regsys_larp WHERE StartDate <= NOW() AND Id IN ".
            "(SELECT DISTINCT regsys_registration.LARPId FROM regsys_registration WHERE ".
            "regsys_registration.PersonId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id));
    }
        
    public static function lastLarpRole(Role $role) {
        $sql="SELECT * FROM regsys_larp WHERE Id IN ".
            "(SELECT LarpId from regsys_larp_role WHERE RoleId=?) ".
            "ORDER BY StartDate DESC";
        return static::getOneObjectQuery($sql, array($role->Id));
    }
    
    public static function lastLarpGroup(Group $group) {
        $sql="SELECT * FROM regsys_larp WHERE Id IN ".
            "(SELECT LarpId from regsys_larp_group WHERE GroupId=?) ".
            "ORDER BY StartDate DESC";
        return static::getOneObjectQuery($sql, array($group->Id));
    }
    
    public static function getPreviousLarpsRole($roleId) {
        if (is_null($roleId)) return Array();

        $sql = "SELECT * FROM regsys_larp WHERE Id IN (SELECT LarpId FROM regsys_larp_role WHERE RoleId = ?) AND EndDate < '".date('Y-m-d')."' ORDER BY StartDate DESC";
        return static::getSeveralObjectsqQuery($sql, array($roleId));
    }
    
    public static function getPreviousLarpsGroup($groupId) {
        if (is_null($groupId)) return Array();
        
        $sql = "SELECT * FROM regsys_larp WHERE Id IN (SELECT LarpId FROM regsys_larp_group WHERE GroupId = ?) AND EndDate < '".date('Y-m-d')."' ORDER BY StartDate DESC";
        return static::getSeveralObjectsqQuery($sql, array($groupId));
    }
    
    public static function allByCampaign($campaignId) {
        $sql = "SELECT * FROM regsys_larp WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($campaignId));
        
    }

    public static function getPreviousLarpsInCampaign(LARP $larp) {
        $sql = "SELECT * FROM regsys_larp WHERE CampaignId = ? AND StartDate < ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId, $larp->StartDate));
        
    }
    
    public static function organizerForLarps(Person $person) {
        $sql="SELECT * FROM regsys_larp WHERE Id IN ".
            "(SELECT LarpId FROM regsys_access_control_larp WHERE ".
            "regsys_access_control_larp.PersonId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id));
    }
    
    
    public function larpInJanuary() {
        $larpmonth = substr($this->StartDate, 5, 2);
        if ($larpmonth == "01") return true;
        return false;
    }
    
    public function getOtherLarpsSameYear() {
        $year = substr($this->StartDate, 0 , 4);
        $sql ="SELECT * FROM regsys_larp WHERE Id != ? AND StartDate LIKE '$year-%' AND CampaignId=?";
        return static::getSeveralObjectsqQuery($sql, array($this->Id, $this->CampaignId));
    }
    
    public static function getAllYears() {
        $sql = "SELECT * FROM regsys_larp ORDER BY StartDate";
        $firstLarp = static::getOneObjectQuery($sql, array());
        $firstYear = substr($firstLarp->StartDate, 0, 4);

        $sql = "SELECT * FROM regsys_larp ORDER BY StartDate DESC";
        $lastLarp = static::getOneObjectQuery($sql, array());
        $lastYear = substr($lastLarp->StartDate, 0, 4);
        
        
        $current_year = date("Y");
        if ($lastYear < $current_year) $lastYear = $current_year;
        
        $years = array();
        for ($i = $firstYear; $i <=$lastYear; $i++) $years[] = $i;
        return $years;
        
    }
    
    public function smallChildAge() {
        return 7;
    }
    
    public function endOfLarp() {
        if ($larpEnded) exit;
        if ()
    }
    
}