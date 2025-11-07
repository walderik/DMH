<?php
use MongoDB\BSON\Persistable;

# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
require_once $root . '/lib/fpdf185/fpdf.php';
require_once $root . '/includes/init.php';


class LABELS_PDF extends FPDF {
   //PDF för att skriva ut klisteretiketter på 7 x 3 
    
    private $x_nr = 0;
    private $y_nr = 0;
    private $rut_width;
    private $rut_height;
    private $margin = 2;
    
    private $font = 'Helvetica';
    private $max_x_nr = 2; //Räknar från 0
    private $max_y_nr = 6; //Räknar från 0
    
    
    function Header()
    {
    }
    
    function Footer()
    {
    }
    
    function PrintSquare(String $text) {
        $size = 44;

        if ($this->x_nr > $this->max_x_nr) {
            $this->x_nr = 0;
            $this->y_nr++;
            if ($this->y_nr > $this->max_y_nr) {
                $this->y_nr = 0;
            }
        }
        
        if (($this->x_nr == 0) && ($this->y_nr == 0)) $this->AddPage();
        
        $squareX = $this->margin+($this->x_nr * $this->rut_width);
        $squareY = $this->y_nr * $this->rut_height;
        
                 
        $size = 20;
                
        # En bit kod för att säkerställa att inget hamnar utanför kanten på rutan
        $this->SetFont($this->font,'',$size);
        $slen = $this->GetStringWidth($text,0);
        while ($slen > ($this->rut_width-7)) {
            $size -= 1;
            $this->SetFont($this->font,'',$size);
            $slen = $this->GetStringWidth($text,0);
        }
        
        $this->SetXY($squareX, ($this->rut_height/2)+2 + $squareY);
        $this->Cell($this->rut_width,10,encode_utf_to_iso(ucfirst($text)),0,1,'C');
        
        $this->x_nr++;
            
    }
    
    function init() {
        $this->SetAutoPageBreak(true , 1.5);
        $this->AddFont($this->font,'');
        
        $height = $this->GetPageHeight();
        $width  = $this->GetPageWidth();
        
        $this->rut_width  = ($width  - 2*$this->margin) / 3;
        $this->rut_height = ($height - 2*$this->margin) / 7;
    }
	
    function allGroups(LARP $larp) {
        $this->init();
	    
	    $groups = Group::getAllRegisteredApproved($larp);
	    foreach ($groups as $group) {
	        $this->PrintSquare($group->Name);
	    }
	}
	
	function allMainRoles(LARP $larp) {
	    $this->init();
	    
	    $roles = Role::getAllMainRoles($larp, false);
	    foreach ($roles as $role) {
	        $this->PrintSquare($role->Name);
	    }
	}
	
	function allPersons(LARP $larp) {
	    $this->init();
	    
	    $persons = Person::getAllRegistered($larp, false);
	    foreach ($persons as $person) {
	        $this->PrintSquare($person->Name);
	    }
	}
	
}
