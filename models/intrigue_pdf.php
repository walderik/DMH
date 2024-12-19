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
    
    public static function getAllByLarp(LARP $larp) {
        $sql = "SELECT regsys_intrigue_pdf.* FROM regsys_intrigue_pdf, regsys_intrigue WHERE ".
        "regsys_intrigue.LarpId = ? AND ".
        "regsys_intrigue_pdf.IntrigueId = regsys_intrigue.Id ".
        "ORDER BY regsys_intrigue.Number";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
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
    
    public function getAllPersonsWhoKnowsPdf() {
        $sql = "SELECT * FROM regsys_person WHERE Id IN ".
        "(SELECT regsys_role.PersonId FROM regsys_role, regsys_intrigueactor, regsys_intrigue_pdf, regsys_intrigueactor_knownpdf WHERE ".
        "regsys_role.Id = regsys_intrigueactor.RoleId AND ".
        "regsys_intrigueactor.Id = regsys_intrigueactor_knownpdf.IntrigueActorId AND ".
        "regsys_intrigueactor_knownpdf.IntriguePdfId = ?) ".
        "ORDER BY Id";
        $knowsDirectly = Person::getSeveralObjectsqQuery($sql, array($this->Id));
        
        $knowsThroughSubdivision= array();
        $sql = "SELECT * FROM regsys_person WHERE Id IN ".
            "(SELECT regsys_role.PersonId FROM regsys_role, regsys_subdivisionmember, regsys_intrigueactor, regsys_intrigue_pdf, regsys_intrigueactor_knownpdf WHERE ".
            "regsys_role.Id = regsys_subdivisionmember.RoleId AND ".
            "regsys_subdivisionmember.SubdivisionId = regsys_intrigueactor.SubdivisionId AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_knownpdf.IntrigueActorId AND ".
            "regsys_intrigueactor_knownpdf.IntriguePdfId = ?) ".
            "ORDER BY Id";
        $knowsThroughSubdivision = Person::getSeveralObjectsqQuery($sql, array($this->Id));
        return array_unique(array_merge($knowsDirectly, $knowsThroughSubdivision), SORT_REGULAR);
            
    }

    public function getAllGroupsWhoKnowsPdf() {
        $sql = "SELECT * FROM regsys_group WHERE Id IN ".
            "(SELECT regsys_intrigueactor.GroupId FROM regsys_intrigueactor, regsys_intrigue_pdf, regsys_intrigueactor_knownpdf WHERE ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_knownpdf.IntrigueActorId AND ".
            "regsys_intrigueactor_knownpdf.IntriguePdfId = ?) ".
            "ORDER BY Id";
        return Group::getSeveralObjectsqQuery($sql, array($this->Id));
        
    }
    
    
    public function mayView(Person $person) {
        
        //Kontrollera om personen har en roll som får se 
        $sql = "SELECT Count(regsys_intrigueactor_knownpdf.IntriguePdfId) as Num FROM ".
            "regsys_role, regsys_intrigueactor, regsys_intrigueactor_knownpdf WHERE ".
            "regsys_role.PersonId = ? AND ".
            "regsys_role.Id = regsys_intrigueactor.RoleId AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_knownpdf.IntrigueActorId AND ".
            "regsys_intrigueactor_knownpdf.IntriguePdfId = ?;";

        $count = static::countQuery($sql, array($person->Id, $this->Id));
        if ($count > 0) return true;
        
        //Kontrollera om personen har en roll i en grupp som får se
        $sql = "SELECT Count(regsys_intrigueactor_knownpdf.IntriguePdfId) as Num FROM ".
            "regsys_role, regsys_intrigueactor, regsys_intrigueactor_knownpdf WHERE ".
            "regsys_role.PersonId = ? AND ".
            "regsys_role.GroupId = regsys_intrigueactor.GroupId AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_knownpdf.IntrigueActorId AND ".
            "regsys_intrigueactor_knownpdf.IntriguePdfId = ?;";
        
        $count = static::countQuery($sql, array($person->Id, $this->Id));
        if ($count > 0) return true;

        //Kontrollera om personen har en roll i en gruppering som får se
        $sql = "SELECT Count(regsys_intrigueactor_knownpdf.IntriguePdfId) as Num FROM ".
            "regsys_role, regsys_subdivisionmember, regsys_intrigueactor, regsys_intrigueactor_knownpdf WHERE ".
            "regsys_role.PersonId = ? AND ".
            "regsys_subdivisionmember.RoleId = regsys_role.Id AND ".
            "regsys_subdivisionmember.SubdivisionId = regsys_intrigueactor.SubdivisionId AND ".
            "regsys_intrigueactor.Id = regsys_intrigueactor_knownpdf.IntrigueActorId AND ".
            "regsys_intrigueactor_knownpdf.IntriguePdfId = ?;";
        
        $count = static::countQuery($sql, array($person->Id, $this->Id));
        if ($count > 0) return true;
        
        
        //TODO kolla dynamiska medlemmar i grupperingar
        
        return false;
    }
}
