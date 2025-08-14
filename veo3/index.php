<?php
session_start();
require_once 'auth.php';
include 'templates/header.php';
?>
        <div class="header">
            <h1>🎬 مولد البروموتس الإعلانية</h1>
            <p>أدخل اسم المنتج أو ارفع صورته واحصل على وصف سينمائي احترافي لفيديو تسويقي مذهل</p>
        </div>
        
        <div class="main-card">
            <div class="flex border-b border-gray-200">
                <button id="simple-tab" class="tab-btn active">🚀 توليد بسيط</button>
                <button id="advanced-tab" class="tab-btn">⚙️ توليد متقدم</button>
            </div>

            <!-- Simple Generation Form -->
            <form id="simplePromptForm" class="tab-content active">
                <div class="input-group my-6">
                    <label for="simpleQuery">اكتب فكرتك أو وصف قصير</label>
                    <textarea id="simpleQuery" rows="3" class="input-field" placeholder="مثال: إعلان عن مشروب طاقة جديد للرياضيين..."></textarea>
                </div>
                <div class="input-group my-6">
                    <label for="simpleProductImage">أو ارفع صورة المنتج (اختياري)</label>
                    <input type="file" id="simpleProductImage" class="file-input" accept="image/*">
                    <label for="simpleProductImage" class="file-label">
                        <span class="text-xl">📷</span> اختر صورة المنتج
                    </label>
                    <div id="simpleImagePreview" class="file-preview"></div>
                </div>
                <button type="submit" class="generate-btn">✨ توليد السيناريو</button>
            </form>

            <!-- Advanced Generation Form (Original Form) -->
            <form id="advancedPromptForm" class="tab-content">
                <div class="input-section">
                    <div class="input-group">
                        <label for="productName">اسم المنتج</label>
                        <input type="text" id="productName" class="input-field" placeholder="مثال: نوتيلا، آيفون، بيتزا...">
                    </div>
                    
                    <div class="input-group">
                        <label for="productImage">أو ارفع صورة المنتج</label>
                        <input type="file" id="productImage" class="file-input" accept="image/*">
                        <label for="productImage" class="file-label">
                            <span class="text-xl">📷</span> اختر صورة المنتج
                        </label>
                        <div id="imagePreview" class="file-preview"></div>
                    </div>
                </div>
                
                <div class="options-section">
                    <h3 class="text-xl font-bold mb-4 text-gray-700">خيارات الفيديو</h3>
                    <p class="text-sm text-gray-500 mb-4">
                       مخرجات البرومبت ستكون باللغة الإنجليزية تلقائيًا، ولكن يمكنك اختيار لغة الحوار والسكربت هنا.
                    </p>
                    <div class="options-grid">
                        <div class="option-group">
                            <label for="videoStyle">نمط الفيديو</label>
                            <select id="videoStyle">
                                <option value="cinematic">سينمائي احترافي</option>
                                <option value="modern">عصري وأنيق</option>
                                <option value="playful">مرح وحيوي</option>
                                <option value="luxury">فاخر وراقي</option>
                                <option value="minimalist">بسيط ونظيف</option>
                            </select>
                        </div>
                        
                        <div class="option-group">
                            <label for="targetAudience">الجمهور المستهدف</label>
                            <select id="targetAudience">
                                <option value="general">عام</option>
                                <option value="young">الشباب</option>
                                <option value="family">العائلات</option>
                                <option value="professional">المهنيين</option>
                                <option value="luxury">الطبقة الراقية</option>
                            </select>
                        </div>
                        
                        <div class="option-group">
                            <label for="videoDuration">مدة الفيديو</label>
                            <select id="videoDuration">
                                <option value="08">08 ثانية</option>
                                <option value="15">15 ثانية</option>
                                <option value="30">30 ثانية</option>
                                <option value="60">60 ثانية</option>
                            </select>
                        </div>
                        
                        <div class="option-group">
                            <label for="mood">المزاج العام</label>
                            <select id="mood">
                                <option value="energetic">حيوي ومفعم بالطاقة</option>
                                <option value="calm">هادئ ومريح</option>
                                <option value="exciting">مثير ومشوق</option>
                                <option value="warm">دافئ وودود</option>
                                <option value="mysterious">غامض وجذاب</option>
                            </select>
                        </div>
                        
                        <div class="option-group">
                            <label for="scriptLanguage">لغة الحوار والسكربت</label>
                            <select id="scriptLanguage">
                                <option value="ar">العربية</option>
                                <option value="en">الإنجليزية</option>
                                <option value="fr">الفرنسية</option>
                                <option value="es">الإسبانية</option>
                                <option value="de">الألمانية</option>
                            </select>
                        </div>
                    </div>

                    <div class="input-group mt-6">
                        <label for="dialogue">الحوار أو السكربت (اختياري)</label>
                        <textarea id="dialogue" rows="4" class="input-field" placeholder="اكتب هنا أي حوار أو نص ترغب في تضمينه في الفيديو..."></textarea>
                    </div>
                </div>
                
                <button type="submit" class="generate-btn" id="generateBtn">
                    ✨ إنتاج البرومبت الإعلاني
                    <div class="loading-spinner" id="loadingSpinner"></div>
                </button>
            </form>
            
            <div id="resultSection" class="result-section">
                <div class="result-title">
                    🎯 البرومبت الإعلاني المولد
                </div>
                <pre id="resultContent" class="result-content"></pre>
                <button class="copy-btn" id="copyBtn">📋 نسخ البرومبت</button>
            </div>
        </div>
<?php include 'templates/footer.php'; ?>
