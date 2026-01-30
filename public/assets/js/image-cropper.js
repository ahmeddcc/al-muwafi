/**
 * Image Cropper - واجهة اقتصاص الصور
 * نظام المُوَفِّي لخدمات ريكو
 * يستخدم Cropper.js للاقتصاص التفاعلي
 */

class ImageCropper {
    constructor(options = {}) {
        this.options = {
            aspectRatio: options.aspectRatio || 1, // نسبة العرض للارتفاع (1 = مربع)
            minWidth: options.minWidth || 200,
            minHeight: options.minHeight || 200,
            maxWidth: options.maxWidth || 800,
            maxHeight: options.maxHeight || 800,
            outputFormat: options.outputFormat || 'image/jpeg',
            outputQuality: options.outputQuality || 0.9,
            ...options
        };

        this.cropper = null;
        this.currentInput = null;
        this.modal = null;
        this.croppedBlob = null;

        this.init();
    }

    /**
     * تهيئة المكتبة
     */
    init() {
        this.createModal();
        this.bindEvents();
    }

    /**
     * إنشاء نافذة الاقتصاص
     */
    createModal() {
        const modalHTML = `
            <div id="cropperModal" class="cropper-modal" style="display: none;">
                <div class="cropper-modal-content">
                    <div class="cropper-modal-header">
                        <h3>اقتصاص الصورة</h3>
                        <button type="button" class="cropper-close" id="cropperClose">&times;</button>
                    </div>
                    <div class="cropper-modal-body">
                        <div class="cropper-container">
                            <img id="cropperImage" src="" alt="صورة للاقتصاص">
                        </div>
                        <div class="cropper-preview-container">
                            <p>معاينة:</p>
                            <div class="cropper-preview" id="cropperPreview"></div>
                        </div>
                    </div>
                    <div class="cropper-modal-footer">
                        <div class="cropper-tools">
                            <button type="button" class="cropper-btn" id="cropperRotateLeft" title="تدوير لليسار">↺</button>
                            <button type="button" class="cropper-btn" id="cropperRotateRight" title="تدوير لليمين">↻</button>
                            <button type="button" class="cropper-btn" id="cropperFlipH" title="قلب أفقي">⇆</button>
                            <button type="button" class="cropper-btn" id="cropperFlipV" title="قلب عمودي">⇅</button>
                            <button type="button" class="cropper-btn" id="cropperReset" title="إعادة تعيين">⟲</button>
                        </div>
                        <div class="cropper-actions">
                            <button type="button" class="cropper-btn cropper-btn-secondary" id="cropperCancel">إلغاء</button>
                            <button type="button" class="cropper-btn cropper-btn-primary" id="cropperApply">تطبيق</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('cropperModal');

        // إضافة الأنماط
        this.addStyles();
    }

    /**
     * إضافة أنماط CSS
     */
    addStyles() {
        if (document.getElementById('cropperStyles')) return;

        const styles = `
            <style id="cropperStyles">
                .cropper-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.8);
                    z-index: 10000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    direction: rtl;
                }
                
                .cropper-modal-content {
                    background: #fff;
                    border-radius: 12px;
                    max-width: 90vw;
                    max-height: 90vh;
                    width: 700px;
                    overflow: hidden;
                    box-shadow: 0 10px 50px rgba(0, 0, 0, 0.3);
                }
                
                .cropper-modal-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem 1.5rem;
                    border-bottom: 1px solid #e5e7eb;
                    background: #f8fafc;
                }
                
                .cropper-modal-header h3 {
                    margin: 0;
                    font-size: 1.1rem;
                    color: #1f2937;
                }
                
                .cropper-close {
                    background: none;
                    border: none;
                    font-size: 1.5rem;
                    cursor: pointer;
                    color: #6b7280;
                    line-height: 1;
                }
                
                .cropper-close:hover {
                    color: #ef4444;
                }
                
                .cropper-modal-body {
                    padding: 1.5rem;
                    display: flex;
                    gap: 1.5rem;
                }
                
                .cropper-container {
                    flex: 1;
                    max-height: 400px;
                    overflow: hidden;
                    background: #1f2937;
                    border-radius: 8px;
                }
                
                .cropper-container img {
                    max-width: 100%;
                    display: block;
                }
                
                .cropper-preview-container {
                    width: 150px;
                    text-align: center;
                }
                
                .cropper-preview-container p {
                    margin: 0 0 0.5rem;
                    color: #6b7280;
                    font-size: 0.875rem;
                }
                
                .cropper-preview {
                    width: 120px;
                    height: 120px;
                    overflow: hidden;
                    border: 2px solid #e5e7eb;
                    border-radius: 8px;
                    margin: 0 auto;
                    background: #f3f4f6;
                }
                
                .cropper-modal-footer {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 1rem 1.5rem;
                    border-top: 1px solid #e5e7eb;
                    background: #f8fafc;
                }
                
                .cropper-tools {
                    display: flex;
                    gap: 0.5rem;
                }
                
                .cropper-actions {
                    display: flex;
                    gap: 0.75rem;
                }
                
                .cropper-btn {
                    padding: 0.5rem 1rem;
                    border: 1px solid #d1d5db;
                    border-radius: 6px;
                    background: #fff;
                    cursor: pointer;
                    font-size: 0.9rem;
                    transition: all 0.2s;
                }
                
                .cropper-btn:hover {
                    background: #f3f4f6;
                }
                
                .cropper-btn-primary {
                    background: #10b981;
                    color: #fff;
                    border-color: #10b981;
                }
                
                .cropper-btn-primary:hover {
                    background: #059669;
                }
                
                .cropper-btn-secondary {
                    background: #6b7280;
                    color: #fff;
                    border-color: #6b7280;
                }
                
                .cropper-btn-secondary:hover {
                    background: #4b5563;
                }
                
                /* Cropper.js overrides */
                .cropper-view-box,
                .cropper-face {
                    border-radius: 0;
                }
                
                .cropper-line {
                    background-color: #10b981;
                }
                
                .cropper-point {
                    background-color: #10b981;
                    width: 10px;
                    height: 10px;
                }
                
                /* مؤشر الصورة المقتصة */
                .cropper-input-wrapper {
                    position: relative;
                }
                
                .cropper-input-wrapper .cropped-indicator {
                    position: absolute;
                    top: 50%;
                    right: 10px;
                    transform: translateY(-50%);
                    background: #10b981;
                    color: #fff;
                    padding: 2px 8px;
                    border-radius: 4px;
                    font-size: 0.75rem;
                }
            </style>
        `;

        document.head.insertAdjacentHTML('beforeend', styles);
    }

    /**
     * ربط الأحداث
     */
    bindEvents() {
        // أزرار النافذة
        document.getElementById('cropperClose')?.addEventListener('click', () => this.close());
        document.getElementById('cropperCancel')?.addEventListener('click', () => this.close());
        document.getElementById('cropperApply')?.addEventListener('click', () => this.apply());

        // أدوات الاقتصاص
        document.getElementById('cropperRotateLeft')?.addEventListener('click', () => this.rotate(-90));
        document.getElementById('cropperRotateRight')?.addEventListener('click', () => this.rotate(90));
        document.getElementById('cropperFlipH')?.addEventListener('click', () => this.flip('h'));
        document.getElementById('cropperFlipV')?.addEventListener('click', () => this.flip('v'));
        document.getElementById('cropperReset')?.addEventListener('click', () => this.reset());

        // إغلاق بالنقر خارج النافذة
        this.modal?.addEventListener('click', (e) => {
            if (e.target === this.modal) this.close();
        });

        // إغلاق بـ Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal?.style.display !== 'none') {
                this.close();
            }
        });
    }

    /**
     * تطبيق الاقتصاص على حقل ملفات
     */
    attach(inputSelector, previewSelector = null) {
        const inputs = document.querySelectorAll(inputSelector);

        inputs.forEach(input => {
            input.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file && file.type.startsWith('image/')) {
                    this.currentInput = input;
                    this.currentPreview = previewSelector ? document.querySelector(previewSelector) : null;
                    this.open(file);
                }
            });
        });
    }

    /**
     * فتح نافذة الاقتصاص
     */
    open(file) {
        const reader = new FileReader();

        reader.onload = (e) => {
            const image = document.getElementById('cropperImage');
            image.src = e.target.result;

            this.modal.style.display = 'flex';

            // تدمير cropper سابق إن وجد
            if (this.cropper) {
                this.cropper.destroy();
            }

            // إنشاء cropper جديد
            this.cropper = new Cropper(image, {
                aspectRatio: this.options.aspectRatio,
                viewMode: 1,
                dragMode: 'move',
                autoCropArea: 0.8,
                restore: false,
                guides: true,
                center: true,
                highlight: false,
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                preview: '#cropperPreview',
                ready: () => {
                    // جاهز للاقتصاص
                }
            });
        };

        reader.readAsDataURL(file);
    }

    /**
     * إغلاق النافذة
     */
    close() {
        this.modal.style.display = 'none';

        if (this.cropper) {
            this.cropper.destroy();
            this.cropper = null;
        }

        // إعادة تعيين الحقل إذا تم الإلغاء
        if (this.currentInput && !this.croppedBlob) {
            this.currentInput.value = '';
        }
    }

    /**
     * تطبيق الاقتصاص
     */
    apply() {
        if (!this.cropper) return;

        const canvas = this.cropper.getCroppedCanvas({
            width: this.options.maxWidth,
            height: this.options.maxHeight,
            minWidth: this.options.minWidth,
            minHeight: this.options.minHeight,
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });

        canvas.toBlob((blob) => {
            this.croppedBlob = blob;

            // تحديث حقل الملفات
            if (this.currentInput) {
                const file = new File([blob], 'cropped-image.jpg', { type: this.options.outputFormat });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                this.currentInput.files = dataTransfer.files;

                // إضافة مؤشر بصري
                this.addCroppedIndicator(this.currentInput);
            }

            // تحديث المعاينة
            if (this.currentPreview) {
                this.currentPreview.src = canvas.toDataURL(this.options.outputFormat, this.options.outputQuality);
            }

            this.close();

        }, this.options.outputFormat, this.options.outputQuality);
    }

    /**
     * إضافة مؤشر بصري للاقتصاص
     */
    addCroppedIndicator(input) {
        // إزالة مؤشر سابق
        const existingIndicator = input.parentElement.querySelector('.cropped-indicator');
        if (existingIndicator) existingIndicator.remove();

        // إضافة wrapper إذا لم يكن موجوداً
        if (!input.parentElement.classList.contains('cropper-input-wrapper')) {
            const wrapper = document.createElement('div');
            wrapper.className = 'cropper-input-wrapper';
            input.parentNode.insertBefore(wrapper, input);
            wrapper.appendChild(input);
        }

        // إضافة المؤشر
        const indicator = document.createElement('span');
        indicator.className = 'cropped-indicator';
        indicator.textContent = '✓ مقتصة';
        input.parentElement.appendChild(indicator);
    }

    /**
     * تدوير الصورة
     */
    rotate(degree) {
        if (this.cropper) {
            this.cropper.rotate(degree);
        }
    }

    /**
     * قلب الصورة
     */
    flip(direction) {
        if (this.cropper) {
            const data = this.cropper.getData();
            if (direction === 'h') {
                this.cropper.scaleX(data.scaleX === -1 ? 1 : -1);
            } else {
                this.cropper.scaleY(data.scaleY === -1 ? 1 : -1);
            }
        }
    }

    /**
     * إعادة تعيين
     */
    reset() {
        if (this.cropper) {
            this.cropper.reset();
        }
    }
}

// تصدير للاستخدام الخارجي
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ImageCropper;
}
