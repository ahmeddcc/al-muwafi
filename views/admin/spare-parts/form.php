<?php
/**
 * نموذج إضافة/تعديل قطعة غيار
 * Refactored for Glassmorphism Identity v2.0
 */

use App\Services\Security;

$currentPage = 'spare-parts';
$isEdit = !empty($sparePart);
$title = $isEdit ? 'تعديل قطعة الفنية' : 'إضافة قطعة جديدة';
$actionUrl = BASE_URL . '/admin/spare-parts/' . ($isEdit ? 'update/' . $sparePart['id'] : 'store');

// Data extraction with safe fallbacks
$tags = $currentTags ?? [];
$compatibleProps = $compatibleProducts ?? [];
$galleryItems = $gallery ?? [];

ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-cogs"></i>
        <?= htmlspecialchars($title) ?>
    </h1>
    <a href="<?= BASE_URL ?>/admin/spare-parts" class="btn-icon-glass" title="عودة للقائمة">
        <i class="fas fa-arrow-right"></i>
    </a>
</div>

<!-- Main Glass Card -->
<div class="glass-card" style="padding: 0; overflow: visible;">
    
    <!-- TABS NAVIGATION -->
    <div class="glass-tabs-nav" style="padding: 0 1.5rem; margin-top: 1rem;">
        <button type="button" class="glass-tab-btn active" onclick="switchTab('basic')">
            <i class="fas fa-info-circle"></i> المعلومات الأساسية
        </button>
        <button type="button" class="glass-tab-btn" onclick="switchTab('compatibility')">
            <i class="fas fa-microchip"></i> التوافقية والماكينات
        </button>
        <button type="button" class="glass-tab-btn" onclick="switchTab('gallery')">
            <i class="fas fa-photo-video"></i> المعرض الفني
        </button>
        <button type="button" class="glass-tab-btn" onclick="switchTab('meta')">
            <i class="fas fa-tags"></i> التصنيف والبيانات
        </button>
    </div>

    <!-- FORM START -->
    <form id="partForm" action="<?= $actionUrl ?>" method="POST" enctype="multipart/form-data" style="padding: 0 2rem 2rem 2rem;">
        <?= Security::csrfField() ?>

        <!-- 1. BASIC INFO TAB -->
        <div id="tab-basic" class="tab-content active">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
                
                <!-- Left Column: Inputs -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    
                    <div>
                        <label class="form-label" style="color: var(--text-muted); margin-bottom: 0.5rem; display: block;">اسم القطعة (عربي/إنجليزي) <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="name" class="search-input" value="<?= htmlspecialchars($sparePart['name'] ?? '') ?>" required placeholder="مثال: وحدة ديفلوبر ريكو أصلي">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div>
                            <label class="form-label" style="color: var(--text-muted); margin-bottom: 0.5rem; display: block;">رقم القطعة (Part Number) <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="part_number" class="search-input" value="<?= htmlspecialchars($sparePart['part_number'] ?? '') ?>" required style="font-family: monospace; letter-spacing: 1px; color: var(--neon-blue);">
                        </div>
                        <div>
                            <label class="form-label" style="color: var(--text-muted); margin-bottom: 0.5rem; display: block;">القسم الرئيسي</label>
                            <select name="category_id" class="search-input" style="appearance: auto;">
                                <option value="">-- اختر القسم --</option>
                                <?php foreach (($categories ?? []) as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($sparePart['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-check form-switch" style="padding-right: 0; margin-top: 1rem;">
                        <label class="form-check-label" for="isActive">
                            <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" <?= ($sparePart['is_active'] ?? 1) ? 'checked' : '' ?> style="margin-left: 10px;">
                            <span style="color: #fff; font-weight: 500;">تفعيل القطعة في الكتالوج</span>
                        </label>
                    </div>

                </div>

                <!-- Right Column: Main Image -->
                <div>
                     <label class="form-label" style="color: var(--text-muted); margin-bottom: 0.5rem; display: block; text-align: center;">الصورة الرئيسية</label>
                     <div class="glass-uploader" onclick="document.getElementById('main-image').click()" style="padding: 1rem; height: 250px; display: flex; align-items: center; justify-content: center;">
                        
                        <img id="preview-main" src="<?= ($isEdit && !empty($sparePart['image'])) ? BASE_URL . '/storage/uploads/' . htmlspecialchars($sparePart['image']) : '' ?>" 
                             style="max-width: 100%; max-height: 100%; border-radius: 8px; display: <?= ($isEdit && !empty($sparePart['image'])) ? 'block' : 'none' ?>; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                        
                        <div class="placeholder" style="display: <?= ($isEdit && !empty($sparePart['image'])) ? 'none' : 'block' ?>;">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p style="margin: 0; color: var(--text-muted);">اضغط لرفع صورة</p>
                        </div>

                     </div>
                     <input type="file" name="image" id="main-image" hidden accept="image/*" onchange="previewImage(this, 'preview-main')">
                </div>
            </div>
        </div>

        <!-- 2. COMPATIBILITY TAB -->
        <div id="tab-compatibility" class="tab-content">
            <div style="background: rgba(255, 255, 255, 0.02); border-radius: 12px; padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <h3 style="margin: 0; font-size: 1.1rem; color: #fff;">الماكينات المتوافقة</h3>
                        <p style="margin: 5px 0 0; font-size: 0.85rem; color: var(--text-muted);">حدد الموديلات التي تعمل عليها هذه القطعة</p>
                    </div>
                    <button type="button" class="btn-icon-glass" onclick="openProductModal()" title="إضافة ماكينة" style="width: auto; padding: 0 1rem; gap: 8px;">
                        <i class="fas fa-plus"></i> إضافة موديل
                    </button>
                </div>

                <div id="compatible-list" class="compat-products-grid">
                    <?php foreach($compatibleProps as $prod): ?>
                    <div class="compat-item-card" id="compat-prod-<?= $prod['id'] ?>">
                        <input type="hidden" name="compatible_products[]" value="<?= $prod['id'] ?>">
                        <button type="button" class="compat-remove-btn" onclick="removeCompat(this)"><i class="fas fa-times"></i></button>
                        <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($prod['thumbnail'] ?? 'default.png') ?>" class="compat-item-thumb" onerror="this.src='<?= BASE_URL ?>/assets/images/no-image.png'">
                        <div class="compat-item-info">
                            <strong><?= htmlspecialchars($prod['model']) ?></strong>
                            <span><?= htmlspecialchars($prod['name']) ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div id="no-compat-msg" style="text-align: center; color: var(--text-muted); padding: 3rem; <?= !empty($compatibleProps) ? 'display: none;' : '' ?>">
                    <i class="fas fa-microchip" style="font-size: 3rem; opacity: 0.2; margin-bottom: 1rem;"></i>
                    <p>لا توجد ماكينات مرتبطة حالياً</p>
                </div>
            </div>
        </div>

        <!-- 3. GALLERY TAB -->
        <div id="tab-gallery" class="tab-content">
            <div class="glass-uploader" onclick="document.getElementById('gallery-input').click()">
                <i class="fas fa-images"></i>
                <h3 style="color: #fff; margin: 0 0 0.5rem;">ارفع صور إضافية أو ملفات PDF</h3>
                <p style="color: var(--text-muted); margin: 0;">يدعم السحب والإفلات أو الضغط للاختيار</p>
                <input type="file" name="gallery[]" id="gallery-input" multiple hidden onchange="handleGalleryFiles(this)">
            </div>

            <div id="gallery-preview" class="glass-media-grid">
                <?php foreach($galleryItems as $item): ?>
                <div class="glass-media-item" id="media-<?= $item['id'] ?>">
                    <?php if($item['file_type'] == 'pdf'): ?>
                        <div style="height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #1e293b; color: #ef4444;">
                            <i class="fas fa-file-pdf" style="font-size: 2rem; margin-bottom: 5px;"></i>
                            <span style="font-size: 0.7rem;">PDF DOC</span>
                        </div>
                    <?php else: ?>
                        <img src="<?= BASE_URL ?>/storage/uploads/<?= htmlspecialchars($item['file_path']) ?>">
                    <?php endif; ?>
                    <div class="glass-media-actions" onclick="deleteMedia(<?= $item['id'] ?>)">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 4. META & TAGS TAB -->
        <div id="tab-meta" class="tab-content">
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <div>
                    <label class="form-label" style="color: var(--text-muted); margin-bottom: 0.5rem; display: block;">الوسوم (Smart Tags)</label>
                    <div class="glass-chips-container" onclick="document.getElementById('tag-input').focus()">
                        <div id="tags-list" style="display: contents;">
                            <?php foreach($tags as $tag): ?>
                            <span class="glass-chip">
                                <i onclick="this.parentElement.remove()" class="fas fa-times"></i>
                                <?= htmlspecialchars($tag) ?>
                                <input type="hidden" name="tags[]" value="<?= htmlspecialchars($tag) ?>">
                            </span>
                            <?php endforeach; ?>
                        </div>
                        <input type="text" id="tag-input" class="glass-chips-input" placeholder="اكتب واضغط Enter..." onkeydown="handleTagInput(event)">
                    </div>
                    <small style="color: var(--text-muted); display: block; margin-top: 5px;">استخدم كلمات مفتاحية مثل: ياباني، أصلي، 220V</small>
                </div>

                <div>
                    <label class="form-label" style="color: var(--text-muted); margin-bottom: 0.5rem; display: block;">الوصف الفني التفصيلي</label>
                    <textarea name="description" class="search-input" rows="6" placeholder="اكتب المواصفات الفنية، تعليمات التركيب، أو أي ملاحظات أخرى..." style="resize: vertical; line-height: 1.6;"><?= htmlspecialchars($sparePart['description'] ?? '') ?></textarea>
                </div>

                <div>
                    <label class="form-label" style="color: var(--text-muted); margin-bottom: 0.5rem; display: block;">ترتيب العرض</label>
                    <input type="number" name="sort_order" class="search-input" value="<?= $sparePart['sort_order'] ?? 0 ?>" style="width: 150px;">
                </div>

            </div>
        </div>

        <!-- Form Actions (Sticky Bottom) -->
        <div style="margin-top: 2rem; border-top: 1px solid var(--glass-border); padding-top: 1.5rem; display: flex; justify-content: flex-end; gap: 1rem;">
            <a href="<?= BASE_URL ?>/admin/spare-parts" class="glass-tab-btn" style="border: 1px solid var(--glass-border); border-radius: 8px;">إلغاء</a>
            <button type="submit" class="glass-tab-btn active" style="background: rgba(14, 165, 233, 0.1); border: 1px solid var(--neon-blue); border-radius: 8px;">
                <i class="fas fa-save"></i> حفظ التغييرات
            </button>
        </div>

    </form>
</div>

<!-- ================= MODAL: PRODUCT SELECTOR ================= -->
<div id="productModal" class="modal-overlay" style="z-index: 9999;">
    <div class="modal-content" style="max-width: 600px; background: #0f172a; border: 1px solid var(--glass-border);">
        <div class="modal-header" style="border-bottom: 1px solid var(--glass-border);">
            <h3 style="color: #fff; margin: 0;">اختر الماكينات المتوافقة</h3>
            <button onclick="closeProductModal()" style="background: none; border: none; color: var(--text-muted); font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        <div class="modal-body" style="padding: 1rem;">
            <input type="text" id="prod-search" class="search-input" placeholder="ابحث باسم الموديل..." oninput="searchProducts(this.value)" style="margin-bottom: 1rem;">
            
            <div id="products-grid" class="compat-products-grid" style="max-height: 400px; overflow-y: auto; margin-top: 0;">
                <!-- Results loaded here -->
                <div style="grid-column: 1/-1; text-align: center; color: var(--text-muted); padding: 2rem;">ابدأ البحث لعرض النتائج</div>
            </div>
        </div>
    </div>
</div>

<script>
// --- TABS LOGIC ---
function switchTab(tabName) {
    document.querySelectorAll('.glass-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
    
    event.currentTarget.classList.add('active');
    document.getElementById('tab-' + tabName).classList.add('active');
}

// --- IMAGE PREVIEW ---
function previewImage(input, imgId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(imgId);
            img.src = e.target.result;
            img.style.display = 'block';
            img.nextElementSibling.style.display = 'none'; // Hide placeholder
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// --- TAGS LOGIC ---
function handleTagInput(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        const val = e.target.value.trim();
        if(val) {
            addTag(val);
            e.target.value = '';
        }
    }
}

function addTag(name) {
    const existing = Array.from(document.querySelectorAll('input[name="tags[]"]')).map(i => i.value);
    if(existing.includes(name)) return;

    const chip = document.createElement('span');
    chip.className = 'glass-chip';
    chip.innerHTML = `<i onclick="this.parentElement.remove()" class="fas fa-times"></i> ${name} <input type="hidden" name="tags[]" value="${name}">`;
    document.getElementById('tags-list').appendChild(chip);
}

// --- PRODUCT MODAL ---
function openProductModal() {
    const m = document.getElementById('productModal');
    m.style.display = 'flex';
    setTimeout(() => m.classList.add('show'), 10);
    document.getElementById('prod-search').focus();
    searchProducts(''); 
}

function closeProductModal() {
    const m = document.getElementById('productModal');
    m.classList.remove('show');
    setTimeout(() => m.style.display = 'none', 300);
}

function searchProducts(q) {
    const grid = document.getElementById('products-grid');
    grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text-muted);padding:1rem;"><i class="fas fa-spinner fa-spin"></i> جاري البحث...</div>';
    
    fetch(`<?= BASE_URL ?>/admin/spare-parts/searchProductsJSON?q=${q}`, {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    })
    .then(r => r.json())
    .then(data => {
        grid.innerHTML = '';
        if(!data.results || data.results.length === 0) {
            grid.innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text-muted);">لا توجد نتائج مطابقة</div>';
            return;
        }
        
        data.results.forEach(prod => {
            const div = document.createElement('div');
            div.className = 'compat-item-card';
            div.style.cursor = 'pointer';
            div.onclick = () => selectProduct(prod);
            
            const imgUrl = prod.image ? `<?= BASE_URL ?>/storage/uploads/${prod.image}` : `<?= BASE_URL ?>/assets/images/no-image.png`;
            
            div.innerHTML = `
                <img src="${imgUrl}" class="compat-item-thumb">
                <div class="compat-item-info">
                    <strong>${prod.text}</strong>
                </div>
                <i class="fas fa-plus-circle" style="margin-right: auto; color: var(--neon-blue);"></i>
            `;
            grid.appendChild(div);
        });
    });
}

function selectProduct(prod) {
    if(document.getElementById(`compat-prod-${prod.id}`)) {
        closeProductModal();
        return;
    }
    document.getElementById('no-compat-msg').style.display = 'none';
    
    const div = document.createElement('div');
    div.className = 'compat-item-card';
    div.id = `compat-prod-${prod.id}`;
    
    const imgUrl = prod.image ? `<?= BASE_URL ?>/storage/uploads/${prod.image}` : `<?= BASE_URL ?>/assets/images/no-image.png`;

    div.innerHTML = `
        <input type="hidden" name="compatible_products[]" value="${prod.id}">
        <button type="button" class="compat-remove-btn" onclick="removeCompat(this)"><i class="fas fa-times"></i></button>
        <img src="${imgUrl}" class="compat-item-thumb">
        <div class="compat-item-info">
            <strong>${prod.text}</strong>
        </div>
    `;
    document.getElementById('compatible-list').appendChild(div);
    closeProductModal();
}

function removeCompat(btn) {
    btn.closest('.compat-item-card').remove();
    if(document.querySelectorAll('#compatible-list .compat-item-card').length === 0) {
        document.getElementById('no-compat-msg').style.display = 'block';
    }
}

// --- GALLERY ---
function handleGalleryFiles(input) {
    const container = document.getElementById('gallery-preview');
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'glass-media-item';
            
            if(file.type === 'application/pdf') {
                div.innerHTML = `
                    <div style="height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; background: #1e293b; color: #ef4444;">
                        <i class="fas fa-file-pdf" style="font-size: 2rem; margin-bottom: 5px;"></i>
                        <span style="font-size: 0.7rem;">${file.name.substring(0, 10)}...</span>
                    </div>`;
            } else {
                div.innerHTML = `<img src="${e.target.result}">`;
            }
            div.innerHTML += `
                <div class="glass-media-actions" onclick="this.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </div>`;
            container.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
}

function deleteMedia(id) {
    if(!confirm('حذف هذا الملف؟')) return;
    fetch(`<?= BASE_URL ?>/admin/spare-parts/deleteMedia/${id}`, {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
    }).then(r => r.json()).then(d => {
        if(d.success) document.getElementById(`media-${id}`).remove();
    });
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>
