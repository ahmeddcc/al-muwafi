<?php
ob_start();
$defaultMap = "https://maps.google.com/maps?q=31.105725,30.943881&hl=ar&z=15&output=embed&iwloc=near";
?>

<style>
/* Gold Premium Layout */
.gold-wrapper {
    background: radial-gradient(circle at top, #1e293b 0%, #000000 100%);
    min-height: 100vh;
    padding-top: 80px;
    position: relative;
    overflow: hidden;
}

/* Background Accents */
.gold-orb {
    position: absolute;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(207, 170, 93, 0.1) 0%, rgba(0,0,0,0) 70%);
    top: -200px;
    left: 50%;
    transform: translateX(-50%);
    pointer-events: none;
}

.gold-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 10;
}

.gold-title {
    text-align: center;
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 20px;
    background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.gold-card {
    background: rgba(20, 20, 20, 0.8);
    backdrop-filter: blur(20px);
    border: 1px solid #b38728;
    border-radius: 4px; /* Sharp corners for premium feel */
    padding: 60px;
    box-shadow: 0 0 50px rgba(179, 135, 40, 0.1);
}

.gold-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #b38728, transparent);
    margin: 40px 0;
}

.gold-input {
    background: transparent;
    border: none;
    border-bottom: 1px solid #444;
    width: 100%;
    padding: 15px 0;
    color: #fcf6ba;
    font-size: 1.1rem;
    margin-bottom: 30px;
    transition: 0.3s;
}
.gold-input:focus {
    border-bottom-color: #fcf6ba;
    outline: none;
    box-shadow: 0 10px 20px -10px rgba(252, 246, 186, 0.1);
}
.gold-input::placeholder { color: #555; }

.gold-btn {
    background: linear-gradient(to bottom, #bf953f, #b38728);
    color: black;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 2px;
    padding: 18px 40px;
    border: 1px solid #fcf6ba;
    width: 100%;
    cursor: pointer;
    transition: 0.3s;
}
.gold-btn:hover {
    background: linear-gradient(to bottom, #fcf6ba, #bf953f);
    box-shadow: 0 0 30px rgba(191, 149, 63, 0.4);
}

.gold-info-row {
    display: flex;
    justify-content: space-around;
    text-align: center;
    margin-bottom: 40px;
    flex-wrap: wrap;
    gap: 20px;
}
.gold-info-item {
    color: #ccc;
}
.gold-info-item i {
    color: #bf953f;
    font-size: 1.5rem;
    margin-bottom: 10px;
    display: block;
}

.gold-map-strip {
    height: 300px;
    width: 100%;
    margin-top: 60px;
    border-top: 1px solid #b38728;
    border-bottom: 1px solid #b38728;
}
</style>

<div class="gold-wrapper">
    <div class="gold-orb"></div>
    
    <div class="gold-container">
        <h1 class="gold-title">اتصل بنا</h1>
        
        <div class="gold-card">
            
            <div class="gold-info-row">
                <div class="gold-info-item">
                    <i class="fas fa-phone"></i>
                    01008658632
                </div>
                <div class="gold-info-item">
                    <i class="fas fa-envelope"></i>
                    info@al-muwafi.com
                </div>
                <div class="gold-info-item">
                    <i class="fas fa-clock"></i>
                    9:00 ص - 10:00 م
                </div>
            </div>

            <div class="gold-divider"></div>

            <form action="/contact/send" method="POST">
                <input type="hidden" name="csrf_token" value="<?= App\Services\Security::generateCsrfToken() ?>">
                
                <div style="display:flex; gap:20px;">
                    <input type="text" name="name" class="gold-input" placeholder="الاسم" required>
                    <input type="tel" name="phone" class="gold-input" placeholder="الهاتف">
                </div>
                
                <input type="email" name="email" class="gold-input" placeholder="البريد الإلكتروني">
                <textarea name="message" class="gold-input" rows="2" placeholder="الرسالة" required></textarea>
                
                <button type="submit" class="gold-btn">إرسال الطلب</button>
            </form>
        </div>
    </div>

    <div class="gold-map-strip">
        <iframe style="width:100%; height:100%; border:0; filter: grayscale(100%) sepia(100%) hue-rotate(5deg) saturate(90%) contrast(90%);" src="<?= $defaultMap ?>" allowfullscreen="" loading="lazy"></iframe>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/public_layout.php';
?>
