// Tab switching logic
const simpleTab = document.getElementById('simple-tab');
const advancedTab = document.getElementById('advanced-tab');
const simpleForm = document.getElementById('simplePromptForm');
const advancedForm = document.getElementById('advancedPromptForm');

if (simpleTab && advancedTab) {
    simpleTab.addEventListener('click', () => {
        simpleTab.classList.add('active');
        advancedTab.classList.remove('active');
        simpleForm.classList.add('active');
        advancedForm.classList.remove('active');
    });

    advancedTab.addEventListener('click', () => {
        advancedTab.classList.add('active');
        simpleTab.classList.remove('active');
        advancedForm.classList.add('active');
        simpleForm.classList.remove('active');
    });
}

// العناصر الرئيسية للواجهة
const generateBtn = document.getElementById('generateBtn');
const loadingSpinner = document.getElementById('loadingSpinner');
const resultSection = document.getElementById('resultSection');
const resultContent = document.getElementById('resultContent');
const copyBtn = document.getElementById('copyBtn');
const productNameInput = document.getElementById('productName');
const productImageInput = document.getElementById('productImage');
const imagePreview = document.getElementById('imagePreview');
const scriptLanguageInput = document.getElementById('scriptLanguage');
const dialogueInput = document.getElementById('dialogue');
const simpleProductImageInput = document.getElementById('simpleProductImage');
const simpleImagePreview = document.getElementById('simpleImagePreview');

// Handle image preview for both forms
function setupImagePreview(inputElement, previewElement) {
    if (inputElement) {
        inputElement.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewElement.innerHTML = `<img src="${e.target.result}" alt="Product Preview" class="rounded-lg shadow-md mt-4">`;
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

setupImagePreview(productImageInput, imagePreview);
setupImagePreview(simpleProductImageInput, simpleImagePreview);

// دالة لتحويل ملف الصورة إلى Base64
function fileToBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result.split(',')[1]);
        reader.onerror = error => reject(error);
        reader.readAsDataURL(file);
    });
}

// Handle Simple Form Submission
if (simpleForm) {
    simpleForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        const query = document.getElementById('simpleQuery').value.trim();
        const imageFile = simpleProductImageInput.files[0];

        if (!query && !imageFile) {
            displayMessage('يرجى إدخال فكرة أو رفع صورة.', 'error');
            return;
        }

        // تفعيل حالة التحميل
        const simpleGenerateBtn = simpleForm.querySelector('.generate-btn');
        simpleGenerateBtn.classList.add('loading');
        simpleGenerateBtn.innerHTML = 'جاري الإنتاج... <div class="loading-spinner" id="loadingSpinner" style="display: inline-block;"></div>';
        resultSection.classList.remove('show');
        resultContent.textContent = '';

        const formData = {
            isSimple: true,
            query: query,
            imageData: null
        };

        if (imageFile) {
            try {
                formData.imageData = await fileToBase64(imageFile);
            } catch (error) {
                displayMessage('حدث خطأ في قراءة الصورة. يرجى المحاولة مرة أخرى.', 'error');
                console.error('Error reading image file:', error);
                simpleGenerateBtn.classList.remove('loading');
                simpleGenerateBtn.innerHTML = '✨ توليد السيناريو';
                return;
            }
        }

        try {
            const response = await fetch('generate_prompt.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                const errorData = await response.json();
                let errorMessage = errorData.error || `API call failed with status: ${response.status}`;
                
                // Check for a more detailed error message from our custom PHP script
                if (errorData.details && errorData.details.error && errorData.details.error.message) {
                    errorMessage = `API Error: ${errorData.details.error.message}`;
                }
                
                throw new Error(errorMessage);
            }

            const result = await response.json();
            const formattedPrompt = JSON.stringify(result, null, 2);
            resultContent.textContent = formattedPrompt;
            resultSection.classList.add('show');

        } catch (error) {
            displayMessage(error.message, 'error');
            console.error('API Error:', error);
        } finally {
            simpleGenerateBtn.classList.remove('loading');
            simpleGenerateBtn.innerHTML = '✨ توليد السيناريو';
        }
    });
}

// معالج إرسال النموذج المتقدم
if (advancedForm) {
    advancedForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // تفعيل حالة التحميل
        generateBtn.classList.add('loading');
        loadingSpinner.style.display = 'inline-block';
        generateBtn.innerHTML = 'جاري الإنتاج... <div class="loading-spinner" id="loadingSpinner" style="display: inline-block;"></div>';
        resultSection.classList.remove('show');
        resultContent.textContent = '';
        
        const productName = productNameInput.value.trim();
        const style = document.getElementById('videoStyle').value;
        const audience = document.getElementById('targetAudience').value;
        const duration = document.getElementById('videoDuration').value;
        const mood = document.getElementById('mood').value;
        const scriptLanguage = scriptLanguageInput.value;
        const dialogue = dialogueInput.value.trim();
        const imageFile = productImageInput.files[0];
        
        if (!productName && !imageFile) {
            displayMessage('يرجى إدخال اسم المنتج أو رفع صورة.', 'error');
            resetButtonState();
            return;
        }
        
        const formData = {
            isSimple: false,
            productName: productName,
            videoStyle: style,
            targetAudience: audience,
            videoDuration: duration,
            mood: mood,
            scriptLanguage: scriptLanguage,
            dialogue: dialogue,
            imageData: null
        };

        if (imageFile) {
            try {
                formData.imageData = await fileToBase64(imageFile);
            } catch (error) {
                displayMessage('حدث خطأ في قراءة الصورة. يرجى المحاولة مرة أخرى.', 'error');
                console.error('Error reading image file:', error);
                resetButtonState();
                return;
            }
        }

        try {
            const response = await fetch('generate_prompt.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(formData)
            });

            if (!response.ok) {
                const errorData = await response.json();
                let errorMessage = errorData.error || `API call failed with status: ${response.status}`;

                // Check for a more detailed error message from our custom PHP script
                if (errorData.details && errorData.details.error && errorData.details.error.message) {
                    errorMessage = `API Error: ${errorData.details.error.message}`;
                }

                throw new Error(errorMessage);
            }

            const result = await response.json();
            const formattedPrompt = JSON.stringify(result, null, 2);
            resultContent.textContent = formattedPrompt;
            resultSection.classList.add('show');

        } catch (error) {
            displayMessage(error.message, 'error');
            console.error('API Error:', error);
        } finally {
            resetButtonState();
        }
    });
}

// دالة لإعادة زر الإنتاج إلى حالته الأصلية
function resetButtonState() {
    if (generateBtn) {
        generateBtn.classList.remove('loading');
        if(loadingSpinner) loadingSpinner.style.display = 'none';
        generateBtn.innerHTML = '✨ إنتاج البرومبت الإعلاني';
    }
}

// دالة لعرض رسالة للمستخدم (بدلاً من alert)
function displayMessage(message, type) {
    const messageBox = document.createElement('div');
    messageBox.textContent = message;
    messageBox.className = `fixed top-5 left-1/2 -translate-x-1/2 p-4 rounded-lg shadow-lg z-50 text-white`;
    if (type === 'error') {
        messageBox.classList.add('bg-red-500');
    } else {
        messageBox.classList.add('bg-green-500');
    }
    document.body.appendChild(messageBox);
    setTimeout(() => {
        messageBox.remove();
    }, 3000);
}

// معالج نسخ النص
if (copyBtn) {
    copyBtn.addEventListener('click', function() {
        const textArea = document.createElement('textarea');
        textArea.value = resultContent.textContent;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        
        copyBtn.textContent = '✅ تم النسخ';
        setTimeout(() => {
            copyBtn.textContent = '📋 نسخ البرومبت';
        }, 2000);
        displayMessage('تم نسخ البرومبت بنجاح!', 'success');
    });
}