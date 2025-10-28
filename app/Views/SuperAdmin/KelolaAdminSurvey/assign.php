<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('superadmin/kelola-admin-surveyprov') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">
        <?= $is_edit ? 'Edit Assignment Admin Survei Provinsi' : 'Assign Admin Survei Provinsi' ?>
    </h1>
    <p class="text-gray-600 mt-1">
        <?= $is_edit ? 'Update kegiatan detail yang di-assign untuk ' . esc($admin['nama_user']) : 'Pilih user dan kegiatan detail yang akan di-assign' ?>
    </p>
</div>

<!-- Flash Messages -->
<?php if (session()->getFlashdata('error')): ?>
<div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
        <p class="text-sm text-red-700"><?= session()->getFlashdata('error') ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Form Card -->
<div class="card max-w-4xl">
    <?php if ($is_edit): ?>
    <!-- Admin Info for Edit Mode -->
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <div class="flex items-center">
            <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mr-4">
                <span class="text-white text-lg font-medium">
                    <?= strtoupper(substr($admin['nama_user'], 0, 2)) ?>
                </span>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900"><?= esc($admin['nama_user']) ?></p>
                <p class="text-xs text-gray-600"><?= esc($admin['email']) ?></p>
                <p class="text-xs text-gray-500 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 bg-blue-100 text-blue-700 rounded">
                        Sobat ID: <?= esc($admin['sobat_id']) ?>
                    </span>
                </p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <form action="<?= $is_edit ? base_url('superadmin/kelola-admin-surveyprov/update/' . $admin['id_admin_provinsi']) : base_url('superadmin/kelola-admin-surveyprov/store-assign') ?>" 
          method="POST" 
          id="assignForm">
        <?= csrf_field() ?>
        
        <?php if (!$is_edit): ?>
        <!-- Pilih User (Only for Create Mode) -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Pilih User <span class="text-red-500">*</span>
            </label>
            
            <?php if (empty($users)): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                        <p class="text-sm text-yellow-700">Tidak ada user yang tersedia</p>
                    </div>
                </div>
            <?php else: ?>
                <select name="sobat_id" id="sobat_id" class="input-field" required>
                    <option value="">-- Pilih User --</option>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['sobat_id'] ?>" 
                                data-nama="<?= esc($user['nama_user']) ?>"
                                data-email="<?= esc($user['email']) ?>"
                                data-hp="<?= esc($user['hp']) ?>">
                            <?= esc($user['nama_user']) ?> - <?= esc($user['sobat_id']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <!-- User Info Preview -->
                <div id="userInfoPreview" class="mt-3 p-4 bg-blue-50 border border-blue-200 rounded-lg hidden">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-white text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900" id="previewNama"></p>
                            <p class="text-xs text-gray-600" id="previewEmail"></p>
                            <p class="text-xs text-gray-500" id="previewHp"></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Pilih Kegiatan Detail (Multiple Selection) -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Kegiatan Detail <span class="text-red-500">*</span>
            </label>
            
            <?php if (empty($kegiatan_details)): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                        <p class="text-sm text-yellow-700">Belum ada kegiatan detail yang tersedia</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- Search Box -->
                <div class="mb-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchKegiatan" 
                               class="input-field pl-10" 
                               placeholder="Cari kegiatan...">
                    </div>
                </div>

                <!-- Select All -->
                <div class="mb-3 flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" id="selectAll" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                        <span class="ml-2 text-sm font-medium text-gray-700">Pilih Semua</span>
                    </label>
                    <span class="text-sm text-gray-600">
                        <span id="selectedCount">0</span> dipilih
                    </span>
                </div>

                <!-- Kegiatan List -->
                <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                    <?php 
                    $currentKegiatan = '';
                    foreach ($kegiatan_details as $detail): 
                        $isChecked = in_array($detail['id_kegiatan_detail'], $assigned_ids);
                        if ($currentKegiatan != $detail['nama_kegiatan']):
                            if ($currentKegiatan != ''): ?>
                            </div>
                            <?php endif; ?>
                            <div class="bg-gray-50 px-4 py-2 border-b border-gray-200 sticky top-0 z-10">
                                <p class="text-sm font-semibold text-gray-700">
                                    <i class="fas fa-folder-open text-blue-600 mr-2"></i>
                                    <?= esc($detail['nama_kegiatan']) ?>
                                </p>
                            </div>
                            <div>
                            <?php 
                            $currentKegiatan = $detail['nama_kegiatan'];
                        endif; 
                    ?>
                    <label class="flex items-start p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 kegiatan-item <?= $isChecked ? 'bg-blue-50' : '' ?>">
                        <input type="checkbox" 
                               name="kegiatan_details[]" 
                               value="<?= $detail['id_kegiatan_detail'] ?>" 
                               class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 mt-1 kegiatan-checkbox"
                               <?= $isChecked ? 'checked' : '' ?>>
                        <div class="ml-3 flex-1">
                            <p class="text-sm font-medium text-gray-900 kegiatan-name">
                                <?= esc($detail['nama_kegiatan_detail']) ?>
                                <?php if ($isChecked): ?>
                                    <span class="ml-2 text-xs text-blue-600">✓ Sudah di-assign</span>
                                <?php endif; ?>
                            </p>
                            <div class="mt-1 flex flex-wrap gap-2 text-xs text-gray-500">
                                <span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                    <i class="fas fa-ruler mr-1"></i>
                                    <?= esc($detail['satuan']) ?>
                                </span>
                                <span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 rounded">
                                    <i class="fas fa-calendar-alt mr-1"></i>
                                    <?= esc($detail['periode']) ?>
                                </span>
                                <span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-700 rounded">
                                    <i class="fas fa-clock mr-1"></i>
                                    <?= esc($detail['tahun']) ?>
                                </span>
                                <?php if (!empty($detail['tanggal_mulai']) && !empty($detail['tanggal_selesai'])): ?>
                                <span class="inline-flex items-center px-2 py-1 bg-orange-100 text-orange-700 rounded">
                                    <i class="fas fa-calendar-check mr-1"></i>
                                    <?= date('d M Y', strtotime($detail['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($detail['tanggal_selesai'])) ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </label>
                    <?php endforeach; ?>
                    </div>
                </div>
                
                <p class="text-xs text-gray-500 mt-2">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pilih minimal satu kegiatan detail untuk di-assign
                </p>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <a href="<?= base_url('superadmin/kelola-admin-surveyprov') ?>" 
               class="btn-secondary flex-1 text-center">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" 
                    class="btn-primary flex-1"
                    <?= empty($kegiatan_details) ? 'disabled' : '' ?>>
                <i class="fas fa-save mr-2"></i>
                <?= $is_edit ? 'Update Assignment' : 'Simpan Assignment' ?>
            </button>
        </div>
    </form>
</div>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- jQuery & Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
.select2-container--default .select2-selection--single {
    height: 42px;
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
    padding: 0.5rem 0.75rem;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 26px;
    padding-left: 0;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 40px;
    right: 8px;
}

.select2-dropdown {
    border: 1px solid #d1d5db;
    border-radius: 0.5rem;
}
</style>

<script>
$(document).ready(function() {
    <?php if (!$is_edit): ?>
    // Initialize Select2 for user selection
    $('#sobat_id').select2({
        placeholder: '-- Pilih User --',
        allowClear: true,
        width: '100%'
    }).on('change', function() {
        updateUserInfo();
    });
    <?php endif; ?>
});

// Select All functionality
document.getElementById('selectAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.kegiatan-checkbox');
    const visibleCheckboxes = Array.from(checkboxes).filter(cb => {
        return cb.closest('.kegiatan-item').style.display !== 'none';
    });
    
    visibleCheckboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
        updateItemStyle(checkbox);
    });
    
    updateSelectedCount();
});

// Update count when individual checkbox changes
document.querySelectorAll('.kegiatan-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updateItemStyle(this);
        updateSelectedCount();
    });
});

// Update item background style
function updateItemStyle(checkbox) {
    const item = checkbox.closest('.kegiatan-item');
    if (checkbox.checked) {
        item.classList.add('bg-blue-50');
    } else {
        item.classList.remove('bg-blue-50');
    }
}

// Update selected count
function updateSelectedCount() {
    const checked = document.querySelectorAll('.kegiatan-checkbox:checked').length;
    document.getElementById('selectedCount').textContent = checked;
    
    const allCheckboxes = document.querySelectorAll('.kegiatan-checkbox');
    const visibleCheckboxes = Array.from(allCheckboxes).filter(cb => {
        return cb.closest('.kegiatan-item').style.display !== 'none';
    });
    const allVisibleChecked = visibleCheckboxes.length > 0 && 
                              visibleCheckboxes.every(cb => cb.checked);
    
    const selectAll = document.getElementById('selectAll');
    if (selectAll) {
        selectAll.checked = allVisibleChecked;
    }
}

// Search functionality
document.getElementById('searchKegiatan')?.addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const items = document.querySelectorAll('.kegiatan-item');
    
    items.forEach(item => {
        const name = item.querySelector('.kegiatan-name').textContent.toLowerCase();
        if (name.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
    
    updateSelectedCount();
});

// Update user info preview
function updateUserInfo() {
    const select = document.getElementById('sobat_id');
    const preview = document.getElementById('userInfoPreview');
    
    if (select && select.value) {
        const option = select.options[select.selectedIndex];
        document.getElementById('previewNama').textContent = option.dataset.nama;
        document.getElementById('previewEmail').textContent = option.dataset.email;
        document.getElementById('previewHp').textContent = option.dataset.hp;
        preview.classList.remove('hidden');
    } else if (preview) {
        preview.classList.add('hidden');
    }
}

// Form validation
document.getElementById('assignForm')?.addEventListener('submit', function(e) {
    const selectedKegiatan = document.querySelectorAll('.kegiatan-checkbox:checked').length;
    
    if (selectedKegiatan === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Silakan pilih minimal satu kegiatan detail',
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }
    
    <?php if (!$is_edit): ?>
    const sobatId = document.getElementById('sobat_id')?.value;
    if (!sobatId) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Silakan pilih user terlebih dahulu',
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }
    <?php endif; ?>
});

// Initialize count
updateSelectedCount();
</script>

<?= $this->endSection() ?>