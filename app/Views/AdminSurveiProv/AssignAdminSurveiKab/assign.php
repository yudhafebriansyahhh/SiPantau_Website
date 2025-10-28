<?= $this->extend('layouts/adminprov_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei/admin-survei-kab') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">
        <?= $is_edit ? 'Edit Assignment Admin Survei Kabupaten' : 'Assign Admin Survei Kabupaten' ?>
    </h1>
    <p class="text-gray-600 mt-1">
        <?= $is_edit ? 'Perbarui assignment kegiatan wilayah untuk admin' : 'Assign admin ke kegiatan wilayah' ?>
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
<div class="card max-w-6xl">
    <form action="<?= $is_edit ? base_url('adminsurvei/admin-survei-kab/update/' . $admin['id_admin_kabupaten']) : base_url('adminsurvei/admin-survei-kab/store') ?>" 
          method="POST" id="assignForm">
        <?= csrf_field() ?>
        
        <?php if ($is_edit): ?>
            <!-- Admin Info (Read-only untuk edit) -->
            <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-user text-white text-lg"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-sm font-medium text-gray-900"><?= esc($admin['nama_user']) ?></p>
                        <p class="text-xs text-gray-600 mt-1"><?= esc($admin['email']) ?> • <?= esc($admin['hp']) ?></p>
                        <p class="text-xs text-gray-600 mt-1">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                            <?= esc($admin['nama_kabupaten']) ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Pilih User -->
            <div class="mb-6">
                <?php if (empty($users)): ?>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pilih User <span class="text-red-500">*</span>
                    </label>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                            <p class="text-sm text-yellow-700">Tidak ada user yang tersedia</p>
                        </div>
                    </div>
                <?php else: ?>
                    <?= view('components/select_component', [
                        'label' => 'Pilih User',
                        'name' => 'sobat_id',
                        'id' => 'sobat_id',
                        'required' => true,
                        'placeholder' => 'Cari dan pilih user...',
                        'options' => $users,
                        'optionValue' => 'sobat_id',
                        'optionText' => function($user) {
                            $kabupaten = $user['nama_kabupaten'] ? ' - ' . $user['nama_kabupaten'] : '';
                            return esc($user['nama_user']) . $kabupaten;
                        },
                        'optionDataAttributes' => ['nama_user', 'email', 'hp', 'id_kabupaten', 'nama_kabupaten'],
                        'onchange' => 'updateUserInfo()',
                        'emptyMessage' => 'Tidak ada user yang tersedia',
                        'enableSearch' => true
                    ]) ?>
                    
                    <!-- User Info Preview -->
                    <div id="userInfoPreview" class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg hidden">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900" id="previewNama"></p>
                                <p class="text-xs text-gray-600" id="previewEmail"></p>
                                <p class="text-xs text-gray-500" id="previewHp"></p>
                                <p class="text-xs text-gray-500" id="previewKabupaten"></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Pilih Kegiatan Wilayah -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Kegiatan Wilayah <span class="text-red-500">*</span>
            </label>
            
            <?php if (empty($kegiatan_wilayah)): ?>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                        <p class="text-sm text-yellow-700">Belum ada kegiatan wilayah yang tersedia</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- Search Box -->
                <div class="relative mb-3">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" id="searchKegiatan"
                        class="input-field w-full pl-10"
                        placeholder="Cari kegiatan wilayah..."
                        onkeyup="filterKegiatan()">
                </div>

                <!-- Filter by Kabupaten (for create mode) -->
                <?php if (!$is_edit): ?>
                <div class="mb-3" id="filterKabupatenContainer" style="display: none;">
                    <select id="filterByKabupaten" class="input-field" onchange="filterByKabupaten()">
                        <option value="">Semua Kabupaten</option>
                    </select>
                </div>
                <?php endif; ?>

                <p class="text-sm text-gray-500 mb-3">
                    <i class="fas fa-info-circle mr-1"></i>
                    Pilih kegiatan wilayah yang akan di-assign ke admin
                </p>
                
                <!-- Kegiatan List dengan Checkbox -->
                <div id="kegiatanList" class="space-y-2 max-h-96 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    <?php 
                    $groupedByKab = [];
                    foreach ($kegiatan_wilayah as $kegiatan) {
                        $kabupaten = $kegiatan['nama_kabupaten'];
                        if (!isset($groupedByKab[$kabupaten])) {
                            $groupedByKab[$kabupaten] = [];
                        }
                        $groupedByKab[$kabupaten][] = $kegiatan;
                    }
                    ?>
                    
                    <?php foreach ($groupedByKab as $kabupaten => $items): ?>
                        <div class="kegiatan-group" data-kabupaten="<?= esc($kabupaten) ?>">
                            <div class="bg-gray-100 px-3 py-2 rounded-t-lg">
                                <p class="text-xs font-semibold text-gray-700 uppercase">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <?= esc($kabupaten) ?>
                                </p>
                            </div>
                            
                            <?php foreach ($items as $kegiatan): ?>
                                <label class="kegiatan-item flex items-start p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100"
                                       data-kegiatan-text="<?= strtolower(esc($kegiatan['nama_kegiatan_detail_proses'] . ' ' . $kegiatan['nama_kegiatan'] . ' ' . $kegiatan['nama_kegiatan_detail'])) ?>"
                                       data-id-kabupaten="<?= $kegiatan['id_kabupaten'] ?>">
                                    <input type="checkbox" 
                                           name="kegiatan_wilayah[]" 
                                           value="<?= $kegiatan['id_kegiatan_wilayah'] ?>"
                                           <?= in_array($kegiatan['id_kegiatan_wilayah'], $assigned_ids) ? 'checked' : '' ?>
                                           class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                    <div class="ml-3 flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">
                                            <?= esc($kegiatan['nama_kegiatan_detail_proses']) ?>
                                        </p>
                                        <p class="text-xs text-gray-600 mt-1">
                                            <?= esc($kegiatan['nama_kegiatan']) ?> - <?= esc($kegiatan['nama_kegiatan_detail']) ?>
                                        </p>
                                        <div class="flex flex-wrap gap-1.5 mt-2">
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
                                </label>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Selected Count -->
                <div class="mt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-600">
                        <span id="selectedCount">0</span> kegiatan wilayah terpilih
                    </span>
                    <button type="button" onclick="clearSelection()" class="text-red-600 hover:text-red-700">
                        <i class="fas fa-times-circle mr-1"></i>
                        Clear All
                    </button>
                </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <a href="<?= base_url('adminsurvei/admin-survei-kab') ?>" 
               class="btn-secondary flex-1 text-center">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" 
                    class="btn-primary flex-1"
                    <?= empty($users) || empty($kegiatan_wilayah) ? 'disabled' : '' ?>>
                <i class="fas fa-save mr-2"></i>
                <?= $is_edit ? 'Update Assignment' : 'Simpan Assignment' ?>
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Update user info preview
function updateUserInfo() {
    const select = document.getElementById('sobat_id');
    const preview = document.getElementById('userInfoPreview');
    const filterContainer = document.getElementById('filterKabupatenContainer');
    const filterSelect = document.getElementById('filterByKabupaten');
    
    if (select && select.value) {
        const option = select.options[select.selectedIndex];
        const idKabupaten = option.dataset.id_kabupaten;
        const namaKabupaten = option.dataset.nama_kabupaten;
        
        document.getElementById('previewNama').textContent = option.dataset.nama_user;
        document.getElementById('previewEmail').textContent = option.dataset.email;
        document.getElementById('previewHp').textContent = option.dataset.hp;
        document.getElementById('previewKabupaten').innerHTML = '<i class="fas fa-map-marker-alt mr-1"></i>' + namaKabupaten;
        preview.classList.remove('hidden');
        
        // Auto-filter kegiatan by kabupaten if user has kabupaten
        if (idKabupaten && filterContainer && filterSelect) {
            filterSelect.value = idKabupaten;
            filterByKabupaten();
            filterContainer.style.display = 'block';
        }
    } else if (preview) {
        preview.classList.add('hidden');
        if (filterContainer) {
            filterContainer.style.display = 'none';
        }
    }
}

// Build kabupaten filter options
document.addEventListener('DOMContentLoaded', function() {
    const filterSelect = document.getElementById('filterByKabupaten');
    if (filterSelect) {
        const kabupatenSet = new Set();
        document.querySelectorAll('.kegiatan-group').forEach(group => {
            const kab = group.dataset.kabupaten;
            if (kab) kabupatenSet.add(kab);
        });
        
        kabupatenSet.forEach(kab => {
            const option = document.createElement('option');
            option.value = kab;
            option.textContent = kab;
            filterSelect.appendChild(option);
        });
    }
    
    updateSelectedCount();
});

// Filter kegiatan by search
function filterKegiatan() {
    const searchValue = document.getElementById('searchKegiatan').value.toLowerCase();
    const items = document.querySelectorAll('.kegiatan-item');
    
    items.forEach(item => {
        const text = item.dataset.kegiatanText;
        if (text.includes(searchValue)) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

// Filter by kabupaten
function filterByKabupaten() {
    const filterValue = document.getElementById('filterByKabupaten').value;
    const groups = document.querySelectorAll('.kegiatan-group');
    
    groups.forEach(group => {
        if (!filterValue || group.dataset.kabupaten === filterValue) {
            group.style.display = '';
        } else {
            group.style.display = 'none';
        }
    });
}

// Update selected count
function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('input[name="kegiatan_wilayah[]"]:checked');
    const countElement = document.getElementById('selectedCount');
    if (countElement) {
        countElement.textContent = checkboxes.length;
    }
}

// Clear all selection
function clearSelection() {
    const checkboxes = document.querySelectorAll('input[name="kegiatan_wilayah[]"]');
    checkboxes.forEach(cb => cb.checked = false);
    updateSelectedCount();
}

// Listen to checkbox changes
document.addEventListener('change', function(e) {
    if (e.target.name === 'kegiatan_wilayah[]') {
        updateSelectedCount();
    }
});

// Form validation
document.getElementById('assignForm')?.addEventListener('submit', function(e) {
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
    
    const kegiatanChecked = document.querySelectorAll('input[name="kegiatan_wilayah[]"]:checked');
    if (kegiatanChecked.length === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Perhatian',
            text: 'Silakan pilih minimal satu kegiatan wilayah',
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }
});
</script>

<?= $this->endSection() ?>