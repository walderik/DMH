<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}



class KITCHEN_PDF extends FPDF {

    function Header()
    {
        //$this->Image('../images/telegram.png',null,null,200);
    }
    
    

    // Colored table
    //$headline - Rubrik ovanför tabell. Ser ut som h2  på våra sidor
    //$header - Array med rubikerna för kolumnerna i tabellen
    //$data - Array av arrayer som innehåller det data som ska skrivas ut
    //$columnWidth - Array som innehåller bredden av varje kolumn
    function FancyTable($headline, $header, $data, $columnWidth)
    {
        $y = $this->GetY();
        $this->Line(10, $y, 210-10, $y);
        $this->SetFont('Arial','',14);
        $this->SetFont('','B');
        $this->Cell(0,10, utf8_decode($headline),0,0,'L');
        $this->Ln();
        
        $this->SetFont('Arial','',12);
        // Colors, line width and bold font
        $this->SetFillColor(128, 128, 128);
        $this->SetTextColor(255);
        $this->SetDrawColor(221, 221, 221);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
        for($i=0;$i<count($header);$i++)
            $this->Cell($columnWidth[$i],7, utf8_decode($header[$i]),1,0,'C',true);
            $this->Ln();
            // Color and font restoration
            $this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont('');
            // Data
            $fill = false;
            foreach($data as $row)
            {
                $y = $this->GetY();
                $x = $this->GetX();
                $tab = 0;
                $maxY = $y;
                for($j=0;$j<count($row);$j++) {
                    $this->SetY($y);
                    $this->SetX($x + $tab);
                    $this->MultiCell($columnWidth[$j],6,utf8_decode($row[$j]),0,'L',$fill);
                    $tab = $tab + $columnWidth[$j];
                    if ($this->GetY() > $maxY) $maxY = $this->GetY();
                }
                $this->SetY($maxY);
                $this->Ln();
                $fill = !$fill;
            }
            // Closing line
            $this->Cell(array_sum($columnWidth),0,'','T');
    }

    function print_mat() {
        global $current_larp;
        //$this->SetFont('Helvetica','',14);    # OK är Times, Arial, Helvetica
        //$left = 21;
        //$this->SetXY($left, 68);
        $this->Cell(0,10,utf8_decode("Totalt ".count(Registration::allBySelectedLARP($current_larp))." deltagare."),0,1); # 0 - No border, 1 -  to the beginning of the next line,
        
        $count = TypeOfFood::countByType($current_larp);
        foreach($count as $item) {
            $this->Cell(0,8,utf8_decode($item['Name'].": ".$item['Num']." st"),0, 1);
        }
        
    }
    

   
    function print_allergies() {
        global $current_larp;
        //$header = array('Namn', 'Epost', 'Telefon', 'Övrigt', 'Vald mat');
        $header = array('Namn', 'Epost', 'Övrigt', 'Vald mat');
        $columnWidth = Array (35, 90, 40);
        
        $allAllergies = NormalAllergyType::all();
        
        foreach($allAllergies as $allergy) {
            $headline = "Enbart " . $allergy->Name;
            $persons = Person::getAllWithSingleAllergy($allergy, $current_larp);
            if (isset($persons) && count($persons) > 0) {
                $data = Array();
                 foreach($persons as $person) {
                     $registration=$person->getRegistration($current_larp);
                     $data[] = Array($person->Name, $person->Email, $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
                }
                $this->FancyTable($headline, $header, $data, $columnWidth);

            }
        }
        
        /*
        
        //Multipla allergier
        $persons = Person::getAllWithMultipleAllergies($current_larp);
        if (isset($persons) && count($persons) > 0) {
            $headline = "Multipla vanliga allergier";
            $header = array('Namn', 'Allergier', 'Övrigt', 'Vald mat');
                    $columnWidth = Array (35, 90, 40);
            $data = Array();
            
            foreach($persons as $person) {
                $registration=$person->getRegistration($current_larp);
                $data[] = Array($person->Name, commaStringFromArrayObject($person->getNormalAllergyTypes()), $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
            }
            $this->FancyTable($headline, $header, $data, $columnWidth);
        }
               */

        /*
        //Hitta alla som inte har någon vald allergi, men som har en kommentar
        $persons = Person::getAllWithoutAllergiesButWithComment($current_larp);
        if (isset($persons) && count($persons) > 0) {
            $headline = "Special";
            $header = array('Namn', 'Övrigt', 'Vald mat');
            $columnWidth = Array (35, 90, 40);
            $data = Array();
            foreach($persons as $person) {
                $registration=$person->getRegistration($current_larp);
                $data[] = Array($person->Name, $person->FoodAllergiesOther, $registration->getTypeOfFood()->Name);
            }
            $this->FancyTable($headline, $header, $data, $columnWidth);
        }
*/
    }

}
$pdf = new KITCHEN_PDF();
$pdf->SetTitle('For koket');
$pdf->SetAuthor('Dod mans hand');
$pdf->SetCreator('Dod mans hand');
$pdf->AddFont('Helvetica','');
$pdf->SetFont('Arial','',12);
$pdf->addPage('L');


$pdf->print_mat();
$pdf->print_allergies();

$pdf->Output();
?>