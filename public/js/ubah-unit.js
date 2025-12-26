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
            document.getElementById('formUbahUnit').submit();
        }
    });
});
