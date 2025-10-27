// Fungsi untuk menampilkan IPK otomatis dan mengatur status form
function checkIPK() {
    // Simulasi mendapatkan IPK otomatis random antara 2.6 - 4.0
    const min = 2.6;
    const max = 4.0;
    const ipkValue = Math.round((min + Math.random() * (max - min)) * 100) / 100; // Bulatkan ke 2 desimal
    
    // Update display IPK
    const ipkDisplay = document.getElementById('ipk-display');
    const ipkInput = document.getElementById('ipk');
    
    if (ipkDisplay && ipkInput) {
        ipkDisplay.textContent = ipkValue.toFixed(1);
        ipkInput.value = ipkValue;
        
        // Atur status form berdasarkan IPK
        toggleFormElements(ipkValue >= 3.0);
        
        // Tampilkan pesan
        showIPKMessage(ipkValue);
    }
}

// Fungsi untuk mengaktifkan/menonaktifkan elemen form
function toggleFormElements(enable) {
    const elements = [
        'jenis_beasiswa',
        'berkas_syarat',
        'submit-btn'
    ];
    
    elements.forEach(id => {
        const element = document.getElementById(id);
        if (element) {
            element.disabled = !enable;
            if (enable) {
                element.classList.remove('disabled');
            } else {
                element.classList.add('disabled');
            }
        }
    });
    
    // Jika IPK memenuhi syarat, fokus ke pilihan beasiswa
    if (enable) {
        const beasiswaSelect = document.getElementById('jenis_beasiswa');
        if (beasiswaSelect) {
            setTimeout(() => beasiswaSelect.focus(), 100);
        }
    }
}

// Fungsi untuk menampilkan pesan IPK
function showIPKMessage(ipk) {
    const messageDiv = document.getElementById('ipk-message');
    if (messageDiv) {
        let message = '';
        let alertClass = '';
        
        if (ipk >= 3.75) {
            message = `IPK ${ipk} - Excellent! Memenuhi syarat semua jenis beasiswa.`;
            alertClass = 'alert-success';
        } else if (ipk >= 3.5) {
            message = `IPK ${ipk} - Sangat Baik! Memenuhi syarat beasiswa akademik dan prestasi.`;
            alertClass = 'alert-success';
        } else if (ipk >= 3.25) {
            message = `IPK ${ipk} - Baik! Memenuhi syarat beasiswa prestasi dan non-akademik.`;
            alertClass = 'alert-success';
        } else if (ipk >= 3.0) {
            message = `IPK ${ipk} - Memenuhi syarat untuk beasiswa non-akademik.`;
            alertClass = 'alert-success';
        } else {
            message = `IPK ${ipk} - Maaf, belum memenuhi syarat minimum (3.0) untuk mendaftar beasiswa.`;
            alertClass = 'alert-danger';
        }
        
        messageDiv.innerHTML = `<div class="alert ${alertClass}">${message}</div>`;
    }
}

// Validasi email real-time
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Validasi nomor HP real-time
function validatePhone(phone) {
    const phoneRegex = /^[0-9]+$/;
    return phoneRegex.test(phone);
}

// Fungsi untuk validasi form
function validateForm() {
    let isValid = true;
    const errors = [];
    
    // Validasi nama
    const nama = document.getElementById('nama').value.trim();
    if (nama === '') {
        errors.push('Nama harus diisi');
        isValid = false;
    }
    
    // Validasi email
    const email = document.getElementById('email').value.trim();
    if (email === '') {
        errors.push('Email harus diisi');
        isValid = false;
    } else if (!validateEmail(email)) {
        errors.push('Format email tidak valid');
        isValid = false;
    }
    
    // Validasi nomor HP
    const noHp = document.getElementById('no_hp').value.trim();
    if (noHp === '') {
        errors.push('Nomor HP harus diisi');
        isValid = false;
    } else if (!validatePhone(noHp)) {
        errors.push('Nomor HP hanya boleh berisi angka');
        isValid = false;
    }
    
    // Validasi semester
    const semester = document.getElementById('semester').value;
    if (semester === '') {
        errors.push('Semester harus dipilih');
        isValid = false;
    }
    
    // Validasi IPK
    const ipk = parseFloat(document.getElementById('ipk').value);
    if (ipk < 3.0) {
        errors.push('IPK tidak memenuhi syarat minimum');
        isValid = false;
    }
    
    // Validasi jenis beasiswa
    const jenisBeasiswa = document.getElementById('jenis_beasiswa').value;
    if (jenisBeasiswa === '') {
        errors.push('Jenis beasiswa harus dipilih');
        isValid = false;
    }
    
    // Validasi berkas
    const berkas = document.getElementById('berkas_syarat').files[0];
    if (!berkas) {
        errors.push('Berkas syarat harus diupload');
        isValid = false;
    } else {
        // Validasi ekstensi file
        const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'zip'];
        const fileExtension = berkas.name.split('.').pop().toLowerCase();
        if (!allowedExtensions.includes(fileExtension)) {
            errors.push('Format file tidak didukung. Gunakan: PDF, JPG, PNG, atau ZIP');
            isValid = false;
        }
        
        // Validasi ukuran file (max 5MB)
        if (berkas.size > 5 * 1024 * 1024) {
            errors.push('Ukuran file maksimal 5MB');
            isValid = false;
        }
    }
    
    // Tampilkan error jika ada
    const errorDiv = document.getElementById('form-errors');
    if (errorDiv) {
        if (errors.length > 0) {
            errorDiv.innerHTML = '<div class="alert alert-danger"><ul>' + 
                errors.map(error => '<li>' + error + '</li>').join('') + 
                '</ul></div>';
        } else {
            errorDiv.innerHTML = '';
        }
    }
    
    return isValid;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate IPK saat halaman dimuat
    const ipkButton = document.getElementById('generate-ipk');
    if (ipkButton) {
        ipkButton.addEventListener('click', checkIPK);
        // Generate IPK otomatis saat halaman dimuat
        checkIPK();
    }
    
    // Validasi email real-time
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value.trim();
            const feedback = document.getElementById('email-feedback');
            if (feedback) {
                if (email && !validateEmail(email)) {
                    feedback.innerHTML = '<small style="color: #e74c3c;">Format email tidak valid</small>';
                } else {
                    feedback.innerHTML = '';
                }
            }
        });
    }
    
    // Validasi nomor HP real-time
    const phoneInput = document.getElementById('no_hp');
    if (phoneInput) {
        phoneInput.addEventListener('input', function() {
            // Hanya izinkan angka
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        phoneInput.addEventListener('blur', function() {
            const phone = this.value.trim();
            const feedback = document.getElementById('phone-feedback');
            if (feedback) {
                if (phone && !validatePhone(phone)) {
                    feedback.innerHTML = '<small style="color: #e74c3c;">Nomor HP hanya boleh berisi angka</small>';
                } else {
                    feedback.innerHTML = '';
                }
            }
        });
    }
    
    // Validasi form sebelum submit
    const form = document.getElementById('beasiswa-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Preview file yang diupload
    const fileInput = document.getElementById('berkas_syarat');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            const preview = document.getElementById('file-preview');
            if (preview) {
                if (file) {
                    const fileSize = (file.size / 1024 / 1024).toFixed(2);
                    preview.innerHTML = `<small>File dipilih: ${file.name} (${fileSize} MB)</small>`;
                } else {
                    preview.innerHTML = '';
                }
            }
        });
    }
});

// Fungsi untuk konfirmasi sebelum submit
function confirmSubmit() {
    return confirm('Apakah Anda yakin data yang diisi sudah benar? Data akan disimpan dan tidak dapat diubah.');
}