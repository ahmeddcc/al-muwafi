<?php
ob_start();
$defaultMap = "https://maps.google.com/maps?q=31.105725,30.943881&hl=ar&z=15&output=embed&iwloc=near";
?>

<style>
/* Split Map Overlay Layout */
.split-map-header {
    height: 45vh;
    width: 100%;
    position: relative;
    z-index: 1;
}

.split-map-frame {
    width: 100%;
    height: 100%;
    border: none;
    filter: grayscale(100%) invert(92%) contrast(83%); /* Dark map style */
}

.split-content-area {
    background: #0f172a;
    min-height: 50vh;
    position: relative;
    padding-bottom: 50px;
}

.floating-contact-card {
    max-width: 1000px;
    margin: -100px auto 0; /* Pull up to overlap map */
    background: #1e293b;
    border: 1px solid rgba(255, 215, 0, 0.1);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    border-radius: 16px;
    position: relative;
    z-index: 10;
    display: flex;
    overflow: hidden;
}

.card-info-side {
    width: 40%;
    background: linear-gradient(135deg, #b48811 0%, #aa771c 100%);
    padding: 40px;
    color: white;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.card-form-side {
    width: 60%;
    padding: 40px;
}

.sm-input {
    background: #0f172a;
    border: 1px solid #334155;
    padding: 12px;
    border-radius: 8px;
    color: white;
    width: 100%;
    margin-bottom: 15px;
}
.sm-input:focus { border-color: #cfaa5d; outline: none; }

.sm-btn {
    background: #cfaa5d; color: black; font-weight: bold;
    padding: 12px 30px; border: none; border-radius: 8px; cursor: pointer;
    width: 100%; font-size: 1.1rem;
}

@media (max-width: 768px) {
    .floating-contact-card { flex-direction: column; margin: 0; border-radius: 0; }
    .card-info-side, .card-form-side { width: 100%; }
}
</style>

<!-- 1. The Map Header -->
<div class="split-map-header">
    <iframe class="split-map-frame" src="<?= $defaultMap ?>" allowfullscreen="" loading="lazy"></iframe>
</div>

<!-- 2. The Content Area -->
<div class="split-content-area">
    <div class="container">
        <div class="floating-contact-card">
            
            <!-- Info Side -->
            <div class="card-info-side">
                <div>
                    <h2 style="font-size: 2rem; margin-bottom: 20px;">تواصل معنا</h2>
                    <p style="opacity: 0.9; line-height: 1.6;">فريقنا جاهز للرد على استفساراتكم بخصوص ماكينات ريكو وخدمات الصيانة.</p>
                </div>
                
                <div style="margin-top: 40px;">
                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>كفر الشيخ، دسوق</span>
                    </div>
                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-phone"></i>
                        <span dir="ltr">01008658632</span>
                    </div>
                    <div style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <i class="fas fa-envelope"></i>
                        <span>info@al-muwafi.com</span>
                    </div>
                </div>
            </div>

            <!-- Form Side -->
            <div class="card-form-side">
                <form action="/contact/send" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= App\Services\Security::generateCsrfToken() ?>">
                    
                    <h3 style="color:white; margin-bottom: 20px;">أرسل استفسارك</h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <input type="text" name="name" class="sm-input" placeholder="الاسم" required>
                        <input type="tel" name="phone" class="sm-input" placeholder="رقم الهاتف">
                    </div>
                    
                    <input type="email" name="email" class="sm-input" placeholder="البريد الإلكتروني">
                    <textarea name="message" class="sm-input" rows="4" placeholder="الرسالة..." required></textarea>
                    
                    <button type="submit" class="sm-btn">إرسال</button>
                </form>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/public_layout.php';
?>
