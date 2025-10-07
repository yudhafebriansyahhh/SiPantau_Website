<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('master-kegiatan-detail') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Kegiatan Detail
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Tambah Master Kegiatan Detail</h1>
    <p class="text-gray-600 mt-1">Buat data detail kegiatan survei/sensus baru</p>
</div>

<!-- Form Card -->
<div class="card max-w-3xl">
    <form id="formKegiatanDetail" method="POST" action="<?= base_url('master-kegiatan-detail/store') ?>">
        <?= csrf_field() ?>
        
        <!-- Info Alert -->
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        Lengkapi semua informasi kegiatan detail dengan benar. Field bertanda <span class="text-red-500 font-semibold">*</span> wajib diisi.
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
                    <option value="1">Pendataan Lahan Pertanian</option>
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
                   placeholder="Contoh: Hektar, Responden, Jiwa, Unit Usaha, Komoditas"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan satuan pengukuran kegiatan (hektar, responden, jiwa, dll)</p>
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
                   placeholder="Contoh: Q1, Q2, Semester 1, Triwulan II, Bulanan"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan periode pelaksanaan (Q1, Q2, Semester 1, Triwulan, Bulanan, dll)</p>
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
                   placeholder="Contoh: 2025"
                   min="2020"
                   max="2030"
                   value="2025"
                   required>
            <p class="mt-1 text-xs text-gray-500">Masukkan tahun pelaksanaan kegiatan</p>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-6"></div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-3">
            <button type="button" 
                    onclick="window.location.href='<?= base_url('master-kegiatan-detail') ?>'"
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
    
    // Get selected master kegiatan name
    const selectElement = document.getElementById('master_kegiatan');
    const masterKegiatanName = selectElement.options[selectElement.selectedIndex].text;
    
    // Konfirmasi sebelum menyimpan
    Swal.fire({
        title: 'Simpan Data Kegiatan Detail?',
        html: `Apakah Anda yakin ingin menyimpan kegiatan detail <strong>"${namaKegiatanDetail}"</strong> pada <strong>${masterKegiatanName}</strong>?`,
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
                    text: 'Data kegiatan detail telah ditambahkan.',
                    confirmButtonColor: '#3b82f6',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
                    }
                }).then(() => {
                    // Redirect ke halaman kegiatan detail
                    window.location.href = '<?= base_url('master-kegiatan-detail') ?>';
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
            document.getElementById('formKegiatanDetail').reset();
            
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

<?= $this->endSection() ?>