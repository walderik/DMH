<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/fpdf185/script/mem_image.php';
require_once $root . '/includes/init.php';



class Bookkeeping_PDF extends PDF_MemImage {
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

        
        $txt = "Verifikationer för ".$current_larp->Name;
        $txt_width = $this->GetStringWidth($txt);
        
        $this->SetXY($mitten-$txt_width/2, 3);
        $this->MultiCell($txt_width, 4, encode_utf_to_iso($txt), 0, 'C', true);
        
        $y = static::$y_min + static::$Margin;
    }
    
    function SetText(Bookkeeping $bookkeeping) {
        global $y, $mitten;
        $txt_font='Helvetica';
        $left = 11;
        //$page_height = $this->GetPageHeight();
        $left2 = $left + 30;

        
        
        if ($bookkeeping->hasImage()) {
            $image = Image::loadById($bookkeeping->ImageId);
        }
       
        $this->SetFont($txt_font,'',16);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite

        $y += 3;
        if (isset($image) && ($image->file_mime != "application/pdf")) $this->MemImage($image->file_data, $mitten+5, $y, $mitten-20);
        
        
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso($bookkeeping->Number." "),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($bookkeeping->Headline),0,0); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
        $this->SetFont($txt_font,'',12);

        $y += 7;
        $this->SetXY($left2, $y);
        $typetxt = '';
        if ($bookkeeping->Amount > 0) $typetxt = "Inkomst";
        else $typetxt = "Utgift";
        $this->Cell(80,10,encode_utf_to_iso($typetxt),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
        $y += 3;
        
        
        $y += 7;
        $this->SetXY($left, $y);
        $whotxt = '';
        if ($bookkeeping->Amount > 0) $whotxt = "Från";
        else $whotxt = "Till";
        $this->Cell(80,10,encode_utf_to_iso($whotxt),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($bookkeeping->Who),0,1);
        
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

        if (isset($image) && ($image->file_mime == "application/pdf")) {
            $y += 7;
            $this->SetXY($left, $y);
            $this->Cell(80,10,encode_utf_to_iso(''),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
            $this->SetXY($left2, $y+2.1);
            $this->MultiCell($mitten-$left2, 5.5, "Kvitto finns i separat pdf");
            
        }
        
        if (!empty($bookkeeping->Text)) {
            $y += 7;
            $this->SetXY($left, $y);
            $this->Cell(80,10,encode_utf_to_iso('Beskrivning'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
            $this->SetXY($left2, $y+2.1);
            $this->MultiCell($mitten-$left2, 5.5, encode_utf_to_iso($bookkeeping->Text));
            
        }
    }
    
    
    function SetTextInvoice(Invoice $invoice) {
        global $y, $mitten;
        $txt_font='Helvetica';
        $left = 11;
        //$page_height = $this->GetPageHeight();
        $left2 = $left + 30;
        
        $this->SetFont($txt_font,'',16);    # OK är Times, Arial, Helvetica, SassyFrass, SpecialElite
        
        $y += 3;
        
        
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso("Faktura ".$invoice->Number." "),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($invoice->Recipient),0,0); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
        $this->SetFont($txt_font,'',12);
        
        $y += 7;
        $this->SetXY($left2, $y);
        $typetxt = '';
        $typetxt = "Inkomst";
        $this->Cell(80,10,encode_utf_to_iso($typetxt),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        
        $y += 3;
        
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Summa'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($invoice->FixedAmount),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Datum'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y);
        $this->Cell(80,10,encode_utf_to_iso($invoice->PayedDate),0,1);
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso(''),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y+2.1);
        $this->MultiCell($mitten-$left2, 5.5, "Kvitto finns i separat pdf");
        
        $y += 7;
        $this->SetXY($left, $y);
        $this->Cell(80,10,encode_utf_to_iso('Specifikation'),0,1); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
        $this->SetXY($left2, $y+2.1);
        $this->MultiCell($mitten-$left2, 5.5, encode_utf_to_iso($invoice->Matter));
            
    }
    
    
    
    function printBookkeepings($bookkeepings) {
        global $mitten;
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        
        foreach($bookkeepings as $bookkeeping) {
            $y=0;
            $this->AddPage();
            $this->SetText($bookkeeping);
        }
    }
 
    function printInvoices($invoices) {
        global $mitten;
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        
        foreach($invoices as $invoice) {
            $y=0;
            $this->AddPage();
            $this->SetTextInvoice($invoice);
        }
    }
    
    
}

