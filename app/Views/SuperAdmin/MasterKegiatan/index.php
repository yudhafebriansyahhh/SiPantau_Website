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
    <!-- Search, Filter and Add Button -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-6">
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <!-- Search Box -->
            <div class="relative w-full sm:w-80">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" 
                       class="input-field w-full pl-10" 
                       placeholder="Cari nama kegiatan, fungsi, atau periode..."
                       onkeyup="searchTable()">
            </div>
            
            <!-- Filter Master Output -->
            <div class="relative w-full sm:w-64">
                <select id="filterOutput" 
                        class="input-field w-full appearance-none pr-10"
                        onchange="filterByOutput(this.value)">
                    <option value="all" <?= $filterOutput == 'all' ? 'selected' : '' ?>>Semua Master Output</option>
                    <?php foreach ($masterOutputs as $output): ?>
                        <option value="<?= $output['id_output'] ?>" <?= $filterOutput == $output['id_output'] ? 'selected' : '' ?>>
                            <?= esc($output['nama_output']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                </div>
            </div>
        </div>
        
        <!-- Add Button -->
        <a href="<?= base_url('superadmin/master-kegiatan/create') ?>" 
           class="btn-primary whitespace-nowrap w-full lg:w-auto text-center">
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
                        Master Output
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Fungsi
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
                <?php if (empty($kegiatans)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500 mb-4">Belum ada data master kegiatan</p>
                            <a href="<?= base_url('superadmin/master-kegiatan/create') ?>" class="btn-primary inline-block">
                                <i class="fas fa-plus mr-2"></i>Tambah Master Kegiatan Pertama
                            </a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $no = 1; ?>
                    <?php foreach ($kegiatans as $kegiatan): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 text-sm text-gray-900"><?= $no++ ?></td>
                            <td class="px-4 py-4">
                                <p class="text-sm font-medium text-gray-900"><?= esc($kegiatan['nama_kegiatan']) ?></p>
                                <?php if (!empty($kegiatan['pelaksana'])): ?>
                                    <p class="text-xs text-gray-500 mt-1">Pelaksana: <?= esc($kegiatan['pelaksana']) ?></p>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <?= esc($kegiatan['nama_output'] ?? 'Tidak ada') ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-600 line-clamp-2"><?= esc($kegiatan['fungsi']) ?></p>
                            </td>
                            <td class="px-4 py-4">
                                <span class="badge badge-info"><?= esc($kegiatan['periode']) ?></span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="<?= base_url('superadmin/master-kegiatan/show/' . $kegiatan['id_kegiatan']) ?>" 
                                       class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('superadmin/master-kegiatan/edit/' . $kegiatan['id_kegiatan']) ?>" 
                                       class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?= $kegiatan['id_kegiatan'] ?>, '<?= esc($kegiatan['nama_kegiatan'], 'js') ?>')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Info -->
    <?php if (!empty($kegiatans)): ?>
        <div class="mt-6">
            <p class="text-sm text-gray-600">
                Menampilkan <span class="font-medium"><?= count($kegiatans) ?></span> data
            </p>
        </div>
    <?php endif; ?>
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
        
        if (cells.length > 1) {
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
        }
        
        row.style.display = found ? '' : 'none';
    }
}

// Filter by master output
function filterByOutput(outputId) {
    const url = new URL(window.location.href);
    url.searchParams.set('output', outputId);
    window.location.href = url.toString();
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

// Fungsi untuk proses delete
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

    // Kirim request delete ke server
    fetch(`<?= base_url('superadmin/master-kegiatan/delete') ?>/${id}`, {
        method: 'DELETE',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil Dihapus!',
                text: data.message,
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
                }
            }).then(() => {
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Menghapus',
                text: data.message,
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
                }
            });
        }
    })
    .catch(error => {
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: 'Gagal menghapus data. Silakan coba lagi.',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        });
    });
}

// Show success/error message from session
<?php if (session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= session()->getFlashdata('success') ?>',
        timer: 3000,
        showConfirmButton: false,
        customClass: {
            popup: 'rounded-xl'
        }
    });
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal!',
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonColor: '#3b82f6',
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
        }
    });
<?php endif; ?>
</script>

<?= $this->endSection() ?>