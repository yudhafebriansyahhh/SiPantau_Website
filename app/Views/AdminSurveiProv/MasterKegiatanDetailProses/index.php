<?= $this->extend('layouts/adminprov_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Master Kegiatan Detail Proses</h1>
    <p class="text-gray-600 mt-1">Kelola data detail kegiatan survei/sensus beserta satuan, periode, dan target pelaksanaan</p>
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
                   placeholder="Cari kegiatan detail atau satuan..."
                   onkeyup="searchTable()">
        </div>
        
        <!-- Add Button -->
        <a href="<?= base_url('master-kegiatan-detail-proses/create') ?>" 
           class="btn-primary whitespace-nowrap w-full sm:w-auto text-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Kegiatan
        </a>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="kegiatanDetailTable">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Master Kegiatan Detail</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kegiatan Detail Proses</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Satuan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Tanggal Mulai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Tanggal Selesai</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Keterangan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Periode</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Target</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <!-- Data Dummy -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">1</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Pendataan Lahan Pertanian</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Pencacahan Lahan Sawah</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Hektar</td>
                    <td class="px-4 py-4 text-sm text-gray-600">2025-01-10</td>
                    <td class="px-4 py-4 text-sm text-gray-600">2025-02-15</td>
                    <td class="px-4 py-4 text-sm text-center text-gray-600">Tahap awal pendataan sawah produktif</td>
                    <td class="px-4 py-4 text-center"><span class="badge badge-info">Q1</span></td>
                    <td class="px-4 py-4 text-center text-gray-900 font-medium">120</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('kegiatan-wilayah/edit/1') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete(1, 'Pencacahan Lahan Sawah')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">2</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Survey Konsumsi Rumah Tangga</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Wawancara Rumah Tangga</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Responden</td>
                    <td class="px-4 py-4 text-sm text-gray-600">2025-03-01</td>
                    <td class="px-4 py-4 text-sm text-gray-600">2025-04-10</td>
                    <td class="px-4 py-4 text-sm text-center text-gray-600">Survei konsumsi pangan dan non-pangan</td>
                    <td class="px-4 py-4 text-center"><span class="badge badge-info">Semester 1</span></td>
                    <td class="px-4 py-4 text-center text-gray-900 font-medium">200</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('kegiatan-wilayah/edit/2') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete(2, 'Wawancara Rumah Tangga')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">3</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Sensus Penduduk Terpencil</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Pendataan Individu</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Jiwa</td>
                    <td class="px-4 py-4 text-sm text-gray-600">2025-05-05</td>
                    <td class="px-4 py-4 text-sm text-gray-600">2025-06-20</td>
                    <td class="px-4 py-4 text-sm text-center text-gray-600">Pendataan populasi di wilayah sulit dijangkau</td>
                    <td class="px-4 py-4 text-center"><span class="badge badge-info">Q2</span></td>
                    <td class="px-4 py-4 text-center text-gray-900 font-medium">350</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('kegiatan-wilayah/edit/3') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete(3, 'Pendataan Individu')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">4</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Pendataan UMKM</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Survei Usaha Mikro</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Unit Usaha</td>
                    <td class="px-4 py-4 text-sm text-gray-600">2025-07-01</td>
                    <td class="px-4 py-4 text-sm text-gray-600">2025-07-31</td>
                    <td class="px-4 py-4 text-sm text-center text-gray-600">Pendataan usaha kecil di sektor perdagangan</td>
                    <td class="px-4 py-4 text-center"><span class="badge badge-info">Q3</span></td>
                    <td class="px-4 py-4 text-center text-gray-900 font-medium">500</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('kegiatan-wilayah/edit/4') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete(4, 'Survei Usaha Mikro')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">5</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Survey Indeks Harga Konsumen</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Pencatatan Harga Pasar</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Komoditas</td>
                    <td class="px-4 py-4 text-sm text-gray-600">2025-08-01</td>
                    <td class="px-4 py-4 text-sm text-gray-600">2025-08-31</td>
                    <td class="px-4 py-4 text-sm text-center text-gray-600">Pemantauan harga bahan pokok di pasar tradisional</td>
                    <td class="px-4 py-4 text-center"><span class="badge badge-info">Bulanan</span></td>
                    <td class="px-4 py-4 text-center text-gray-900 font-medium">150</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('kegiatan-wilayah/edit/5') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"><i class="fas fa-edit"></i></a>
                        <button onclick="confirmDelete(5, 'Pencatatan Harga Pasar')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium">5</span> data kegiatan detail proses.
        </p>
        <div class="flex items-center space-x-2">
            <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg" disabled><i class="fas fa-chevron-left"></i></button>
            <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg">1</button>
            <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('kegiatanDetailTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        for (let j = 1; j < cells.length - 1; j++) {
            if (cells[j] && cells[j].innerText.toLowerCase().includes(filter)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    }
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Data?',
        html: `Yakin ingin menghapus <strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire('Terhapus!', `Data "${name}" telah dihapus.`, 'success');
        }
    });
}
</script>

<?= $this->endSection() ?>
