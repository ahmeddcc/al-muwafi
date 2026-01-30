<?php
ob_start();
$defaultMap = "https://maps.google.com/maps?q=31.105725,30.943881&hl=ar&z=15&output=embed&iwloc=near";
?>

<style>
/* Modern Material Layout */
.mat-wrapper {
    background: #f1f5f9; /* Light Light Grey */
    padding: 80px 0;
    min-height: 100vh;
}
/* Adaptation for Dark Theme if site is dark global */
.dark-theme .mat-wrapper { background: #020617; } 

.mat-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.mat-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 30px;
    margin-bottom: 50px;
}

.mat-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    transition: 0.3s;
    text-align: center;
}
.mat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
/* Dark theme adaptation */
.dark-theme .mat-card { background: #1e293b; color: white; }

.mat-icon-bg {
    width: 60px; height: 60px;
    background: #eef2ff;
    color: #4f46e5;
    border-radius: 50%;
    display: flex;
    align-items: center; justify-content: center;
    margin: 0 auto 20px;
    font-size: 1.5rem;
}
/* Brand adaptation */
.mat-icon-bg { background: rgba(207, 170, 93, 0.1); color: #cfaa5d; }

.mat-label { font-weight: bold; margin-bottom: 5px; font-size: 1.1rem; }
.mat-text { color: #64748b; }
.dark-theme .mat-text { color: #94a3b8; }

.mat-main-section {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 30px;
    background: white;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
}
.dark-theme .mat-main-section { background: #1e293b; }

.mat-form-area { padding: 40px; }
.mat-map-area { position: relative; min-height: 400px; }

.mat-input {
    width: 100%;
    padding: 12px 15px;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    margin-bottom: 15px;
    font-family: inherit;
    transition: 0.2s;
    background: transparent;
}
.dark-theme .mat-input { border-color: #334155; color: white; }

.mat-input:focus { border-color: #cfaa5d; outline: none; }

.mat-btn {
    background: #0f172a;
    color: white;
    padding: 12px 25px;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    cursor: pointer;
}
.dark-theme .mat-btn { background: #cfaa5d; color: black; }

@media (max-width: 991px) {
    .mat-grid { grid-template-columns: 1fr; }
    .mat-main-section { grid-template-columns: 1fr; }
    .mat-map-area { height: 300px; min-height: 0; order: -1; }
}
</style>

<!-- Force Dark Theme wrapper for this preview since site is dark -->
<div class="dark-theme"> 
    <div class="mat-wrapper">
        <div class="mat-container">
            
            <div style="text-align:center; margin-bottom:50px;">
                <h1 style="font-size:2.5rem; color:white; margin-bottom:10px;">هل لديك استفسار؟</h1>
                <p style="color:#94a3b8;">فريقنا هنا لمساعدتك في كل ما يتعلق بحلول الطباعة</p>
            </div>

            <!-- Top Cards -->
            <div class="mat-grid">
                <div class="mat-card">
                    <div class="mat-icon-bg"><i class="fas fa-phone"></i></div>
                    <div class="mat-label">اتصل بنا</div>
                    <div class="mat-text">01008658632</div>
                </div>
                <div class="mat-card">
                    <div class="mat-icon-bg"><i class="fas fa-envelope"></i></div>
                    <div class="mat-label">البريد الإلكتروني</div>
                    <div class="mat-text">info@al-muwafi.com</div>
                </div>
                <div class="mat-card">
                    <div class="mat-icon-bg"><i class="fas fa-search-location"></i></div>
                    <div class="mat-label">الموقع</div>
                    <div class="mat-text">كفر الشيخ، دسوق</div>
                </div>
            </div>

            <!-- Main Form & Map -->
            <div class="mat-main-section">
                <div class="mat-form-area">
                    <h2 style="color:white; margin-bottom:25px;">أرسل رسالة</h2>
                    <form action="/contact/send" method="POST">
                        <input type="hidden" name="csrf_token" value="<?= App\Services\Security::generateCsrfToken() ?>">
                        
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:15px;">
                            <input type="text" name="name" class="mat-input" placeholder="الاسم" required>
                            <input type="tel" name="phone" class="mat-input" placeholder="رقم الموبايل">
                        </div>
                        <input type="email" name="email" class="mat-input" placeholder="البريد الإلكتروني">
                        <textarea name="message" class="mat-input" rows="5" placeholder="تفاصيل الرسالة..." required></textarea>
                        
                        <button type="submit" class="mat-btn">تأكيد الإرسال</button>
                    </form>
                </div>
                <div class="mat-map-area">
                    <iframe style="width:100%; height:100%; border:0;" src="<?= $defaultMap ?>" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require_once VIEWS_PATH . '/layouts/public_layout.php';
?>
