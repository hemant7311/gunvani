<?php
if (!isset($_COOKIE['googtrans'])) {
    setcookie('googtrans', '/en/hi', time() + (86400 * 30), '/');
    $_COOKIE['googtrans'] = '/en/hi';
}

require_once __DIR__ . '/db.php';
$defaultNavCategories = ['Agra', 'Lucknow', 'Mathura', 'Noida', 'Uttar Pradesh', 'India'];


function escape($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

$messageSent = false;
$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $errorMsg = 'Please fill in all required fields (Name, Email, Message).';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            $messageSent = true;
        } catch (Exception $e) {
            $errorMsg = 'Failed to submit contact message. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Gunvani Official Favicon & Icons -->
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/icon.png">
    <link rel="shortcut icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <base href="/">
    <meta charset="utf-8">
    <title>Contact Us | Gunvani News</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="manifest" href="site.webmanifest">
    <link rel="apple-touch-icon" href="icon.png">
    <meta name="theme-color" content="#116530">

    <!-- Google Fonts & FontAwesome 6 -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --gn-green: #116530;
            --gn-green-dark: #0b4520;
            --gn-green-light: #eaf4ed;
            --gn-red: #dc2626;
            --gn-bg: #f8faf9;
            --gn-card-bg: #ffffff;
            --gn-border: #e2e8f0;
            --gn-text-dark: #1e293b;
            --gn-text-muted: #64748b;
            --container-max-w: 1360px;
        }

        html, body {
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: clip !important;
            position: relative;
            margin: 0;
            padding: 0;
        }

        *, *::before, *::after {
            box-sizing: border-box;
            max-width: 100%;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gn-bg);
            color: var(--gn-text-dark);
        }

        img, video, iframe {
            max-width: 100% !important;
            height: auto;
        }

        .gn-container {
            width: 100%;
            max-width: var(--container-max-w);
            margin: 0 auto;
            padding: 0 16px;
        }
        @media (max-width: 575px) {
            .gn-container {
                padding: 0 12px;
            }
        }

        

        .lang-pill-box {
            background: rgba(0,0,0,0.25);
            border-radius: 4px;
            padding: 2px;
            display: inline-flex;
            align-items: center;
            gap: 2px;
            margin-top: -2px;
            margin-bottom: -2px;
        }
        .lang-pill-btn {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 3px;
            border: none;
            color: #ffffff;
            background: transparent;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .lang-pill-btn.active {
            background-color: var(--gn-green-dark);
            color: #ffffff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        /* Navigation Header */
        .gn-main-header {
            background: #ffffff;
            border-bottom: 1px solid var(--gn-border);
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .gn-logo {
            height: 80px;
            max-height: 80px;
            width: auto;
            object-fit: contain;
            transition: height 0.3s ease;
        }
        @media (max-width: 991px) {
            .gn-logo {
                height: 60px;
                max-height: 60px;
            }
        }
        @media (max-width: 575px) {
            .gn-logo {
                height: 46px;
                max-height: 46px;
            }
        }

        /* Hide Google Translate Top Banner Frame & prevent body push down */
        iframe.goog-te-banner-frame,
        .goog-te-banner-frame,
        #goog-gt-tt,
        .goog-gt-tt,
        .goog-te-balloon-frame {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
        }

        .goog-te-combo {
            display: none !important;
        }
        .gn-nav-link {
            font-size: 0.88rem;
            font-weight: 600;
            color: #334155;
            padding: 8px 12px;
            text-decoration: none;
            transition: color 0.2s ease;
            white-space: nowrap;
        }
        @media (max-width: 768px) {
            .gn-nav-link {
                padding: 5px 7px;
                font-size: 0.82rem;
            }
        }
        .gn-nav-link:hover, .gn-nav-link.active {
            color: var(--gn-green);
            font-weight: 700;
        }
        .gn-nav-link.active {
            border-bottom: 2px solid var(--gn-green);
        }

        /* Page Banner */
        .contact-hero {
            background: linear-gradient(135deg, var(--gn-green-dark) 0%, var(--gn-green) 100%);
            color: #ffffff;
            padding: 40px 0;
            margin-bottom: 30px;
        }
        @media (max-width: 575px) {
            .contact-hero {
                padding: 24px 0;
                margin-bottom: 20px;
            }
            .contact-hero h1 {
                font-size: 1.5rem !important;
            }
        }

        /* Info Location Card */
        .info-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--gn-border);
            padding: 24px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        @media (max-width: 575px) {
            .info-card {
                padding: 16px;
            }
        }
        .info-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 0;
            border-bottom: 1px dashed var(--gn-border);
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-icon-box {
            width: 44px;
            height: 44px;
            background: var(--gn-green-light);
            color: var(--gn-green);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
        }
        .info-content h6 {
            margin: 0 0 2px 0;
            font-size: 0.78rem;
            color: var(--gn-text-muted);
            text-transform: uppercase;
            font-weight: 700;
        }
        .info-content p, .info-content a {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--gn-text-dark);
            text-decoration: none;
        }
        .info-content a:hover {
            color: var(--gn-green);
        }

        /* Social Icon Buttons */
        .social-circle-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--gn-border);
            background: #ffffff;
            color: var(--gn-text-dark);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .social-circle-btn:hover {
            background: var(--gn-green);
            color: #ffffff;
            border-color: var(--gn-green);
            transform: translateY(-2px);
        }

        /* Contact Form */
        .contact-form-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid var(--gn-border);
            padding: 32px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }
        @media (max-width: 575px) {
            .contact-form-card {
                padding: 18px;
            }
        }
        .form-label {
            font-size: 0.86rem;
            font-weight: 700;
            color: var(--gn-text-dark);
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid var(--gn-border);
            padding: 10px 14px;
            font-size: 0.9rem;
        }
        .form-control:focus {
            border-color: var(--gn-green);
            box-shadow: 0 0 0 3px rgba(17,101,48,0.15);
        }

        /* Footer */
        .gn-footer {
            background-color: #0b1e13;
            color: #e2e8f0 !important;
            padding: 40px 0 20px 0;
            margin-top: 50px;
            border-top: 3px solid var(--gn-green);
        }
        .gn-footer h5 {
            color: #ffffff !important;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 14px;
        }
        .gn-footer p, .gn-footer span, .gn-footer div, .gn-footer li, .gn-footer .text-muted {
            color: #cbd5e1 !important;
        }
        .gn-footer a {
            color: #e2e8f0 !important;
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s ease;
        }
        .gn-footer a:hover {
            color: #4ade80 !important;
        }

        /* Google Translate frame hide */
        .goog-te-banner-frame.skiptranslate, .goog-te-banner-frame, .goog-gt-tt, .goog-te-balloon-frame, #goog-gt-tt { display: none !important; }
        body { top: 0px !important; }
        .goog-te-combo { display: none !important; }
        .skiptranslate { font-size: 0 !important; }
        .skiptranslate * { font-size: initial; }
            /* --- Responsive Nav Sizes --- */
        @media (min-width: 768px) {
            .gn-desktop-nav-container {
                justify-content: center !important;
                gap: 30px !important;
            }
            .gn-desktop-nav-container .gn-nav-link {
                font-size: 16px !important;
            }
        }
        @media (max-width: 767px) {
            .gn-nav-link { font-size: 14px !important; }
        }
            /* Mobile Form CSS for Contact Page */
        @media (max-width: 767px) {
            .form-control, .form-label, .btn, .btn i, .info-box h4, .info-box p, .info-box i, .form-label span {
                font-size: 12px !important;
            }
            .form-control {
                padding: 6px 10px !important;
                height: auto !important;
            }
            .info-box .icon-wrap {
                width: 32px !important;
                height: 32px !important;
            }
            .info-box .icon-wrap i {
                font-size: 14px !important; /* Keep icon slightly larger than 10px so it's visible, or strictly 10px if required */
            }
        }
            /* Mobile Headings & Category Sidebar */
        @media (max-width: 767px) {
            h1, h2, h3, .sec-title-bar h2, .sec-title-bar h3 {
                font-size: 19px !important;
                margin-bottom: 6px !important;
            }
            h4, h5, h6, .subheading, p.lead {
                font-size: 12px !important;
            }
            .list-group-item, .list-group-item a {
                font-size: 12px !important;
                padding: 6px 12px !important;
            }
            .list-group-item i {
                font-size: 12px !important;
            }
        }
            /* Mobile Footer Typography */
        @media (max-width: 767px) {
            .gn-footer h5 {
                font-size: 14px !important;
                margin-bottom: 8px !important;
            }
            .gn-footer p, .gn-footer a, .gn-footer li, .gn-footer div, .gn-footer span {
                font-size: 12px !important;
                line-height: 1.4 !important;
            }
            .gn-footer {
                padding: 20px 0 10px 0 !important;
            }
            .gn-footer img {
                height: 36px !important;
            }
        }
            /* Mobile Circular Buttons Fix */
        @media (max-width: 767px) {
            .btn.rounded-circle {
                width: 32px !important;
                height: 32px !important;
                padding: 0 !important;
                display: inline-flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
        }
            /* Mobile Contact Info Card Fix */
        @media (max-width: 767px) {
            .info-card {
                padding: 12px !important;
            }
            .info-item {
                gap: 10px !important;
                padding: 10px 0 !important;
            }
            .info-icon-box {
                width: 32px !important;
                height: 32px !important;
                font-size: 12px !important;
            }
            .info-content h6 {
                font-size: 10px !important;
                margin-bottom: 2px !important;
                color: #6c757d !important;
            }
            .info-content p, .info-content a {
                font-size: 12px !important;
                margin: 0 !important;
                font-weight: 600 !important;
            }
            .info-card h3 {
                font-size: 16px !important;
                margin-bottom: 12px !important;
            }
        }
            @media (max-width: 767px) {
            html, body {
                overflow-x: hidden !important;
                width: 100% !important;
                max-width: 100vw !important;
            }
            /* Fix for Bootstrap rows causing horizontal scroll on mobile */
            .row {
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .container, .container-fluid, .gn-container {
                padding-left: 12px !important;
                padding-right: 12px !important;
                overflow-x: hidden !important;
            }
            .news-card, .trend-card-item, .city-box-card {
                max-width: 100% !important;
            }
        }
    </style>
    <script>
        var savedLang = localStorage.getItem('gunvani_lang') || 'hi';
        var cookieVal = (savedLang === 'hi') ? '/en/hi' : '/en/en';
        
        document.cookie = "googtrans=" + cookieVal + "; path=/;";
        if (location.hostname && location.hostname !== 'localhost') {
            document.cookie = "googtrans=" + cookieVal + "; domain=." + location.hostname.replace(/^www\./, '') + "; path=/;";
            document.cookie = "googtrans=" + cookieVal + "; domain=" + location.hostname.replace(/^www\./, '') + "; path=/;";
        }
    </script>
</head>
<body>

    
    </div>

    <!-- 2. Navigation Header -->
                <!-- Dynamic Header Menu -->
    <?php include 'nav.php'; ?>

    <!-- 3. Contact Page Hero Banner -->
    <section class="contact-hero">
        <div class="gn-container">
            <h1 class="display-6 fw-bold mb-2">Get in Touch</h1>
            <p class="m-0 opacity-85">Have news tips, feedback, or inquiries? We would love to hear from you.</p>
        </div>
    </section>

    <!-- 4. Contact Main Content -->
    <main class="gn-container mb-5">
        <div class="row g-4">
            <!-- Left: Contact Form -->
            <div class="col-lg-7">
                <div class="contact-form-card">
                    <h3 class="fw-bold mb-3 text-dark">Send Us a Message</h3>
                    
                    <?php if ($messageSent): ?>
                        <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                            <i class="fa-solid fa-circle-check fs-5"></i>
                            <div>
                                <strong>Thank you!</strong> Your message has been sent successfully. Our team will get back to you shortly.
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($errorMsg): ?>
                        <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" role="alert">
                            <i class="fa-solid fa-triangle-exclamation fs-5"></i>
                            <div><?= escape($errorMsg) ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="contact">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="e.g. Rahul Sharma" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="e.g. rahul@gmail.com" required>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone / Mobile</label>
                                <input type="text" name="phone" class="form-control" placeholder="+91 7088824006">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Subject</label>
                                <input type="text" name="subject" class="form-control" placeholder="News Tip / Editorial Enquiry">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Your Message <span class="text-danger">*</span></label>
                            <textarea name="message" rows="6" class="form-control" placeholder="Type your message here..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success px-4 py-2 fw-bold" style="background-color:var(--gn-green); border-color:var(--gn-green);">
                            <i class="fa-solid fa-paper-plane me-2"></i>Send Message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Info Location Sidebar -->
            <div class="col-lg-5">
                <div class="info-card">
                    <h3 class="fw-bold mb-4 text-dark">Info Location</h3>
                    
                    <div class="info-item">
                        <div class="info-icon-box">
                            <i class="fa-solid fa-house"></i>
                        </div>
                        <div class="info-content">
                            <h6>Postal Address</h6>
                            <p>PO Box 16122 Gwalior</p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon-box">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <h6>Phone Number</h6>
                            <p><a href="tel:+917088824006">+91 7088824006</a></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon-box">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h6>Email Address</h6>
                            <p><a href="mailto:editorgunvani@gmail.com">editorgunvani@gmail.com</a></p>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="info-icon-box">
                            <i class="fa-solid fa-globe"></i>
                        </div>
                        <div class="info-content">
                            <h6>Official Website</h6>
                            <p><a href="https://www.gunvani.com" target="_blank">https://www.gunvani.com</a></p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold text-dark mb-3">Find Us On Social Media</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="social-circle-btn" title="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                            <a href="#" class="social-circle-btn" title="Twitter"><i class="fa-brands fa-twitter"></i></a>
                            <a href="#" class="social-circle-btn" title="Instagram"><i class="fa-brands fa-instagram"></i></a>
                            <a href="#" class="social-circle-btn" title="YouTube"><i class="fa-brands fa-youtube"></i></a>
                            <a href="#" class="social-circle-btn" title="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

        <?php include __DIR__ . '/footer.php'; ?>
</body>
</html>
