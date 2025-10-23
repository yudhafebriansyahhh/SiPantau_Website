<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<style>
.toggle-switch {
    position: relative;
    display: inline-block;
    width: 50px;
    height: 24px;
}

.toggle-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e0;
    transition: .3s;
    border-radius: 24px;
}

.toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .3s;
    border-radius: 50%;
}

input:checked + .toggle-slider {
    background-color: #3b82f6;
}

input:checked + .toggle-slider:before {
    transform: translateX(26px);
}

.status-label {
    font-size: 0.75rem;
    font-weight: 500;
    margin-left: 8px;
}
</style>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('superadmin') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Pengguna</h1>
    <p class="text-gray-600 mt-1">Kelola data pengguna sistem SiPantau</p>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-green-600 mr-3"></i>
        <p class="text-sm text-green-700"><?= session()->getFlashdata('success') ?></p>
    </div>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
        <p class="text-sm text-red-700"><?= session()->getFlashdata('error') ?></p>
    </div>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('import_errors')): ?>
<div class="mb-4 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
    <div class="flex items-start">
        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3 mt-0.5"></i>
        <div>
            <p class="text-sm font-semibold text-yellow-800 mb-1">Detail Error Import:</p>
            <pre class="text-xs text-yellow-700 whitespace-pre-wrap"><?= session()->getFlashdata('import_errors') ?></pre>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Main Card -->
<div class="card">
    <!-- Search and Actions -->
    <div class="space-y-4 mb-6">
        <!-- Row 1: Search and Filter -->
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" 
                       class="input-field w-full pl-10" 
                       placeholder="Cari nama, email, atau HP..."
                       value="<?= esc($search) ?>"
                       onkeyup="searchTable()">
            </div>
            
            <div class="w-full sm:w-64">
                <select id="roleFilter" class="input-field w-full" onchange="filterByRole()">
                    <option value="">Semua Role</option>
                    <?php foreach ($roles as $role): ?>
                    <option value="<?= $role['id_roleuser'] ?>" <?= $roleFilter == $role['id_roleuser'] ? 'selected' : '' ?>>
                        <?= esc($role['roleuser']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        
        <!-- Row 2: Action Buttons -->
        <div class="flex flex-wrap gap-2 justify-end">
            <button onclick="showImportModal()" class="btn-secondary whitespace-nowrap">
                <i class="fas fa-file-import mr-2"></i>Import
            </button>
            
            <a href="<?= base_url('superadmin/kelola-pengguna/export') ?>" 
               class="btn-secondary whitespace-nowrap inline-flex items-center">
                <i class="fas fa-file-export mr-2"></i>Export
            </a>
            
            <a href="<?= base_url('superadmin/kelola-pengguna/create') ?>" 
               class="btn-primary whitespace-nowrap inline-flex items-center">
                <i class="fas fa-plus mr-2"></i>Tambah Pengguna
            </a>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="penggunaTable">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama & Email</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kab/Kota</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">No HP</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Role</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Status</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Tidak ada data pengguna</p>
                    </td>
                </tr>
                <?php else: ?>
                    <?php foreach ($users as $index => $user): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-4 py-4 text-sm text-gray-900"><?= $index + 1 ?></td>
                        <td class="px-4 py-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                    <span class="text-white text-sm font-medium">
                                        <?= strtoupper(substr($user['nama_user'], 0, 2)) ?>
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= esc($user['nama_user']) ?></p>
                                    <p class="text-xs text-gray-500"><?= esc($user['email']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm text-gray-600"><?= esc($user['nama_kabupaten'] ?? '-') ?></p>
                        </td>
                        <td class="px-4 py-4">
                            <p class="text-sm text-gray-600"><?= esc($user['hp']) ?></p>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex flex-wrap gap-1">
                                <?php if (!empty($user['role_names'])): ?>
                                    <?php 
                                    $badgeColors = ['bg-green-100 text-green-700', 'bg-blue-100 text-blue-700', 'bg-yellow-100 text-yellow-700', 'bg-purple-100 text-purple-700', 'bg-pink-100 text-pink-700', 'bg-indigo-100 text-indigo-700'];
                                    foreach ($user['role_names'] as $idx => $roleName): 
                                        $colorClass = $badgeColors[$idx % count($badgeColors)];
                                    ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $colorClass ?>">
                                        <?= esc($roleName) ?>
                                    </span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">Belum ada role</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center">
                                <label class="toggle-switch">
                                    <input type="checkbox" 
                                           <?= $user['is_active'] ? 'checked' : '' ?>
                                           onchange="toggleStatus(<?= $user['sobat_id'] ?>, this)">
                                    <span class="toggle-slider"></span>
                                </label>
                                <span class="status-label" id="status-label-<?= $user['sobat_id'] ?>">
                                    <?= $user['is_active'] ? '<span class="text-blue-600">Aktif</span>' : '<span class="text-gray-500">Nonaktif</span>' ?>
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="<?= base_url('superadmin/kelola-pengguna/edit/' . $user['sobat_id']) ?>" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                   title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="confirmDelete(<?= $user['sobat_id'] ?>, '<?= esc($user['nama_user']) ?>')"
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
    
    <div class="mt-6 flex items-center justify-between">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium" id="showingCount"><?= count($users) ?></span> data
        </p>
    </div>
</div>

<!-- Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Import Data Pengguna</h3>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-blue-700">
                        <i class="fas fa-info-circle mr-2"></i>
                        Download template Excel terlebih dahulu
                    </p>
                </div>
                
                <a href="<?= base_url('superadmin/kelola-pengguna/download-template') ?>" 
                   class="btn-secondary w-full text-center mb-4">
                    <i class="fas fa-download mr-2"></i>Download Template
                </a>
            </div>
            
            <form id="formImport" action="<?= base_url('superadmin/kelola-pengguna/import') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload File Excel <span class="text-red-500">*</span>
                    </label>
                    <input type="file" 
                           name="file" 
                           id="fileImport"
                           accept=".xlsx,.xls"
                           class="input-field"
                           required>
                    <p class="mt-1 text-xs text-gray-500">Format: .xlsx atau .xls</p>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" 
                            onclick="closeImportModal()"
                            class="btn-secondary flex-1">
                        Batal
                    </button>
                    <button type="submit" 
                            class="btn-primary flex-1">
                        <i class="fas fa-upload mr-2"></i>Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('penggunaTable');
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        for (let j = 1; j < cells.length - 2; j++) {
            const cell = cells[j];
            if (cell) {
                const textValue = cell.textContent || cell.innerText;
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        if (found) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }
    
    document.getElementById('showingCount').textContent = visibleCount;
}

function filterByRole() {
    const select = document.getElementById('roleFilter');
    const roleId = select.value;
    
    const url = new URL(window.location.href);
    if (roleId) {
        url.searchParams.set('role', roleId);
    } else {
        url.searchParams.delete('role');
    }
    window.location.href = url.toString();
}

function toggleStatus(id, checkbox) {
    const isActive = checkbox.checked;
    const labelEl = document.getElementById('status-label-' + id);
    
    fetch(`<?= base_url('superadmin/kelola-pengguna/toggle-status') ?>/${id}`, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (labelEl) {
                labelEl.innerHTML = isActive ? '<span class="text-blue-600">Aktif</span>' : '<span class="text-gray-500">Nonaktif</span>';
            }
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: data.message,
                timer: 1500,
                showConfirmButton: false
            });
        } else {
            checkbox.checked = !isActive;
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: data.message
            });
        }
    })
    .catch(error => {
        checkbox.checked = !isActive;
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Terjadi kesalahan saat mengubah status'
        });
    });
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Pengguna?',
        html: `Apakah Anda yakin ingin menghapus <strong>"${name}"</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`<?= base_url('superadmin/kelola-pengguna/delete') ?>/${id}`, {
                method: 'DELETE',
                headers: {
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
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message
                    });
                }
            });
        }
    });
}

function showImportModal() {
    document.getElementById('importModal').classList.remove('hidden');
}

function closeImportModal() {
    document.getElementById('importModal').classList.add('hidden');
    document.getElementById('formImport').reset();
}

document.getElementById('importModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeImportModal();
    }
});
</script>

<?= $this->endSection() ?>