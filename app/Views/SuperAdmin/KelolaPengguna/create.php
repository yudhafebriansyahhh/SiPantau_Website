<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('kelola-pengguna') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Kelola Pengguna
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Tambah Pengguna</h1>
    <p class="text-gray-600 mt-1">Buat akun pengguna baru untuk sistem SiPantau</p>
</div>

<!-- Form Card -->
<div class="card max-w-3xl">
    <form id="formPengguna" method="POST" action="<?= base_url('kelola-pengguna/store') ?>">
        <?= csrf_field() ?>
        
        <!-- Info Alert -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Lengkapi semua informasi pengguna dengan benar. Field bertanda <span class="text-red-500 font-semibold">*</span> wajib diisi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Informasi Personal -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Personal</h3>
            
            <!-- Nama Lengkap -->
            <div class="mb-4">
                <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Lengkap <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="nama" 
                       name="nama" 
                       class="input-field" 
                       placeholder="Contoh: Ahmad Hidayat"
                       required>
                <p class="mt-1 text-xs text-gray-500">Masukkan nama lengkap pengguna</p>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                    Email <span class="text-red-500">*</span>
                </label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="input-field" 
                       placeholder="contoh@bps.go.id"
                       required>
                <p class="mt-1 text-xs text-gray-500">Email akan digunakan untuk login</p>
            </div>

            <!-- No HP -->
            <div class="mb-4">
                <label for="no_hp" class="block text-sm font-medium text-gray-700 mb-2">
                    No HP <span class="text-red-500">*</span>
                </label>
                <input type="tel" 
                       id="no_hp" 
                       name="no_hp" 
                       class="input-field" 
                       placeholder="08xxxxxxxxxx"
                       pattern="[0-9]{10,13}"
                       required>
                <p class="mt-1 text-xs text-gray-500">Nomor HP aktif untuk notifikasi</p>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-6"></div>

        <!-- Informasi Wilayah & Role -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Wilayah & Role</h3>
            
            <!-- Kabupaten/Kota -->
            <div class="mb-4">
                <label for="kab_kota" class="block text-sm font-medium text-gray-700 mb-2">
                    Kabupaten/Kota <span class="text-red-500">*</span>
                </label>
                <select id="kab_kota" name="kab_kota" class="input-field" required>
                    <option value="">-- Pilih Kabupaten/Kota --</option>
                    <option value="Provinsi Riau">Provinsi Riau</option>
                    <option value="Kab. Bengkalis">Kab. Bengkalis</option>
                    <option value="Kab. Indragiri Hilir">Kab. Indragiri Hilir</option>
                    <option value="Kab. Indragiri Hulu">Kab. Indragiri Hulu</option>
                    <option value="Kab. Kampar">Kab. Kampar</option>
                    <option value="Kab. Kepulauan Meranti">Kab. Kepulauan Meranti</option>
                    <option value="Kab. Kuantan Singingi">Kab. Kuantan Singingi</option>
                    <option value="Kab. Pelalawan">Kab. Pelalawan</option>
                    <option value="Kab. Rokan Hilir">Kab. Rokan Hilir</option>
                    <option value="Kab. Rokan Hulu">Kab. Rokan Hulu</option>
                    <option value="Kab. Siak">Kab. Siak</option>
                    <option value="Kota Dumai">Kota Dumai</option>
                    <option value="Kota Pekanbaru">Kota Pekanbaru</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Pilih wilayah kerja pengguna</p>
            </div>

            <!-- Role -->
            <div class="mb-4">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                    Role <span class="text-red-500">*</span>
                </label>
                <select id="role" name="role" class="input-field" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="Admin Provinsi">Admin Provinsi</option>
                    <option value="Admin Kabupaten/Kota">Admin Kabupaten/Kota</option>
                    <option value="Operator">Operator</option>
                </select>
                <p class="mt-1 text-xs text-gray-500">Tentukan hak akses pengguna</p>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-6"></div>

        <!-- Informasi Akun -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Akun</h3>
            
            <!-- Password -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="input-field pr-10" 
                           placeholder="Minimal 8 karakter"
                           minlength="8"
                           required>
                    <button type="button" 
                            onclick="togglePassword('password')"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-600 hover:text-gray-900">
                        <i class="fas fa-eye" id="password-icon"></i>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">Password minimal 8 karakter</p>
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-4">
                <label for="password_confirm" class="block text-sm font-medium text-gray-700 mb-2">
                    Konfirmasi Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" 
                           id="password_confirm" 
                           name="password_confirm" 
                           class="input-field pr-10" 
                           placeholder="Ulangi password"
                           minlength="8"
                           required>
                    <button type="button" 
                            onclick="togglePassword('password_confirm')"
                            class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-600 hover:text-gray-900">
                        <i class="fas fa-eye" id="password_confirm-icon"></i>
                    </button>
                </div>
                <p class="mt-1 text-xs text-gray-500">Masukkan kembali password yang sama</p>
            </div>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-6"></div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" 
                    onclick="window.location.href='<?= base_url('kelola-pengguna') ?>'"
                    class="btn-secondary w-full sm:w-auto order-2 sm:order-1">
                <i class="fas fa-times mr-2"></i>
                Batal
            </button>
            <button type="button" 
                    onclick="resetForm()"
                    class="btn-secondary w-full sm:w-auto order-3 sm:order-2">
                <i class="fas fa-undo mr-2"></i>
                Reset
            </button>
            <button type="submit" 
                    class="btn-primary w-full sm:w-auto sm:ml-auto order-1 sm:order-3">
                <i class="fas fa-save mr-2"></i>
                Simpan Data
            </button>
        </div>
    </form>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Form validation dan submit
document.getElementById('formPengguna').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Get form values
    const nama = document.getElementById('nama').value.trim();
    const email = document.getElementById('email').value.trim();
    const noHp = document.getElementById('no_hp').value.trim();
    const kabKota = document.getElementById('kab_kota').value;
    const role = document.getElementById('role').value;
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    // Validasi field kosong
    if (!nama || !email || !noHp || !kabKota || !role || !password || !passwordConfirm) {
        Swal.fire({
            icon: 'error',
            title: 'Form Tidak Lengkap',
            text: 'Harap lengkapi semua field yang wajib diisi!',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        });
        return;
    }
    
    // Validasi email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        Swal.fire({
            icon: 'error',
            title: 'Email Tidak Valid',
            text: 'Format email tidak sesuai',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        });
        return;
    }
    
    // Validasi No HP
    const phoneRegex = /^[0-9]{10,13}$/;
    if (!phoneRegex.test(noHp)) {
        Swal.fire({
            icon: 'error',
            title: 'No HP Tidak Valid',
            text: 'No HP harus berisi 10-13 digit angka',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        });
        return;
    }
    
    // Validasi password length
    if (password.length < 8) {
        Swal.fire({
            icon: 'error',
            title: 'Password Terlalu Pendek',
            text: 'Password minimal 8 karakter',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        });
        return;
    }
    
    // Validasi password match
    if (password !== passwordConfirm) {
        Swal.fire({
            icon: 'error',
            title: 'Password Tidak Cocok',
            text: 'Password dan Konfirmasi Password harus sama',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        });
        return;
    }
    
    // Konfirmasi sebelum menyimpan
    Swal.fire({
        title: 'Simpan Data Pengguna?',
        html: `Apakah Anda yakin ingin menambahkan pengguna <strong>"${nama}"</strong>?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-save mr-2"></i>Ya, Simpan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-6 py-2.5 rounded-lg font-medium'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Tampilkan loading
            Swal.fire({
                title: 'Menyimpan Data...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Simulasi proses simpan (untuk static demo)
            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disimpan!',
                    text: 'Data pengguna telah ditambahkan.',
                    confirmButtonColor: '#3b82f6',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
                    }
                }).then(() => {
                    // Redirect ke halaman kelola pengguna
                    window.location.href = '<?= base_url('kelola-pengguna') ?>';
                });
            }, 1000);
            
            // Untuk implementasi real, uncomment ini:
            // this.submit();
        }
    });
});

// Reset form
function resetForm() {
    Swal.fire({
        title: 'Reset Form?',
        text: 'Semua data yang telah diisi akan dihapus',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Reset',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-6 py-2.5 rounded-lg font-medium'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('formPengguna').reset();
            
            Swal.fire({
                icon: 'success',
                title: 'Form Direset',
                text: 'Semua data telah dihapus',
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-xl'
                }
            });
        }
    });
}

// Auto-capitalize first letter untuk nama
document.getElementById('nama').addEventListener('blur', function() {
    if (this.value) {
        this.value = this.value.split(' ').map(word => 
            word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()
        ).join(' ');
    }
});

// Real-time password match indicator
document.getElementById('password_confirm').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirm = this.value;
    
    if (confirm && password !== confirm) {
        this.classList.add('border-red-500');
        this.classList.remove('border-gray-300');
    } else {
        this.classList.remove('border-red-500');
        this.classList.add('border-gray-300');
    }
});
</script>

<?= $this->endSection() ?>