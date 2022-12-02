<?php
# Läs mer på http://www.fpdf.org/

require('includes/fpdf185/fpdf.php');
# $this->MultiCell(0,5,$txt);

class TELEGRAM extends FPDF {
    
    function Header()
    {
//         global $title;
        
//         // Arial bold 15
//         $this->SetFont('Arial','B',15);
//         // Calculate width of title and position
//         $w = $this->GetStringWidth($title)+6;
//         $this->SetX((210-$w)/2);
//         // Colors of frame, background and text
//         $this->SetDrawColor(0,80,180);
//         $this->SetFillColor(230,230,0);
//         $this->SetTextColor(220,50,50);
//         // Thickness of frame (1 mm)
//         $this->SetLineWidth(1);
//         // Title
//         $this->Cell($w,9,$title,1,1,'C',true);
//         // Line break
//         $this->Ln(10);
        $this->Image('telegram.png');
    }
    
    function SetText($text) {
        $this->SetFont('Times','B',16);    # OK är 'Times', 'Arial'
        $this->Cell(40,10,'Hello World!'); # http://www.fpdf.org/en/doc/cell.htm
        $str = "ÅÄÖ åäö. &aring;";         # https://stackoverflow.com/questions/3514076/special-characters-in-fpdf-with-php
        $str = utf8_decode($str); 
        $this->Cell(60,10,$str,0,1,'C'); # 0 - No border, 1 -  to the beginning of the next line, C - Centrerad
    }
    
}



$pdf = new TELEGRAM();
$pdf->SetTitle('Telegram');
$pdf->SetAuthor('Dod mans hand');
$pdf->AddPage();
$pdf->SetText('Åtal');    # OK är 'Times', 'Arial'
$pdf->Output();
?>