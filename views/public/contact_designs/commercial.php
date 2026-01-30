<?php
ob_start();
$defaultMap = "https://maps.google.com/maps?q=31.105725,30.943881&hl=ar&z=15&output=embed&iwloc=near";
?>

<style>
/* Commercial Standard Layout */
.comm-wrapper {
    background: #0f172a; /* Main Dark Background */
    padding: 80px 0;
}

.comm-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.comm-grid {
    display: grid;
    grid-template-columns: 1fr 1.5fr; /* Info/Map : Form */
    gap: 40px;
    align-items: start;
}

/* Section Title */
.comm-header {
    text-align: center;
    margin-bottom: 60px;
}
.comm-title {
    font-size: 2.5rem;
    color: white;
    margin-bottom: 15px;
    position: relative;
    display: inline-block;
}
.comm-title::after {
    content: '';
    display: block;
    width: 60px;
    height: 4px;
    background: #cfaa5d;
    margin: 10px auto 0;
    border-radius: 2px;
}
.comm-desc {
    color: #94a3b8;
    max-width: 600px;
    margin: 0 auto;
    font-size: 1.1rem;
}

/* Info Side (Left) */
.comm-info-box {
    background: #1e293b;
    border-radius: 12px;
    padding: 30px;
    border: 1px solid #334155;
    margin-bottom: 30px;
}

.comm-info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 25px;
    padding-bottom: 25px;
    border-bottom: 1px solid #334155;
}
.comm-info-item:last-child { margin: 0; padding: 0; border: none; }

.comm-icon {
    width: 50px;
    height: 50px;
    background: rgba(207, 170, 93, 0.1);
    color: #cfaa5d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    margin-left: 20px;
    flex-shrink: 0;
}

.comm-map-container {
    height: 300px;
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #334155;
}

/* Form Side (Right) */
.comm-form-box {
    background: #1e293b;
    border-radius: 12px;
    padding: 40px;
    border-top: 5px solid #cfaa5d;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
}

.comm-input-group {
    margin-bottom: 20px;
}
.comm-label {
    display: block;
    color: white;
    margin-bottom: 8px;
    font-weight: bold;
}
.comm-input {
    width: 100%;
    padding: 15px;
    background: #0f172a;
    border: 1px solid #334155;
    border-radius: 8px;
    color: white;
    font-size: 1rem;
    transition: 0.2s;
}
.comm-input:focus {
    border-color: #cfaa5d;
    outline: none;
    background: #0f172a;
}

.comm-btn {
    background: #cfaa5d;
    color: black;
    width: 100%;
    padding: 16px;
    border: none;
    border-radius: 8px;
    font-weight: bold;
    font-size: 1.1rem;
    cursor: pointer;
    transition: 0.2s;
}
.comm-btn:hover {
    background: #e8c76d;
    transform: translateY(-2px);
}

@media (max-width: 991px) {
    .comm-grid { grid-template-columns: 1fr; }
    .comm-form-box { order: -1; } /* Form first on mobile */
}
</style>

<div class="comm-wrapper">
    <div class="comm-container">
        
        <div class="comm-header">
            <h1 class="comm-title">تواصل مع المُوَافي</h1>
            <p class="comm-desc">نسعد باستقبال استفساراتكم وطلبات الصيانة عبر القنوات الرسمية التالية.</p>
        </div>

        <div class="comm-grid">
            <!-- Info Sidebar -->
            <div class="comm-sidebar">
                <div class="comm-info-box">
                    <div class="comm-info-item">
                        <div class="comm-icon"><i class="fas fa-phone-alt"></i></div>
                        <div>
                           <div style="color:#94a3b8; font-size:0.9rem;">خدمة العملاء</div>
                           <div style="color:white; font-size:1.2rem; font-weight:bold;">01008658632</div>
                        </div>
                    </div>
                    <div class="comm-info-item">
                        <div class="comm-icon"><i class="fas fa-envelope"></i></div>
                        <div>
                           <div style="color:#94a3b8; font-size:0.9rem;">البريد الإلكتروني</div>
                           <div style="color:white; font-size:1.1rem;">info@al-muwafi.com</div>
                        </div>
                    </div>
                    <div class="comm-info-item">
                        <div class="comm-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                           <div style="color:#94a3b8; font-size:0.9rem;">العنوان الرئيسي</div>
                           <div style="color:white; font-size:1.1rem;">محافظة كفر الشيخ - دسوق</div>
                        </div>
                    </div>
                </div>

                <div class="comm-map-container">
                    <iframe style="width:100%; height:100%; border:0;" src="<?= $defaultMap ?>" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

            <!-- Form Area -->
            <div class="comm-form-box">
                <form action="/contact/send" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= App\Services\Security::generateCsrfToken() ?>">
                    
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                        <div class="comm-input-group">
                            <label class="comm-label">الاسم</label>
                            <input type="text" name="name" class="comm-input" required>
                        </div>
                        <div class="comm-input-group">
                            <label class="comm-label">رقم الهاتف</label>
                            <input type="tel" name="phone" class="comm-input">
                        </div>
                    </div>

                    <div class="comm-input-group">
                        <label class="comm-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="comm-input">
                    </div>

                    <div class="comm-input-group">
                        <label class="comm-label">الرسالة</label>
                        <textarea name="message" class="comm-input" rows="6" required></textarea>
                    </div>

                    <button type="submit" class="comm-btn">إرسال الرسالة</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/public_layout.php';
?>
