<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('admin') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Master Kegiatan</h1>
    <p class="text-gray-600 mt-1">Kelola data master kegiatan survei/sensus beserta detail periode dan fungsinya</p>
</div>

<!-- Main Card -->
<div class="card">
    <!-- Search and Add Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Search Box -->
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" id="searchInput" 
                   class="input-field w-full pl-10" 
                   placeholder="Cari nama kegiatan, fungsi, atau periode..."
                   onkeyup="searchTable()">
        </div>
        
        <!-- Add Button -->
        <a href="<?= base_url('master-kegiatan/create') ?>" 
           class="btn-primary whitespace-nowrap w-full sm:w-auto text-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Kegiatan
        </a>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="masterKegiatanTable">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">
                        No
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nama Kegiatan
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Fungsi
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Keterangan
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">
                        Periode
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <!-- Static Data untuk Demo -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">1</td>
                    <td class="px-4 py-4">
                        <p class="text-sm font-medium text-gray-900">Pendataan Lahan Pertanian</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Pendataan luas lahan dan jenis tanaman pertanian</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Mencakup seluruh kabupaten di Provinsi Riau</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-info">2025</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-kegiatan/detail') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('master-kegiatan/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(1, 'Pendataan Lahan Pertanian')"
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
                        <p class="text-sm font-medium text-gray-900">Survey Konsumsi Rumah Tangga</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Mengumpulkan data pola konsumsi rumah tangga</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Dilakukan secara berkala setiap 6 bulan</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-info">2025 Semester 1</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-kegiatan/detail') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('master-kegiatan/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(2, 'Survey Konsumsi Rumah Tangga')"
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
                        <p class="text-sm font-medium text-gray-900">Sensus Penduduk Daerah Terpencil</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Pendataan populasi di wilayah terpencil dan perbatasan</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Fokus pada daerah pedalaman dan pulau terluar</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-info">Q1 2025</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-kegiatan/detail') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('master-kegiatan/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(3, 'Sensus Penduduk Daerah Terpencil')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">4</td>
                    <td class="px-4 py-4">
                        <p class="text-sm font-medium text-gray-900">Pendataan Usaha Mikro Kecil Menengah</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Survey potensi dan perkembangan UMKM</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Mencakup sektor perdagangan dan jasa</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-info">Triwulan II 2025</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-kegiatan/detail') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('master-kegiatan/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(4, 'Pendataan Usaha Mikro Kecil Menengah')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">5</td>
                    <td class="px-4 py-4">
                        <p class="text-sm font-medium text-gray-900">Survey Indeks Harga Konsumen</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Pengumpulan data harga barang dan jasa konsumen</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Dilaksanakan bulanan di seluruh kota</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-info">2025 (Bulanan)</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-kegiatan/detail') ?>" 
                               class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                               title="Lihat Detail">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="<?= base_url('master-kegiatan/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(5, 'Survey Indeks Harga Konsumen')"
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
    
    <!-- Pagination -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium">5</span> data
        </p>
        
        <div class="flex items-center space-x-2">
            <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg">1</button>
            <button class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">2</button>
            <button class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">3</button>
            <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Search functionality
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('masterKegiatanTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        for (let j = 1; j < cells.length - 1; j++) {
            const cell = cells[j];
            if (cell) {
                const textValue = cell.textContent || cell.innerText;
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        row.style.display = found ? '' : 'none';
    }
}

// Delete confirmation dengan SweetAlert2
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Data Kegiatan?',
        html: `Apakah Anda yakin ingin menghapus kegiatan <strong>"${name}"</strong>?<br><span class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan.</span>`,
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
        },
        buttonsStyling: true
    }).then((result) => {
        if (result.isConfirmed) {
            deleteData(id, name);
        }
    });
}

// Fungsi untuk proses delete (simulasi)
function deleteData(id, name) {
    Swal.fire({
        title: 'Menghapus...',
        html: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Dihapus!',
            text: `Kegiatan "${name}" telah dihapus.`,
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        }).then(() => {
            const row = event.target.closest('tr');
            if (row) {
                row.remove();
            }
        });
    }, 1000);
}
</script>

<?= $this->endSection() ?>