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

function phoneFieldHandler(inputClass, fieldName = "Nomor Telepon", min = 10, max = 15) {
    const input = document.querySelector(inputClass);
    if (!input) return;

    input.addEventListener('input', (e) => {
        const oldValue = e.target.value;
        const newValue = oldValue.replace(/[^0-9]/g, '');
        const errorId = `error-${fieldName.replace(/\s+/g, '').toLowerCase()}`;

        // 1. GET THE PARENT WRAPPER (The <div class="relative">)
        const wrapper = input.parentElement;

        let errorEl = document.getElementById(errorId);

        // Sanitize value (numbers only)
        if (oldValue !== newValue) e.target.value = newValue;

        // Validate length
        if (newValue.length < min || newValue.length > max) {
            // Style the Input
            input.classList.add('border-red-500', 'bg-red-50');
            input.classList.remove('border-gray-500', 'bg-gray-50');

            // Create Error Message if it doesn't exist
            if (!errorEl) {
                errorEl = document.createElement('p');
                errorEl.id = errorId;
                errorEl.className = "text-red-600 text-xs mt-1 ml-1";
                errorEl.textContent = `${fieldName} harus terdiri dari ${min}-${max} angka`;

                // 2. INSERT ERROR AFTER THE WRAPPER (Outside the relative div)
                wrapper.insertAdjacentElement('afterend', errorEl);
            }
        } else {
            // Clear Error
            input.classList.remove('border-red-500', 'bg-red-50');
            input.classList.add('border-gray-500', 'bg-gray-50');

            if (errorEl) {
                errorEl.remove();
            }
        }
    });
}

// Initialize
phoneFieldHandler('.telp_perusahaan-input', "Nomor Telepon Perusahaan", 10, 13);

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
            document.getElementById('formTambahMitraKerja').submit();
        }
    });
});
