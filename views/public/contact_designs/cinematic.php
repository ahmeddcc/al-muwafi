<?php
ob_start();
?>

<style>
/* Cinematic Studio Layout */
.cine-wrapper {
    position: relative;
    height: 90vh;
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    background-image: url('<?= BASE_URL ?>/public/assets/images/hero-neon.png'); /* Replace with local image or color */
    background-size: cover;
    background-position: center;
}
.cine-overlay {
    position: absolute; inset: 0;
    background: radial-gradient(circle, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.9) 100%);
    backdrop-filter: blur(5px);
}

.cine-card {
    position: relative;
    z-index: 10;
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(25px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 30px;
    width: 900px;
    max-width: 95%;
    display: flex;
    overflow: hidden;
    box-shadow: 0 50px 100px -20px rgba(0,0,0,0.8);
}

.cine-form-side {
    flex: 1.2;
    padding: 50px;
}

.cine-info-side {
    flex: 0.8;
    background: rgba(0, 0, 0, 0.3);
    padding: 50px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    border-right: 1px solid rgba(255,255,255,0.05);
}

.cine-input {
    background: rgba(0,0,0,0.3);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    padding: 12px 15px;
    color: white;
    width: 100%;
    margin-bottom: 15px;
}

.cine-submit {
    width: 100%;
    padding: 15px;
    background: white;
    color: black;
    border: none;
    border-radius: 8px;
    font-weight: 800;
    cursor: pointer;
    box-shadow: 0 0 20px rgba(255,255,255,0.2);
    transition: 0.3s;
}
.cine-submit:hover {
    box-shadow: 0 0 40px rgba(255,255,255,0.4);
    transform: scale(1.02);
}

.cine-highlight {
    color: #f9f295;
    font-size: 1.5rem;
    margin-bottom: 20px;
    font-weight: bold;
}

@media (max-width: 768px) {
    .cine-card { flex-direction: column-reverse; height: auto; }
}
</style>

<div class="cine-wrapper">
    <div class="cine-overlay"></div>
    
    <div class="cine-card">
        <!-- Form Side -->
        <div class="cine-form-side">
            <h2 style="color:white; margin-bottom:30px; font-weight:300;">بدء محادثة جديدة</h2>
            <form action="/contact/send" method="POST">
                <input type="hidden" name="csrf_token" value="<?= App\Services\Security::generateCsrfToken() ?>">
                
                <input type="text" name="name" class="cine-input" placeholder="الاسم" required>
                <input type="tel" name="phone" class="cine-input" placeholder="الجوال">
                <textarea name="message" class="cine-input" rows="4" placeholder="اكتب رسالتك..." required></textarea>
                
                <button type="submit" class="cine-submit">إرسال الطلب</button>
            </form>
        </div>

        <!-- Info Side -->
        <div class="cine-info-side">
            <div>
                <div class="cine-highlight">المُوَافي</div>
                <p style="color:#aaa; line-height:1.6;">نسعد بزيارتكم لمقرنا الرئيسي والتعرف على أحدث حلول الطباعة الرقمية والخدمات المكتبية.</p>
            </div>
            
            <div style="margin-top:40px;">
                <div style="margin-bottom:15px; color:white;">
                    <i class="fas fa-map-pin" style="color:#f9f295; margin-left:10px;"></i>
                    كفر الشيخ - دسوق
                </div>
                <div style="margin-bottom:15px; color:white;">
                    <i class="fas fa-phone" style="color:#f9f295; margin-left:10px;"></i>
                    01008658632
                </div>
            </div>
        </div>
    </div>
</div>
