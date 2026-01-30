<?php
ob_start();
$defaultMap = "https://maps.google.com/maps?q=31.105725,30.943881&hl=ar&z=15&output=embed&iwloc=near";
?>

<style>
/* Glass Sidebar Map Layout */
.glass-map-wrapper {
    position: relative;
    height: 90vh; /* Full viewport height minus header */
    width: 100%;
    overflow: hidden;
}

.full-bg-map {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: none;
    z-index: 0;
}

.glass-sidebar {
    position: absolute;
    right: 50px;
    top: 50%;
    transform: translateY(-50%);
    width: 450px;
    max-width: 90%;
    background: rgba(15, 23, 42, 0.75);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    border-radius: 20px;
    padding: 40px;
    z-index: 10;
}

.gs-title {
    font-size: 2rem;
    color: white;
    margin-bottom: 10px;
    font-weight: 700;
}

.gs-subtitle {
    color: #cbd5e1;
    font-size: 0.9rem;
    margin-bottom: 30px;
}

.gs-input {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    padding: 12px;
    color: white;
    width: 100%;
    margin-bottom: 15px;
    transition: 0.3s;
}
.gs-input:focus { background: rgba(0,0,0,0.5); border-color: #f9f295; outline:none; }

.gs-btn {
    width: 100%;
    padding: 14px;
    background: linear-gradient(90deg, #cfaa5d, #e8c76d);
    border: none;
    border-radius: 8px;
    color: black;
    font-weight: bold;
    cursor: pointer;
    margin-top: 10px;
}

@media (max-width: 768px) {
    .glass-sidebar {
        position: relative;
        right: auto; top: auto; transform: none;
        width: 100%; margin: 0; border-radius: 0;
        height: auto;
    }
    .glass-map-wrapper { height: auto; display: flex; flex-direction: column-reverse; }
    .full-bg-map { position: relative; height: 300px; }
}
</style>

<div class="glass-map-wrapper">
    <!-- Background Map -->
    <iframe class="full-bg-map" src="<?= $defaultMap ?>" allowfullscreen="" loading="lazy"></iframe>
    
    <!-- Floating Sidebar -->
    <div class="glass-sidebar">
        <h1 class="gs-title">زيارة المُوَافِي</h1>
        <p class="gs-subtitle">نحن متواجدون في قلب كفر الشيخ لخدمتكم. استخدم الخريطة للوصول إلينا أو راسلنا هنا.</p>
        
        <form action="/contact/send" method="POST">
            <input type="hidden" name="csrf_token" value="<?= App\Services\Security::generateCsrfToken() ?>">
            
            <input type="text" name="name" class="gs-input" placeholder="الاسم" required>
            <input type="tel" name="phone" class="gs-input" placeholder="رقم الجوال">
            <textarea name="message" class="gs-input" rows="4" placeholder="استفسارك..." required></textarea>
            
            <button type="submit" class="gs-btn">إرسال الرسالة</button>
        </form>
        
        <div style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px;">
            <div style="color: #94a3b8; font-size: 0.9rem; margin-bottom: 8px;">
                <i class="fas fa-phone" style="color:#cfaa5d; margin-left:8px;"></i> 01008658632
            </div>
            <div style="color: #94a3b8; font-size: 0.9rem;">
                <i class="fas fa-map-marker-alt" style="color:#cfaa5d; margin-left:8px;"></i> دسوق، كفر الشيخ
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/public_layout.php';
?>
