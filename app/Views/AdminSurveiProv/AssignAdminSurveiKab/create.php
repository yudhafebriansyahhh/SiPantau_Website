<?= $this->extend('layouts/adminprov_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('master-kegiatan') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Kegiatan
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Tambah admin survei kab/kota</h1>
    <p class="text-gray-600 mt-1">Assign admin survei kab/kota baru</p>
</div>

<!-- Form Card -->
<div class="card max-w-5xl">
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

        <!-- Pilih Kegiatan Detail -->
        <div class="mb-6">
            <label for="kegiatan_detail" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Kab/Kota<span class="text-red-500">*</span>
            </label>
            <select id="kegiatan_detail" 
                    name="kegiatan_detail" 
                    class="input-field" 
                    required>
                <option value="">-- Pilih Kab/Kota --</option>
                <option value="1">Pekanbaru</option>
                <option value="2">KUANSING</option>
                <option value="3">Dumai</option>
            </select>
        </div>
        <div class="mb-6">
            <label for="satuan" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih admin Survei Kab/kota<span class="text-red-500">*</span>
            </label>
            <select id="satuan" 
                    name="satuan" 
                    class="input-field" 
                    required>
                <option value="">-- Pilih admin Survei Kab/kota --</option>
                <option value="unit">Arif</option>
                <option value="orang">Yudha</option>
                <option value="dokumen">Elsa</option>
            </select>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-200 my-6"></div>

        <!-- Action Buttons -->
        <div class="flex justify-center">
            <button type="submit" 
                    class="btn-primary px-24 py-3 text-base">
                Simpan
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
    const kegiatanDetail = document.getElementById('kegiatan_detail').value;
    const namaProses = document.getElementById('nama_proses').value.trim();
    const tanggalMulai = document.getElementById('tanggal_mulai').value;
    const tanggalSelesai = document.getElementById('tanggal_selesai').value;
    const satuan = document.getElementById('satuan').value;
    const keterangan = document.getElementById('keterangan').value.trim();
    const periode = document.getElementById('periode').value.trim();
    const target = document.getElementById('target').value;
    const targetHariPertama = document.getElementById('target_hari_pertama').value;
    const targetTanggalSelesai = document.getElementById('target_tanggal_selesai').value;
    
    if (!kegiatanDetail || !namaProses || !tanggalMulai || !tanggalSelesai || 
        !satuan || !keterangan || !periode || !target || !targetHariPertama || !targetTanggalSelesai) {
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
    
    // Validasi tanggal
    if (new Date(tanggalSelesai) < new Date(tanggalMulai)) {
        Swal.fire({
            icon: 'error',
            title: 'Tanggal Tidak Valid',
            text: 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai!',
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
        html: `Apakah Anda yakin ingin menyimpan data kegiatan detail proses <strong>"${namaProses}"</strong>?`,
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
                    text: 'Data master kegiatan detail proses telah ditambahkan.',
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
</script>

<?= $this->endSection() ?>