<?php
# Läs mer på http://www.fpdf.org/

require('includes/fpdf185/fpdf.php');
# $this->MultiCell(0,5,$txt);

$pdf = new FPDF();
$pdf->SetTitle('Telegram');
$pdf->SetAuthor('Död mans hand');
$pdf->AddPage();
$pdf->SetFont('Times','B',16); # OK är 'Times', 'Arial'
$pdf->Cell(40,10,'Hello World!');
$pdf->Cell(60,10,'Powered by FPDF.',0,1,'C');
$pdf->Output();
?>