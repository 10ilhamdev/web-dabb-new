(function () {
    const photoInput = document.getElementById("photo");
    const preview = document.getElementById("photoPreview");
    if (photoInput && preview) {
        photoInput.addEventListener("change", function () {
            const file = this.files && this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.innerHTML =
                    '<img src="' +
                    e.target.result +
                    '" alt="preview" class="w-full h-full object-cover">';
            };
            reader.readAsDataURL(file);
        });
    }

    const form = document.getElementById("formPengguna");
    if (!form) return;

    form.addEventListener("submit", function (e) {
        const password = form.querySelector('input[name="password"]');
        const confirm = form.querySelector(
            'input[name="password_confirmation"]',
        );
        if (!password || !password.value) return; // password is optional on edit
        if (password.value !== (confirm ? confirm.value : "")) {
            e.preventDefault();
            if (window.Swal) {
                Swal.fire({
                    icon: "error",
                    title: "Password tidak cocok",
                    text: "Password dan konfirmasi password harus sama.",
                    confirmButtonColor: "#174E93",
                });
            } else {
                alert("Password dan konfirmasi password harus sama.");
            }
        }
    });

    // Toggle role-specific profile sections
    const roleSelect = form.querySelector('select[name="role"]');
    if (roleSelect) {
        function updateRoleSections() {
            const role = roleSelect.value;
            document
                .querySelectorAll("[data-role-section]")
                .forEach(function (el) {
                    const active = el.dataset.roleSection === role;
                    el.classList.toggle("hidden", !active);
                    el.querySelectorAll("input, select, textarea").forEach(
                        function (input) {
                            input.disabled = !active;
                        },
                    );
                });
        }
        roleSelect.addEventListener("change", updateRoleSections);
        updateRoleSections(); // initial show based on current role
    }
})();
