<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('superadmin/kelola-admin-surveyprov') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Edit Assignment Admin Survei Provinsi</h1>
    <p class="text-gray-600 mt-1">Update kegiatan detail yang di-assign untuk <?= esc($admin['nama_user']) ?></p>
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
    <!-- Admin Info -->
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

    <form action="<?= base_url('superadmin/kelola-admin-surveyprov/update/' . $admin['id_admin_provinsi']) ?>" method="POST" id="editForm">
        <?= csrf_field() ?>
        
        <!-- Pilih Kegiatan Detail -->
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
                Update Assignment
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
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
    
    // Update select all checkbox state
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

// Form validation
document.getElementById('editForm')?.addEventListener('submit', function(e) {
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
});

// Initialize count
updateSelectedCount();
</script>

<?= $this->endSection() ?>