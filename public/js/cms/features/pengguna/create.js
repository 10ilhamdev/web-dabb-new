(function () {
    const photoInput = document.getElementById('photo');
    const preview = document.getElementById('photoPreview');
    if (photoInput && preview) {
        photoInput.addEventListener('change', function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="preview" class="w-full h-full object-cover">';
            };
            reader.readAsDataURL(file);
        });
    }

    const form = document.getElementById('formPengguna');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        const password = form.querySelector('input[name="password"]');
        const confirm = form.querySelector('input[name="password_confirmation"]');
        if (password && confirm && password.value !== confirm.value) {
            e.preventDefault();
            if (window.Swal) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password tidak cocok',
                    text: 'Password dan konfirmasi password harus sama.',
                    confirmButtonColor: '#174E93'
                });
            } else {
                alert('Password dan konfirmasi password harus sama.');
            }
        }
    });
})();
