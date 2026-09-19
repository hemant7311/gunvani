<?php
ob_start();
require_once __DIR__ . '/../includes/auth.php';
require_login();

$frontTemplate = 'uploads/front.jpg';
$backTemplate  = 'uploads/back.jpg';

require_once __DIR__.'/fpdf.php';
require_once __DIR__.'/phpqrcode/qrlib.php';   // ✅ Local QR library

if (!isset($_GET['id'])) exit("ID missing");
$id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM members WHERE id=?");
$stmt->execute([$id]);
$member = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$member) exit("Member not found");

/* -------------------------------------------------------
   LOCAL QR — 100% WORKING
--------------------------------------------------------- */
$qrTemp = __DIR__."/qr_".$member['id'].".png";

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$qrString = $protocol . $host . "/verification?member_id=" . urlencode($member['member_id']);

QRcode::png($qrString, $qrTemp, QR_ECLEVEL_L, 6, 2); // perfect quality QR

/* -------------------------------------------------------
   PDF CLASS
--------------------------------------------------------- */
class PDF extends FPDF {
    function fullBG($file){
        if (file_exists($file)) {
            $this->Image($file, 0, 0, $this->GetPageWidth(), $this->GetPageHeight());
        }
    }
}

$pdf = new PDF('P','mm',array(50.8, 88.9));
$pdf->SetAutoPageBreak(false);

function fixedText($pdf, $x, $y, $w, $text, $align='L', $size=7, $bold='') {
    $pdf->SetFont('Arial', $bold, $size);
    $pdf->SetXY($x, $y);
    $pdf->Cell($w, 4, $text, 0, 0, $align);
}

/* -------------------------------------------------------
   FRONT SIDE
--------------------------------------------------------- */
$pdf->AddPage();
$pdf->fullBG($frontTemplate);

// PHOTO
$photo = __DIR__."/uploads/".$member['photo'];
if (file_exists($photo)) {
    $x = 22; $y = 15; $w = 11; $h = 13;

    $pdf->SetDrawColor(184,23,38);
    $pdf->SetLineWidth(.4);
    $pdf->Rect($x-1, $y-1, $w+2, $h+2);

    $pdf->SetDrawColor(0,0,0);
    $pdf->SetLineWidth(0.4);
    $pdf->Rect($x, $y, $w, $h);

    $pdf->Image($photo, $x, $y, $w, $h);
}

$leftX = 22;
$cellWidth = 44;

fixedText($pdf, $leftX, 31,   $cellWidth, $member['member_id'], 'L', 7);
fixedText($pdf, $leftX, 35.5, $cellWidth, $member['name'], 'L', 7);
fixedText($pdf, $leftX, 40,   $cellWidth, $member['designation'], 'L', 7);
fixedText($pdf, $leftX, 44.8, $cellWidth, $member['mobile'], 'L', 7);
fixedText($pdf, $leftX, 49.2, $cellWidth, $member['dob'], 'L', 7);
fixedText($pdf, $leftX, 53.6, $cellWidth, $member['location'], 'L', 7);
fixedText($pdf, $leftX, 58.2, $cellWidth, $member['blood_group'], 'L', 7);
fixedText($pdf, $leftX, 62.7, $cellWidth, $member['doi'], 'L', 7);
fixedText($pdf, $leftX, 66.8, $cellWidth, $member['doe'], 'L', 7);

// QR Image
if (file_exists($qrTemp)) {
    $pdf->Image($qrTemp, 37.8, 79.3, 5.2, 5.2);  // PERFECT QR
}

/* -------------------------------------------------------
   BACK SIDE
--------------------------------------------------------- */
$pdf->AddPage();
$pdf->fullBG($backTemplate);

$backLeftX = 5;
$backCellWidth = 44;
$lineHeight = 2;

$pdf->SetXY($backLeftX, 19);
$pdf->SetFont('Arial', '', 4.5);
$pdf->MultiCell($backCellWidth, $lineHeight, "Address: ".$member['address'], 0, 'L');

/* -------------------------------------------------------
   OUTPUT
--------------------------------------------------------- */
ob_end_clean();
header("Content-Type: application/pdf");
header("Content-Disposition: attachment; filename=\"VisitingCard_".$member['member_id'].".pdf\"");
header("Cache-Control: no-store, no-cache");
header("Pragma: no-cache");

$pdf->Output('D');

@unlink($qrTemp);
exit;
?>
