<?php


class Registration extends BaseModel{

    
    public $Id;
    public $LARPId;
    public $PersonId;
    public $RegisteredAt;
    public $PaymentReference;
    public $AmountToPay;
    public $AmountPayed = 0;
    public $Payed; //Datum
    public $PaymentComment;
    public $IsMember; 
    public $NotComing = 0;
    public $IsToBeRefunded = 0;
    public $RefundAmount = 0;
    public $RefundDate; 
    public $IsOfficial = 0;
    public $OfficialDetails = "";
    public $NPCDesire;
    public $HousingRequestId;
    public $LarpHousingComment;
    public $TentType;
    public $TentSize;
    public $TentHousing;
    public $TentPlace;    
    public $GuardianId;
    public $NotComingReason;
    public $SpotAtLARP = 0;
    public $TypeOfFoodId;
    public $FoodChoice;
    public $LarpPartNotAttending;
    public $LarpPartAcknowledged = 1;
    public $EvaluationDone = 0;
    public $PhotographyApproval;
    public $ArrivalDate;
    public $VehicleLicencePlate;
    public $CheckinTime;
    public $CheckoutTime;
    
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
        if (isset($arr['PaymentReference'])) $this->PaymentReference = $arr['PaymentReference'];
        if (isset($arr['AmountToPay'])) $this->AmountToPay = $arr['AmountToPay'];
        if (isset($arr['AmountPayed'])) $this->AmountPayed = $arr['AmountPayed'];
        if (isset($arr['Payed'])) $this->Payed = $arr['Payed'];
        if (isset($arr['PaymentComment'])) $this->PaymentComment = $arr['PaymentComment'];
        if (isset($arr['IsMember'])) $this->IsMember = $arr['IsMember'];
        if (isset($arr['NotComing'])) $this->NotComing = $arr['NotComing'];
        if (isset($arr['IsToBeRefunded'])) $this->IsToBeRefunded = $arr['IsToBeRefunded'];
        if (isset($arr['RefundAmount'])) $this->RefundAmount = $arr['RefundAmount'];
        if (isset($arr['RefundDate'])) $this->RefundDate = $arr['RefundDate'];
        if (isset($arr['IsOfficial'])) $this->IsOfficial = $arr['IsOfficial'];
        if (isset($arr['OfficialDetails'])) $this->OfficialDetails = $arr['OfficialDetails'];
        if (isset($arr['NPCDesire'])) $this->NPCDesire = $arr['NPCDesire'];
        if (isset($arr['HousingRequestId'])) $this->HousingRequestId = $arr['HousingRequestId'];
        if (isset($arr['LarpHousingComment'])) $this->LarpHousingComment = $arr['LarpHousingComment'];
        if (isset($arr['TentType'])) $this->TentType = $arr['TentType'];
        if (isset($arr['TentSize'])) $this->TentSize = $arr['TentSize'];
        if (isset($arr['TentHousing'])) $this->TentHousing = $arr['TentHousing'];
        if (isset($arr['TentPlace'])) $this->TentPlace = $arr['TentPlace'];
        if (isset($arr['GuardianId'])) $this->GuardianId = $arr['GuardianId'];
        if (isset($arr['NotComingReason'])) $this->NotComingReason = $arr['NotComingReason'];
        if (isset($arr['SpotAtLARP'])) $this->SpotAtLARP = $arr['SpotAtLARP'];
        if (isset($arr['TypeOfFoodId'])) $this->TypeOfFoodId = $arr['TypeOfFoodId'];
        if (isset($arr['FoodChoice'])) $this->FoodChoice = $arr['FoodChoice'];
        if (isset($arr['LarpPartNotAttending'])) $this->LarpPartNotAttending = $arr['LarpPartNotAttending'];
        if (isset($arr['LarpPartAcknowledged'])) $this->LarpPartAcknowledged = $arr['LarpPartAcknowledged'];
        if (isset($arr['EvaluationDone'])) $this->EvaluationDone = $arr['EvaluationDone'];
        if (isset($arr['PhotographyApproval'])) $this->PhotographyApproval = $arr['PhotographyApproval'];
        if (isset($arr['ArrivalDate'])) $this->ArrivalDate = $arr['ArrivalDate'];

        if (isset($arr['VehicleLicencePlate'])) $this->VehicleLicencePlate = $arr['VehicleLicencePlate'];
        if (isset($arr['CheckinTime'])) $this->CheckinTime = $arr['CheckinTime'];
        if (isset($arr['CheckoutTime'])) $this->CheckoutTime = $arr['CheckoutTime'];
        
        
        if (empty($this->FoodChoice)) $this->FoodChoice = null;
        if (empty($this->LarpPartNotAttending)) $this->LarpPartNotAttending = null;
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }

    public static function allBySelectedLARP(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_registration WHERE LARPid = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function countAllComing(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE LARPid = ? AND NotComing=0;";
        return static::countQuery($sql, array($larp->Id));
    }
    
    
    public static function countAllNonOfficials(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE LARPid = ? AND IsOfficial=0 AND NotComing=0;";
        return static::countQuery($sql, array($larp->Id));
    }
 
    public static function countAllOfficials(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE LARPid = ? AND IsOfficial=1 AND NotComing=0;";
        return static::countQuery($sql, array($larp->Id));
    }
    
    
    # Update an existing registration in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_registration SET LARPId=?, PersonId=?,  
                RegisteredAt=?, PaymentReference=?, AmountToPay=?, AmountPayed=?,
                Payed=?, PaymentComment=?, IsMember=?, NotComing=?, IsToBeRefunded=?, RefundAmount=?,
                RefundDate=?, IsOfficial=?, OfficialDetails=?, NPCDesire=?, HousingRequestId=?, LarpHousingComment=?, TentType=?, TentSize=?, TentHousing=?, TentPlace=?, 
                GuardianId=?, NotComingReason=?,
                SpotAtLARP=?, TypeOfFoodId=?, FoodChoice=?, LarpPartNotAttending=?, LarpPartAcknowledged=?, EvaluationDone=?, 
                PhotographyApproval=?, ArrivalDate=?, VehicleLicencePlate=?, CheckinTime=?, CheckoutTime=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId,  
            $this->RegisteredAt, $this->PaymentReference, $this->AmountToPay, $this->AmountPayed, 
            $this->Payed, $this->PaymentComment, $this->IsMember, $this->NotComing, $this->IsToBeRefunded, $this->RefundAmount, 
            $this->RefundDate, $this->IsOfficial, $this->OfficialDetails, $this->NPCDesire, $this->HousingRequestId, $this->LarpHousingComment, 
            $this->TentType, $this->TentSize, $this->TentHousing, $this->TentPlace, 
            $this->GuardianId, $this->NotComingReason, $this->SpotAtLARP, $this->TypeOfFoodId, $this->FoodChoice, 
            $this->LarpPartNotAttending, $this->LarpPartAcknowledged, $this->EvaluationDone, 
            $this->PhotographyApproval, $this->ArrivalDate, $this->VehicleLicencePlate, $this->CheckinTime, $this->CheckoutTime, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    	
    
    
    # Create a new registration in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_registration (LARPId, PersonId, RegisteredAt, 
            PaymentReference, AmountToPay, AmountPayed, Payed, PaymentComment, IsMember,
            NotComing, IsToBeRefunded, RefundAmount, RefundDate, IsOfficial, OfficialDetails, 
            NPCDesire, HousingRequestId, LarpHousingComment, TentType, TentSize, TentHousing, TentPlace, GuardianId, NotComingReason, 
            SpotAtLARP, TypeOfFoodId, FoodChoice, LarpPartNotAttending, LarpPartAcknowledged, EvaluationDone, PhotographyApproval, ArrivalDate, VehicleLicencePlate, CheckinTime, CheckoutTime) 
            VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?,?,?,?,?,?)");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId, $this->RegisteredAt, $this->PaymentReference, $this->AmountToPay,
            $this->AmountPayed, $this->Payed, $this->PaymentComment, $this->IsMember, $this->NotComing, $this->IsToBeRefunded, $this->RefundAmount,
            $this->RefundDate, $this->IsOfficial, $this->OfficialDetails, $this->NPCDesire, $this->HousingRequestId, $this->LarpHousingComment,
            $this->TentType, $this->TentSize, $this->TentHousing, $this->TentPlace, 
            $this->GuardianId, $this->NotComingReason,
            $this->SpotAtLARP, $this->TypeOfFoodId, $this->FoodChoice, $this->LarpPartNotAttending, $this->LarpPartAcknowledged, $this->EvaluationDone, $this->PhotographyApproval, 
            $this->ArrivalDate, $this->VehicleLicencePlate, $this->CheckinTime, $this->CheckoutTime))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function hasPayed() {
        if (is_null($this->AmountToPay)) return false;
        if ($this->AmountToPay <= $this->AmountPayed) {
            return true;
        }
        return false;
        
    }
    
    public function hasDoneEvaluation() {
        if ($this->EvaluationDone == 1) {
            return true;
        }
        return false;
    }
    

    public function isRefundAmount() {
        if ($this->RefundAmount > 0 && empty($this->RefundDate)) {
            return true;
        }
        return false;
        
    }

    public function isNotComing() {
        if ($this->NotComing == 1) {
            return true;
        }
        return false;
        
    }
    
    public function isToBeRefunded() {
        if ($this->IsToBeRefunded == 1) {
            return true;
        }
        return false;
        
    }

    public function approvesPhotography() {
        if ($this->PhotographyApproval == 1) {
            return true;
        }
        return false;
        
    }
    
    public function isCheckedIn() {
        if (isset($this->CheckinTime)) {
            return true;
        }
        return false;
        
    }

    public function isCheckedOut() {
        if (isset($this->CheckoutTime)) {
            return true;
        }
        return false;
        
    }
    
    
    
    public function hasSpotAtLarp() {
        if ($this->NotComing == 1) return false;
        if ($this->SpotAtLARP == 1) return true;
        return false;
    }
 
    public static function loadByIds($personId, $larpId)
    {
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $sql = "SELECT * FROM regsys_registration WHERE PersonId = ? AND LARPId = ?";
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
    
    public function getHousingRequest() {
        if (is_null($this->HousingRequestId)) return null;
        return HousingRequest::loadById($this->HousingRequestId);
    }
    
    
    
    public function allChecksPassed() {
        if (!$this->hasPayed()) return false;
        
        $larp = $this->getLARP();
        if (!$this->isMember() && !$larp->larpInJanuary()) return false;

        
        $person = $this->getPerson();
        if (!$person->isApprovedCharacters($larp)) return false;

        if ($person->getAgeAtLarp($larp) < $larp->getCampaign()->MinimumAgeWithoutGuardian && 
            empty($this->GuardianId))  return false;
        return true;
            
            
    }

    public function getOfficialTypes() {
        if (is_null($this->Id)) return array();
        
        $stmt = $this->connect()->prepare("SELECT * FROM ".
            "regsys_officialtype_person where RegistrationId = ? ORDER BY OfficialTypeId;");
        
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
    public function saveAllOfficialTypes($officialtypeids) {
        if (!isset($officialtypeids)) {
            return;
        }
        foreach($officialtypeids as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                "regsys_officialtype_person (OfficialTypeId, RegistrationId) VALUES (?,?);");
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
            "regsys_officialtype_person WHERE RegistrationId = ?;");
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
            "regsys_officialtype_person where RegistrationId = ? ORDER BY OfficialTypeId;");
        
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
    
    public static function totalIncomeToBe(LARP $larp) {
        //Ett försök att gissa på hur inkomstern kommer att bli.
        $registrationArr = static::allBySelectedLARP($larp);
        $income = 0;
        foreach($registrationArr as $registration) {
            if ($registration->isNotComing()) {
                $income = $income + $registration->AmountPayed;
                
            } elseif (!is_null($registration->AmountToPay)) {
                $income = $income + $registration->AmountToPay;
            }
        }
        return $income;
    }

    public static function totalRefundsToBe(LARP $larp) {
        //Ett försök att gissa på hur inkomstern kommer att bli.
        $registrationArr = static::allBySelectedLARP($larp);
        $refunds = 0;
        foreach($registrationArr as $registration) {
            if ($registration->isToBeRefunded()) {
                if (!empty($registration->RefundAmount)) {
                    $refunds = $refunds - $registration->RefundAmount;
                } elseif (!is_null($registration ->AmountToPay)) {
                    $refunds = $refunds - $registration ->AmountToPay;
                    
                }
            } elseif (isset($registration->RefundAmount) && isset($registration->RefundDate)) {
                $refunds = $refunds - $registration->RefundAmount;
            }
        }
        return $refunds;
    }
    
    
    public static function totalIncomeToday(LARP $larp) {
        return static::totalFeesPayed($larp);
    }

    
    public static function totalFeesPayed(LARP $larp) {
        $registrationArr = static::allBySelectedLARP($larp);
        $income = 0;
        foreach($registrationArr as $registration) {
            $income = $income + $registration ->AmountPayed;
        }
        return $income;
        
    }
    
    public static function totalFeesReturned(LARP $larp) {
        $registrationArr = static::allBySelectedLARP($larp);
        $income = 0;
        foreach($registrationArr as $registration) {
            if (isset($registration->RefundAmount) && isset($registration->RefundDate)) {
                $income = $income + $registration->RefundAmount;
            }
        }
        return $income;
        
    }
    
    
    public function createPaymentReference() {
        $larp = LARP::loadById($this->LARPId);
        if (!empty($larp->PaymentReferencePrefix)) return $larp->PaymentReferencePrefix . $this->PersonId . "B";
        else return $this->LARPId . $this->PersonId . "B";
    }
    
    public function paymentDueDate() {
        $larp = LARP::loadById($this->LARPId);

        $date=date_create($this->RegisteredAt);
        $dateLastPaymentDate = date_create($larp->LastPaymentDate);
        
        date_add($date,date_interval_create_from_date_string("$larp->NetDays days"));
        if ($date < $dateLastPaymentDate) return date_format($date,"Y-m-d");
        return date_format($dateLastPaymentDate,"Y-m-d");
    }
    
    public function isPastPaymentDueDate() {
        $larp = LARP::loadById($this->LARPId);
        
        $dateRegistration=date_create($this->RegisteredAt);
        $today=date_create();
        $diffRegistration=date_diff($dateRegistration,$today);
        $dateLastPaymentDate = date_create($larp->LastPaymentDate);
        
        if ($diffRegistration->days > $larp->NetDays || $dateLastPaymentDate < $today) return true;
        return false;
    }
    
    
    public static function getFoodVariants($larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT FoodChoice, regsys_typeoffood.Name as Name, count(regsys_registration.Id) as Count FROM regsys_registration, regsys_typeoffood WHERE ".
            "regsys_registration.LarpId = ? AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_registration.TypeOfFoodId = regsys_typeoffood.Id ".
            "GROUP BY FoodChoice, regsys_typeoffood.Name ".
            "ORDER BY FoodChoice, regsys_typeoffood.SortOrder";
         
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return null;
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $res = array();
        
        foreach ($rows as $row) {
            $res[] = array($row['FoodChoice'], $row['Name'], $row['Count']);
        }
        $stmt = null;
        
        return $res;
        
    }
    
    public function isPartOfInvoice() {
        $invoice = $this->getInvoice();
        if (isset($invoice)) return true;
        return false;
    }
    
    public function getInvoice() {
        return Invoice::getInvoiceForRegistration($this);
    }
    
    public function changeRegistrationToReserve() {
        //Skapa en registration
        $reserve_registration = Reserve_Registration::newWithDefault();
        $reserve_registration->LARPId = $this->LARPId;
        $reserve_registration->PersonId = $this->PersonId;
        $reserve_registration->NPCDesire = $this->NPCDesire;
        $reserve_registration->HousingRequestId = $this->HousingRequestId;
        $reserve_registration->LarpHousingComment = $this->LarpHousingComment;
        $reserve_registration->TentType = $this->TentType;
        $reserve_registration->TentSize = $this->TentSize;
        $reserve_registration->TentHousing = $this->TentHousing;
        $reserve_registration->TentPlace = $this->TentPlace;
        $reserve_registration->GuardianId = $this->GuardianId;
        $reserve_registration->TypeOfFoodId = $this->TypeOfFoodId;
        $reserve_registration->FoodChoice = $this->FoodChoice;
        $reserve_registration->LarpPartNotAttending = $this->LarpPartNotAttending;
        $reserve_registration->RegisteredAt = $this->RegisteredAt;
        
        $reserve_registration->create();
        
        //Official types
        $officialTypeIds = $this->getSelectedOfficialTypeIds();
        $reserve_registration->saveAllOfficialTypes($officialTypeIds);
        
        //Gör Reserve_Larp_Role av alla LARP_Role
        $larp_roles = LARP_Role::getRegisteredRolesForPerson($this->LARPId, $this->PersonId);
        foreach($larp_roles as $larp_role) {
            $reserve_role = Reserve_LARP_Role::newWithDefault();
            $reserve_role->LARPId = $larp_role->LARPId;
            $reserve_role->RoleId = $larp_role->RoleId;
            $reserve_role->IsMainRole = $larp_role->IsMainRole;
            $reserve_role->create();
        }
        
        
        //Ta bort allt kring reservationen
        $this->deleteAllOfficialTypes();
        Registration::delete($this->Id);
        foreach($larp_roles as $larp_role) {
            LARP_Role::deleteByIds($larp_role->LARPId, $larp_role->RoleId);
        }
        
        
        
    }
    
    public static function calculateDaysNotComing(LARP $larp, $daysComingArr) {
        $daysNotComingsArr = array();
        $begin = new DateTime(substr($larp->StartDate,0,10));
        $end   = new DateTime(substr($larp->EndDate,0,10));
        
        for($i = $begin; $i <= $end; $i->modify('+1 day')){
            $datestr = $i->format("Y-m-d");
            if (in_array($datestr, $daysComingArr)) continue;
            $daysNotComingsArr[] = $datestr;
        }
        
        return implode(", ",$daysNotComingsArr);
    }
    
    public static function getAllToApprove(Larp $larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_registration WHERE LarpId=? AND LarpPartAcknowledged=0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public function isMember() {
        //Vi har fått svar på att man har betalat medlemsavgift för året. Behöver inte kolla fler gånger.
        if ($this->IsMember == 1) return true;
        
        $person = $this->getPerson();
        $larp = LARP::loadById($this->LARPId);
        
        $isMember = $person->isMemberAtLarp($larp);
        
        if ($isMember) {
            $this->IsMember = 1;
            $this->update();
            return true;
        }
        
        return false;
    }
    
    public static function getByLicencePlate(String $licesncePlate, Larp $larp) {
        $sql = "SELECT * FROM regsys_registration WHERE VehicleLicencePlate=? AND LARPId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($licesncePlate, $larp->Id));    
    }
    
    
}