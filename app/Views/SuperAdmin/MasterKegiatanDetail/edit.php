<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('kegiatan-wilayah') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Kegiatan Detail
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Edit Master Kegiatan Detail</h1>
    <p class="text-gray-600 mt-1">Perbarui data detail kegiatan survei/sensus</p>
</div>

<!-- Form Card -->
<div class="card max-w-3xl">
    <form id="formKegiatanDetail" method="POST" action="<?= base_url('kegiatan-wilayah/update/1') ?>">
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
                        Anda sedang mengedit data: <strong>Pencacahan Lahan Sawah</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Master Kegiatan (Dropdown) -->
        <div class="mb-6">
            <label for="master_kegiatan" class="block text-sm font-medium text-gray-700 mb-2">
                Master Kegiatan <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <select id="master_kegiatan" 
                        name="master_kegiatan" 
                        class="input-field w-full pr-10"
                        required>
                    <option value="">-- Pilih Master Kegiatan --</option>
                    <option value="1" selected>Pendataan Lahan Pertanian</option>
                    <option value="2">Survey Konsumsi Rumah Tangga</option>
                    <option value="3">Sensus Penduduk Daerah Terpencil</option>
                    <option value="4">Pendataan Usaha Mikro Kecil Menengah</option>
                    <option value="5">Survey Indeks Harga Konsumen</option>
                </select>
                <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                </div>
            </div>
            <p class="mt-1 text-xs text-gray-500">Pilih master kegiatan yang sesuai</p>
        </div>

        <!-- Nama Kegiatan Detail -->
        <div class="mb-6">
            <label for="nama_kegiatan_detail" class="block text-sm font-medium text-gray-700 mb-2">
                Nama Kegiatan Detail <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="nama_kegiatan_detail" 
                   name="nama_kegiatan_detail" 
                   class="input-field" 
                   value="Pencacahan Lahan Sawah"
                   placeholder="Contoh: Pencacahan Lahan Sawah"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan nama detail kegiatan yang akan dilaksanakan</p>
        </div>

        <!-- Satuan -->
        <div class="mb-6">
            <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                Satuan <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   id="satuan" 
                   name="satuan" 
                   class="input-field" 
                   value="Hektar"
                   placeholder="Contoh: Hektar, Responden, Jiwa"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan satuan pengukuran kegiatan</p>
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
                   value="Q1"
                   placeholder="Contoh: Q1, Q2, Semester 1, Triwulan II"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan periode pelaksanaan</p>
        </div>

        <!-- Tahun -->
        <div class="mb-6">
            <label for="tahun" class="block text-sm font-medium text-gray-700 mb-2">
                Tahun <span class="text-red-500">*</span>
            </label>
            <input type="number" 
                   id="tahun" 
                   name="tahun" 
                   class="input-field" 
                   value="2025"
                   min="2020"
                   max="2030"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan tahun pelaksanaan kegiatan</p>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-6"></div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" 
                    onclick="window.location.href='<?= base_url('kegiatan-wilayah') ?>'"
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
    master_kegiatan: '1',
    nama_kegiatan_detail: 'Pencacahan Lahan Sawah',
    satuan: 'Hektar',
    periode: 'Q1',
    tahun: '2025'
};

// Form validation dan submit
document.getElementById('formKegiatanDetail').addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validasi form
    const masterKegiatan = document.getElementById('master_kegiatan').value;
    const namaKegiatanDetail = document.getElementById('nama_kegiatan_detail').value.trim();
    const satuan = document.getElementById('satuan').value.trim();
    const periode = document.getElementById('periode').value.trim();
    const tahun = document.getElementById('tahun').value.trim();
    
    if (!masterKegiatan) {
        Swal.fire({
            icon: 'error',
            title: 'Master Kegiatan Belum Dipilih',
            text: 'Harap pilih master kegiatan terlebih dahulu!',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        });
        return;
    }
    
    if (!namaKegiatanDetail || !satuan || !periode || !tahun) {
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
        masterKegiatan !== originalValues.master_kegiatan || 
        namaKegiatanDetail !== originalValues.nama_kegiatan_detail || 
        satuan !== originalValues.satuan ||
        periode !== originalValues.periode ||
        tahun !== originalValues.tahun;
    
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
    if (namaKegiatanDetail.length < 5) {
        Swal.fire({
            icon: 'error',
            title: 'Nama Kegiatan Detail Terlalu Pendek',
            text: 'Nama kegiatan detail minimal 5 karakter',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        });
        return;
    }
    
    // Validasi tahun
    const tahunInt = parseInt(tahun);
    if (tahunInt < 2020 || tahunInt > 2030) {
        Swal.fire({
            icon: 'error',
            title: 'Tahun Tidak Valid',
            text: 'Tahun harus antara 2020 dan 2030',
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
                    text: 'Data kegiatan detail telah diperbarui.',
                    confirmButtonColor: '#3b82f6',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
                    }
                }).then(() => {
                    // Redirect ke halaman kegiatan detail
                    window.location.href = '<?= base_url('kegiatan-wilayah') ?>';
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
            document.getElementById('master_kegiatan').value = originalValues.master_kegiatan;
            document.getElementById('nama_kegiatan_detail').value = originalValues.nama_kegiatan_detail;
            document.getElementById('satuan').value = originalValues.satuan;
            document.getElementById('periode').value = originalValues.periode;
            document.getElementById('tahun').value = originalValues.tahun;
            
            // Reset highlight
            const formInputs = ['master_kegiatan', 'nama_kegiatan_detail', 'satuan', 'periode', 'tahun'];
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
const formInputs = ['master_kegiatan', 'nama_kegiatan_detail', 'satuan', 'periode', 'tahun'];
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

// For select element, use 'change' event
document.getElementById('master_kegiatan').addEventListener('change', function() {
    const originalValue = originalValues.master_kegiatan;
    if (this.value !== originalValue) {
        this.classList.add('border-blue-500', 'bg-blue-50');
    } else {
        this.classList.remove('border-blue-500', 'bg-blue-50');
    }
});

// Auto-capitalize first letter untuk nama kegiatan detail
document.getElementById('nama_kegiatan_detail').addEventListener('blur', function() {
    if (this.value) {
        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
    }
});

// Auto-capitalize satuan
document.getElementById('satuan').addEventListener('blur', function() {
    if (this.value) {
        this.value = this.value.charAt(0).toUpperCase() + this.value.slice(1);
    }
});
</script>

<?= $this->endSection() ?>