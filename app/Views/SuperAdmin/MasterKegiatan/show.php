<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('kegiatan-detail') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Kegiatan
        </a>
    </div>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Detail Master Kegiatan</h1>
            <p class="text-gray-600 mt-1">Lihat informasi lengkap master kegiatan dan detail kegiatan di bawahnya</p>
        </div>
        <div class="flex gap-2">
            <a href="<?= base_url('kegiatan-detail/edit/1') ?>" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Edit Kegiatan
            </a>
        </div>
    </div>
</div>

<!-- Info Master Kegiatan -->
<div class="card mb-6">
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Informasi Master Kegiatan</h2>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Nama Kegiatan -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Nama Kegiatan</label>
            <p class="text-base text-gray-900 font-medium">Pendataan Lahan Pertanian</p>
        </div>
        
        <!-- Periode -->
        <div>
            <label class="block text-sm font-medium text-gray-600 mb-1">Periode</label>
            <p class="text-base text-gray-900">
                <span class="badge badge-info">2025</span>
            </p>
        </div>
        
        <!-- Fungsi -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-600 mb-1">Fungsi</label>
            <p class="text-base text-gray-900">Pendataan luas lahan dan jenis tanaman pertanian untuk mendukung program ketahanan pangan nasional</p>
        </div>
        
        <!-- Keterangan -->
        <div class="md:col-span-2">
            <label class="block text-sm font-medium text-gray-600 mb-1">Keterangan</label>
            <p class="text-base text-gray-900">Mencakup seluruh kabupaten di Provinsi Riau dengan fokus pada sektor pertanian tanaman pangan dan hortikultura</p>
        </div>
    </div>
</div>

<!-- List Master Kegiatan Detail -->
<div class="card">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Daftar Kegiatan Detail</h2>
            <p class="text-sm text-gray-600 mt-1">Kegiatan detail yang terkait dengan master kegiatan ini</p>
        </div>
        
        <a href="<?= base_url('master-kegiatan-detail/create') ?>" class="btn-primary whitespace-nowrap">
            <i class="fas fa-plus mr-2"></i>Tambah Kegiatan Detail
        </a>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kegiatan Detail</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Satuan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Periode</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Tahun</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">1</td>
                    <td class="px-4 py-4">
                        <p class="text-sm font-medium text-gray-900">Pencacahan Lahan Sawah</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-sm text-gray-600">Hektar</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-info">Q1</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-sm font-medium text-gray-900">2025</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-kegiatan-detail/detail') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('master-kegiatan-detail/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(1, 'Pencacahan Lahan Sawah')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">2</td>
                    <td class="px-4 py-4">
                        <p class="text-sm font-medium text-gray-900">Pencacahan Lahan Perkebunan</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-sm text-gray-600">Hektar</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-info">Q1</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-sm font-medium text-gray-900">2025</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-kegiatan-detail/detail') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('master-kegiatan-detail/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(2, 'Pencacahan Lahan Perkebunan')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">3</td>
                    <td class="px-4 py-4">
                        <p class="text-sm font-medium text-gray-900">Verifikasi Data Lapangan</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-sm text-gray-600">Dokumen</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-info">Q2</span>
                    </td>
                    <td class="px-4 py-4">
                        <span class="text-sm font-medium text-gray-900">2025</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-kegiatan-detail/detail') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('master-kegiatan-detail/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(3, 'Verifikasi Data Lapangan')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Empty State (uncomment jika tidak ada data) -->
    <!-- <div class="text-center py-12">
        <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
        <p class="text-gray-500 mb-4">Belum ada kegiatan detail</p>
        <a href="<?= base_url('master-kegiatan/create') ?>" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>Tambah Kegiatan Detail Pertama
        </a>
    </div> -->
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Kegiatan Detail?',
        html: `Apakah Anda yakin ingin menghapus <strong>"${name}"</strong>?<br><span class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan.</span>`,
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
                    text: `"${name}" telah dihapus.`,
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