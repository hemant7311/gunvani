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
$m_rni_no      = $member['rni_no'] ?? '';

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
    $text = (string)$text;
    $pdf->SetFont('Arial', $bold, $size);
    // DO NOT auto-shrink font size. Truncate text if it exceeds width.
    while ($pdf->GetStringWidth($text) > $w && mb_strlen($text) > 0) {
        $text = mb_substr($text, 0, -1);
    }
    $pdf->SetXY($x, $y);
    $pdf->Cell($w, 4, $text, 0, 0, $align);
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
            // Target box coordinates: fills red frame cleanly with no top/bottom gaps
            $boxX = 18.55; $boxY = 13.75; $boxW = 13.7; $boxH = 15.5;
            
            // Generate a temporary cropped PNG image with 5px rounded corners using GD
            $croppedTemp = __DIR__ . '/uploads/temp_crop_' . $id . '_' . uniqid() . '.png';
            
            $imgInfo = @getimagesize($photoPath);
            if ($imgInfo && extension_loaded('gd')) {
                $origW = $imgInfo[0];
                $origH = $imgInfo[1];
                $mime = $imgInfo['mime'];
                
                $srcImg = null;
                if ($mime == 'image/jpeg') $srcImg = @imagecreatefromjpeg($photoPath);
                elseif ($mime == 'image/png') $srcImg = @imagecreatefrompng($photoPath);
                elseif ($mime == 'image/webp') $srcImg = @imagecreatefromwebp($photoPath);
                
                if ($srcImg) {
                    $targetRatio = $boxW / $boxH;
                    $origRatio = $origW / $origH;
                    
                    $cropW = $origW;
                    $cropH = $origH;
                    $cropX = 0;
                    $cropY = 0;
                    
                    if ($origRatio > $targetRatio) {
                        // Image is too wide, crop horizontally
                        $cropW = (int)($origH * $targetRatio);
                        $cropX = (int)(($origW - $cropW) / 2);
                    } elseif ($origRatio < $targetRatio) {
                        // Image is too tall, crop vertically (preserve head/face by biasing towards the upper part)
                        $cropH = (int)($origW / $targetRatio);
                        $topBias = ($origRatio < 0.85) ? 0.15 : 0.35;
                        $cropY = (int)(($origH - $cropH) * $topBias);
                    }
                    
                    // Create destination image matching exactly the target box ratio (high res)
                    $destW = 400;
                    $destH = (int)($destW / $targetRatio);
                    
                    $destImg = imagecreatetruecolor($destW, $destH);
                    imagecopyresampled($destImg, $srcImg, 0, 0, $cropX, $cropY, $destW, $destH, $cropW, $cropH);
                    
                    // Apply 5px rounded corners with transparent background
                    $roundedImg = imagecreatetruecolor($destW, $destH);
                    imagealphablending($roundedImg, false);
                    imagesavealpha($roundedImg, true);
                    $transparent = imagecolorallocatealpha($roundedImg, 0, 0, 0, 127);
                    imagefill($roundedImg, 0, 0, $transparent);
                    imagecopy($roundedImg, $destImg, 0, 0, 0, 0, $destW, $destH);
                    
                    // 5px corner radius scaled to high-res image
                    $radius = 14;
                    $corners = [
                        ['cx' => $radius, 'cy' => $radius, 'x1' => 0, 'x2' => $radius, 'y1' => 0, 'y2' => $radius],
                        ['cx' => $destW - $radius - 1, 'cy' => $radius, 'x1' => $destW - $radius, 'x2' => $destW, 'y1' => 0, 'y2' => $radius],
                        ['cx' => $radius, 'cy' => $destH - $radius - 1, 'x1' => 0, 'x2' => $radius, 'y1' => $destH - $radius, 'y2' => $destH],
                        ['cx' => $destW - $radius - 1, 'cy' => $destH - $radius - 1, 'x1' => $destW - $radius, 'x2' => $destW, 'y1' => $destH - $radius, 'y2' => $destH]
                    ];
                    foreach ($corners as $c) {
                        $cx = $c['cx'];
                        $cy = $c['cy'];
                        for ($x = $c['x1']; $x < $c['x2']; $x++) {
                            for ($y = $c['y1']; $y < $c['y2']; $y++) {
                                $dx = $x - $cx;
                                $dy = $y - $cy;
                                $dist = sqrt($dx * $dx + $dy * $dy);
                                if ($dist > $radius) {
                                    imagesetpixel($roundedImg, $x, $y, $transparent);
                                } elseif ($dist > $radius - 1.0) {
                                    $factor = $dist - ($radius - 1.0);
                                    $rgb = imagecolorat($roundedImg, $x, $y);
                                    $r = ($rgb >> 16) & 0xFF;
                                    $g = ($rgb >> 8) & 0xFF;
                                    $b = $rgb & 0xFF;
                                    $origAlpha = ($rgb >> 24) & 0x7F;
                                    $newAlpha = (int)($origAlpha + (127 - $origAlpha) * $factor);
                                    imagesetpixel($roundedImg, $x, $y, imagecolorallocatealpha($roundedImg, $r, $g, $b, min(127, $newAlpha)));
                                }
                            }
                        }
                    }
                    
                    imagepng($roundedImg, $croppedTemp, 6);
                    
                    imagedestroy($srcImg);
                    imagedestroy($destImg);
                    imagedestroy($roundedImg);
                    
                    // Place the perfectly cropped rounded image
                    $pdf->Image($croppedTemp, $boxX, $boxY, $boxW, $boxH);
                    
                    // Delete temp file immediately
                    if (is_file($croppedTemp)) {
                        @unlink($croppedTemp);
                    }
                } else {
                    $pdf->Image($photoPath, $boxX, $boxY, $boxW, $boxH);
                }
            } else {
                // Fallback
                $pdf->Image($photoPath, $boxX, $boxY, $boxW, $boxH);
            }
        }
    }

    $leftX = 19.5; // Moved left to give more width for values, safe from labels
    $cellWidth = 29.5; // Increased available space before the right edge

    // Adjusted Y-coordinates to align perfectly with the red labels on the new PDF template
    // Measured exactly from the template image pixels:
    // Increased font size from 7 to 7.8 (approx +1px)
    fixedText($pdf, $leftX, 30.15, $cellWidth, $m_member_id, 'L', 7.8);
    fixedText($pdf, $leftX, 35.5, $cellWidth, $m_name, 'L', 7.8);
    fixedText($pdf, $leftX, 40.1, $cellWidth, $m_designation, 'L', 7.8);
    fixedText($pdf, $leftX, 44.3, $cellWidth, $m_mobile, 'L', 7.8);
    fixedText($pdf, $leftX, 48.5, $cellWidth, $m_dob, 'L', 7.8);

    fixedText($pdf, $leftX, 52.9, $cellWidth, $m_location, 'L', 7.8);
    fixedText($pdf, $leftX, 57.2, $cellWidth, $m_blood_group, 'L', 7.8);
    fixedText($pdf, $leftX, 61.6, $cellWidth, $m_doi, 'L', 7.8);
    fixedText($pdf, $leftX, 66.0, $cellWidth, $m_doe, 'L', 7.8);

    // RNI Number (Rendered near top right, perfectly aligned on one line)
    if (!empty($m_rni_no)) {
        // Erase baked-in "RNI NO." label (X=35 to X=42, Y=0.5 to Y=3)
        // Card top-right background color is approx R:244 G:242 B:243
        $pdf->SetFillColor(244, 242, 243);
        $pdf->Rect(34.0, 0.5, 16.0, 3.5, 'F');
        
        // Print combined label + value, right-aligned to edge of card
        $pdf->SetTextColor(0, 0, 0); // Black text
        // X = 20, Y = 1.0, W = 29.5 (leaves 1.3mm padding on right edge)
        fixedText($pdf, 20.0, 1.0, 29.5, 'RNI NO.: ' . $m_rni_no, 'R', 5, 'B');
    }

    // QR Image (Fits perfectly into the red QR box on the bottom right)
    if (is_file($qrTemp) && is_readable($qrTemp)) {
        // Red box is ~7x6mm. Scaling QR to 5.5x5.5mm to be scannable while keeping quiet zone.
        $pdf->Image($qrTemp, 35.7, 77.8, 5.5, 5.5);
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
    $backCellWidth = 39.8;

    // Print Address on back card on the same line, with aligned multi-line wrap
    if (!empty(trim($m_address))) {
        $backValX = 14.8;
        $backValW = 30.5;
        $backY = 14.0;
        $lineHeight = 2.2;
        $fontSize = 4.5;

        // If address is very long, slightly reduce font size to prevent overlapping Hindi instructions
        if ($pdf->GetStringWidth($m_address) > ($backValW * 2.5)) {
            $fontSize = 4.0;
            $lineHeight = 2.0;
        }

        // 1. Red label "Address:" on line 1
        $pdf->SetXY($backLeftX, $backY);
        $pdf->SetTextColor(167, 31, 41);
        $pdf->SetFont('Arial', 'B', 5.0);
        $pdf->Cell($backValX - $backLeftX, $lineHeight, 'Address:', 0, 0, 'L');

        // 2. Address value starting on the same line, wrapping aligned to $backValX
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', '', $fontSize);
        $pdf->SetLeftMargin($backValX);
        $pdf->SetXY($backValX, $backY);
        $pdf->MultiCell($backValW, $lineHeight, $m_address, 0, 'L');
        $pdf->SetLeftMargin(0);
    }

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

