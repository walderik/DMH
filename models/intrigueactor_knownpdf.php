<?php

class IntrigueActor_KnownPdf extends BaseModel{
    
    public $Id;
    public $IntrigueActorId;
    public $IntriguePdfId;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueActorId'])) $this->IntrigueActorId = $arr['IntrigueActorId'];
        if (isset($arr['IntriguePDFId'])) $this->IntriguePdfId = $arr['IntriguePDFId'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigueactor_knownpdf SET IntrigueActorId=?, IntriguePdfId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntriguePdfId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigueactor_knownpdf (IntrigueActorId, IntriguePdfId) VALUES (?,?)");
        
        if (!$stmt->execute(array($this->IntrigueActorId, $this->IntriguePdfId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getIntrigueActor() {
        return IntrigueActor::loadById($this->IntrigueActorId);
    }
    
    public function getIntriguePDF() {
        return Intrigue_Pdf::loadById($this->IntriguePdfId);
    }
    

    public static function getAllKnownPdfsForRole(Role $role, LARP $larp) {
        $sql = "SELECT regsys_intrigueactor_knownpdf.* FROM regsys_intrigueactor_knownpdf, regsys_intrigueactor, regsys_intrigue WHERE ".
            "regsys_intrigueactor_knownpdf.IntrigueActorId = regsys_intrigueactor.Id AND ".
            "regsys_intrigueactor.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ? AND ".
            "regsys_intrigueactor.RoleId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $role->Id));
    }
    
    public static function getAllKnowninPdfsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT * FROM regsys_intrigueactor_knownpdf WHERE IntrigueActorId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public static function getAllKnownPdfsForIntriguePdf(Intrigue_Pdf $intrigue_pdf) {
        $sql = "SELECT * FROM regsys_intrigueactor_knownpdf WHERE IntriguePdfId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue_pdf->Id));
    }
    
    
    public static function loadByIds($intriguePdfId, $intrigueActorId) {
        $sql = "SELECT regsys_intrigueactor_knownpdf.* FROM regsys_intrigueactor_knownpdf, regsys_intrigue_pdf WHERE ".
            "regsys_intrigueactor_knownpdf.IntrigueActorId = ? AND ".
            "regsys_intrigue_pdf.Id = regsys_intrigueactor_knownpdf.IntriguePdfId AND ".
            "regsys_intrigue_pdf.Id = ?";
        return static::getOneObjectQuery($sql, array($intrigueActorId, $intriguePdfId));
    }
    
}
