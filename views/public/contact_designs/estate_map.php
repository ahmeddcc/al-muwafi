<?php
ob_start();
$defaultMap = "https://maps.google.com/maps?q=31.105725,30.943881&hl=ar&z=15&output=embed&iwloc=near";
?>

<style>
/* Estate Split Layout */
.estate-wrapper {
    display: flex;
    min-height: 85vh;
    background: #fff; /* Light theme for the form side potentially, or dark */
}

/* Right Side: Form */
.estate-form-col {
    width: 50%;
    background: #0f172a;
    padding: 60px 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.est-title {
    font-size: 2.5rem;
    color: white;
    margin-bottom: 10px;
    font-weight: 800;
}

.est-input {
    background: transparent;
    border: none;
    border-bottom: 2px solid #334155;
    padding: 15px 0;
    width: 100%;
    color: white;
    font-size: 1.1rem;
    margin-bottom: 25px;
    transition: 0.3s;
}
.est-input:focus {
    border-bottom-color: #cfaa5d;
    outline: none;
}

.est-btn {
    background: white;
    color: #0f172a;
    padding: 15px 40px;
    border-radius: 50px;
    border: none;
    font-weight: bold;
    font-size: 1.1rem;
    cursor: pointer;
    align-self: flex-start;
    transition: 0.3s;
    margin-top: 20px;
}
.est-btn:hover {
    background: #cfaa5d;
    transform: translateX(-5px);
}

/* Left Side: Map */
.estate-map-col {
    width: 50%;
    position: relative;
}

.full-height-map {
    width: 100%;
    height: 100%;
    border: none;
    filter: grayscale(20%); /* Slight desaturation for elegance */
}

.map-overlay-info {
    position: absolute;
    bottom: 40px;
    left: 40px;
    background: white;
    padding: 20px 30px;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    min-width: 250px;
}

@media (max-width: 991px) {
    .estate-wrapper { flex-direction: column-reverse; }
    .estate-form-col, .estate-map-col { width: 100%; }
    .estate-map-col { height: 400px; }
}
</style>

<div class="estate-wrapper">
    <!-- Form Side -->
    <div class="estate-form-col">
        <h1 class="est-title">تواصل معنا</h1>
        <p style="color: #94a3b8; margin-bottom: 50px; font-size: 1.1rem;">
            املأ النموذج أدناه وسيقوم فريقنا بالتواصل معك في أقرب وقت ممكن.
        </p>
        
        <form action="/contact/send" method="POST">
            <input type="hidden" name="csrf_token" value="<?= App\Services\Security::generateCsrfToken() ?>">
            
            <input type="text" name="name" class="est-input" placeholder="الاسم الكامل" required>
            <input type="tel" name="phone" class="est-input" placeholder="رقم الهاتف">
            <input type="email" name="email" class="est-input" placeholder="البريد الإلكتروني">
            <textarea name="message" class="est-input" rows="2" placeholder="اكتب رسالتك هنا..." style="resize:none;" required></textarea>
            
            <button type="submit" class="est-btn">إرسال ➜</button>
        </form>
    </div>

    <!-- Map Side -->
    <div class="estate-map-col">
        <iframe class="full-height-map" src="<?= $defaultMap ?>" allowfullscreen="" loading="lazy"></iframe>
        
        <div class="map-overlay-info">
            <h4 style="margin:0 0 10px 0; color:#0f172a;">مقر الشركة</h4>
            <p style="margin:0; color:#64748b; font-size:0.9rem; line-height:1.5;">
                محافظة كفر الشيخ<br>
                مركز دسوق، تقسيم زهدي
            </p>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/public_layout.php';
?>
