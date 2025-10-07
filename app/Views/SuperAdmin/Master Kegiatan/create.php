<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('master-kegiatan') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Kegiatan
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Tambah Master Kegiatan</h1>
    <p class="text-gray-600 mt-1">Buat data master kegiatan survei/sensus baru</p>
</div>

<!-- Form Card -->
<div class="card max-w-3xl">
    <form id="formMasterKegiatan" method="POST" action="<?= base_url('master-kegiatan/store') ?>">
        <?= csrf_field() ?>
        
        <!-- Info Alert -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Lengkapi semua informasi kegiatan dengan benar. Field bertanda <span class="text-red-500 font-semibold">*</span> wajib diisi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Nama Kegiatan -->
        <div class="mb-6">
            <label for="nama_kegiatan" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Kegiatan <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="nama_kegiatan" 
                   name="nama_kegiatan" 
                   class="input-field" 
                   placeholder="Contoh: Pendataan Lahan Pertanian"
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
                      placeholder="Contoh: Pendataan luas lahan dan jenis tanaman pertanian untuk mendukung program ketahanan pangan"
                      required></textarea>
            <p class="mt-1 text-xs text-gray-500">Jelaskan fungsi atau tujuan dari kegiatan ini</p>
        </div>

        <!-- Keterangan -->
        <div class="mb-6">
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                Keterangan <span class="text-red-500">*</span>
            </label>
            <textarea id="keterangan" 
                      name="keterangan" 
                      rows="3" 
                      class="input-field resize-none" 
                      placeholder="Contoh: Mencakup seluruh kabupaten di Provinsi Riau dengan fokus pada sektor pertanian pangan"
                      required></textarea>
            <p class="mt-1 text-xs text-gray-500">Tambahkan keterangan atau detail tambahan kegiatan</p>
        </div>

        <!-- Periode -->
        <div class="mb-6">
            <label for="periode" class="block text-sm font-medium text-gray-700 mb-2">
                Periode <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="periode" 
                   name="periode" 
                   class="input-field" 
                   placeholder="Contoh: 2025, Q1 2025, Semester 1 2025, atau Januari-Juni 2025"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan periode pelaksanaan kegiatan (tahun, triwulan, semester, atau bulan)</p>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-6"></div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" 
                    onclick="window.location.href='<?= base_url('master-kegiatan') ?>'"
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
// Form validation dan submit
document.getElementById('formMasterKegiatan').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi form
    const namaKegiatan = document.getElementById('nama_kegiatan').value.trim();
    const fungsi = document.getElementById('fungsi').value.trim();
    const keterangan = document.getElementById('keterangan').value.trim();
    const periode = document.getElementById('periode').value.trim();
    
    if (!namaKegiatan || !fungsi || !keterangan || !periode) {
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
    
    // Validasi minimal panjang input
    if (namaKegiatan.length < 5) {
        Swal.fire({
            icon: 'error',
            title: 'Nama Kegiatan Terlalu Pendek',
            text: 'Nama kegiatan minimal 5 karakter',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        });
        return;
    }
    
    if (fungsi.length < 10) {
        Swal.fire({
            icon: 'error',
            title: 'Fungsi Terlalu Pendek',
            text: 'Fungsi minimal 10 karakter',
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
        title: 'Simpan Data Kegiatan?',
        html: `Apakah Anda yakin ingin menyimpan kegiatan <strong>"${namaKegiatan}"</strong>?`,
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
                    text: 'Data master kegiatan telah ditambahkan.',
                    confirmButtonColor: '#3b82f6',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
                    }
                }).then(() => {
                    // Redirect ke halaman master kegiatan
                    window.location.href = '<?= base_url('master-kegiatan') ?>';
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
            document.getElementById('formMasterKegiatan').reset();
            
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

// Real-time character counter untuk textarea (optional enhancement)
const textareas = ['fungsi', 'keterangan'];
textareas.forEach(id => {
    const textarea = document.getElementById(id);
    textarea.addEventListener('input', function() {
        const length = this.value.length;
        const hint = this.nextElementSibling;
        
        if (id === 'fungsi' && length < 10) {
            hint.classList.add('text-red-500');
            hint.classList.remove('text-gray-500');
        } else {
            hint.classList.remove('text-red-500');
            hint.classList.add('text-gray-500');
        }
    });
});

// Auto-capitalize first letter untuk nama kegiatan
document.getElementById('nama_kegiatan').addEventListener('blur', function() {
    if (this.value) {
        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
    }
});
</script>

<?= $this->endSection() ?>