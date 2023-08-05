<?php

class Intrigue_Pdf extends BaseModel{
    
    public $Id;
    public $IntrigueId;
    public $Filename;
    public $FileData;
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $house = static::newWithDefault();
        $house->setValuesByArray($post);
        return $house;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['IntrigueId'])) $this->IntrigueId = $arr['IntrigueId'];
        if (isset($arr['Filename'])) $this->Filename = $arr['Filename'];
        if (isset($arr['FileData'])) $this->FileData = $arr['FileData'];
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_intrigue_pdf SET IntrigueId=?, Filename=?, FileData=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->PropId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_intrigue_pdf (IntrigueId, Filename, FileData) VALUES (?,?,?)");
        
        if (!$stmt->execute(array($this->IntrigueId, $this->Filename, $this->FileData))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public function getIntrigue() {
        return Intrigue::loadById($this->IntrigueId);
    }
    
    public static function delete($id)
    {
        $intriguePdf = static::loadById($id);
        $known_pdfs = $intriguePdf->getAllKnownPdfs();
        foreach ($known_pdfs as $known_pdf) IntrigueActor_KnownPdf::delete($known_pdf->Id);
        
        parent::delete($id);
    }
    
    public static function getAllPDFsForIntrigue(Intrigue $intrigue) {
        $sql = "SELECT * FROM regsys_intrigue_pdf WHERE IntrigueId = ? ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigue->Id));
    }
    
    public static function getAllPDFsForIntrigueActor(IntrigueActor $intrigueActor) {
        $sql = "SELECT regsys_intrigue_pdf.* FROM regsys_intrigue_pdf, regsys_intrigueactor_knownpdf WHERE ".
        "regsys_intrigueactor_knownpdf.IntrigueActorId = ? AND ".
        "regsys_intrigueactor_knownpdf.IntriguePdfId = regsys_intrigue_pdf.Id ".
        "ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($intrigueActor->Id));
    }
    
    public function getAllKnownPdfs() {
        return IntrigueActor_KnownPdf::getAllKnownPdfsForIntriguePdf($this);
    }
    
    
}
