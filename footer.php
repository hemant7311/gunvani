<footer class="gn-footer">
        <div class="gn-container">
            <div class="row" style="--bs-gutter-x: 10px; --bs-gutter-y: 10px;">
                <div class="col-lg-4">
                    <a href="/"><img src="/images/gunvani_main_logo.webp" alt="Gunvani News" style="height:54px; max-width:220px; object-fit:contain; background:#ffffff; padding:6px 12px; border-radius:8px; margin-bottom:14px;" onerror="this.onerror=null; this.src='/images/placeholder/logos.png'"></a>
                    <p class="small text-muted pe-lg-4">Gunvani News ek vishwasniya aur nishpaksh samachar manch hai jo desh-duniya ki mahatvapurn khabrein tezi aur satyata ke saath pahunchata hai.</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/">Home</a></li>
                        <li class="mb-2"><a href="/about-us">About Us</a></li>
                        <li class="mb-2"><a href="/categories">Categories</a></li>
                        <li class="mb-2"><a href="/verification">Verification</a></li>
                        <li class="mb-2"><a href="/contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <h5>News Categories</h5>
                    <ul class="list-unstyled">
                        <?php
                        if (!isset($pdo) && file_exists(__DIR__ . '/db.php')) {
                            require_once __DIR__ . '/db.php';
                        }
                        $footerCats = [];
                        if (isset($parentCats) && !empty($parentCats)) {
                            $footerCats = $parentCats;
                        } elseif (isset($pdo)) {
                            try {
                                $footerCats = $pdo->query("SELECT id, name, slug FROM menus WHERE parent_id IS NULL AND status = 'active' ORDER BY display_order ASC, id ASC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
                            } catch (Exception $e) {
                                $footerCats = [];
                            }
                        }
                        ?>
                        <?php if (!empty($footerCats)): ?>
                            <?php foreach ($footerCats as $fm): ?>
                                <li class="mb-2"><a href="/category/<?= htmlspecialchars($fm['slug']) ?>"><?= htmlspecialchars($fm['name']) ?></a></li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Follow Us</h5>
                    <p class="small text-muted mb-3">Janta ki awaaz, Gunvani News ke saath</p>
                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;" aria-label="Twitter"><i class="fa-brands fa-twitter"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="btn btn-sm btn-outline-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width:36px; height:36px;" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>
            </div>

            <div class="border-top border-secondary opacity-25 my-4"></div>

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 small">
                <div>© <?= date('Y') ?> Gunvani News — All Rights Reserved.</div>
                <div class="d-flex gap-3">
                    <a href="#" class="text-muted text-decoration-none">Privacy Policy</a>
                    <a href="#" class="text-muted text-decoration-none">Terms of Use</a>
                    <a href="#" class="text-muted text-decoration-none">Sitemap</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 & Language Switcher Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script type="text/javascript">
    function googleTranslateElementInit() {
        new google.translate.TranslateElement({
            pageLanguage: 'en',
            includedLanguages: 'en,hi',
            autoDisplay: false
        }, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <script>
    function changeLanguage(lang) {
        localStorage.setItem('gunvani_lang', lang);
        updateLangBtnUI(lang);
        setCookieLanguage(lang);
        
        var select = document.querySelector('.goog-te-combo');
        if (select) {
            select.value = lang;
            select.dispatchEvent(new Event('change'));
        }
        
        setTimeout(function() {
            location.reload();
        }, 150);
    }

    function setCookieLanguage(lang) {
        var cookieVal = (lang === 'hi') ? '/en/hi' : '/en/en';
        
        document.cookie = "googtrans=" + cookieVal + "; path=/;";
        if (location.hostname && location.hostname !== 'localhost') {
            var domain = location.hostname.replace(/^www\./, '');
            document.cookie = "googtrans=" + cookieVal + "; domain=." + domain + "; path=/;";
            document.cookie = "googtrans=" + cookieVal + "; domain=" + domain + "; path=/;";
        }
    }

    function updateLangBtnUI(lang) {
        var btnHi = document.getElementById('btn-lang-hi');
        var btnEn = document.getElementById('btn-lang-en');
        if (!btnHi || !btnEn) return;
        if (lang === 'hi') {
            btnHi.classList.add('active');
            btnEn.classList.remove('active');
        } else {
            btnEn.classList.add('active');
            btnHi.classList.remove('active');
        }
    }

    function hideGoogleTranslateFrames() {
        document.documentElement.style.top = '0px';
        document.body.style.top = '0px';
        document.body.style.marginTop = '0px';
        
        var bannerFrames = document.querySelectorAll('iframe.goog-te-banner-frame, .goog-te-banner-frame, #goog-gt-tt, .goog-te-balloon-frame');
        bannerFrames.forEach(function(f) {
            f.style.setProperty('display', 'none', 'important');
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (!localStorage.getItem('gunvani_visited')) {
            localStorage.setItem('gunvani_visited', '1');
            localStorage.setItem('gunvani_lang', 'hi');
        } else if (!localStorage.getItem('gunvani_lang')) {
            localStorage.setItem('gunvani_lang', 'hi');
        }

        var savedLang = localStorage.getItem('gunvani_lang');
        
        updateLangBtnUI(savedLang);
        setCookieLanguage(savedLang);
        
        setTimeout(function() {
            var select = document.querySelector('.goog-te-combo');
            if (select && select.value !== savedLang) {
                select.value = savedLang;
                select.dispatchEvent(new Event('change'));
            }
            hideGoogleTranslateFrames();
        }, 400);
        
        setInterval(hideGoogleTranslateFrames, 300);
    });
    </script>
