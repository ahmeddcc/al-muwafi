<?php
ob_start();
?>

<style>
/* Bento Grid Layout */
.contact-bento-wrapper {
    max-width: 1400px;
    margin: 40px auto;
    padding: 0 20px;
    display: grid;
    grid-template-columns: 1fr 1fr 1.5fr;
    grid-template-rows: auto auto;
    gap: 20px;
}

.bento-card {
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(16px);
    border: 1px solid rgba(255, 215, 0, 0.1);
    border-radius: 24px;
    padding: 30px;
    transition: transform 0.3s ease, border-color 0.3s ease;
    overflow: hidden;
    position: relative;
}

.bento-card:hover {
    transform: translateY(-5px);
    border-color: rgba(255, 215, 0, 0.3);
    box-shadow: 0 20px 40px rgba(0,0,0,0.4);
}

/* Specific Areas */
.area-headline {
    grid-column: 1 / 3;
    grid-row: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.8), rgba(255, 215, 0, 0.05));
}

.area-info {
    grid-column: 1 / 3;
    grid-row: 2;
    display: flex;
    gap: 15px;
}

.area-form {
    grid-column: 3;
    grid-row: 1 / 3;
    background: rgba(0, 0, 0, 0.4);
}

/* Typography & Icons */
.bento-title {
    font-size: 2.5rem;
    background: linear-gradient(to right, #cfaa5d, #f9f295);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 800;
    margin-bottom: 15px;
}

.bento-subtitle {
    color: #94a3b8;
    font-size: 1.1rem;
    line-height: 1.6;
}

.info-tile {
    flex: 1;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}
.info-tile:hover { background: rgba(255, 215, 0, 0.1); }

.info-icon {
    font-size: 1.8rem;
    color: #f9f295;
    margin-bottom: 10px;
}

/* Form Styling */
.bento-input {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: white;
    border-radius: 12px;
    padding: 15px;
    width: 100%;
    margin-bottom: 15px;
    transition: all 0.3s;
}
.bento-input:focus {
    border-color: #cfaa5d;
    background: rgba(255, 255, 255, 0.1);
    outline: none;
}

.bento-btn {
    width: 100%;
    background: linear-gradient(45deg, #cfaa5d, #b68d2f);
    color: #000;
    font-weight: bold;
    padding: 15px;
    border-radius: 12px;
    border: none;
    cursor: pointer;
    font-size: 1.1rem;
}

@media (max-width: 991px) {
    .contact-bento-wrapper {
        grid-template-columns: 1fr;
    }
    .area-headline, .area-info, .area-form {
        grid-column: auto;
        grid-row: auto;
    }
    .area-info {
        flex-direction: column;
    }
}
</style>

<div class="contact-bento-wrapper">
    <!-- 1. Headline Area -->
    <div class="bento-card area-headline">
        <h1 class="bento-title">جاهزون لخدمتك دائماً</h1>
        <p class="bento-subtitle">في المُوَافي، نؤمن بأن التواصل الفعال هو بداية كل شراكة ناجحة. فريقنا مستعد للإجابة على استفساراتك وتقديم الدعم الفني لمعداتك المكتبية على مدار الساعة.</p>
    </div>

    <!-- 2. Contact Info Area -->
    <div class="bento-card area-info">
        <div class="info-tile">
            <i class="fas fa-phone-alt info-icon"></i>
            <h3>اتصل بنا</h3>
            <p>01008658632</p>
        </div>
        <div class="info-tile">
            <i class="fas fa-envelope info-icon"></i>
            <h3>راسلنا</h3>
            <p>info@al-muwafi.com</p>
        </div>
        <div class="info-tile">
            <i class="fas fa-map-marker-alt info-icon"></i>
            <h3>زرنا</h3>
            <p>كفر الشيخ - دسوق</p>
        </div>
    </div>

    <!-- 3. Form Area -->
    <div class="bento-card area-form">
        <h2 style="color:white; margin-bottom:20px; border-bottom:1px solid #333; padding-bottom:10px;">أرسل رسالة</h2>
        <form action="/contact/send" method="POST">
            <input type="hidden" name="csrf_token" value="<?= App\Services\Security::generateCsrfToken() ?>">
            
            <label style="color:#aaa; display:block; margin-bottom:5px;">الاسم الكامل</label>
            <input type="text" name="name" class="bento-input" placeholder="اكتب اسمك هنا..." required>

            <label style="color:#aaa; display:block; margin-bottom:5px;">رقم الهاتف</label>
            <input type="tel" name="phone" class="bento-input" placeholder="01xxxxxxxxx">

            <label style="color:#aaa; display:block; margin-bottom:5px;">البريد الإلكتروني</label>
            <input type="email" name="email" class="bento-input" placeholder="email@example.com">

            <label style="color:#aaa; display:block; margin-bottom:5px;">الرسالة</label>
            <textarea name="message" class="bento-input" rows="5" placeholder="كيف يمكننا مساعدتك؟" required></textarea>

            <button type="submit" class="bento-btn">إرسال الآن <i class="fas fa-paper-plane"></i></button>
        </form>
    </div>
</div>
