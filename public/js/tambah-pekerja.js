// Preview Photo Section
function previewPhoto(event) {
    const input = event.target;
    const preview = document.getElementById('previewImage');
    const placeholder = document.getElementById('placeholder');
    const removeButton = document.getElementById('removeBtn');

    const file = input.files[0];
    if (!file) return;

    // Validasi ukuran max 2MB
    if (file.size > 2 * 1024 * 1024) {
        alert("Ukuran foto maksimal 2MB!");
        input.value = "";
        return;
    }

    // Preview image
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        placeholder.classList.add('hidden');
        removeButton.classList.remove('hidden');
    };
    reader.readAsDataURL(file);
}

function removePhoto(event) {
    event.stopPropagation(); // Mencegah klik area upload

    document.getElementById('fotoInput').value = "";
    document.getElementById('previewImage').src = "";
    document.getElementById('previewImage').classList.add('hidden');
    document.getElementById('placeholder').classList.remove('hidden');
    document.getElementById('removeBtn').classList.add('hidden');
}

function numericFieldHandler(inputClass, fieldName, requiredLength = null) {
    const input = document.querySelector(inputClass);
    if (!input) return;

    input.addEventListener('input', (e) => {
        let val = e.target.value.replace(/[^0-9]/g, '');
        e.target.value = val;

        const errorId = `error-${fieldName}`;
        let errorEl = document.getElementById(errorId);

        // Validasi default: hanya angka
        let errorMsg = "";
        if (val !== e.target.value) {
            errorMsg = `Input ${fieldName} hanya boleh angka`;
        }

        // Validasi panjang wajib 16 digit
        if (requiredLength && val.length > 0 && val.length !== requiredLength) {
            errorMsg = `${fieldName} harus ${requiredLength} digit`;
        }

        // Jika ada error → tampilkan styling & pesan
        if (errorMsg) {
            input.classList.add('border-red-500', 'bg-red-50');
            input.classList.remove('border-gray-500', 'bg-gray-50');

            if (!errorEl) {
                errorEl = document.createElement('p');
                errorEl.id = errorId;
                errorEl.className = "text-red-600 text-xs mt-1";
                input.insertAdjacentElement('afterend', errorEl);
            }
            errorEl.textContent = errorMsg;

        } else {
            // tidak error
            input.classList.remove('border-red-500', 'bg-red-50');
            input.classList.add('border-gray-500', 'bg-gray-50');
            errorEl?.remove();
        }
    });
}


// Apply untuk masing-masing
numericFieldHandler('.nik-input', 'NIK', 16);
numericFieldHandler('.no_kk-input', 'No KK', 16);
numericFieldHandler('.kpj-input', 'KPJ', 11);
numericFieldHandler('.naker-input', 'Naker', 13);
numericFieldHandler('.anak-input', 'Anak')
numericFieldHandler('.rt-input', 'RT');
numericFieldHandler('.rw-input', 'RW');

document.addEventListener("DOMContentLoaded", () => {
    const today = new Date().toISOString().split("T")[0];

    const tglLahir     = document.querySelector('input[name="tgl_lahir"]');
    const tglBergabung = document.querySelector('input[name="tgl_bergabung"]');

    // --- RULES ---
    if (tglLahir) tglLahir.max = today;          // Birth date cannot be in future
    if (tglBergabung) tglBergabung.max = today;  // Join date cannot be in future
});



function phoneFieldHandler(inputClass, fieldName = "Nomor Telepon", min = 10, max = 15) {
    const input = document.querySelector(inputClass);
    if (!input) return;

    input.addEventListener('input', (e) => {
        const oldValue = e.target.value;
        const newValue = oldValue.replace(/[^0-9]/g, '');
        const errorId = `error-${fieldName.replace(/\s+/g, '').toLowerCase()}`;

        let errorEl = document.getElementById(errorId);

        // sanitize value → hanya angka
        if (oldValue !== newValue) e.target.value = newValue;

        // Validasi panjang angka
        if (newValue.length < min || newValue.length > max) {
            input.classList.add('border-red-500', 'bg-red-50');
            input.classList.remove('border-gray-500', 'bg-gray-50');

            if (!errorEl) {
                errorEl = document.createElement('p');
                errorEl.id = errorId;
                errorEl.className = "text-red-600 text-xs mt-1";
                errorEl.textContent = `${fieldName} harus terdiri dari ${min}-${max} angka`;
                input.insertAdjacentElement('afterend', errorEl);
            }
        } else {
            // Jika valid
            input.classList.remove('border-red-500', 'bg-red-50');
            input.classList.add('border-gray-500', 'bg-gray-50');
            errorEl?.remove();
        }
    });
}

phoneFieldHandler('.telp_pribadi-input', "Nomor Telepon Pribadi", 10, 13);
phoneFieldHandler('.telp_emergency-input', "Nomor Telepon Emergency", 10, 13);

function emailValidationHandler() {
    const input = document.querySelector('.email-input');
    const error = document.querySelector('.email-error');

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // format valid

    input.addEventListener('input', (e) => {
        let value = e.target.value.trim();

        // valid?
        if (!emailRegex.test(value) && value !== "") {
            input.classList.remove('border-gray-500', 'bg-gray-50');
            input.classList.add('border-red-500', 'bg-red-50');
            error.classList.remove('hidden');
            error.textContent = "Format email tidak valid";
        } else {
            input.classList.add('border-gray-500', 'bg-gray-50');
            input.classList.remove('border-red-500', 'bg-red-50');
            error.classList.add('hidden');
        }
    });
}

emailValidationHandler();

function rekeningFieldHandler(inputClass, fieldName = "Nomor Rekening", min = 10, max = 16) {
    const input = document.querySelector(inputClass);
    if (!input) return;

    input.addEventListener('input', (e) => {
        const value = e.target.value.replace(/[^0-9]/g, '');
        const errorId = `error-${fieldName.replace(/\s+/g,'').toLowerCase()}`;
        let errorEl = document.getElementById(errorId);

        // Set sanitized value
        e.target.value = value;

        // Validation
        if (value.length < min || value.length > max) {
            input.classList.add('border-red-500', 'bg-red-50');
            input.classList.remove('border-gray-500', 'bg-gray-50');

            if (!errorEl) {
                errorEl = document.createElement('p');
                errorEl.id = errorId;
                errorEl.className = "text-red-600 text-xs mt-1";
                errorEl.textContent = `${fieldName} harus ${min}-${max} digit angka`;
                input.insertAdjacentElement('afterend', errorEl);
            }

        } else {
            input.classList.remove('border-red-500', 'bg-red-50');
            input.classList.add('border-gray-500', 'bg-gray-50');
            errorEl?.remove();
        }
    });
}

rekeningFieldHandler('.rekening-input', "Nomor Rekening", 10, 16);

document.getElementById('save-btn').addEventListener('click', function(){
    Swal.fire({
        title: 'Yakin Simpan Data?',
        text: "Pastikan data sudah benar!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Cek lagi'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formTambahPekerja').submit();
        }
    });
});
