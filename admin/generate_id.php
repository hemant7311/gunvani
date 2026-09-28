<?php
ob_start();
require_once __DIR__ . '/../includes/auth.php';
require_login();

// 1. Verify GD Extension
if (!extension_loaded('gd')) {
    ob_end_clean();
    http_response_code(500);
    exit('Error: PHP GD extension is required for generating QR codes.');
}

// 2. Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    ob_end_clean();
    http_response_code(400);
    exit('Invalid or missing ID parameter.');
}
$id = (int)$_GET['id'];

// 3. Fetch Member Data
try {
    $stmt = $pdo->prepare("SELECT * FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log('[Gunvani generate_id Database Error] ' . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    exit('Database error occurred while fetching member data.');
}

if (!$member) {
    ob_end_clean();
    http_response_code(404);
    exit('Member not found.');
}

// Ensure safe variables for PHP 8+
$m_member_id   = $member['member_id'] ?? '';
$m_name        = $member['name'] ?? '';
$m_designation = $member['designation'] ?? '';
$m_mobile      = $member['mobile'] ?? '';
$m_dob         = $member['dob'] ?? '';
$m_location    = $member['location'] ?? '';
$m_blood_group = $member['blood_group'] ?? '';
$m_doi         = $member['doi'] ?? '';
$m_doe         = $member['doe'] ?? '';
$m_address     = $member['address'] ?? '';
$m_photo       = $member['photo'] ?? '';

// 4. Dependencies
if (!is_file(__DIR__.'/fpdf.php')) {
    ob_end_clean();
    http_response_code(500);
    exit('Error: FPDF library not found.');
}
if (!is_file(__DIR__.'/phpqrcode/qrlib.php')) {
    ob_end_clean();
    http_response_code(500);
    exit('Error: PHP QR Code library not found.');
}

require_once __DIR__.'/fpdf.php';
require_once __DIR__.'/phpqrcode/qrlib.php';

// 5. Setup Temporary QR Directory
$qrDir = __DIR__ . '/uploads/qr';
if (!is_dir($qrDir)) {
    if (!@mkdir($qrDir, 0755, true)) {
        ob_end_clean();
        http_response_code(500);
        exit('Error: Could not create temporary QR directory.');
    }
}
if (!is_writable($qrDir)) {
    ob_end_clean();
    http_response_code(500);
    exit('Error: Temporary QR directory is not writable.');
}

$qrTemp = $qrDir . '/qr_' . $id . '_' . uniqid() . '.png';

// 6. Generate QR Code
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'] ?? 'gunvani.com';
$qrString = $protocol . $host . "/verification?member_id=" . urlencode($m_member_id);

try {
    QRcode::png($qrString, $qrTemp, QR_ECLEVEL_L, 6, 2);
} catch (Exception $e) {
    error_log('[Gunvani generate_id QR Error] ' . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    exit('Failed to generate QR code.');
}

if (!is_file($qrTemp)) {
    ob_end_clean();
    http_response_code(500);
    exit('Error: QR code file was not created successfully.');
}

// 7. PDF Generation
class PDF extends FPDF {
    function fullBG($file) {
        if (is_file($file) && is_readable($file)) {
            $this->Image($file, 0, 0, $this->GetPageWidth(), $this->GetPageHeight());
        }
    }
}

function fixedText($pdf, $x, $y, $w, $text, $align='L', $size=7, $bold='') {
    $pdf->SetFont('Arial', $bold, $size);
    $pdf->SetXY($x, $y);
    $pdf->Cell($w, 4, (string)$text, 0, 0, $align);
}

try {
    $pdf = new PDF('P', 'mm', array(50.8, 88.9));
    $pdf->SetAutoPageBreak(false);

    /* -------------------------------------------------------
       FRONT SIDE
    --------------------------------------------------------- */
    $pdf->AddPage();
    $frontTemplate = __DIR__ . '/uploads/front.jpg';
    if (is_file($frontTemplate) && is_readable($frontTemplate)) {
        $pdf->fullBG($frontTemplate);
    } else {
        error_log('[Gunvani generate_id Warning] Front template not found: ' . $frontTemplate);
    }

    // PHOTO (safely check to prevent FPDF crashing on empty/directory)
    if (!empty($m_photo)) {
        $photoPath = __DIR__ . "/uploads/" . basename($m_photo);
        if (is_file($photoPath) && is_readable($photoPath)) {
            // New coordinates based on the template's existing red box
            $x = 18.9; $y = 15.3; $w = 12.8; $h = 15.3;
            $pdf->Image($photoPath, $x, $y, $w, $h);
        }
    }

    $leftX = 23;
    $cellWidth = 44;

    // Adjusted Y-coordinates to align perfectly with the red labels on the new PDF template
    fixedText($pdf, $leftX, 34,   $cellWidth, $m_member_id, 'L', 7);
    fixedText($pdf, $leftX, 39,   $cellWidth, $m_name, 'L', 7);
    fixedText($pdf, $leftX, 44,   $cellWidth, $m_designation, 'L', 7);
    fixedText($pdf, $leftX, 48.7, $cellWidth, $m_mobile, 'L', 7);
    fixedText($pdf, $leftX, 53.5, $cellWidth, $m_dob, 'L', 7);
    fixedText($pdf, $leftX, 58.5, $cellWidth, $m_location, 'L', 7);
    fixedText($pdf, $leftX, 63.5, $cellWidth, $m_blood_group, 'L', 7);
    fixedText($pdf, $leftX, 68.5, $cellWidth, $m_doi, 'L', 7);
    fixedText($pdf, $leftX, 73.5, $cellWidth, $m_doe, 'L', 7);

    // QR Image (Fits perfectly into the red QR box on the bottom right)
    if (is_file($qrTemp) && is_readable($qrTemp)) {
        $pdf->Image($qrTemp, 35.8, 78.2, 4.6, 4.6);
    }

    /* -------------------------------------------------------
       BACK SIDE
    --------------------------------------------------------- */
    $pdf->AddPage();
    $backTemplate = __DIR__ . '/uploads/back.jpg';
    if (is_file($backTemplate) && is_readable($backTemplate)) {
        $pdf->fullBG($backTemplate);
    } else {
        error_log('[Gunvani generate_id Warning] Back template not found: ' . $backTemplate);
    }

    $backLeftX = 5.5;
    $backCellWidth = 44;
    $lineHeight = 2.5;

    $pdf->SetXY($backLeftX, 20);
    $pdf->SetFont('Arial', '', 4.5);
    $pdf->MultiCell($backCellWidth, $lineHeight, $m_address, 0, 'L');

    /* -------------------------------------------------------
       OUTPUT
    --------------------------------------------------------- */
    ob_end_clean(); // Clean output buffer before sending PDF headers

    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=\"VisitingCard_" . preg_replace('/[^A-Za-z0-9_-]/', '', $m_member_id) . ".pdf\"");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");

    $pdf->Output('D');

} catch (Exception $e) {
    error_log('[Gunvani generate_id FPDF Error] ' . $e->getMessage());
    ob_end_clean();
    http_response_code(500);
    exit('An error occurred while generating the PDF. Please try again later.');
} finally {
    // Cleanup temporary QR code file
    if (file_exists($qrTemp)) {
        @unlink($qrTemp);
    }
}
exit;
