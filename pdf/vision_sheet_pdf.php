<?php
# Läs mer på http://www.fpdf.org/

# För avgöra om intriger skall skrivas ut:
# $larp->isIntriguesReleased()

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/fpdf185/script/mem_image.php';
require_once $root . '/includes/all_includes.php';


class VisionSheet_PDF extends PDF_MemImage {
    
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 12;
    public static $text_max_length = 50;
    
    public $supplier;
    public $role;
    public $person;
    public $larp;
    public $current_left;
    public $cell_y_space;        # Standardhöjden på en cell 
    public $current_cell_height; # Nuvarande höjden på den här radens celler
    public $cell_width;
    
    
    public $handfonts = ['cherish','dancingscript','daniel','dawningofanewday','ekologiehand','homemadeapple','mynerve',
        'reeniebeanie','simplyglamorous','splash','sueellenfrancisco','zeyada'];
    
    public $calligraphyfonts = ['BelweGotisch', 'AliceInWonderland', 'DSCaslonGotisch', 'eaglelake', 'UncialAntiqua'];
    
    
    function Header() {
        global $root, $y, $mitten, $rowImageHeight, $lovest_y;
        $this->SetLineWidth(0.6);
        $this->Line(static::$x_min, static::$y_min, static::$x_max, static::$y_min);
        $this->Line(static::$x_min, static::$y_min, static::$x_min, static::$y_max);
        $this->Line(static::$x_min, static::$y_max, static::$x_max, static::$y_max);
        $this->Line(static::$x_max, static::$y_min, static::$x_max, static::$y_max);
        
        $space = 1.2;
        $this->Line(static::$x_min-$space, static::$y_min-$space, static::$x_max+$space, static::$y_min-$space);
        $this->Line(static::$x_min-$space, static::$y_min-$space, static::$x_min-$space, static::$y_max+$space);
        $this->Line(static::$x_min-$space, static::$y_max+$space, static::$x_max+$space, static::$y_max+$space);
        $this->Line(static::$x_max+$space, static::$y_min-$space, static::$x_max+$space, static::$y_max+$space);
        
        
        $this->SetXY($mitten-15, 3);
        $this->SetFont('Helvetica','',static::$text_fontsize/1.1);
        $this->SetFillColor(255,255,255);

        
        $txt = 'Syner';
       
        

        $this->MultiCell(30, 4, utf8_decode($txt), 0, 'C', true);
        
        $this->SetXY($mitten-15,  static::$y_min + 2*static::$Margin);
        $y = $this->GetY();
        $rowImageHeight = 0;
        $lovest_y = $y;
    }
    
    # Skriv ut lajvnamnet högst upp.
    function title($left, ?String $text = null) {
        global $y;

        if (empty($text)) $text =  $this->larp->Name;
        
        $font_size = (850 / strlen(utf8_decode($text)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($left, $y-2);
        $this->Cell(0, static::$cell_y*5, utf8_decode($text),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*5) + (static::$Margin);
        
        //$this->bar();
    }
    
    function single_vision_reciever_sheet(Role $role_in, LARP $larp_in) {
        global $x, $y, $left, $left2, $mitten;
        $space = 3;
        
        $this->role = $role_in;
        $this->person = $this->role->getPerson();
        
        $this->larp = $larp_in;
        $this->cell_y_space = static::$cell_y + (2*static::$Margin);
        $this->current_cell_height = $this->cell_y_space;
        
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $this->cell_width = (static::$x_max - static::$x_min) / 2 - (2*static::$Margin);     
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        $left2 = $mitten + static::$Margin;
        
        $this->current_left = $left;
        
        $this->AddPage();
        
        $this->title($left, $this->role->Name);
         
        $y += 5;
        
        $this->bar();
        
        
        
        //Lista vilka tider de ska ha syner
        $visions = Vision::allVisionsByRole($larp_in, $role_in);
        $this->current_left = $left;
        $y = $this->GetY() + $space*10;
        $this->SetXY($this->current_left, $y);
        $this->SetFont('Helvetica','B',static::$text_fontsize);
        $this->Cell($this->cell_width, static::$cell_y, utf8_decode("Tider du ska ha syner"),0,0,'L');
        
        $list = "";
        foreach ($visions as $vision) {
                $list .= $vision->getWhenStr() . "\n\n";
        }
        $y = $this->GetY() + $space*2;
        $this->SetXY($this->current_left, $y);
        $this->SetFont('Helvetica','',static::$text_fontsize);
        $text = trim(utf8_decode($list));
        $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
            
        $y = $this->GetY() + $space*5;
        $this->SetXY($this->current_left, $y);
        $this->SetFont('Helvetica','B',static::$text_fontsize);
        $this->Cell($this->cell_width, static::$cell_y, utf8_decode("Gå till sekretariatet och hämta ut synen vid den anvisade tiden."),0,0,'L');
        
        
 	}
	
	function all_vision_sheets(LARP $larp_in) {
	    $this->larp = $larp_in;

	    $roles_with_visions = Vision::allRolesWithVisions($larp_in);
	    foreach($roles_with_visions as $role) {
	        $this->single_vision_reciever_sheet($role, $larp_in);
	    }
	}

	
	

	
	# Dra en linje tvärs över arket på höjd $y
	private function bar() {
	    global $y;
	    $this->Line(static::$x_min, $y, static::$x_max, $y);
	}
}
