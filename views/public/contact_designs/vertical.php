<?php
ob_start();
?>

<style>
/* Vertical Luxury Layout */
.vertical-layout-container {
    display: flex;
    min-height: 85vh;
    background: #0f172a;
    position: relative;
    overflow: hidden;
}

/* Sidebar (Right) */
.vertical-sidebar {
    width: 35%;
    background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
    border-left: 1px solid rgba(255, 215, 0, 0.1);
    padding: 60px 40px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    position: relative;
    z-index: 2;
}

.sidebar-deco-line {
    position: absolute;
    right: 0;
    top: 50px;
    width: 4px;
    height: 100px;
    background: #f9f295;
    box-shadow: 0 0 15px rgba(249, 242, 149, 0.5);
}

.vl-title {
    font-size: 3rem;
    color: white;
    font-weight: 300;
    margin-bottom: 40px;
    line-height: 1.2;
}
.vl-title strong {
    display: block;
    font-weight: 800;
    color: #cfaa5d;
}

.vl-info-item {
    margin-bottom: 30px;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    padding-bottom: 20px;
}
.vl-label {
    text-transform: uppercase;
    letter-spacing: 2px;
    font-size: 0.8rem;
    color: #64748b;
    margin-bottom: 10px;
    display: block;
}
.vl-value {
    color: white;
    font-size: 1.2rem;
}

/* Content Area (Left) */
.vertical-content {
    width: 65%;
    padding: 60px 80px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: radial-gradient(circle at center, #1e293b 0%, #0f172a 100%);
}

.minimal-input {
    background: transparent;
    border: none;
    border-bottom: 1px solid rgba(255,255,255,0.2);
    width: 100%;
    padding: 20px 0;
    color: white;
    font-size: 1.1rem;
    transition: all 0.3s;
    margin-bottom: 20px;
}
.minimal-input:focus {
    border-bottom-color: #cfaa5d;
    outline: none;
    padding-right: 10px;
}

.minimal-btn {
    margin-top: 30px;
    background: transparent;
    border: 1px solid #cfaa5d;
    color: #cfaa5d;
    padding: 15px 40px;
    font-size: 1.2rem;
    cursor: pointer;
    transition: all 0.3s;
    align-self: flex-start;
}
.minimal-btn:hover {
    background: #cfaa5d;
    color: #000;
}

@media (max-width: 991px) {
    .vertical-layout-container { flex-direction: column; }
    .vertical-sidebar, .vertical-content { width: 100%; padding: 40px 20px; }
}
</style>

<div class="vertical-layout-container">
    <!-- Right Sidebar -->
    <div class="vertical-sidebar">
        <div class="sidebar-deco-line"></div>
        <h1 class="vl-title">
            تواصل<br>
            <strong>مع النخبة</strong>
        </h1>
        
        <div class="vl-info-item">
            <span class="vl-label">العنوان</span>
            <div class="vl-value">محافظة كفر الشيخ - دسوق</div>
        </div>
        
        <div class="vl-info-item">
            <span class="vl-label">خدمة العملاء</span>
            <div class="vl-value">01008658632</div>
        </div>
        
        <div class="vl-info-item">
            <span class="vl-label">البريد المباشر</span>
            <div class="vl-value">info@al-muwafi.com</div>
        </div>
    </div>

    <!-- Left Content (Form) -->
    <div class="vertical-content">
        <form action="/contact/send" method="POST">
            <input type="hidden" name="csrf_token" value="<?= App\Services\Security::generateCsrfToken() ?>">
            
            <input type="text" name="name" class="minimal-input" placeholder="الاسم الكريم" required>
            
            <input type="tel" name="phone" class="minimal-input" placeholder="رقم الجوال لتأكيد التواصل">
            
            <input type="email" name="email" class="minimal-input" placeholder="البريد الإلكتروني (اختياري)">
            
            <textarea name="message" class="minimal-input" rows="1" placeholder="موضوع الرسالة..." style="resize:none; height:auto; min-height:50px;" required></textarea>
            
            <button type="submit" class="minimal-btn">تأكيد الإرسال</button>
        </form>
    </div>
</div>
