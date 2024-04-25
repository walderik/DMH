<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/fpdf185/script/mem_image.php';
require_once $root . '/includes/init.php';



class Props_PDF extends PDF_MemImage {
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 12;
    public static $text_max_length = 50;
    
    public $y = 0;
    
    
    function Header()
    {
        global $root, $y, $mitten, $current_larp;
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
        
        
        $this->SetFont('Helvetica','',static::$text_fontsize/1.1);
        $this->SetFillColor(255,255,255);

        
        $txt = "Rekvisita för ".$current_larp->Name;
        $txt_width = $this->GetStringWidth($txt);
        
        $this->SetXY($mitten-$txt_width/2, 3);
        $this->MultiCell($txt_width, 4, encode_utf_to_iso($txt), 0, 'C', true);
        
        $y = static::$y_min + static::$Margin;
    }
    
    function SetText(Prop $prop) {
        global $y, $mitten;
        $txt_font='Helvetica';
        $left = 11;
        $page_height = $this->GetPageHeight();
        $left2 = $left + 30;
        
       
        $this->SetFont($txt_font,'',16);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite

        $y += 7;
        $image_y_finish = 0;
        if ($prop->hasImage()) {
            $image = Image::loadById($prop->ImageId);
            $image_width = $mitten-20;
            
            $v = 'img'.md5($image->file_data);
            $GLOBALS[$v] = $image->file_data;
            
            list($width, $new_y) =  getimagesize('var://'.$v);
            
            $realHeight = round(($new_y / $width) * $image_width);
            
            $image_y_finish = $y + $realHeight;
            
            $this->MemImage($image->file_data, $mitten, $y, $image_width);
        }
        
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso("Namn"),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        //$this->Cell(80,10,encode_utf_to_iso($prop->Name),0,0); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->Cell(80,10,encode_utf_to_iso($prop->Name),0,0); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
        $this->SetFont($txt_font,'',12);


        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso("Beskrivning"),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($prop->Description),0,0); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
        
        $owner = "";
        if (isset($prop->GroupId)) {
            $group = Group::loadById($prop->GroupId);
            $owner = $group->Name;
        }
        elseif (isset($prop->RoleId)) {
            $role = Role::loadById($prop->RoleId);
            $owner = $role->Name;
        }
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Ägare'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($owner),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $y += 7;
        
        
        if ($y < $image_y_finish) $y = $image_y_finish;
        /*
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Konto'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($bookkeeping->getBookkeepingAccount()->Name),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Summa'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($bookkeeping->Amount),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Datum'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($bookkeeping->Date),0,1);        

        if (!empty($bookkeeping->Text)) {
            $y += 7;
            $this->SetXY($left, $y);
            $this->Cell(80,10,encode_utf_to_iso('Beskrivning'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
            $this->SetXY($left2, $y+2.1);
            $this->MultiCell($mitten-$left2, 5.5, encode_utf_to_iso($bookkeeping->Text));
            
        }
        */
        
        if($y+100 > $page_height) {
            $y=0;
            $this->AddPage();
        }
        
    }
    
    function printProps($props) {
        global $mitten;
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        
        $this->AddPage();
        
        foreach($props as $prop) {
            $this->SetText($prop);
        }
    }
    
}

