<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class MagicScroll_PDF extends FPDF {
    
    public $handfonts = ['xiparoslombard','YourRoyalMajesty','UncialAntiqua','TypographerRotunda','medievalsharp','rocksalt','Goticabastard',
        'ComicRunes'];
    
    public $fonts = ['xiparoslombard','YourRoyalMajesty','UncialAntiqua','TypographerRotunda','medievalsharp','rocksalt','Goticabastard',
        'ComicRunes'];
    
    function Header()
    {
        global $root;
        $this->Image($root . '/images/scroll.jpeg',null,null,208);
    }
    
 
    function PrintScroll(Magic_Spell $spell, $larpname) {
        $this->SetMargins(0, 0);
        $this->SetAutoPageBreak(false);
        $this->AddPage();
        
        //$font = $this->handfonts[array_rand($this->handfonts, 1)];
        $font = 'rocksalt';
        
        $this->AddFont($font);
        $size = 20;
        $this->SetFont($font,'',$size);
        $this->SetXY(30, 115);
        $this->MultiCell(150,6,utf8_decode($spell->Name),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är
        
        $size = 14;
        $this->SetFont($font,'',$size);
        $this->SetXY(30, 140);
        $this->MultiCell(150,6,utf8_decode($spell->Description),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        

        $size = 8;
        $this->SetFont($font,'',$size);
        $this->SetXY(30, 250);
        $this->MultiCell(150,6,utf8_decode($larpname),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är
        
    }
     
}

