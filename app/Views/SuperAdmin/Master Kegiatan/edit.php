<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('master-kegiatan') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Kegiatan
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Edit Master Kegiatan</h1>
    <p class="text-gray-600 mt-1">Perbarui data master kegiatan survei/sensus</p>
</div>

<!-- Form Card -->
<div class="card max-w-3xl">
    <form id="formMasterKegiatan" method="POST" action="<?= base_url('master-kegiatan/update/1') ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="_method" value="PUT">
        
        <!-- Info Alert -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Anda sedang mengedit data: <strong>Pendataan Lahan Pertanian</strong>
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
                   value="Pendataan Lahan Pertanian"
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
                      placeholder="Contoh: Pendataan luas lahan dan jenis tanaman pertanian"
                      required>Pendataan luas lahan dan jenis tanaman pertanian untuk mendukung program ketahanan pangan nasional</textarea>
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
                      placeholder="Contoh: Mencakup seluruh kabupaten di Provinsi Riau"
                      required>Mencakup seluruh kabupaten di Provinsi Riau dengan fokus pada sektor pertanian tanaman pangan dan hortikultura</textarea>
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
                   value="2025"
                   placeholder="Contoh: 2025, Q1 2025, Semester 1 2025"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan periode pelaksanaan kegiatan</p>
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
                    onclick="resetToOriginal()"
                    class="btn-secondary w-full sm:w-auto order-3 sm:order-2">
                <i class="fas fa-undo mr-2"></i>
                Kembalikan
            </button>
            <button type="submit" 
                    class="btn-primary w-full sm:w-auto sm:ml-auto order-1 sm:order-3">
                <i class="fas fa-save mr-2"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Store original values
const originalValues = {
    nama_kegiatan: 'Pendataan Lahan Pertanian',
    fungsi: 'Pendataan luas lahan dan jenis tanaman pertanian untuk mendukung program ketahanan pangan nasional',
    keterangan: 'Mencakup seluruh kabupaten di Provinsi Riau dengan fokus pada sektor pertanian tanaman pangan dan hortikultura',
    periode: '2025'
};

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
    
    // Cek apakah ada perubahan
    const hasChanges = 
        namaKegiatan !== originalValues.nama_kegiatan || 
        fungsi !== originalValues.fungsi || 
        keterangan !== originalValues.keterangan ||
        periode !== originalValues.periode;
    
    if (!hasChanges) {
        Swal.fire({
            icon: 'info',
            title: 'Tidak Ada Perubahan',
            text: 'Tidak ada data yang diubah',
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
        title: 'Simpan Perubahan?',
        text: 'Apakah Anda yakin ingin menyimpan perubahan data ini?',
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
                title: 'Menyimpan Perubahan...',
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
                    title: 'Berhasil Diperbarui!',
                    text: 'Data master kegiatan telah diperbarui.',
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

// Reset to original values
function resetToOriginal() {
    Swal.fire({
        title: 'Kembalikan Data?',
        text: 'Semua perubahan akan dikembalikan ke data awal',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Kembalikan',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-6 py-2.5 rounded-lg font-medium'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('nama_kegiatan').value = originalValues.nama_kegiatan;
            document.getElementById('fungsi').value = originalValues.fungsi;
            document.getElementById('keterangan').value = originalValues.keterangan;
            document.getElementById('periode').value = originalValues.periode;
            
            // Reset highlight
            const formInputs = ['nama_kegiatan', 'fungsi', 'keterangan', 'periode'];
            formInputs.forEach(inputId => {
                const input = document.getElementById(inputId);
                input.classList.remove('border-blue-500', 'bg-blue-50');
            });
            
            Swal.fire({
                icon: 'success',
                title: 'Data Dikembalikan',
                text: 'Data telah dikembalikan ke nilai awal',
                timer: 1500,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-xl'
                }
            });
        }
    });
}

// Track changes untuk highlight
const formInputs = ['nama_kegiatan', 'fungsi', 'keterangan', 'periode'];
formInputs.forEach(inputId => {
    const input = document.getElementById(inputId);
    input.addEventListener('input', function() {
        const originalValue = originalValues[inputId];
        if (this.value !== originalValue) {
            this.classList.add('border-blue-500', 'bg-blue-50');
        } else {
            this.classList.remove('border-blue-500', 'bg-blue-50');
        }
    });
});

// Auto-capitalize first letter untuk nama kegiatan
document.getElementById('nama_kegiatan').addEventListener('blur', function() {
    if (this.value) {
        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
    }
});

// Real-time character counter untuk textarea
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
</script>

<?= $this->endSection() ?>