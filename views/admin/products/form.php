<?php
/**
 * صفحة إضافة/تعديل منتج
 * نظام المُوَفِّي لمهمات المكاتب
 */

$currentPage = 'products';
$isEdit = !empty($product);
$title = $isEdit ? 'تعديل المنتج: ' . htmlspecialchars($product['name']) : 'إضافة منتج جديد';
ob_start();
?>

<!-- Page Header -->
<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-box" style="color: #60a5fa;"></i>
        <?= $title ?>
    </h1>
    <a href="<?= BASE_URL ?>/admin/products" class="btn-glass-primary">
        <i class="fas fa-arrow-right"></i> عودة للمنتجات
    </a>
</div>

<div class="glass-card" style="width: 95%; margin: 0 auto; animation: fadeInUp 0.5s ease;">
    <div style="padding: 2rem;">
        
        <?php if (!empty($_SESSION['flash']['error'])): ?>
        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); color: #fca5a5; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle" style="font-size: 1.2rem;"></i>
            <?= $_SESSION['flash']['error'] ?>
            <?php unset($_SESSION['flash']['error']); ?>
        </div>
        <?php endif; ?>
        
        <form action="<?= BASE_URL ?>/admin/products/<?= $isEdit ? 'update/' . $product['id'] : 'store' ?>" method="POST" enctype="multipart/form-data">
            <?= $csrf_field ?>
            
            <!-- Row 1: Basic Info -->
            <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                
                <div class="form-group" style="margin: 0;">
                    <label class="form-label">اسم المنتج *</label>
                    <div style="position: relative;">
                        <i class="fas fa-heading" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
                        <input type="text" name="name" id="productName" class="form-control" value="<?= htmlspecialchars($product['name'] ?? '') ?>" required style="padding-right: 45px;">
                    </div>
                </div>

                <div class="form-group" style="margin: 0;">
                    <label class="form-label">القسم *</label>
                    <div style="position: relative;">
                        <i class="fas fa-folder" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
                        <select name="category_id" class="form-control" required style="padding-right: 45px; appearance: none;">
                            <option value="">اختر القسم...</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($product['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <i class="fas fa-chevron-down" style="position: absolute; left: 15px; top: 14px; color: #64748b; pointer-events: none;"></i>
                    </div>
                </div>

                <div class="form-group" style="margin: 0;">
                    <label class="form-label">الموديل</label>
                    <div style="position: relative;">
                        <i class="fas fa-barcode" style="position: absolute; right: 15px; top: 14px; color: #64748b;"></i>
                        <input type="text" name="model" id="productModel" class="form-control" value="<?= htmlspecialchars($product['model'] ?? '') ?>" style="padding-right: 45px;">
                    </div>
                </div>
            </div>

            <!-- Row 2: Description & Specifications -->
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-bottom: 2rem;">
                
                <!-- Description -->
                <div>
                    <div class="form-group">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                            <label class="form-label" style="margin: 0;">الوصف</label>
                            <button type="button" onclick="generateDescription()" style="
                                background: linear-gradient(135deg, rgba(124, 58, 237, 0.2) 0%, rgba(192, 38, 211, 0.2) 100%);
                                border: 1px solid rgba(167, 139, 250, 0.4);
                                color: #f3f4f6;
                                padding: 6px 16px;
                                border-radius: 9999px; /* Pill shape */
                                font-size: 0.85rem;
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                cursor: pointer;
                                transition: all 0.3s ease;
                                box-shadow: 0 0 10px rgba(124, 58, 237, 0.1);
                            " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 0 15px rgba(124, 58, 237, 0.3)'; this.style.borderColor='rgba(192, 132, 252, 0.6)';" 
                              onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 0 10px rgba(124, 58, 237, 0.1)'; this.style.borderColor='rgba(167, 139, 250, 0.4)';">
                                <i class="fas fa-magic" style="background: linear-gradient(135deg, #a78bfa 0%, #e879f9 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; filter: drop-shadow(0 0 2px rgba(167, 139, 250, 0.5));"></i>
                                <span style="font-weight: 500;">توليد بالذكاء الاصطناعي</span>
                            </button>
                        </div>
                        <textarea name="description" id="productDescription" class="form-control" rows="5" placeholder="اكتب وصفاً تفصيلياً..."><?= htmlspecialchars($product['description'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">المواصفات الفنية</label>
                        <textarea name="specifications" class="form-control" rows="4" placeholder="مثال: السرعة: 50 صفحة/دقيقة..."><?= htmlspecialchars($product['specifications'] ?? '') ?></textarea>
                    </div>

                    <!-- SEO Section (Inline) -->
                    <div class="glass-card" style="padding: 1.5rem; background: rgba(30, 41, 59, 0.3); border: 1px dashed rgba(255,255,255,0.05); margin-top: 1.5rem;">
                        <h5 style="color: #94a3b8; margin-bottom: 1rem; font-size: 0.9rem;"><i class="fas fa-search"></i> تحسين محركات البحث (SEO)</h5>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group" style="margin: 0;">
                                <label class="form-label">عنوان SEO</label>
                                <input type="text" name="meta_title" class="form-control" value="<?= htmlspecialchars($product['meta_title'] ?? '') ?>" placeholder="اتركه فارغاً للافتراضي">
                            </div>
                            <div class="form-group" style="margin: 0;">
                                <label class="form-label">وصف SEO</label>
                                <input type="text" name="meta_description" class="form-control" value="<?= htmlspecialchars($product['meta_description'] ?? '') ?>" placeholder="وصف مختصر للبحث">
                            </div>
                        </div>
                    </div>
                    <?php if ($isEdit): ?>
                    <!-- Enhanced Admin Cards -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-top: 1.5rem;">
                        
                        <!-- Spare Parts Card -->
                        <div class="glass-card interactive-card" onclick="openSparePartsModal()" 
                             style="padding: 2rem 1rem; text-align: center; position: relative; overflow: hidden; border: 1px solid rgba(245, 158, 11, 0.2); background: linear-gradient(145deg, rgba(30, 41, 59, 0.6) 0%, rgba(245, 158, 11, 0.05) 100%); cursor: pointer; transition: all 0.3s;">
                            
                            <!-- Decorative Background Icon -->
                            <i class="fas fa-cogs" style="position: absolute; top: -10px; left: -10px; font-size: 5rem; opacity: 0.05; transform: rotate(-15deg); color: #f59e0b;"></i>
                            
                            <div style="margin-bottom: 1rem; position: relative;">
                                <div style="width: 60px; height: 60px; margin: 0 auto; background: rgba(245, 158, 11, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(245, 158, 11, 0.2);">
                                    <i class="fas fa-cogs" style="color: #f59e0b; font-size: 1.8rem;"></i>
                                </div>
                                <span class="badge-count" style="position: absolute; top: -5px; right: calc(50% - 40px); background: #f59e0b; color: #000; font-weight: bold; width: 24px; height: 24px; border-radius: 50%; border: 2px solid #1e293b; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;" id="count-spare-parts-badge"><?= count($linkedSparePartIds ?? []) ?></span>
                            </div>

                            <h5 style="margin-bottom: 0.5rem; font-size: 1rem; color: #fff; font-weight: 600;">قطع الغيار المتوافقة</h5>
                            <p style="margin-bottom: 1.5rem; font-size: 0.8rem; color: #94a3b8;">إدارة القطع المرتبطة بهذه الماكينة</p>

                            <button type="button" class="btn-glass-primary" style="padding: 8px 25px; border-radius: 20px; font-size: 0.9rem; pointer-events: none;">
                                <i class="fas fa-edit"></i> تعديل القائمة
                            </button>

                            <!-- Hidden Inputs & Summaries -->
                            <div id="container-spare-parts-inputs">
                                <?php foreach (($linkedSparePartIds ?? []) as $spId): ?>
                                    <input type="hidden" name="spare_parts[]" value="<?= $spId ?>">
                                <?php endforeach; ?>
                            </div>
                            <div id="summary-spare-parts" style="display:none"></div>
                            <span id="count-spare-parts" style="display:none"><?= count($linkedSparePartIds ?? []) ?></span>
                        </div>

                        <!-- Faults Card -->
                        <div class="glass-card interactive-card" onclick="openFaultsModal()"
                             style="padding: 2rem 1rem; text-align: center; position: relative; overflow: hidden; border: 1px solid rgba(239, 68, 68, 0.2); background: linear-gradient(145deg, rgba(30, 41, 59, 0.6) 0%, rgba(239, 68, 68, 0.05) 100%); cursor: pointer; transition: all 0.3s;">
                            
                            <i class="fas fa-exclamation-triangle" style="position: absolute; top: -10px; right: -10px; font-size: 5rem; opacity: 0.05; transform: rotate(15deg); color: #ef4444;"></i>

                            <div style="margin-bottom: 1rem; position: relative;">
                                <div style="width: 60px; height: 60px; margin: 0 auto; background: rgba(239, 68, 68, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 0 15px rgba(239, 68, 68, 0.2);">
                                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 1.8rem;"></i>
                                </div>
                                <span class="badge-count" style="position: absolute; top: -5px; right: calc(50% - 40px); background: #ef4444; color: #fff; font-weight: bold; width: 24px; height: 24px; border-radius: 50%; border: 2px solid #1e293b; display: flex; align-items: center; justify-content: center; font-size: 0.8rem;" id="count-faults-badge"><?= count($productFaults ?? []) ?></span>
                            </div>

                            <h5 style="margin-bottom: 0.5rem; font-size: 1rem; color: #fff; font-weight: 600;">الأعطال الشائعة</h5>
                            <p style="margin-bottom: 1.5rem; font-size: 0.8rem; color: #94a3b8;">تحديد الأعطال وحلولها المتكررة</p>

                            <button type="button" class="btn-glass-danger" style="padding: 8px 25px; border-radius: 20px; font-size: 0.9rem; pointer-events: none;">
                                <i class="fas fa-edit"></i> تعديل القائمة
                            </button>
                            
                            <div id="summary-faults" style="display:none;"></div>
                            <div id="container-faults-inputs"></div>
                            <span id="count-faults" style="display:none"><?= count($productFaults ?? []) ?></span>
                        </div>

                    </div>
                    
                    <style>
                        .interactive-card:hover {
                            transform: translateY(-5px);
                            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
                        }
                        .interactive-card:hover .btn-glass-primary {
                            background: rgba(245, 158, 11, 0.2);
                            border-color: rgba(245, 158, 11, 0.5);
                        }
                        .interactive-card:hover .btn-glass-danger {
                            background: rgba(239, 68, 68, 0.2);
                            border-color: rgba(239, 68, 68, 0.5);
                        }
                    </style>
                    <?php endif; ?>

                </div>

                <!-- Images & Settings -->
                <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                    
                    <!-- Main Image -->
                    <div class="glass-card" style="padding: 1.5rem; background: rgba(30, 41, 59, 0.3); text-align: center;">
                        <label class="form-label" style="margin-bottom: 1rem; display: block;">الصورة الرئيسية (Thumbnail)</label>
                        
                        <div id="thumbnail-container" style="position: relative; width: 100%; aspect-ratio: 16/9; background: rgba(0,0,0,0.2); border-radius: 12px; overflow: hidden; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center;">
                            <img id="main-preview" src="<?= ($isEdit && $product['thumbnail']) ? BASE_URL . '/storage_proxy.php?path=' . urlencode($product['thumbnail']) . '&v=3' : '' ?>" 
                                 style="width: 100%; height: 100%; object-fit: cover; display: <?= ($isEdit && $product['thumbnail']) ? 'block' : 'none' ?>;">
                            
                            <div id="main-placeholder" style="display: <?= ($isEdit && $product['thumbnail']) ? 'none' : 'flex' ?>; flex-direction: column; align-items: center; color: #64748b;">
                                <i class="fas fa-image" style="font-size: 3rem; margin-bottom: 10px;"></i>
                                <span>اختر صورة</span>
                            </div>

                            <!-- زر الحذف (يظهر فوق الصورة) -->
                            <button type="button" id="delete-thumbnail-btn" onclick="deleteThumbnail()" 
                                    style="display: <?= ($isEdit && $product['thumbnail']) ? 'flex' : 'none' ?>; position: absolute; top: 10px; left: 10px; width: 36px; height: 36px; border-radius: 50%; background: rgba(239, 68, 68, 0.9); border: none; cursor: pointer; align-items: center; justify-content: center; color: white; transition: all 0.3s; box-shadow: 0 2px 10px rgba(0,0,0,0.3);"
                                    title="حذف الصورة">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>

                        <!-- حقل مخفي لتتبع حذف الصورة -->
                        <input type="hidden" name="delete_thumbnail" id="delete-thumbnail-input" value="0">

                        <input type="file" name="thumbnail" id="main-input" class="form-control" accept="image/*" style="display: none;">
                        
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="button" onclick="document.getElementById('main-input').click()" class="btn-glass-primary" style="flex: 1; justify-content: center;">
                                <i class="fas fa-<?= ($isEdit && $product['thumbnail']) ? 'sync-alt' : 'upload' ?>"></i> 
                                <?= ($isEdit && $product['thumbnail']) ? 'تغيير الصورة' : 'رفع صورة' ?>
                            </button>
                        </div>
                        
                        <small style="display: block; margin-top: 0.75rem; color: #64748b; font-size: 0.8rem;">
                            <i class="fas fa-info-circle"></i> عند تغيير الصورة، سيتم حذف الصورة القديمة تلقائياً
                        </small>
                    </div>

                    <!-- Gallery -->
                    <div class="glass-card" style="padding: 1rem; background: rgba(30, 41, 59, 0.3);">
                        <label class="form-label">صور إضافية</label>
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 5px; margin-bottom: 1rem;">
                            <?php if (!empty($images)): ?>
                                <?php foreach ($images as $img): ?>
                                <div class="gallery-item-container" style="position: relative;">
                                    <img src="<?= BASE_URL ?>/storage_proxy.php?path=<?= urlencode($img['thumbnail_path'] ?? $img['image_path']) ?>&v=3" style="width: 100%; height: 80px; object-fit: cover; border-radius: 6px;">
                                    <button type="button" onclick="deleteGalleryImage(<?= $img['id'] ?>, this)" class="btn-delete-img"
                                            style="position: absolute; top: -5px; right: -5px; width: 24px; height: 24px; border-radius: 50%; background: #ef4444; color: white; border: none; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                                        <i class="fas fa-times" style="font-size: 0.8rem;"></i>
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <!-- Upload Placeholder for visuals -->
                            <div style="height: 60px; background: rgba(255,255,255,0.05); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: #64748b;">+</div>
                        </div>
                        <input type="file" name="images[]" class="form-control" accept="image/*" multiple style="font-size: 0.9rem;">
                    </div>

                    <!-- Settings -->
                    <div class="glass-card" style="padding: 1.5rem; background: rgba(30, 41, 59, 0.3);">
                        <h4 style="color: #cbd5e1; font-size: 1rem; margin-bottom: 1.2rem; font-weight: 600;">
                            <i class="fas fa-cogs"></i> الإعدادات
                        </h4>

                        <div style="display: flex; flex-direction: column; gap: 1rem;">
                            
                            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <label class="form-label" style="margin: 0; font-size: 0.9rem;">تفعيل المنتج</label>
                                <label class="switch-toggle">
                                    <input type="checkbox" name="is_active" value="1" <?= ($product['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <label class="form-label" style="margin: 0; font-size: 0.9rem;">إظهار الأعطال</label>
                                <label class="switch-toggle" style="--active-color: #ef4444;">
                                    <input type="checkbox" name="show_faults" value="1" <?= ($product['show_faults'] ?? 1) ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>

                            <div style="display: flex; align-items: center; justify-content: space-between;">
                                <label class="form-label" style="margin: 0; font-size: 0.9rem;">إظهار قطع الغيار</label>
                                <label class="switch-toggle" style="--active-color: #f59e0b;">
                                    <input type="checkbox" name="show_spare_parts" value="1" <?= ($product['show_spare_parts'] ?? 1) ? 'checked' : '' ?>>
                                    <span class="slider"></span>
                                </label>
                            </div>

                        </div>
                    </div>

                </div>

            </div>

            <?php if ($isEdit): ?>
            <!-- قسم الإدارة المتقدمة (Modals Triggers) -->

            
            <!-- تمرير البيانات الأولية للـ JS -->
            <script>
                const INITIAL_SPARE_PARTS = <?= json_encode(array_filter($allSpareParts ?? [], function($p) use ($linkedSparePartIds) {
                    return in_array($p['id'], $linkedSparePartIds ?? []);
                })) ?>;
                const INITIAL_FAULTS = <?= json_encode($productFaults ?? []) ?>;
            </script>
            <?php endif; ?>

            <!-- Actions -->
            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid rgba(255,255,255,0.05); display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="<?= BASE_URL ?>/admin/products" class="btn-glass-danger" style="text-decoration: none;">إلغاء</a>
                <button type="submit" class="btn-glass-primary" style="padding: 10px 40px; font-size: 1.1rem;">
                    <i class="fas fa-save"></i> <?= $isEdit ? 'حفظ التعديلات' : 'إضافة المنتج' ?>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
// Main Image Preview
document.getElementById('main-input').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('main-preview');
            const placeholder = document.getElementById('main-placeholder');
            const deleteBtn = document.getElementById('delete-thumbnail-btn');
            
            img.src = e.target.result;
            img.style.display = 'block';
            placeholder.style.display = 'none';
            deleteBtn.style.display = 'flex';
            
            // إلغاء علامة الحذف إذا كانت مفعلة
            document.getElementById('delete-thumbnail-input').value = '0';
        }
        reader.readAsDataURL(file);
    }
});

// حذف الصورة المصغرة
function deleteThumbnail() {
    showSystemConfirm('تأكيد الحذف', 'هل أنت متأكد من حذف الصورة الرئيسية؟', function() {
        const img = document.getElementById('main-preview');
        const placeholder = document.getElementById('main-placeholder');
        const deleteBtn = document.getElementById('delete-thumbnail-btn');
        const deleteInput = document.getElementById('delete-thumbnail-input');
        const fileInput = document.getElementById('main-input');
        
        // إخفاء الصورة وإظهار placeholder
        img.src = '';
        img.style.display = 'none';
        placeholder.style.display = 'flex';
        deleteBtn.style.display = 'none';
        
        // تعيين علامة الحذف
        deleteInput.value = '1';
        
        // مسح input الملف
        fileInput.value = '';
    });
}

// AI Generation
function generateDescription() {
    const name = document.getElementById('productName').value;
    const model = document.getElementById('productModel').value;
    
    if (!name) {
        showSystemAlert('الرجاء إدخال اسم المنتج أولاً', 'error');
        return;
    }
    
    const btn = event.target;
    const originalContent = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التوليد...';
    
    fetch('<?= BASE_URL ?>/admin/products/generate-description', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `name=${encodeURIComponent(name)}&model=${encodeURIComponent(model)}&<?= CSRF_TOKEN_NAME ?>=<?= $_SESSION[CSRF_TOKEN_NAME] ?? '' ?>`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            document.getElementById('productDescription').value = data.description;
            showSystemAlert('تم توليد الوصف بنجاح', 'success');
        } else {
            showSystemAlert(data.error || 'فشل التوليد', 'error');
        }
    })
    .catch(() => showSystemAlert('حدث خطأ في الاتصال', 'error'))
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalContent;
    });
}

// إضافة صف عطل جديد
function addFaultRow() {
    const template = document.getElementById('fault-template');
    const container = document.getElementById('faults-container');
    
    if (template && container) {
        const clone = template.content.cloneNode(true);
        container.appendChild(clone);
        
        // انتقل للصف الجديد
        container.scrollTop = container.scrollHeight;
    }
}

// ========================================
// نظام اقتصاص الصور المتقدم
// ========================================
let cropper = null;
let cropperOriginalFile = null;

// فتح محرر الاقتصاص عند اختيار صورة
document.getElementById('main-input').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    cropperOriginalFile = file;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        // فتح نافذة الاقتصاص
        openCropperModal(e.target.result);
    }
    reader.readAsDataURL(file);
});

// فتح نافذة الاقتصاص
function openCropperModal(imageSrc) {
    const modal = document.getElementById('cropperModal');
    const cropperImage = document.getElementById('cropperImage');
    
    cropperImage.src = imageSrc;
    modal.style.display = 'flex';
    setTimeout(() => modal.classList.add('show'), 10);
    
    // تهيئة Cropper.js
    if (cropper) {
        cropper.destroy();
    }
    
    cropper = new Cropper(cropperImage, {
        aspectRatio: NaN, // اقتصاص حر
        viewMode: 2,
        dragMode: 'move',
        autoCropArea: 1,
        responsive: true,
        restore: false,
        guides: true,
        center: true,
        highlight: true,
        cropBoxMovable: true,
        cropBoxResizable: true,
        toggleDragModeOnDblclick: true,
    });
}

// إغلاق نافذة الاقتصاص
function closeCropperModal() {
    const modal = document.getElementById('cropperModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
    }, 300);
}

// تعيين نسبة اقتصاص
function setAspectRatio(ratio) {
    if (cropper) {
        cropper.setAspectRatio(ratio);
    }
}

// تدوير الصورة
function rotateImage(deg) {
    if (cropper) {
        cropper.rotate(deg);
    }
}

// قلب الصورة
function flipImage(direction) {
    if (cropper) {
        if (direction === 'h') {
            const scaleX = cropper.getData().scaleX || 1;
            cropper.scaleX(-scaleX);
        } else {
            const scaleY = cropper.getData().scaleY || 1;
            cropper.scaleY(-scaleY);
        }
    }
}

// تطبيق الاقتصاص مع الحفاظ على الجودة
function applyCrop() {
    if (!cropper) return;
    
    // الحصول على البيانات المقتصة بجودة عالية
    const canvas = cropper.getCroppedCanvas({
        maxWidth: 2000,  // حجم أكبر للحفاظ على الجودة
        maxHeight: 2000,
        imageSmoothingEnabled: true,
        imageSmoothingQuality: 'high',
        fillColor: '#fff', // خلفية بيضاء للصور الشفافة
    });
    
    // تحديث المعاينة
    const preview = document.getElementById('main-preview');
    const placeholder = document.getElementById('main-placeholder');
    const deleteBtn = document.getElementById('delete-thumbnail-btn');
    
    // تحديد نوع الملف (PNG للجودة الأفضل)
    const outputType = 'image/png';
    const outputQuality = 1; // أعلى جودة
    
    preview.src = canvas.toDataURL(outputType, outputQuality);
    preview.style.display = 'block';
    placeholder.style.display = 'none';
    deleteBtn.style.display = 'flex';
    
    // تحويل Canvas إلى Blob مع الحفاظ على الجودة
    canvas.toBlob(function(blob) {
        // استخدام PNG للجودة الأفضل
        const fileName = cropperOriginalFile.name.replace(/\.(jpg|jpeg|gif|webp)$/i, '.png');
        const newFile = new File([blob], fileName, { type: outputType });
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(newFile);
        document.getElementById('main-input').files = dataTransfer.files;
    }, outputType, outputQuality);
    
    closeCropperModal();
    showSystemAlert('تم اقتصاص الصورة بنجاح!', 'success');
}

// استخدام الصورة بدون اقتصاص
function useWithoutCrop() {
    const preview = document.getElementById('main-preview');
    const placeholder = document.getElementById('main-placeholder');
    const deleteBtn = document.getElementById('delete-thumbnail-btn');
    
    preview.src = document.getElementById('cropperImage').src;
    preview.style.display = 'block';
    placeholder.style.display = 'none';
    deleteBtn.style.display = 'flex';
    
    closeCropperModal();
}

// حذف صور المعرض
function deleteGalleryImage(imageId, btn) {
    showSystemConfirm('حذف الصورة', 'هل أنت متأكد من حذف هذه الصورة؟', function() {
        const container = btn.parentElement;
        
        // Visual feedback
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        
        fetch('<?= BASE_URL ?>/admin/products/delete-image/' + imageId, {
            method: 'POST', // or GET depending on route, usually POST better for actions but simple delete route often GET if no body. Check controller.
            // Using typical fetch with no body if ID in URL
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                // Animate removal
                container.style.transition = 'all 0.3s';
                container.style.opacity = '0';
                container.style.transform = 'scale(0.8)';
                setTimeout(() => container.remove(), 300);
            } else {
                showSystemAlert(data.error || 'فشل الحذف', 'error');
                btn.innerHTML = '<i class="fas fa-times"></i>';
            }
        })
        .catch(() => {
            showSystemAlert('خطأ في الاتصال', 'error');
            btn.innerHTML = '<i class="fas fa-times"></i>';
        });
    });
}
</script>

<!-- نافذة اقتصاص الصور -->
<div id="cropperModal" class="modal-overlay" style="display: none;">
    <div class="glass-card" style="width: 90%; max-width: 900px; max-height: 90vh; overflow: hidden; display: flex; flex-direction: column;">
        <div style="padding: 1rem 1.5rem; border-bottom: 1px solid rgba(255,255,255,0.1); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0; color: #f1f5f9; font-size: 1.1rem;">
                <i class="fas fa-crop-alt" style="color: #60a5fa;"></i> اقتصاص الصورة
            </h3>
            <button type="button" onclick="closeCropperModal()" style="background: none; border: none; color: #94a3b8; font-size: 1.5rem; cursor: pointer;">&times;</button>
        </div>
        
        <div style="flex: 1; padding: 1rem; overflow: hidden; min-height: 300px; max-height: 50vh;">
            <img id="cropperImage" src="" style="display: block; max-width: 100%; max-height: 100%;">
        </div>
        
        <!-- أدوات التحكم -->
        <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center;">
            <!-- نسب الاقتصاص -->
            <div style="display: flex; gap: 0.5rem; margin-left: 1rem;">
                <button type="button" onclick="setAspectRatio(NaN)" class="btn-glass-secondary" style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    <i class="fas fa-expand"></i> حر
                </button>
                <button type="button" onclick="setAspectRatio(1)" class="btn-glass-secondary" style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    1:1
                </button>
                <button type="button" onclick="setAspectRatio(16/9)" class="btn-glass-secondary" style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    16:9
                </button>
                <button type="button" onclick="setAspectRatio(4/3)" class="btn-glass-secondary" style="padding: 0.5rem 0.75rem; font-size: 0.85rem;">
                    4:3
                </button>
            </div>
            
            <!-- التدوير والقلب -->
            <div style="display: flex; gap: 0.5rem;">
                <button type="button" onclick="rotateImage(-90)" class="btn-glass-secondary" style="padding: 0.5rem 0.75rem;" title="تدوير يسار">
                    <i class="fas fa-undo"></i>
                </button>
                <button type="button" onclick="rotateImage(90)" class="btn-glass-secondary" style="padding: 0.5rem 0.75rem;" title="تدوير يمين">
                    <i class="fas fa-redo"></i>
                </button>
                <button type="button" onclick="flipImage('h')" class="btn-glass-secondary" style="padding: 0.5rem 0.75rem;" title="قلب أفقي">
                    <i class="fas fa-arrows-alt-h"></i>
                </button>
                <button type="button" onclick="flipImage('v')" class="btn-glass-secondary" style="padding: 0.5rem 0.75rem;" title="قلب عمودي">
                    <i class="fas fa-arrows-alt-v"></i>
                </button>
            </div>
        </div>
        
        <!-- أزرار الإجراء -->
        <div style="padding: 1rem 1.5rem; border-top: 1px solid rgba(255,255,255,0.1); display: flex; gap: 0.5rem; justify-content: flex-end;">
            <button type="button" onclick="useWithoutCrop()" class="btn-glass-secondary">
                <i class="fas fa-check"></i> استخدام بدون اقتصاص
            </button>
            <button type="button" onclick="applyCrop()" class="btn-glass-primary">
                <i class="fas fa-crop-alt"></i> تطبيق الاقتصاص
            </button>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- MODALS HTML & LOGIC (Updated v2.0 Glass)   -->
<!-- ============================================== -->

<!-- 1. SPARA PARTS VISUAL CATALOG MODAL -->
<div id="modal-spare-parts" class="modal-overlay">
    <div class="modal-content" style="width: 900px; max-width: 95%; height: 80vh;">
        <div class="modal-header">
            <h3 style="margin:0; color:#fff"><i class="fas fa-boxes"></i> كتالوج قطع الغيار</h3>
            <button type="button" class="btn-icon-glass" onclick="closeModals()"><i class="fas fa-times"></i></button>
        </div>
        
        <!-- Filter Toolbar -->
        <div style="padding: 1rem 1.5rem; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--glass-border);">
             <div style="color: #cbd5e1; font-size: 0.9rem;">
                 <i class="fas fa-check-circle" style="color: #10b981;"></i> تم اختيار: <b id="catalog-selected-count" style="color:#fff">0</b> قطعة
             </div>
             <div style="display: flex; gap: 10px; align-items: center;">
                 <input type="text" id="catalog-search" onkeyup="filterCatalog()" placeholder="بحث سريع..." class="search-input" style="padding: 5px 10px; font-size: 0.85rem; width: 200px;">
                 <button onclick="clearSelection()" class="glass-tab-btn" style="padding: 5px 10px; font-size: 0.85rem; color: #ef4444;"><i class="fas fa-trash"></i> إلغاء التحديد</button>
             </div>
        </div>

        <div class="modal-body">
            <!-- Parts Grid -->
            <div id="sp-catalog-grid" class="selectable-grid">
                <!-- Loading Indicator -->
                <div class="grid-loading" style="grid-column: 1/-1; text-align: center; padding: 3rem; color: var(--text-muted);">
                    <i class="fas fa-spinner fa-spin" style="font-size: 2rem; margin-bottom: 10px;"></i>
                    <p>جاري تحميل الكتالوج...</p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-glass-primary" onclick="closeModals()">
                <i class="fas fa-check"></i> إتمام وحفظ
            </button>
        </div>
    </div>
</div>

<!-- 2. FAULTS MODAL -->
<!-- 2. FAULTS MODAL -->
<div id="modal-faults" class="modal-overlay">
    <div class="modal-content" style="width: 1200px; max-width: 95vw; height: 85vh; display: flex; flex-direction: column;">
        <div class="modal-header">
            <h3 style="margin:0; color:#fff"><i class="fas fa-exclamation-triangle" style="color:#ef4444; margin-left:10px;"></i> إدارة الأعطال الشائعة</h3>
            <button type="button" class="btn-icon-glass" onclick="closeModals()"><i class="fas fa-times"></i></button>
        </div>
        
        <!-- Split Layout Body -->
        <div class="modal-body" style="flex: 1; min-height: 0; display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding: 2rem; overflow: hidden;">
            
            <!-- COL 1: Add New Form (Fixed Width, Scrollable) -->
            <div style="display: flex; flex-direction: column; height: 100%; overflow-y: auto; padding-left: 5px;">
                <div class="glass-card" style="padding: 1.5rem; background: linear-gradient(180deg, rgba(30, 41, 59, 0.4) 0%, rgba(30, 41, 59, 0.2) 100%); display: flex; flex-direction: column; min-height: min-content;">
                    
                    <div style="display: flex; align-items: center; margin-bottom: 1.5rem; gap: 10px; padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div style="width: 32px; height: 32px; background: rgba(16, 185, 129, 0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <i class="fas fa-plus" style="color: #10b981; font-size: 0.9rem;"></i>
                        </div>
                        <h4 style="margin: 0; color: #f1f5f9; font-size: 1rem; white-space: nowrap;">إضافة عطل جديد</h4>
                    </div>

                    <!-- Title & Code Row -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                        <div class="form-group" style="margin:0;">
                             <label style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 5px; display: block;">عنوان العطل <span style="color:#ef4444">*</span></label>
                             <input type="text" id="new-fault-title" placeholder="مثال: حشر ورق" class="form-control" style="background: rgba(15, 23, 42, 0.6); border-color: rgba(255,255,255,0.1); height: 42px;">
                        </div>
                        <div class="form-group" style="margin:0;">
                             <label style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 5px; display: block;">كود الخطأ</label>
                             <input type="text" id="new-fault-code" placeholder="SC-542" class="form-control" style="background: rgba(15, 23, 42, 0.6); border-color: rgba(255,255,255,0.1); font-family: monospace; height: 42px;">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 5px; display: block;">تشخيص المشكلة</label>
                        <textarea id="new-fault-desc" placeholder="وصف تفصيلي..." class="form-control" style="background: rgba(15, 23, 42, 0.6); border-color: rgba(255,255,255,0.1); height: 65px; resize: vertical; min-height: 65px;"></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label style="font-size: 0.8rem; color: #94a3b8; margin-bottom: 5px; display: block;">الحل المقترح</label>
                        <textarea id="new-fault-sol" placeholder="خطوات الإصلاح..." class="form-control" style="background: rgba(15, 23, 42, 0.6); border-color: rgba(255,255,255,0.1); height: 65px; resize: vertical; min-height: 65px;"></textarea>
                    </div>

                    <button type="button" onclick="addNewFault()" class="btn-glass-primary" style="width: 100%; justify-content: center; padding: 0.8rem; margin-top: auto;">
                        <i class="fas fa-save margin-left-5"></i> إضافة للقائمة
                    </button>
                </div>
            </div>

            <!-- COL 2: List of Faults (Fluid Width, Scrollable) -->
            <div style="display: flex; flex-direction: column; height: 100%; background: rgba(0,0,0,0.2); border-radius: 12px; border: 1px solid var(--glass-border); overflow: hidden;">
                
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid var(--glass-border); display: flex; justify-content: space-between; align-items: center; background: rgba(30, 41, 59, 0.3); min-height: 45px;">
                    <h4 style="margin:0; font-size: 0.9rem; color: #e2e8f0;">الأعطال المسجلة (<span id="faults-list-count" style="color: #f59e0b;">0</span>)</h4>
                    <button type="button" onclick="productFaults=[]; renderFaults();" class="glass-tab-btn" style="color: #ef4444; font-size: 0.75rem; padding: 4px 10px;"><i class="fas fa-trash-alt"></i> حذف الجميع</button>
                </div>

                <!-- Scrollable List Container -->
                <div id="faults-list-container" style="flex: 1; overflow-y: auto; padding: 1rem; display: flex; flex-direction: column; gap: 10px;">
                     <!-- List items inject here -->
                     <div style="text-align: center; color: var(--text-muted); margin-top: 2rem;">
                        <i class="fas fa-clipboard-list" style="font-size: 2rem; margin-bottom: 1rem; opacity: 0.5;"></i>
                        <p>لم يتم إضافة أعطال حتى الآن</p>
                     </div>
                </div>

            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn-glass-primary" onclick="closeModals()">
                <i class="fas fa-check"></i> إتمام وحفظ
            </button>
        </div>
    </div>
</div>

<script>
// STATE MANAGEMENT
let selectedSpareParts = []; // IDs only
let loadedParts = []; // Full Objects
let productFaults = []; 
let isCatalogLoaded = false;

document.addEventListener('DOMContentLoaded', function() {
    // 1. Initialize Faults
    if(typeof INITIAL_FAULTS !== 'undefined') {
        productFaults = INITIAL_FAULTS;
        renderFaults();
    }

    // 2. Initialize Spare Parts
    if(typeof INITIAL_SPARE_PARTS !== 'undefined') {
        // We get objects, extract IDs
        const parts = Object.values(INITIAL_SPARE_PARTS);
        selectedSpareParts = parts.map(p => p.id);
        loadedParts = parts; // Keep them for summary
        renderSparePartsSummary();
    }
});


// ===================================
// CATALOG LOGIC
// ===================================
function openSparePartsModal() {
    const m = document.getElementById('modal-spare-parts');
    m.style.display = 'flex'; // Ensure flex first
    setTimeout(() => {
        m.classList.add('active'); // CSS opacity transition
        if(!isCatalogLoaded) loadCatalog();
    }, 10);
}

function loadCatalog() {
    const resultsDiv = document.getElementById('sp-catalog-grid');
    // Keep loading spinner if initial
    
    fetch(`<?= BASE_URL ?>/admin/spare-parts/search-json`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(data => {
        isCatalogLoaded = true;
        if(data.results) {
            // Merge new parts into loadedParts unique by ID
            const existingIds = new Set(loadedParts.map(p => p.id));
            data.results.forEach(p => {
                if(!existingIds.has(p.id)) loadedParts.push(p);
            });
        }
        renderCatalogGrid(data.results || []);
    })
    .catch(err => {
        resultsDiv.innerHTML = '<div style="color:#ef4444; text-align:center; grid-column:1/-1">فشل تحميل القائمة</div>';
    });
}

function renderCatalogGrid(parts) {
    const grid = document.getElementById('sp-catalog-grid');
    grid.innerHTML = '';
    
    if(parts.length === 0) {
        grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:2rem; color:var(--text-muted)">لا توجد قطع غيار متاحة</div>';
        return;
    }

    parts.forEach(p => {
        const isSelected = selectedSpareParts.some(id => id == p.id);

        const card = document.createElement('div');
        card.className = `selectable-card ${isSelected ? 'selected' : ''}`;
        card.onclick = () => togglePartSelection(p.id, card);
        card.id = `part-card-${p.id}`;
        card.dataset.name = p.name.toLowerCase(); // For search
        
        const imgSrc = p.image ? `<?= BASE_URL ?>/storage/uploads/${p.image}` : `<?= BASE_URL ?>/assets/images/no-image.png`;
        
        card.innerHTML = `
            <img src="${imgSrc}" onerror="this.src='<?= BASE_URL ?>/assets/images/no-image.png'">
            <div style="font-size:0.9rem; color:#fff; margin-bottom:5px; line-height:1.2;">${p.name}</div>
            <div style="font-size:0.75rem; color:var(--text-muted); font-family:monospace;">${p.part_number}</div>
        `;
        grid.appendChild(card);
    });
    
    updateCatalogCount();
}

function filterCatalog() {
    const q = document.getElementById('catalog-search').value.toLowerCase();
    const cards = document.querySelectorAll('.selectable-card');
    cards.forEach(c => {
        const name = c.dataset.name || '';
        if(name.includes(q)) c.style.display = 'flex';
        else c.style.display = 'none';
    });
}

function togglePartSelection(id, cardEl) {
    const idx = selectedSpareParts.findIndex(pid => pid == id);
    if(idx > -1) {
        selectedSpareParts.splice(idx, 1);
        cardEl.classList.remove('selected');
    } else {
        selectedSpareParts.push(id);
        cardEl.classList.add('selected');
    }
    updateCatalogCount();
    renderSparePartsSummary();
    syncHiddenInputs();
}

function clearSelection() {
    selectedSpareParts = [];
    document.querySelectorAll('.selectable-card.selected').forEach(el => el.classList.remove('selected'));
    updateCatalogCount();
    renderSparePartsSummary();
    syncHiddenInputs();
}

function updateCatalogCount() {
    document.getElementById('catalog-selected-count').innerText = selectedSpareParts.length;
    if(document.getElementById('count-spare-parts'))
        document.getElementById('count-spare-parts').innerText = selectedSpareParts.length;
}

function syncHiddenInputs() {
    const containerInputs = document.getElementById('container-spare-parts-inputs');
    if(!containerInputs) return;
    containerInputs.innerHTML = '';
    selectedSpareParts.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'spare_parts[]'; // Correct attribute for POST
        input.value = id;
        containerInputs.appendChild(input);
    });
}

function renderSparePartsSummary() {
    const summaryContainer = document.getElementById('summary-spare-parts');
    if(!summaryContainer) return;
    summaryContainer.innerHTML = '';
    
    let count = 0;
    const limit = 6;
    
    selectedSpareParts.forEach(id => {
        if(count >= limit) return;
        const part = loadedParts.find(p => p.id == id);
        if(part) {
            const tag = document.createElement('span');
            tag.className = 'glass-chip'; // Use new class!
            tag.innerHTML = `${part.name}`;
            tag.style.fontSize = '0.75rem';
            tag.style.padding = '2px 8px';
            summaryContainer.appendChild(tag);
            count++;
        }
    });

    if(selectedSpareParts.length > limit) {
         summaryContainer.innerHTML += `<span style="font-size:0.8rem; opacity:0.6; align-self:center;">+${selectedSpareParts.length - limit}</span>`;
    }
}


// ===================================
// MODAL CONTROLS COMMON
// ===================================
function openFaultsModal() {
    const m = document.getElementById('modal-faults');
    m.style.display = 'flex';
    setTimeout(() => m.classList.add('active'), 10);
}

function closeModals() {
    const overlays = document.querySelectorAll('.modal-overlay');
    overlays.forEach(m => {
        m.classList.remove('active');
        setTimeout(() => m.style.display = 'none', 300);
    });
}

// ===================================
// FAULTS LOGIC (Updated Style)
// ===================================
function addNewFault() {
    const titleInp = document.getElementById('new-fault-title');
    const title = titleInp.value.trim();
    if(!title) {
        alert('العنوان مطلوب'); // Could use custom alert
        return; 
    }

    const newFault = {
        title: title,
        fault_name: title,
        error_code: document.getElementById('new-fault-code').value,
        description: document.getElementById('new-fault-desc').value,
        solution: document.getElementById('new-fault-sol').value
    };

    productFaults.push(newFault);
    renderFaults();

    // Reset Form
    titleInp.value = '';
    document.getElementById('new-fault-code').value = '';
    document.getElementById('new-fault-desc').value = '';
    document.getElementById('new-fault-sol').value = '';
    titleInp.focus();
}

function removeFault(index) {
    productFaults.splice(index, 1);
    renderFaults();
}

function renderFaults() {
    const containerInputs = document.getElementById('container-faults-inputs');
    if(!containerInputs) return;

    containerInputs.innerHTML = '';
    productFaults.forEach((f, idx) => {
        containerInputs.innerHTML += `
            <input type="hidden" name="fault_title[]" value="${(f.title || f.fault_name).replace(/"/g, '&quot;')}">
            <input type="hidden" name="fault_description[]" value="${(f.description || '').replace(/"/g, '&quot;')}">
            <input type="hidden" name="fault_solution[]" value="${(f.solution || '').replace(/"/g, '&quot;')}">
        `;
    });

    document.getElementById('count-faults').innerText = productFaults.length;
    document.getElementById('faults-list-count').innerText = productFaults.length;

    const listContainer = document.getElementById('faults-list-container');
    listContainer.innerHTML = '';
    
    productFaults.forEach((f, idx) => {
        const item = document.createElement('div');
        item.style.cssText = 'background: rgba(255,255,255,0.03); border-radius: 8px; border: 1px solid var(--glass-border); overflow: hidden;';
        
        item.innerHTML = `
            <div onclick="this.nextElementSibling.style.display = this.nextElementSibling.style.display == 'block' ? 'none' : 'block'" 
                 style="padding: 10px 15px; cursor: pointer; display: flex; justify-content: space-between; align-items: center; user-select: none;">
                <span style="display: flex; align-items: center; gap: 8px; color: #e2e8f0; font-weight: 500;">
                    <i class="fas fa-exclamation-circle" style="color: #ef4444;"></i>
                    ${f.title || f.fault_name}
                </span>
                <span style="display: flex; gap: 10px; align-items: center;">
                     <button type="button" onclick="event.stopPropagation(); removeFault(${idx})" style="background: none; border: none; color: #ef4444; cursor: pointer;">
                        <i class="fas fa-trash-alt"></i>
                     </button>
                     <i class="fas fa-chevron-down" style="color: var(--text-muted); font-size: 0.8rem;"></i>
                </span>
            </div>
            <div style="display: none; padding: 15px; border-top: 1px solid var(--glass-border); background: rgba(0,0,0,0.1); color: var(--text-muted); font-size: 0.9rem;">
                <div style="margin-bottom: 5px;"><strong>الوصف:</strong> ${f.description || '-'}</div>
                <div><strong>الحل:</strong> ${f.solution || '-'}</div>
            </div>
        `;
        listContainer.appendChild(item);
    });

    // Update Summary in Main Form
    const summaryContainer = document.getElementById('summary-faults');
    if(summaryContainer) {
        summaryContainer.innerHTML = '';
        productFaults.forEach(f => {
            const div = document.createElement('div');
            div.innerText = '• ' + (f.title || f.fault_name);
            summaryContainer.appendChild(div);
        });
    }
}
</script>

<?php
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/admin_layout.php';
?>

