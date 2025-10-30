<?= $this->extend('layouts/adminkab_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei-kab/assign-petugas') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Edit Assign Petugas Survei</h1>
    <p class="text-gray-600 mt-1">Edit assignment PML dan PCL ke kegiatan survei</p>
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

<?php if (session()->getFlashdata('success')): ?>
<div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-4">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-green-600 mr-3"></i>
        <p class="text-sm text-green-700"><?= session()->getFlashdata('success') ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Form Card -->
<div class="card max-w-6xl">
    <form action="<?= base_url('adminsurvei-kab/assign-petugas/update/' . $pml['id_pml']) ?>" method="POST" id="editAssignForm">
        <?= csrf_field() ?>
        
        <!-- Step 1: Kegiatan Survei -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-600 text-white rounded-full text-xs mr-2">1</span>
                Kegiatan Survei <span class="text-red-500">*</span>
            </label>
            <?= view('components/select_component', [
                'name' => 'kegiatan_survei',
                'id' => 'kegiatanSurvei',
                'required' => true,
                'placeholder' => 'Cari dan pilih kegiatan survei...',
                'options' => $kegiatanList,
                'optionValue' => 'id_kegiatan_wilayah',
                'optionText' => function($k) {
                    return $k['nama_kegiatan'] . (!empty($k['nama_kegiatan_detail_proses']) ? ' - ' . $k['nama_kegiatan_detail_proses'] : '');
                },
                'selected' => $pml['id_kegiatan_wilayah'],
                'onchange' => 'handleKegiatanChange()',
                'emptyMessage' => 'Tidak ada kegiatan yang tersedia',
                'enableSearch' => true
            ]) ?>
        </div>

        <!-- Step 2: PML Info -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-600 text-white rounded-full text-xs mr-2">2</span>
                Nama PML <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   class="input-field bg-gray-50"
                   value="<?= esc($pml['nama_pml']) ?>" 
                   readonly>
            <p class="text-sm text-gray-500 mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                PML tidak dapat diubah saat edit
            </p>
        </div>

        <!-- Step 3: Target PML -->
        <div class="mb-6" id="targetPMLSection">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-600 text-white rounded-full text-xs mr-2">3</span>
                Target PML <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="number" 
                       name="pml_target" 
                       id="pmlTarget"
                       class="input-field pr-16"
                       placeholder="Masukkan target PML..."
                       value="<?= esc($pml['target']) ?>"
                       min="1"
                       oninput="updateSisaTarget()"
                       required>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <span class="text-gray-500 text-sm">unit</span>
                </div>
            </div>
            <p id="sisaTargetInfo" class="text-sm text-gray-500 mt-1"></p>
            <p class="text-sm text-blue-600 mt-2" id="sisaTargetPML">
                <i class="fas fa-chart-line mr-1"></i>
                Sisa target tersedia untuk PCL: <span class="font-bold" id="sisaTargetValue">0</span> unit
            </p>
        </div>

        <!-- Step 4: Daftar PCL -->
        <div class="mb-6" id="pclSection">
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-gray-700">
                    <span class="inline-flex items-center justify-center w-6 h-6 bg-blue-600 text-white rounded-full text-xs mr-2">4</span>
                    Daftar PCL (Opsional)
                </label>
                <button type="button" onclick="addPCLRow()" class="btn-primary btn-sm">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah PCL
                </button>
            </div>

            <div id="pclContainer">
                <?php if (!empty($pcls)): ?>
                    <?php foreach ($pcls as $index => $pcl): ?>
                        <div class="pcl-row border border-gray-200 rounded-lg p-4 mb-3" id="pclRow<?= $index ?>" data-pcl-number="<?= $index + 1 ?>">
                            <div class="flex items-center gap-3 mb-3">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-semibold pcl-number">
                                    <?= $index + 1 ?>
                                </span>
                                <h3 class="text-sm font-medium text-gray-900">PCL #<?= $index + 1 ?></h3>
                            </div>
                            <div class="flex gap-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama PCL</label>
                                    <select name="pcl[<?= $index ?>][sobat_id]" 
                                            class="select2-pcl pcl-select" 
                                            style="width: 100%"
                                            data-row="<?= $index ?>">
                                        <option value="">Pilih PCL...</option>
                                        <?php foreach ($availablePCL as $user): ?>
                                            <option value="<?= $user['sobat_id'] ?>" 
                                                <?= $pcl['sobat_id'] == $user['sobat_id'] ? 'selected' : '' ?>>
                                                <?= esc($user['nama_user']) ?> - <?= esc($user['email']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                        <!-- Preserve existing PCL if not in available list -->
                                        <?php if (!in_array($pcl['sobat_id'], array_column($availablePCL, 'sobat_id'))): ?>
                                            <option value="<?= $pcl['sobat_id'] ?>" selected>
                                                <?= esc($pcl['nama_user']) ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="w-48">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Target</label>
                                    <input type="number" 
                                           name="pcl[<?= $index ?>][target]" 
                                           class="input-field pcl-target"
                                           placeholder="Target..."
                                           value="<?= esc($pcl['target']) ?>"
                                           min="1"
                                           oninput="validatePCLTarget(this)">
                                </div>
                                <div class="w-12 flex items-end">
                                    <button type="button" 
                                            onclick="removePCLRow(<?= $index ?>)"
                                            class="btn-danger btn-sm w-full h-10">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Target maksimal akan diperbarui secara otomatis
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-blue-600 mr-3 mt-0.5"></i>
                    <div class="flex-1">
                        <p class="text-sm text-blue-900 font-medium">Informasi Target</p>
                        <p class="text-xs text-blue-700 mt-1">
                            Total target PCL: <span class="font-bold" id="totalTargetPCL">0</span> unit
                        </p>
                        <p class="text-xs text-blue-700">
                            Sisa target PML: <span class="font-bold" id="sisaTargetPMLForPCL">0</span> unit
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-200">
            <a href="<?= base_url('adminsurvei-kab/assign-petugas') ?>" 
               class="btn-secondary flex-1 text-center">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" 
                    class="btn-primary flex-1"
                    id="submitBtn">
                <i class="fas fa-save mr-2"></i>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Global variables
let pclCounter = <?= !empty($pcls) ? count($pcls) : 0 ?>;
let selectedKegiatan = '<?= $pml['id_kegiatan_wilayah'] ?>';
let targetPML = <?= $pml['target'] ?>;
let availablePCL = <?= json_encode($availablePCL) ?>;
let sisaTargetWilayah = 0;
let targetWilayah = 0;
let targetTerpakai = 0;
let originalTarget = <?= $pml['target'] ?>; // Store original target
let csrfName = '<?= csrf_token() ?>';
let csrfHash = '<?= csrf_hash() ?>';

// Initialize on page load
$(document).ready(function() {
    // Initialize Select2 for kegiatan
    if ($('#kegiatanSurvei').length && !$('#kegiatanSurvei').data('select2')) {
        $('#kegiatanSurvei').select2({
            placeholder: 'Cari dan pilih kegiatan survei...',
            allowClear: true,
            width: '100%'
        });
    }
    
    // Set selected value for kegiatan survei
    if (selectedKegiatan) {
        $('#kegiatanSurvei').val(selectedKegiatan).trigger('change.select2');
    }
    
    // Initialize Select2 for existing PCL
    $('.select2-pcl').each(function() {
        $(this).select2({
            placeholder: 'Cari dan pilih PCL...',
            allowClear: true,
            width: '100%'
        });
    });
    
    // Load initial target data
    if (selectedKegiatan) {
        loadSisaTargetWilayah(selectedKegiatan);
    }
    
    // Calculate initial target info
    updateTargetInfo();
});

// Handle kegiatan change
function handleKegiatanChange() {
    const kegiatanValue = $('#kegiatanSurvei').val();
    selectedKegiatan = kegiatanValue;
    
    if (selectedKegiatan) {
        loadSisaTargetWilayah(selectedKegiatan);
    } else {
        Swal.fire({
            icon: 'warning',
            title: 'Peringatan',
            text: 'Pilih kegiatan survei terlebih dahulu',
            confirmButtonColor: '#3b82f6'
        });
    }
}

// Load sisa target wilayah
function loadSisaTargetWilayah(idKegiatanWilayah) {
    $.ajax({
        url: '<?= base_url('adminsurvei-kab/assign-petugas/get-sisa-target-wilayah') ?>',
        method: 'POST',
        data: {
            id_kegiatan_wilayah: idKegiatanWilayah,
            [csrfName]: csrfHash
        },
        success: function(response) {
            csrfHash = response.csrf_hash || csrfHash;
            
            if (response.success) {
                targetWilayah = response.target_wilayah;
                targetTerpakai = response.target_terpakai;
                sisaTargetWilayah = response.sisa_target + originalTarget; // Add back original target
                
                updateSisaTargetInfo();
                updateSisaTarget();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.error || 'Gagal memuat data target wilayah',
                    confirmButtonColor: '#ef4444'
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Error loading sisa target:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Gagal memuat data target wilayah. Silakan coba lagi.',
                confirmButtonColor: '#ef4444'
            });
        }
    });
}

// Update sisa target info
function updateSisaTargetInfo() {
    const sisaInfo = document.getElementById('sisaTargetInfo');
    if (sisaInfo) {
        sisaInfo.innerHTML = `
            <span class="inline-flex items-center gap-3 text-xs">
                <span><strong>Target Wilayah:</strong> ${targetWilayah.toLocaleString()}</span>
                <span class="text-gray-300">|</span>
                <span><strong>Terpakai:</strong> ${(targetTerpakai - originalTarget).toLocaleString()}</span>
                <span class="text-gray-300">|</span>
                <span class="text-green-600 font-semibold"><strong>Sisa:</strong> ${sisaTargetWilayah.toLocaleString()}</span>
            </span>
        `;
    }
}

// Update sisa target
function updateSisaTarget() {
    const pmlTargetInput = document.getElementById('pmlTarget');
    targetPML = parseInt(pmlTargetInput.value) || 0;
    
    // Validasi tidak melebihi sisa target wilayah
    if (targetPML > sisaTargetWilayah) {
        Swal.fire({
            icon: 'error',
            title: 'Target Melebihi Batas',
            text: `Target PML (${targetPML.toLocaleString()}) melebihi sisa target wilayah (${sisaTargetWilayah.toLocaleString()})`,
            confirmButtonColor: '#ef4444'
        });
        pmlTargetInput.value = sisaTargetWilayah;
        targetPML = sisaTargetWilayah;
        return;
    }
    
    if (targetPML > 0) {
        document.getElementById('sisaTargetValue').textContent = targetPML;
        updateTargetInfo();
    }
}

// Add PCL row
function addPCLRow() {
    if (availablePCL.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada PCL',
            text: 'Tidak ada PCL yang tersedia untuk ditambahkan',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    const sisaTarget = targetPML - getTotalTargetPCL();
    if (sisaTarget <= 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Target Penuh',
            text: 'Target PML sudah terpenuhi. Tidak dapat menambah PCL lagi.',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    pclCounter++;
    const container = document.getElementById('pclContainer');
    const pclNumber = document.querySelectorAll('.pcl-row').length + 1;
    
    const row = document.createElement('div');
    row.className = 'pcl-row border border-gray-200 rounded-lg p-4 mb-3';
    row.id = `pclRow${pclCounter}`;
    row.dataset.pclNumber = pclNumber;
    
    let optionsHTML = '<option value="">Pilih PCL...</option>';
    availablePCL.forEach(pcl => {
        optionsHTML += `<option value="${pcl.sobat_id}">${escapeHtml(pcl.nama_user)} - ${escapeHtml(pcl.email)}</option>`;
    });
    
    row.innerHTML = `
        <div class="flex items-center gap-3 mb-3">
            <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-600 text-white rounded-full text-sm font-semibold pcl-number">
                ${pclNumber}
            </span>
            <h3 class="text-sm font-medium text-gray-900">PCL #${pclNumber}</h3>
        </div>
        <div class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama PCL</label>
                <select name="pcl[${pclCounter}][sobat_id]" 
                        class="select2-pcl pcl-select" 
                        style="width: 100%"
                        data-row="${pclCounter}">
                    ${optionsHTML}
                </select>
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-2">Target</label>
                <input type="number" 
                       name="pcl[${pclCounter}][target]" 
                       class="input-field pcl-target"
                       placeholder="Target..."
                       min="1"
                       max="${sisaTarget}"
                       oninput="validatePCLTarget(this)">
            </div>
            <div class="w-12 flex items-end">
                <button type="button" 
                        onclick="removePCLRow(${pclCounter})"
                        class="btn-danger btn-sm w-full h-10">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <p class="text-xs text-gray-500 mt-2">
            <i class="fas fa-info-circle mr-1"></i>
            Maksimal target: <span class="font-medium text-gray-700">${sisaTarget}</span> unit
        </p>
    `;
    
    container.appendChild(row);
    
    // Initialize Select2 for new row
    $(`#pclRow${pclCounter} .select2-pcl`).select2({
        placeholder: 'Cari dan pilih PCL...',
        allowClear: true,
        width: '100%'
    });
    
    updateTargetInfo();
}

// Remove PCL row
function removePCLRow(id) {
    Swal.fire({
        title: 'Hapus PCL?',
        text: 'PCL akan dihapus dari daftar',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            const row = document.getElementById(`pclRow${id}`);
            if (row) {
                // Destroy Select2 before removing
                $(row).find('.select2-pcl').select2('destroy');
                row.remove();
                
                // Renumber remaining PCL rows
                renumberPCLRows();
                updateTargetInfo();
            }
        }
    });
}

// Renumber PCL rows after deletion
function renumberPCLRows() {
    const rows = document.querySelectorAll('.pcl-row');
    rows.forEach((row, index) => {
        const number = index + 1;
        row.dataset.pclNumber = number;
        
        const numberBadge = row.querySelector('.pcl-number');
        if (numberBadge) {
            numberBadge.textContent = number;
        }
        
        const heading = row.querySelector('h3');
        if (heading) {
            heading.textContent = `PCL #${number}`;
        }
    });
}

// Validate PCL target
function validatePCLTarget(input) {
    const target = parseInt(input.value) || 0;
    const currentTotal = getTotalTargetPCL();
    const otherTargets = currentTotal - target;
    const sisaTarget = targetPML - otherTargets;
    
    if (target > sisaTarget) {
        Swal.fire({
            icon: 'warning',
            title: 'Target Melebihi Batas',
            text: `Target PCL tidak boleh melebihi sisa target PML (${sisaTarget} unit)`,
            confirmButtonColor: '#3b82f6'
        });
        input.value = sisaTarget;
    }
    
    updateTargetInfo();
}

// Get total target PCL
function getTotalTargetPCL() {
    let total = 0;
    document.querySelectorAll('.pcl-target').forEach(input => {
        total += parseInt(input.value) || 0;
    });
    return total;
}

// Update target info
function updateTargetInfo() {
    const totalTargetPCL = getTotalTargetPCL();
    const sisaTarget = targetPML - totalTargetPCL;
    
    document.getElementById('totalTargetPCL').textContent = totalTargetPCL.toLocaleString();
    document.getElementById('sisaTargetPMLForPCL').textContent = sisaTarget.toLocaleString();
    document.getElementById('sisaTargetValue').textContent = sisaTarget.toLocaleString();
    
    // Update max attribute for all PCL target inputs
    document.querySelectorAll('.pcl-target').forEach(input => {
        const currentValue = parseInt(input.value) || 0;
        const otherTargets = totalTargetPCL - currentValue;
        input.max = targetPML - otherTargets;
        
        // Update info text in parent row
        const row = input.closest('.pcl-row');
        if (row) {
            const infoText = row.querySelector('.text-gray-700');
            if (infoText) {
                infoText.textContent = (targetPML - otherTargets).toLocaleString();
            }
        }
    });
}

// Helper function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Form validation
document.getElementById('editAssignForm').addEventListener('submit', function(e) {
    const totalTargetPCL = getTotalTargetPCL();
    
    if (totalTargetPCL > targetPML) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Target Tidak Valid',
            text: `Total target PCL (${totalTargetPCL.toLocaleString()}) melebihi target PML (${targetPML.toLocaleString()})`,
            confirmButtonColor: '#3b82f6'
        });
        return false;
    }
    
    if (targetPML > sisaTargetWilayah) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Target Melebihi Batas',
            text: `Target PML (${targetPML.toLocaleString()}) melebihi sisa target wilayah (${sisaTargetWilayah.toLocaleString()})`,
            confirmButtonColor: '#ef4444'
        });
        return false;
    }
    
    // Show loading
    Swal.fire({
        title: 'Menyimpan...',
        text: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
});
</script>

<?= $this->endSection() ?>  