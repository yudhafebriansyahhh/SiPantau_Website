<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('kegiatan-detail/detail/1') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Detail Master Kegiatan
        </a>
    </div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Kegiatan Detail</h1>
            <p class="text-gray-600 mt-1">Lihat informasi lengkap kegiatan detail dan proses-proses di bawahnya</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('kegiatan-wilayah/edit/1') ?>" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Edit Kegiatan Detail
            </a>
        </div>
    </div>
</div>

<!-- Breadcrumb Info -->
<div class="card mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
    <div class="flex items-center text-sm">
        <span class="text-gray-600">Master Output:</span>
        <span class="mx-2 text-gray-400">/</span>
        <span class="font-medium text-gray-900">Statistik Kesejahteraan Rakyat</span>
        <span class="mx-2 text-gray-400">/</span>
        <span class="text-gray-600">Master Kegiatan:</span>
        <span class="mx-2 text-gray-400">/</span>
        <a href="<?= base_url('kegiatan-detail/detail/1') ?>" class="font-medium text-blue-600 hover:text-blue-700">
            Pendataan Lahan Pertanian
        </a>
        <span class="mx-2 text-gray-400">/</span>
        <span class="font-semibold text-gray-900">Pencacahan Lahan Sawah</span>
    </div>
</div>

<!-- Info Kegiatan Detail -->
<div class="card mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kegiatan Detail</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Nama Kegiatan Detail -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Kegiatan Detail</label>
            <p class="text-base text-gray-900 font-medium">Pencacahan Lahan Sawah</p>
        </div>
        
        <!-- Satuan -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Satuan</label>
            <p class="text-base text-gray-900">Hektar</p>
        </div>
        
        <!-- Periode -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Periode</label>
            <p class="text-base text-gray-900">
                <span class="badge badge-info">Q1</span>
            </p>
        </div>
        
        <!-- Tahun -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Tahun</label>
            <p class="text-base text-gray-900 font-medium">2025</p>
        </div>
    </div>
</div>

<!-- List Master Kegiatan Detail Proses -->
<div class="card">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Daftar Proses Kegiatan</h2>
            <p class="text-sm text-gray-600 mt-1">Proses-proses yang terkait dengan kegiatan detail ini</p>
        </div>
        
        <a href="<?= base_url('kegiatan-detail-proses/create') ?>" class="btn-primary whitespace-nowrap">
            <i class="fas fa-plus mr-2"></i>Tambah Proses
        </a>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Proses</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Deskripsi</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">1</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-3">
                                <i class="fas fa-map-marked-alt text-sm"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-900">Lapangan</p>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Pengumpulan data di lapangan oleh petugas survei</p>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('comingsoon') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">2</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-3">
                                <i class="fas fa-clipboard-check text-sm"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-900">Administrasi</p>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Verifikasi dan validasi data hasil lapangan</p>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('comingsoon') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">3</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center mr-3">
                                <i class="fas fa-cogs text-sm"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-900">Pengolahan</p>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Pengolahan dan analisis data untuk menghasilkan output</p>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('comingsoon') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Summary Stats -->
    <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                        <i class="fas fa-tasks text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Total Proses</p>
                        <p class="text-xl font-bold text-gray-900">3</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Proses Aktif</p>
                        <p class="text-xl font-bold text-gray-900">3</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                        <i class="fas fa-project-diagram text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Tahapan</p>
                        <p class="text-xl font-bold text-gray-900">3 Tahap</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Proses?',
        html: `Apakah Anda yakin ingin menghapus proses <strong>"${name}"</strong>?<br><span class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-6 py-2.5 rounded-lg font-medium'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Menghapus...',
                html: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => { Swal.showLoading(); }
            });

            setTimeout(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Dihapus!',
                    text: `Proses "${name}" telah dihapus.`,
                    confirmButtonColor: '#3b82f6',
                    customClass: {
                        popup: 'rounded-xl',
                        confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
                    }
                });
            }, 1000);
        }
    });
}
</script>

<?= $this->endSection() ?>