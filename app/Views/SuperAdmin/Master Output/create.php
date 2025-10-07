<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('master-output') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Output
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Tambah Master Output</h1>
    <p class="text-gray-600 mt-1">Tambahkan data master output kegiatan survei/sensus baru</p>
</div>

<!-- Form Card -->
<div class="card max-w-3xl">
    <form id="formMasterOutput" method="POST" action="<?= base_url('kegiatan/store') ?>">
        <?= csrf_field() ?>
        
        <!-- Nama Output -->
        <div class="mb-6">
            <label for="nama" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Output <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="nama" 
                   name="nama" 
                   class="input-field" 
                   placeholder="Contoh: SUNSENAS 2025"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan nama lengkap kegiatan survei/sensus</p>
        </div>

        <!-- Fungsi -->
        <div class="mb-6">
            <label for="fungsi" class="block text-sm font-medium text-gray-700 mb-2">
                Fungsi <span class="text-red-500">*</span>
            </label>
            <textarea id="fungsi" 
                      name="fungsi" 
                      rows="4" 
                      class="input-field resize-none" 
                      placeholder="Contoh: Survei Sosial Ekonomi Nasional untuk mengumpulkan data kondisi sosial ekonomi masyarakat"
                      required></textarea>
            <p class="mt-1 text-xs text-gray-500">Jelaskan fungsi atau tujuan dari kegiatan ini</p>
        </div>

        <!-- Alias -->
        <div class="mb-6">
            <label for="alias" class="block text-sm font-medium text-gray-700 mb-2">
                Alias/Singkatan <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="alias" 
                   name="alias" 
                   class="input-field" 
                   placeholder="Contoh: SUSENAS"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan singkatan atau alias dari kegiatan</p>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-6"></div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" 
                    onclick="window.location.href='<?= base_url('master-output') ?>'"
                    class="btn-secondary w-full sm:w-auto order-2 sm:order-1">
                <i class="fas fa-times mr-2"></i>
                Batal
            </button>
            <button type="button" 
                    onclick="resetForm()"
                    class="btn-secondary w-full sm:w-auto order-3 sm:order-2">
                <i class="fas fa-redo mr-2"></i>
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
// Form validation dan submit
document.getElementById('formMasterOutput').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi form
    const nama = document.getElementById('nama').value.trim();
    const fungsi = document.getElementById('fungsi').value.trim();
    const alias = document.getElementById('alias').value.trim();
    
    if (!nama || !fungsi || !alias) {
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
    
    // Konfirmasi sebelum menyimpan
    Swal.fire({
        title: 'Simpan Data?',
        text: 'Apakah Anda yakin ingin menyimpan data master output ini?',
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
                title: 'Menyimpan...',
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
                    text: 'Data master output telah ditambahkan.',
                    confirmButtonColor: '#3b82f6',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
                    }
                }).then(() => {
                    // Redirect ke halaman master output
                    window.location.href = '<?= base_url('master-output') ?>';
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
            document.getElementById('formMasterOutput').reset();
            Swal.fire({
                icon: 'success',
                title: 'Form Direset',
                text: 'Form telah dikosongkan',
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-xl'
                }
            });
        }
    });
}

// Auto-capitalize input
document.getElementById('alias').addEventListener('input', function(e) {
    this.value = this.value.toUpperCase();
});
</script>

<?= $this->endSection() ?>