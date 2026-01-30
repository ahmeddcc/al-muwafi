<?php
/**
 * صفحة طلب الصيانة - تصميم مركز القيادة (Cockpit Design)
 * هوية "الموافي" الذهبية والسينمائية
 */

$currentPage = 'maintenance';
// We set hideFooter to true to keep the cinematic feel immersive, similar to contact page
$hideFooter = true; 

ob_start();
?>

<!-- FontAwesome & Fonts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
@import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800;900&display=swap');

/* --- GLOBAL LAYOUT --- */
* { 
    box-sizing: border-box;
    margin: 0; 
    padding: 0;
}

/* === PAGE LOADER === */
.page-loader {
    position: fixed; inset: 0; z-index: 9999;
    background: #020617;
    display: flex; align-items: center; justify-content: center;
    transition: opacity 0.5s, visibility 0.5s;
}
.page-loader.hidden { opacity: 0; visibility: hidden; pointer-events: none; }
.loader-spinner {
    width: 50px; height: 50px;
    border: 3px solid rgba(255,255,255,0.1);
    border-top-color: #D4AF37;
    border-radius: 50%;
    animation: loaderSpin 1s linear infinite;
}
@keyframes loaderSpin { to { transform: rotate(360deg); } }

/* === GOLDEN SHINE ANIMATION === */
@keyframes shine { to { background-position: 200% center; } }

/* WRAPPER - Perfect Centering */
.maintenance-cockpit {
    min-height: 100vh;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 120px 20px 40px; /* More top padding for navbar */
    box-sizing: border-box;
    position: relative;
    background: #020617;
}

/* Tech Grid Background */
.maintenance-cockpit::before {
    content: "";
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background-image:
        linear-gradient(rgba(14, 165, 233, 0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(14, 165, 233, 0.05) 1px, transparent 1px);
    background-size: 40px 40px;
    mask-image: radial-gradient(circle at center, black 40%, transparent 90%);
    -webkit-mask-image: radial-gradient(circle at center, black 40%, transparent 90%);
    pointer-events: none;
    z-index: 1;
}

/* Floating Orbs */
.maintenance-cockpit::after {
    content: "";
    position: fixed;
    top: -100px; right: -100px;
    width: 500px; height: 500px;
    background: radial-gradient(circle, rgba(14, 165, 233, 0.15), transparent 70%);
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
    z-index: 0;
    animation: orbFloat 20s ease-in-out infinite;
}

@keyframes orbFloat { 
    0%, 100% { transform: translate(0, 0); } 
    50% { transform: translate(30px, -30px); } 
}

/* --- GLASS PANEL DYNAMIC --- */
.cockpit-panel {
    position: relative;
    z-index: 10;
    width: 100%;
    max-width: 1300px;
    /* More transparent glass effect */
    background: rgba(2, 6, 23, 0.6);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 24px;
    padding: 40px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 
        0 50px 100px -20px rgba(0,0,0,0.5),
        inset 0 0 0 1px rgba(255,255,255,0.05);
    display: flex;
    flex-direction: column;
    gap: 30px;
}
/* Golden Shine Border for Main Panel */
.cockpit-panel::before {
    content: ""; position: absolute; inset: 0; border-radius: 24px; padding: 1px;
    background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c); 
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor; mask-composite: exclude;
    z-index: 2; pointer-events: none; background-size: 200% auto; animation: shine 5s linear infinite;
}

/* HEADER */
.cockpit-header { 
    text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; margin-bottom: 10px;
}
.cockpit-title {
    font-size: 2rem; font-weight: 800; color: white; margin: 0 0 5px 0; letter-spacing: -0.5px;
}
.cockpit-subtitle { color: #94a3b8; font-size: 0.95rem; }

/* --- FORM STRUCTURE --- */
.cockpit-form {
    display: grid;
    grid-template-columns: 350px 1fr; /* Fixed Sidebar + Fluid Content */
    gap: 25px; /* Restored original gap */
}

/* MODULES */
.form-module {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    border-radius: 16px;
    padding: 20px; /* Restored original padding */
    height: 100%;
    display: flex; flex-direction: column; gap: 20px; /* Restored original gap */
    position: relative; /* For shine border */
}
/* Golden Shine Border for Modules */
.form-module::before {
    content: ""; position: absolute; inset: 0; border-radius: 16px; padding: 1px;
    background: linear-gradient(to right, rgba(255,255,255,0.1), rgba(255,255,255,0.3), rgba(255,255,255,0.1)); 
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor; mask-composite: exclude;
    z-index: 2; pointer-events: none;
}
.module-header {
    display: flex; align-items: center; gap: 10px;
    color: #cfaa5d; font-weight: 700; font-size: 1.1rem;
    padding-bottom: 15px; border-bottom: 1px solid rgba(255,255,255,0.05);
}

/* LEFT SIDEBAR (Client & Type) */
.sidebar-stack { display: flex; flex-direction: column; gap: 25px; height: 100%; }

/* MACHINE SELECTOR (Vertical in Sidebar) */
.machine-selector-stack { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.machine-card-small {
    background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.1);
    border-radius: 12px; padding: 15px; text-align: center; cursor: pointer; transition: 0.3s;
}
.machine-card-small i { display: block; font-size: 1.5rem; margin-bottom: 8px; color: #64748b; }
.machine-card-small span { font-size: 0.85rem; font-weight: 700; color: #94a3b8; }
input:checked + .machine-card-small { background: rgba(207, 170, 93, 0.1); border-color: #cfaa5d; }
input:checked + .machine-card-small i { color: #cfaa5d; }
input:checked + .machine-card-small span { color: white; }

/* MAIN CONTENT (Fault & Uploads) */
.main-content { display: flex; flex-direction: column; gap: 25px; }

/* INPUTS */
.glass-inp {
    width: 100%; background: rgba(0, 0, 0, 0.3); border: 1px solid rgba(255,255,255,0.1);
    padding: 14px; border-radius: 10px; color: white; font-family: 'Tajawal'; transition: 0.3s;
}
.glass-inp:focus { border-color: #cfaa5d; background: black; outline: none; }
.glass-inp::placeholder { color: rgba(255,255,255,0.3); }

.inp-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }

/* UPLOADS */
.upload-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.upload-box {
    border: 1px dashed rgba(255,255,255,0.2); border-radius: 12px; padding: 15px;
    text-align: center; position: relative; background: rgba(0,0,0,0.2);
}
.upload-box:hover { border-color: #cfaa5d; background: rgba(207, 170, 93, 0.05); }
.upload-box input { position: absolute; inset: 0; opacity: 0; cursor: pointer; width: 100%; }

/* FOOTER */
.form-footer { margin-top: 10px; }
.gold-btn-wide {
    width: 100%; padding: 18px; font-size: 1.1rem; font-weight: 800;
    background: linear-gradient(135deg, #b48811 0%, #ecd76d 50%, #b48811 100%);
    border: none; border-radius: 12px; cursor: pointer; color: #000;
    box-shadow: 0 10px 30px -10px rgba(234, 179, 8, 0.5); transition: 0.3s;
}
.gold-btn-wide:hover { transform: translateY(-3px); box-shadow: 0 20px 40px -10px rgba(234, 179, 8, 0.7); }

/* RESPONSIVE */
@media (max-width: 1000px) {
    .maintenance-cockpit { 
        padding: 100px 0 20px 0; 
        height: auto; 
        min-height: auto; 
        display: flex; 
        flex-direction: column; 
        align-items: center; 
    }
    .cockpit-panel { 
        padding: 25px; 
        width: 100%; /* Full width to avoid scrollbar issues */
        margin-top: 0; 
    }
    
    .cockpit-form { grid-template-columns: 1fr; }
    
    .sidebar-stack { order: 1; margin-bottom: 20px; }
    .main-content { order: 2; }
    
    .cockpit-title { font-size: 1.6rem; }
    
    /* Ensure forms aren't too wide on tablets but full on mobile */
    .form-module { padding: 15px; }
    .inp-row { grid-template-columns: 1fr; }
}
</style>

<!-- Page Loader -->
<div class="page-loader" id="page-loader">
    <div class="loader-spinner"></div>
</div>

<div class="maintenance-cockpit">
    <div class="cockpit-panel">
        
        <div class="cockpit-header">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <div style="text-align: right;">
                    <h1 class="cockpit-title" style="margin-bottom: 5px;">مركز خدمة العملاء</h1>
                    <p class="cockpit-subtitle">نظام تذاكر الصيانة الذكي</p>
                </div>
                <button type="button" onclick="openTrackModal()" class="track-btn">
                    <i class="fas fa-search"></i> تتبع حالة تذكرة سابقة
                </button>
            </div>
        </div>

        <style>
            .track-btn {
                display: inline-flex; align-items: center; gap: 8px;
                background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
                padding: 8px 20px; border-radius: 20px; color: #cfaa5d;
                cursor: pointer; font-family: inherit;
                text-decoration: none; font-size: 0.9rem; transition: 0.3s;
            }
            .track-btn:hover {
                background: rgba(207, 170, 93, 0.1); border-color: #cfaa5d; color: #fff; transform: translateY(-2px);
            }

            /* MODAL STYLES */
            .modal-overlay {
                position: fixed; inset: 0; z-index: 9999;
                background: rgba(0, 0, 0, 0.8); backdrop-filter: blur(8px);
                display: none; justify-content: center; align-items: center;
                opacity: 0; transition: opacity 0.3s;
            }
            .modal-overlay.active { display: flex; opacity: 1; }
            
            .modal-glass {
                background: rgba(15, 23, 42, 0.95);
                border: 1px solid rgba(207, 170, 93, 0.3);
                box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                border-radius: 24px;
                padding: 40px;
                width: 90%; max-width: 500px;
                text-align: center;
                transform: scale(0.95); transition: transform 0.3s;
                position: relative;
            }
            .modal-overlay.active .modal-glass { transform: scale(1); }
            
            .modal-close {
                position: absolute; top: 20px; left: 20px; /* RTL switch if needed */
                background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;
                transition: 0.3s;
            }
            .modal-close:hover { color: #ef4444; transform: rotate(90deg); }
            
            .modal-icon {
                font-size: 3rem; color: #cfaa5d; margin-bottom: 20px;
                filter: drop-shadow(0 0 15px rgba(207, 170, 93, 0.3));
            }
        </style>

        <!-- TRACK MODAL -->
        <div id="trackModal" class="modal-overlay">
            <div class="modal-glass">
                <button type="button" class="modal-close" onclick="closeTrackModal()">&times;</button>
                
                <div class="modal-icon"><i class="fas fa-search-location"></i></div>
                <h2 style="color:white; margin-bottom:10px; font-weight:800;">تتبع حالة التذكرة</h2>
                <p style="color:#94a3b8; margin-bottom:30px;">أدخل رقم التذكرة للاستعلام عن حالتها الحالية</p>
                
                <?php if (isset($_GET['search_error']) && $_GET['search_error'] == 'not_found'): ?>
                <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.5); color: #fca5a5; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem;">
                    <i class="fas fa-exclamation-circle"></i> لم يتم العثور على تذكرة بهذا الرقم
                </div>
                <?php endif; ?>
                
                <form action="<?= BASE_URL ?>/maintenance/search" method="GET">
                    <div style="position:relative; margin-bottom:20px;">
                        <input type="text" name="ticket_number" class="glass-inp" 
                               value="<?= htmlspecialchars($_GET['ticket'] ?? '') ?>"
                               placeholder="رقم التذكرة (مثال: TK...)" required 
                               style="text-align:center; font-size:1.1rem; letter-spacing:1px; border-color: rgba(207, 170, 93, 0.3);">
                    </div>
                    
                    <button type="submit" class="gold-btn-wide" style="padding:12px;">بحث الآن</button>
                </form>
            </div>
        </div>

        <script>
            function openTrackModal() {
                const modal = document.getElementById('trackModal');
                modal.classList.add('active');
            }
            function closeTrackModal() {
                const modal = document.getElementById('trackModal');
                modal.classList.remove('active');
                // Remove URL params to clean up
                if (window.history.replaceState) {
                    const url = new URL(window.location);
                    url.searchParams.delete('search_error');
                    url.searchParams.delete('ticket');
                    window.history.replaceState(null, '', url);
                }
            }
            // Close on outside click
            document.getElementById('trackModal').addEventListener('click', (e) => {
                if(e.target === e.currentTarget) closeTrackModal();
            });
            
            // Auto open if error
            <?php if (isset($_GET['search_error'])): ?>
            document.addEventListener('DOMContentLoaded', function() {
                openTrackModal();
            });
            <?php endif; ?>
        </script>

        <?php if (!empty($success)): ?>
            <div style="background: rgba(16, 185, 129, 0.1); padding: 10px; color: #34d399; margin-bottom: 25px; border-radius: 12px; text-align: center; border: 1px solid rgba(16, 185, 129, 0.2);">
                <i class="fas fa-check-circle" style="margin-left:5px;"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div style="background: rgba(239, 68, 68, 0.1); padding: 10px; color: #f87171; margin-bottom: 25px; border-radius: 12px; text-align: center; border: 1px solid rgba(239, 68, 68, 0.2);">
                <i class="fas fa-triangle-exclamation" style="margin-left:5px;"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_URL ?>/maintenance/submit" method="POST" enctype="multipart/form-data" class="cockpit-form">
            <?= $csrf_field ?? App\Services\Security::csrfField() ?>

            <!-- LEFT SIDEBAR: IDENTIFICATION -->
            <div class="sidebar-stack">
                
                <!-- TYPE -->
                <div class="form-module">
                    <div class="module-header"><i class="fas fa-microchip"></i> نوع الجهاز</div>
                    <div class="machine-selector-stack">
                        <label class="machine-option" style="position:relative">
                            <input type="radio" name="machine_type" value="copier" checked style="position:absolute; opacity:0; pointer-events:none;">
                            <div class="machine-card-small">
                                <i class="fas fa-print"></i> <span>ماكينة تصوير</span>
                            </div>
                        </label>
                        <label class="machine-option" style="position:relative">
                            <input type="radio" name="machine_type" value="printer" style="position:absolute; opacity:0; pointer-events:none;">
                            <div class="machine-card-small">
                                <i class="fas fa-laptop-code"></i> <span>طابعة</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- CLIENT -->
                <div class="form-module" style="flex: 1;">
                    <div class="module-header"><i class="fas fa-address-card"></i> بيانات التواصل</div>
                    <div style="display:flex; flex-direction:column; gap:15px;">
                        <input type="text" name="customer_name" class="glass-inp" placeholder="اسم العميل / الشركة" required>
                        <input type="tel" name="customer_phone" class="glass-inp" placeholder="رقم الموبايل" required style="direction: rtl;">
                        
                        <!-- ADDRESS WITH GEO -->
                        <div style="position: relative;">
                            <input type="text" name="customer_address" id="addressInp" class="glass-inp" placeholder="العنوان بالتفصيل" required style="padding-left: 40px;">
                            <button type="button" id="geoBtn" onclick="getLocation()" style="position: absolute; left: 5px; top: 50%; transform: translateY(-50%); background: none; border: none; color: #cfaa5d; cursor: pointer; font-size: 1.2rem;" title="تحديد موقعي الآن">
                                <i class="fas fa-location-crosshairs"></i>
                            </button>
                        </div>
                        
                        <!-- HIDDEN LOCATION FIELDS -->
                        <input type="hidden" name="latitude" id="lat">
                        <input type="hidden" name="longitude" id="lng">
                        <div id="geoStatus" style="font-size: 0.8rem; color: #94a3b8; display: none; align-items: center; gap: 5px;"></div>
                    </div>
                </div>

            </div>

            <!-- RIGHT MAIN: TECHNICAL -->
            <div class="main-content">
                
                <div class="form-module" style="flex: 1;">
                    <div class="module-header"><i class="fas fa-cogs"></i> تفاصيل الصيانة</div>
                    
                    <div class="inp-row">
                        <input type="text" name="machine_model" class="glass-inp" placeholder="موديل الماكينة (مثال: Ricoh 305)">
                        <input type="text" name="error_code" class="glass-inp" placeholder="كود العطل (مثال: SC 542)">
                    </div>

                    <textarea name="fault_description" class="glass-inp" rows="5" style="resize: none; margin-top: 15px;" placeholder="وصف تفصيلي للمشكلة..." required></textarea>

                    <div style="margin-top: 20px;">
                        <div style="color:rgba(255,255,255,0.5); font-size:0.9rem; margin-bottom:10px;"><i class="fas fa-paperclip"></i> إرفاق صور (مهم جداً لتشخيص العطل)</div>
                        <div class="upload-grid">
                            <div class="upload-box">
                                <input type="file" name="model_image" accept="image/*" onchange="this.nextElementSibling.nextElementSibling.innerText = this.files[0].name">
                                <i class="fas fa-camera" style="color: #cfaa5d; font-size: 1.5rem; margin-bottom:5px;"></i>
                                <div style="font-size:0.8rem; color:#94a3b8;">صورة الملصق الخلفي</div>
                            </div>
                            <div class="upload-box">
                                <input type="file" name="screen_image" accept="image/*" onchange="this.nextElementSibling.nextElementSibling.innerText = this.files[0].name">
                                <i class="fas fa-desktop" style="color: #cfaa5d; font-size: 1.5rem; margin-bottom:5px;"></i>
                                <div style="font-size:0.8rem; color:#94a3b8;">صورة شاشة العطل</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-footer">
                    <input type="hidden" name="fault_type" value="new">
                    <button type="submit" class="gold-btn-wide">تسجيل بلاغ الصيانة</button>
                </div>

            </div>

        </form>
    </div>
</div>

<script>
    // Hide page loader when content is ready
    window.addEventListener('load', function() {
        const loader = document.getElementById('page-loader');
        if (loader) {
            loader.classList.add('hidden');
        }
    });

    function getLocation() {
        const btn = document.getElementById('geoBtn');
        const status = document.getElementById('geoStatus');
        const latInp = document.getElementById('lat');
        const lngInp = document.getElementById('lng');

        if (!navigator.geolocation) {
            alert("المتصفح لا يدعم تحديد الموقع.");
            return;
        }

        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        status.style.display = 'flex';
        status.innerHTML = 'جاري تحديد الموقع...';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                latInp.value = position.coords.latitude;
                lngInp.value = position.coords.longitude;
                
                // Visual Success
                btn.innerHTML = '<i class="fas fa-check-circle" style="color:#4ade80"></i>';
                status.innerHTML = '<i class="fas fa-check" style="color:#4ade80"></i> تم حفظ إحداثيات الموقع بنجاح';
                status.style.color = '#4ade80';
                
                // Optional: Reverse Geocoding via OpenStreetMap (Nominatim) could go here to fill address
            },
            (error) => {
                btn.innerHTML = '<i class="fas fa-triangle-exclamation" style="color:#f87171"></i>';
                let msg = "فشل تحديد الموقع.";
                if(error.code == 1) msg = "تم رفض إذن الوصول للموقع.";
                status.innerHTML = msg;
                status.style.color = '#f87171';
            }
        );
    }
</script>

<?php
$content = ob_get_clean();
// We use a blank layout or custom include to avoid double headers if the layout adds one.
// But since the user wants the header, we'll include public_layout but handle the spacing via padding-top.
require_once VIEWS_PATH . '/layouts/public_layout.php';
?>
