<?php
/**
 * Contact Design Previewer
 * Allows switching between 3 layouts: Split, Command, Minimalist
 */
$currentPage = 'contact';
ob_start();
?>
<style>
    /* PREVIEWER UI */
    .design-switcher {
        position: fixed;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        background: rgba(0,0,0,0.8);
        padding: 10px 20px;
        border-radius: 50px;
        border: 1px solid #333;
        display: flex;
        gap: 10px;
        backdrop-filter: blur(10px);
    }
    .switcher-btn {
        background: transparent;
        color: #fff;
        border: 1px solid #555;
        padding: 8px 16px;
        border-radius: 20px;
        cursor: pointer;
        font-family: 'Cairo';
        transition: 0.3s;
    }
    .switcher-btn.active {
        background: #bf953f;
        color: #000;
        border-color: #bf953f;
        font-weight: bold;
    }
    .design-view {
        display: none;
        width: 100%;
        min-height: 100vh;
    }
    .design-view.active {
        display: block;
    }

    /* === OPTION 1: EXECUTIVE SPLIT === */
    .split-container {
        display: flex;
        min-height: 100vh;
        padding-top: 80px; /* Nav space */
    }
    .split-info {
        width: 45%;
        background: #0f172a;
        padding: 4rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .split-info::after {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(circle at 0% 0%, rgba(191, 149, 63, 0.1), transparent 60%);
    }
    .split-form-area {
        width: 55%;
        background: #020617;
        padding: 4rem;
        display: flex;
        align-items: center;
    }
    .split-title {
        font-size: 3.5rem;
        font-weight: 900;
        background: linear-gradient(to right, #bf953f, #aa771c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 2rem;
    }
    .info-row {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 30px;
        font-size: 1.2rem;
        color: #cbd5e1;
        position: relative;
        z-index: 2;
    }
    .info-row i {
        color: #bf953f;
        font-size: 1.5rem;
    }

    /* === OPTION 2: GLOBAL COMMAND === */
    .command-container {
        position: relative;
        height: 100vh;
        width: 100%;
        overflow: hidden;
        padding-top: 80px;
    }
    .full-map {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        filter: grayscale(100%) invert(92%) contrast(83%);
        z-index: 0;
    }
    .floating-hud {
        position: absolute;
        top: 55%;
        right: 10%;
        transform: translateY(-50%);
        width: 450px;
        background: rgba(15, 23, 42, 0.85);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 215, 0, 0.2);
        border-right: 4px solid #bf953f;
        padding: 40px;
        z-index: 2;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    .hud-header {
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 20px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .hud-status {
        font-family: monospace;
        color: #0ea5e9;
        font-size: 0.9rem;
    }
    
    /* === OPTION 3: GOLDEN MONOLITH === */
    .mono-container {
        min-height: 100vh;
        background: #000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding-top: 80px;
        position: relative;
    }
    .mono-center-line {
        position: absolute;
        top: 0; left: 50%; width: 1px; height: 100%;
        background: linear-gradient(to bottom, transparent, #bf953f, transparent);
        opacity: 0.3;
    }
    .mono-form {
        width: 100%;
        max-width: 600px;
        position: relative;
        z-index: 2;
    }
    .mono-input-group {
        position: relative;
        margin-bottom: 4rem;
    }
    .mono-input {
        width: 100%;
        background: transparent;
        border: none;
        border-bottom: 1px solid #333;
        padding: 15px 0;
        color: #bf953f;
        font-size: 1.5rem;
        transition: 0.5s;
    }
    .mono-input:focus {
        outline: none;
        border-bottom-color: #bf953f;
    }
    .mono-label {
        position: absolute;
        top: -10px;
        right: 0;
        color: #64748b;
        font-size: 0.9rem;
        letter-spacing: 2px;
        text-transform: uppercase;
    }
    .mono-btn {
        background: transparent;
        border: 1px solid #bf953f;
        color: #bf953f;
        width: 100%;
        padding: 20px;
        font-size: 1.2rem;
        letter-spacing: 5px;
        text-transform: uppercase;
        cursor: pointer;
        transition: 0.5s;
    }
    .mono-btn:hover {
        background: #bf953f;
        color: #000;
        letter-spacing: 10px;
    }

</style>

<!-- SWITCHER -->
<div class="design-switcher">
    <button class="switcher-btn active" onclick="showDesign(1)">1. Executive Split</button>
    <button class="switcher-btn" onclick="showDesign(2)">2. Global Command</button>
    <button class="switcher-btn" onclick="showDesign(3)">3. Minimalist</button>
</div>

<!-- OPTION 1: EXECUTIVE SPLIT -->
<div id="design-1" class="design-view active">
    <div class="split-container">
        <div class="split-info">
            <h1 class="split-title">التميز<br>في خدمتك</h1>
            <p style="color: #94a3b8; margin-bottom: 3rem; line-height: 1.8;">نحن هنا لتقديم أفضل حلول ريكو لمكتبك. تواصل معنا مباشرة للحصول على استشارة احترافية.</p>
            
            <div class="info-row">
                <i class="fas fa-phone-alt"></i>
                <span>+966 50 000 0000</span>
            </div>
            <div class="info-row">
                <i class="fas fa-envelope"></i>
                <span>info@almuwafi.com</span>
            </div>
            <div class="info-row">
                <i class="fas fa-map-marker-alt"></i>
                <span>الرياض، المملكة العربية السعودية</span>
            </div>
        </div>
        <div class="split-form-area">
            <form style="width: 100%; max-width: 500px;">
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; color: #fff; margin-bottom: 10px;">الاسم الكريم</label>
                    <input type="text" style="width: 100%; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px;">
                </div>
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; color: #fff; margin-bottom: 10px;">البريد الإلكتروني</label>
                    <input type="email" style="width: 100%; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px;">
                </div>
                <div style="margin-bottom: 2rem;">
                    <label style="display: block; color: #fff; margin-bottom: 10px;">الرسالة</label>
                    <textarea rows="4" style="width: 100%; padding: 15px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 8px;"></textarea>
                </div>
                <button type="button" class="nav-cta" style="width: 100%; border: none; padding: 15px; cursor: pointer;">إرسال الطلب</button>
            </form>
        </div>
    </div>
</div>

<!-- OPTION 2: GLOBAL COMMAND -->
<div id="design-2" class="design-view">
    <div class="command-container">
        <!-- Background Map -->
        <iframe class="full-map" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3624.088673775089!2d46.6752953!3d24.7135517!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjTCszQyJzQ4LjgiTiA0NsKwNDAnMzEuMSJF!5e0!3m2!1sen!2ssa" allowfullscreen="" loading="lazy"></iframe>
        
        <!-- Floating HUD -->
        <div class="floating-hud">
            <div class="hud-header">
                <h2 style="color: #fff; margin: 0; font-size: 1.5rem;">قناة الاتصال الآمنة</h2>
                <span class="hud-status">● SYSTEM READY</span>
            </div>
            
            <form>
                <input type="text" placeholder="تعريف المرسل (الاسم)" style="width: 100%; padding: 12px; margin-bottom: 15px; background: rgba(0,0,0,0.3); border: 1px solid #334155; color: #fff;">
                <input type="text" placeholder="نقطة الاتصال (الهاتف)" style="width: 100%; padding: 12px; margin-bottom: 15px; background: rgba(0,0,0,0.3); border: 1px solid #334155; color: #fff;">
                <textarea rows="3" placeholder="البيانات المرسلة (الرسالة)" style="width: 100%; padding: 12px; margin-bottom: 20px; background: rgba(0,0,0,0.3); border: 1px solid #334155; color: #fff;"></textarea>
                
                <button type="button" style="width: 100%; padding: 15px; background: #bf953f; color: #000; border: none; font-weight: bold; cursor: pointer;">بدء الإرسال >></button>
            </form>
            
            <div style="margin-top: 30px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 20px; display: flex; justify-content: space-between; color: #94a3b8; font-size: 0.8rem;">
                <span>RIYADH HQ</span>
                <span>LAT: 24.7136 | LNG: 46.6753</span>
            </div>
        </div>
    </div>
</div>

<!-- OPTION 3: MINIMALIST -->
<div id="design-3" class="design-view">
    <div class="mono-container">
        <div class="mono-center-line"></div>
        <div class="mono-form">
            <div style="text-align: center; margin-bottom: 4rem;">
                <i class="fas fa-infinity" style="color: #bf953f; font-size: 3rem; margin-bottom: 1rem;"></i>
                <h1 style="color: #fff; letter-spacing: 5px;">تواصل بأسلوبك</h1>
            </div>
            
            <div class="mono-input-group">
                <label class="mono-label">01 // الاسم</label>
                <input type="text" class="mono-input">
            </div>
            
            <div class="mono-input-group">
                <label class="mono-label">02 // وسيلة الاتصال</label>
                <input type="text" class="mono-input">
            </div>
            
            <div class="mono-input-group">
                <label class="mono-label">03 // الموضوع</label>
                <input type="text" class="mono-input">
            </div>
            
            <button class="mono-btn">إرسال</button>
        </div>
    </div>
</div>

<script>
    function showDesign(num) {
        // Build URL param to keep state on refresh or just toggle DOM
        document.querySelectorAll('.design-view').forEach(el => el.classList.remove('active'));
        document.getElementById('design-' + num).classList.add('active');
        
        document.querySelectorAll('.switcher-btn').forEach(btn => btn.classList.remove('active'));
        document.querySelector(`.switcher-btn:nth-child(${num})`).classList.add('active');
    }
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/public_layout.php';
?>
