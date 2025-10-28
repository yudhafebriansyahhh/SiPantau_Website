<?= $this->extend('layouts/adminprov_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Admin Survei Kabupaten</h1>
    <p class="text-gray-600 mt-1">Kelola assignment admin survei untuk setiap kabupaten/kota</p>
</div>

<!-- Main Card -->
<div class="card">
    <!-- Filter Section -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-filter text-gray-600"></i>
            <h3 class="text-sm font-semibold text-gray-700">Filter Data</h3>
        </div>
        
        <form method="GET" action="<?= base_url('adminsurvei/admin-survei-kab') ?>" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Filter Kabupaten -->
                <div>
                    <?= view('components/select_component', [
                        'label' => 'Filter Kabupaten/Kota',
                        'name' => 'kabupaten',
                        'id' => 'filterKabupaten',
                        'required' => false,
                        'placeholder' => 'Semua Kabupaten/Kota',
                        'options' => $allKabupaten,
                        'optionValue' => 'id_kabupaten',
                        'optionText' => 'nama_kabupaten',
                        'value' => $filterKabupaten,
                        'onchange' => 'document.getElementById("filterForm").submit()',
                        'enableSearch' => true
                    ]) ?>
                </div>

                <!-- Search Box -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pencarian</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" name="search" id="searchInput" 
                               value="<?= esc($search) ?>"
                               class="input-field w-full pl-10" 
                               placeholder="Cari nama admin atau email..."
                               onkeypress="if(event.key === 'Enter') document.getElementById('filterForm').submit()">
                    </div>
                </div>
            </div>

            <!-- Reset Filter Button -->
            <?php if (!empty($search) || !empty($filterKabupaten)): ?>
            <div class="mt-3 pt-3 border-t border-gray-200">
                <a href="<?= base_url('adminsurvei/admin-survei-kab') ?>" 
                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 hover:underline">
                    <i class="fas fa-times-circle mr-1.5"></i>
                    Reset Semua Filter
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Add Button -->
    <div class="flex justify-end mb-6">
        <a href="<?= base_url('adminsurvei/admin-survei-kab/assign') ?>" 
           class="btn-primary whitespace-nowrap">
            <i class="fas fa-plus mr-2"></i>
            Assign Admin Baru
        </a>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="adminSurveiTable">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Admin</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kabupaten/Kota</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Jumlah Kegiatan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($admin_list)): ?>
                    <?php foreach ($admin_list as $index => $admin): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 text-sm text-gray-900"><?= $index + 1 ?></td>
                            <td class="px-4 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-user text-blue-600 text-xs"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900"><?= esc($admin['nama_user']) ?></p>
                                        <p class="text-xs text-gray-500"><?= esc($admin['hp']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600">
                                <?= esc($admin['nama_kabupaten'] ?? '-') ?>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($admin['email']) ?></td>
                            <td class="px-4 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <?= $admin['jumlah_kegiatan'] ?> Kegiatan
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="viewDetail(<?= $admin['id_admin_kabupaten'] ?>)" 
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg" 
                                            title="Lihat Detail">
                                        <i class="fas fa-eye text-sm"></i>
                                    </button>
                                    <a href="<?= base_url('adminsurvei/admin-survei-kab/assign/' . $admin['id_admin_kabupaten']) ?>" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" 
                                       title="Edit Assignment">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?= $admin['id_admin_kabupaten'] ?>, '<?= esc($admin['nama_user']) ?>')" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg" 
                                            title="Hapus">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Detail Row (Hidden by default) -->
                        <tr id="detail-<?= $admin['id_admin_kabupaten'] ?>" class="bg-gray-50 hidden">
                            <td colspan="6" class="px-4 py-4">
                                <div class="bg-white rounded-lg border border-gray-200 p-4">
                                    <h4 class="text-sm font-semibold text-gray-900 mb-3">
                                        <i class="fas fa-list-check mr-2 text-blue-600"></i>
                                        Kegiatan Wilayah yang Di-assign
                                    </h4>
                                    
                                    <?php if (!empty($admin['kegiatan_wilayah'])): ?>
                                        <div class="space-y-2">
                                            <?php foreach ($admin['kegiatan_wilayah'] as $kegiatan): ?>
                                                <div class="flex items-start justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                    <div class="flex-1">
                                                        <div class="flex items-start gap-3">
                                                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                                                                <i class="fas fa-map-marked-alt text-green-600 text-xs"></i>
                                                            </div>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-medium text-gray-900">
                                                                    <?= esc($kegiatan['nama_kegiatan_detail_proses']) ?>
                                                                </p>
                                                                <p class="text-xs text-gray-600 mt-1">
                                                                    <?= esc($kegiatan['nama_kegiatan']) ?> - <?= esc($kegiatan['nama_kegiatan_detail']) ?>
                                                                </p>
                                                                <div class="flex flex-wrap gap-2 mt-2">
                                                                    <span class="inline-flex items-center px-2 py-0.5 bg-white border border-gray-300 text-gray-700 rounded text-xs">
                                                                        <i class="fas fa-bullseye mr-1 text-gray-500"></i>
                                                                        Target: <?= number_format($kegiatan['target_wilayah']) ?>
                                                                    </span>
                                                                    <span class="inline-flex items-center px-2 py-0.5 bg-white border border-gray-300 text-gray-700 rounded text-xs">
                                                                        <i class="fas fa-calendar mr-1 text-gray-500"></i>
                                                                        <?= date('d M Y', strtotime($kegiatan['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($kegiatan['tanggal_selesai'])) ?>
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <button onclick="deleteAssignment(<?= $admin['id_admin_kabupaten'] ?>, <?= $kegiatan['id_kegiatan_wilayah'] ?>, '<?= esc($kegiatan['nama_kegiatan_detail_proses']) ?>')"
                                                            class="ml-3 p-1.5 text-red-600 hover:bg-red-50 rounded"
                                                            title="Hapus Assignment">
                                                        <i class="fas fa-times text-sm"></i>
                                                    </button>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-4 text-gray-500 text-sm">
                                            <i class="fas fa-inbox text-3xl text-gray-300 mb-2"></i>
                                            <p>Belum ada kegiatan wilayah yang di-assign</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada admin survei kabupaten yang di-assign.</p>
                            <?php if (!empty($search) || !empty($filterKabupaten)): ?>
                                <a href="<?= base_url('adminsurvei/admin-survei-kab') ?>" 
                                   class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-block">
                                    Reset filter untuk melihat semua data
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Toggle detail view
function viewDetail(idAdmin) {
    const detailRow = document.getElementById(`detail-${idAdmin}`);
    if (detailRow) {
        detailRow.classList.toggle('hidden');
    }
}

// Delete entire admin
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Admin?',
        html: `Yakin ingin menghapus admin <strong>${name}</strong> beserta semua assignment-nya?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= base_url('adminsurvei/admin-survei-kab/delete/') ?>${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#3b82f6'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus data',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}

// Delete specific assignment
function deleteAssignment(idAdmin, idKegiatan, namaKegiatan) {
    Swal.fire({
        title: 'Hapus Assignment?',
        html: `Yakin ingin menghapus assignment untuk kegiatan <strong>${namaKegiatan}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= base_url('adminsurvei/admin-survei-kab/delete-assignment') ?>`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    id_admin_kabupaten: idAdmin,
                    id_kegiatan_wilayah: idKegiatan
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message,
                        confirmButtonColor: '#3b82f6'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message,
                        confirmButtonColor: '#ef4444'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus assignment',
                    confirmButtonColor: '#ef4444'
                });
            });
        }
    });
}

// ✅ Alert sukses dari session flashdata
<?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "<?= session()->getFlashdata('success') ?>",
        confirmButtonColor: '#3b82f6'
    });
<?php endif; ?>

// ⚠️ Alert error dari session flashdata
<?php if (session()->getFlashdata('error')) : ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: "<?= session()->getFlashdata('error') ?>",
        confirmButtonColor: '#ef4444'
    });
<?php endif; ?>
</script>

<?= $this->endSection() ?> 