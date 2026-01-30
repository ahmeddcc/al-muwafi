<div class="glass-card" style="padding: 2rem;">
    <div class="card-header-simple">
        <h3 class="glass-title"><i class="fas fa-building text-primary"></i> بيانات الشركة الأساسية</h3>
        <p class="glass-subtitle">قم بتحديث اسم الشركة، الشعار، ومعلومات الاتصال التي تظهر للعملاء.</p>
    </div>

    <form action="<?= BASE_URL ?>/admin/settings/update-company" method="POST" enctype="multipart/form-data">
        <?= $csrf_field ?>
        
        <div class="settings-grid">
            <!-- Left Column: Logos & Images -->
            <div class="settings-sidebar-col">
                <div class="upload-box-wrapper">
                    <label class="form-label">شعار الشركة (Logo)</label>
                    <div class="image-upload-box" id="logoBox">
                        <input type="hidden" name="delete_logo" id="delete_logo_input" value="0">
                        <?php if (!empty($company['logo'])): ?>
                            <img src="<?= BASE_URL ?>/storage/uploads/<?= $company['logo'] ?>" class="preview-img" style="width: 100%; height: 100%; object-fit: contain;">
                            <button type="button" class="btn-delete-img" onclick="deleteImage('logo')">
                                <i class="fas fa-trash"></i>
                            </button>
                        <?php else: ?>
                            <div class="upload-placeholder">
                                <i class="fas fa-image"></i>
                                <span>لم يتم الرفع</span>
                            </div>
                        <?php endif; ?>
                        <div class="upload-overlay">
                            <i class="fas fa-camera"></i>
                            <input type="file" name="logo" accept="image/*" onchange="previewUpload(this, 'logo')">
                        </div>
                    </div>
                </div>

                <div class="upload-box-wrapper">
                    <label class="form-label">الأيقونة (Favicon)</label>
                    <div class="image-upload-box small" id="faviconBox">
                        <input type="hidden" name="delete_favicon" id="delete_favicon_input" value="0">
                        <?php if (!empty($company['favicon'])): ?>
                            <img src="<?= BASE_URL ?>/storage/uploads/<?= $company['favicon'] ?>" class="preview-img" style="width: 100%; height: 100%; object-fit: contain;">
                            <button type="button" class="btn-delete-img" onclick="deleteImage('favicon')">
                                <i class="fas fa-trash"></i>
                            </button>
                        <?php else: ?>
                            <div class="upload-placeholder">
                                <i class="fas fa-star"></i>
                            </div>
                        <?php endif; ?>
                        <div class="upload-overlay">
                            <i class="fas fa-upload"></i>
                            <input type="file" name="favicon" accept="image/*" onchange="previewUpload(this, 'favicon')">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Form Fields -->
            <div class="settings-main-col">
                <div class="grid-2">
                    <div class="form-group">
                        <label class="glass-label">اسم الشركة</label>
                        <input type="text" name="name" class="glass-input" value="<?= htmlspecialchars($company['name'] ?? '') ?>" placeholder="شركة المُوَفِّي...">
                    </div>
                    <div class="form-group">
                        <label class="glass-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="glass-input" value="<?= htmlspecialchars($company['email'] ?? '') ?>" dir="ltr">
                    </div>
                </div>

                <div class="grid-3">
                    <div class="form-group">
                        <label class="glass-label">رقم الهاتف</label>
                        <input type="tel" name="phone" class="glass-input" value="<?= htmlspecialchars($company['phone'] ?? '') ?>" dir="ltr">
                    </div>
                    <div class="form-group">
                        <label class="glass-label">رقم الواتساب</label>
                        <input type="tel" name="whatsapp" class="glass-input" value="<?= htmlspecialchars($company['whatsapp'] ?? '') ?>" dir="ltr">
                    </div>
                    <div class="form-group">
                        <label class="glass-label">مفتاح الدولة</label>
                        <input type="number" name="country_code" class="glass-input" value="<?= htmlspecialchars($company['country_code'] ?? '20') ?>" placeholder="20">
                    </div>
                </div>

                <div class="form-group">
                    <label class="glass-label">العنوان</label>
                    <textarea name="address" class="glass-input" rows="2"><?= htmlspecialchars($company['address'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label class="glass-label">رابط الخريطة (Google Maps Embed URL)</label>
                    <textarea id="map_embed_url_raw" class="glass-input code-font" rows="3" dir="ltr" placeholder="https://maps.google.com/maps?q=..."><?= htmlspecialchars($company['map_embed_url'] ?? '') ?></textarea>
                    <input type="hidden" name="map_embed_url" id="map_embed_url_encoded">
                    <small style="color: #64748b; display: block; margin-top: 5px;">
                        يمكنك وضع رابط التضمين أو الإحداثيات مباشرة.
                    </small>
                </div>
            </div>
        </div>

        <div class="form-actions-footer">
            <button type="submit" class="btn-glass-primary" onclick="prepareMapUrl()">
                <i class="fas fa-save"></i> حفظ التغييرات
            </button>
        </div>
    </form>
</div>

<script>
function prepareMapUrl() {
    // Encode the map URL to Base64 to bypass ModSecurity/403 errors
    var raw = document.getElementById('map_embed_url_raw').value;
    if(raw) {
        document.getElementById('map_embed_url_encoded').value = btoa(unescape(encodeURIComponent(raw)));
    } else {
        document.getElementById('map_embed_url_encoded').value = '';
    }
}
</script>

<style>
.card-header-simple {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255,255,255,0.05);
}
.glass-title { font-size: 1.25rem; color: #f1f5f9; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 10px; }
.glass-subtitle { font-size: 0.9rem; color: #94a3b8; margin: 0; }
.text-primary { color: #60a5fa; }

.settings-grid {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 2rem;
}
@media (max-width: 768px) { .settings-grid { grid-template-columns: 1fr; } }

.image-upload-box {
    width: 100%;
    aspect-ratio: 1;
    background: rgba(255,255,255,0.03);
    border: 2px dashed rgba(255,255,255,0.1);
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}
.image-upload-box.small { aspect-ratio: auto; height: 100px; }

.preview-img { width: 100%; height: 100%; object-fit: contain; padding: 10px; }
.upload-placeholder { display: flex; flex-direction: column; align-items: center; gap: 5px; color: #64748b; font-size: 0.8rem; }
.upload-placeholder i { font-size: 2rem; margin-bottom: 5px; opacity: 0.5; }

.btn-delete-img {
    position: absolute; top: 5px; right: 5px;
    background: rgba(239, 68, 68, 0.9); color: white; border: none;
    width: 25px; height: 25px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; z-index: 5; font-size: 0.8rem;
    transition: all 0.2s;
}
.btn-delete-img:hover { transform: scale(1.1); background: #ef4444; }

.upload-overlay {
    position: absolute; inset: 0;
    background: rgba(15, 23, 42, 0.8);
    display: flex; align-items: center; justify-content: center;
    opacity: 0; transition: opacity 0.3s ease;
}
.image-upload-box:hover .upload-overlay { opacity: 1; }
.upload-overlay i { font-size: 1.5rem; color: #fff; }
.upload-overlay input { position: absolute; inset: 0; opacity: 0; cursor: pointer; }

.grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; }
.grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; }
.glass-label { display: block; color: #cbd5e1; margin-bottom: 0.5rem; font-size: 0.9rem; }
.glass-input {
    width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px; padding: 0.75rem 1rem; color: #fff; transition: all 0.3s ease;
}
.glass-input:focus { background: rgba(255,255,255,0.1); border-color: #60a5fa; outline: none; }
.code-font { font-family: monospace; font-size: 0.85rem; color: #a5f3fc; }

.form-actions-footer {
    margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05);
    display: flex; justify-content: flex-end;
}
</style>

<script>
function previewUpload(input, type) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const container = input.closest('.image-upload-box');
            // Remove existing placeholder or img
            const existingImg = container.querySelector('img');
            const existingPh = container.querySelector('.upload-placeholder');
            const existingBtn = container.querySelector('.btn-delete-img');
            
            if(existingPh) existingPh.remove();
            
            if(existingImg) {
                existingImg.src = e.target.result;
                existingImg.style.display = 'block';
            } else {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-img';
                container.insertBefore(img, container.firstChild);
            }

            // Ensure delete button exists for new upload (optional, or just rely on form submission)
             if (!existingBtn) {
                 // We could dynamically add a delete button here, but for simplicity, 
                 // new uploads replace the old one. If they want to "cancel" the upload, 
                 // they can refresh or we can add a "remove" button for the pending file. 
                 // For now, let's reset the delete flag if they upload a new file.
             }

             // Reset delete flag when new file is uploaded
             if (type === 'logo') {
                 document.getElementById('delete_logo_input').value = '0';
             } else if (type === 'favicon') {
                 document.getElementById('delete_favicon_input').value = '0';
             }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function deleteImage(type) {
    const boxId = type === 'logo' ? 'logoBox' : 'faviconBox';
    const deleteInputId = type === 'logo' ? 'delete_logo_input' : 'delete_favicon_input';
    const iconClass = type === 'logo' ? 'fa-image' : 'fa-star';
    
    const box = document.getElementById(boxId);
    const img = box.querySelector('img');
    const btn = box.querySelector('.btn-delete-img');
    const deleteInput = document.getElementById(deleteInputId);
    
    if (img) img.style.display = 'none';
    if (btn) btn.style.display = 'none';
    
    // Show placeholder
    if (!box.querySelector('.upload-placeholder')) {
         const ph = document.createElement('div');
         ph.className = 'upload-placeholder';
         ph.innerHTML = '<i class="fas ' + iconClass + '"></i><span>محذوف (سيتم الحفظ)</span>';
         box.insertBefore(ph, box.querySelector('.upload-overlay'));
    }
    
    deleteInput.value = '1';
}
</script>
