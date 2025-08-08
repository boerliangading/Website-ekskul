// Form Validation untuk Sistem Ekstrakurikuler UNINDRA
document.addEventListener('DOMContentLoaded', function() {
    
    // Validasi form mahasiswa
    const formMahasiswa = document.getElementById('formMahasiswa');
    if (formMahasiswa) {
        formMahasiswa.addEventListener('submit', function(e) {
            if (!validateMahasiswaForm()) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const nimInput = document.getElementById('nim');
        const emailInput = document.getElementById('email');
        
        if (nimInput) {
            nimInput.addEventListener('input', function() {
                validateNIM(this);
            });
        }
        
        if (emailInput) {
            emailInput.addEventListener('input', function() {
                validateEmail(this);
            });
        }
    }
    
    // Validasi form ekstrakurikuler
    const formEkskul = document.getElementById('formEkskul');
    if (formEkskul) {
        formEkskul.addEventListener('submit', function(e) {
            if (!validateEkskulForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Validasi semua form
    const allForms = document.querySelectorAll('form');
    allForms.forEach(form => {
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(field => {
            field.addEventListener('blur', function() {
                validateRequiredField(this);
            });
            
            field.addEventListener('input', function() {
                if (this.classList.contains('error')) {
                    validateRequiredField(this);
                }
            });
        });
    });
});

function validateMahasiswaForm() {
    const nim = document.getElementById('nim');
    const nama = document.getElementById('nama');
    const email = document.getElementById('email');
    const jurusan = document.getElementById('jurusan');
    const semester = document.getElementById('semester');
    
    let isValid = true;
    
    if (!validateNIM(nim)) isValid = false;
    if (!validateRequiredField(nama)) isValid = false;
    if (!validateEmail(email)) isValid = false;
    if (!validateRequiredField(jurusan)) isValid = false;
    if (!validateRequiredField(semester)) isValid = false;
    
    if (!isValid) {
        showNotification('Mohon perbaiki kesalahan pada form!', 'error');
    }
    
    return isValid;
}

function validateEkskulForm() {
    const namaEkskul = document.getElementById('nama_ekskul');
    const deskripsi = document.getElementById('deskripsi');
    const pembina = document.getElementById('pembina');
    const hari = document.getElementById('hari');
    const waktu = document.getElementById('waktu');
    const tempat = document.getElementById('tempat');
    
    let isValid = true;
    
    if (!validateRequiredField(namaEkskul)) isValid = false;
    if (!validateRequiredField(deskripsi)) isValid = false;
    if (!validateRequiredField(pembina)) isValid = false;
    if (!validateRequiredField(hari)) isValid = false;
    if (!validateRequiredField(waktu)) isValid = false;
    if (!validateRequiredField(tempat)) isValid = false;
    
    if (!isValid) {
        showNotification('Mohon lengkapi semua field yang wajib diisi!', 'error');
    }
    
    return isValid;
}

function validateNIM(nimField) {
    const nim = nimField.value.trim();
    const nimPattern = /^[0-9]{8,}$/;
    
    clearFieldError(nimField);
    
    if (nim === '') {
        showFieldError(nimField, 'NIM tidak boleh kosong');
        return false;
    }
    
    if (!nimPattern.test(nim)) {
        showFieldError(nimField, 'NIM harus berupa angka minimal 8 digit');
        return false;
    }
    
    return true;
}

function validateEmail(emailField) {
    const email = emailField.value.trim();
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    clearFieldError(emailField);
    
    if (email === '') {
        showFieldError(emailField, 'Email tidak boleh kosong');
        return false;
    }
    
    if (!emailPattern.test(email)) {
        showFieldError(emailField, 'Format email tidak valid');
        return false;
    }
    
    return true;
}

function validateRequiredField(field) {
    const value = field.value.trim();
    
    clearFieldError(field);
    
    if (value === '') {
        showFieldError(field, 'Field ini wajib diisi');
        return false;
    }
    
    return true;
}

function showFieldError(field, message) {
    field.classList.add('error');
    field.style.borderColor = '#f44336';
    
    // Remove existing error message
    const existingError = field.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Add error message
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.style.color = '#f44336';
    errorDiv.style.fontSize = '0.8rem';
    errorDiv.style.marginTop = '0.25rem';
    errorDiv.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('error');
    field.style.borderColor = '#e0e0e0';
    
    const errorMessage = field.parentNode.querySelector('.error-message');
    if (errorMessage) {
        errorMessage.remove();
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type}`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    notification.style.animation = 'slideInRight 0.3s ease';
    
    const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
    notification.innerHTML = `
        <i class="fas fa-${icon}"></i>
        ${message}
        <button onclick="this.parentNode.remove()" style="background: none; border: none; float: right; font-size: 1.2rem; cursor: pointer;">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// Prevent double submission
function preventDoubleSubmit() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                
                // Re-enable after 3 seconds (safety measure)
                setTimeout(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.innerHTML.replace('<i class="fas fa-spinner fa-spin"></i> Memproses...', submitBtn.textContent);
                }, 3000);
            }
        });
    });
}

// Character counter for textarea
function addCharacterCounter() {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        if (maxLength) {
            const counter = document.createElement('div');
            counter.style.textAlign = 'right';
            counter.style.fontSize = '0.8rem';
            counter.style.color = '#666';
            counter.style.marginTop = '0.25rem';
            
            textarea.parentNode.appendChild(counter);
            
            function updateCounter() {
                const remaining = maxLength - textarea.value.length;
                counter.textContent = `${remaining} karakter tersisa`;
                counter.style.color = remaining < 20 ? '#f44336' : '#666';
            }
            
            textarea.addEventListener('input', updateCounter);
            updateCounter();
        }
    });
}

// Initialize additional features
document.addEventListener('DOMContentLoaded', function() {
    preventDoubleSubmit();
    addCharacterCounter();
});

// CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .form-control.error {
        border-color: #f44336 !important;
        box-shadow: 0 0 0 0.2rem rgba(244, 67, 54, 0.25);
    }
    
    .error-message {
        animation: fadeIn 0.3s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
`;
document.head.appendChild(style);