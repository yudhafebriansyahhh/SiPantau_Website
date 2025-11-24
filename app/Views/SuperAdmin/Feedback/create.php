<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="<?= base_url('superadmin/feedback') ?>" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Buat Feedback Baru</h1>
            <p class="text-gray-600 mt-1">Berikan feedback kepada pengguna</p>
        </div>
    </div>
</div>

<!-- Error Messages -->
<?php if (session()->getFlashdata('errors')): ?>
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
    <div class="flex">
        <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
        <div class="flex-1">
            <p class="font-medium text-red-800 mb-1">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form -->
    <div class="lg:col-span-2">
        <div class="card">
            <form action="<?= base_url('superadmin/feedback/store') ?>" method="POST" id="feedbackForm">
                <?= csrf_field() ?>

                <!-- Pilih User -->
                <div class="mb-6">
                    <?php if (empty($users)): ?>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Pilih Pengguna <span class="text-red-500">*</span>
                        </label>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-3"></i>
                                <p class="text-sm text-yellow-700">Tidak ada pengguna yang tersedia</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?= view('components/select_component', [
                            'label' => 'Pilih Pengguna',
                            'name' => 'sobat_id',
                            'id' => 'sobat_id',
                            'required' => true,
                            'placeholder' => 'Cari berdasarkan nama atau Sobat ID...',
                            'options' => $users,
                            'optionValue' => 'sobat_id',
                            'optionText' => function($user) {
                                return esc($user['nama_user']) . ' (' . esc($user['sobat_id']) . ')';
                            },
                            'optionDataAttributes' => ['nama_user', 'sobat_id', 'nama_kabupaten'],
                            'onchange' => 'loadUserDetail()',
                            'emptyMessage' => 'Tidak ada pengguna yang tersedia',
                            'enableSearch' => true,
                            'value' => old('sobat_id')
                        ]) ?>
                        
                        <!-- User Info Preview -->
                        <div id="userInfoPreview" class="mt-3 p-3 bg-gray-50 border border-gray-200 rounded-lg hidden">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <span id="userInitialsSmall" class="text-white text-sm font-semibold"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900" id="previewNama"></p>
                                    <p class="text-xs text-gray-600" id="previewKabupaten"></p>
                                    <p class="text-xs text-gray-500" id="previewSobatId"></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Feedback -->
                <div class="mb-6">
                    <label for="feedback" class="block text-sm font-medium text-gray-700 mb-2">
                        Isi Feedback <span class="text-red-500">*</span>
                    </label>
                    <textarea name="feedback" 
                              id="feedback" 
                              rows="5" 
                              class="input-field"
                              placeholder="Tulis feedback untuk pengguna..."
                              required
                              maxlength="255"><?= old('feedback') ?></textarea>
                    <p class="text-sm text-gray-500 mt-1">
                        <span id="charCount">0</span>/255 karakter
                    </p>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="<?= base_url('superadmin/feedback') ?>" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" class="btn-primary" <?= empty($users) ? 'disabled' : '' ?>>
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Feedback
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Info -->
    <div class="lg:col-span-1">
        <div class="card" id="userInfoCard" style="display: none;">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengguna</h3>
            
            <div class="space-y-4">
                <!-- Avatar -->
                <div class="flex justify-center">
                    <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center">
                        <span id="userInitials" class="text-white text-2xl font-bold"></span>
                    </div>
                </div>

                <!-- User Details -->
                <div class="text-center">
                    <p id="userName" class="text-lg font-semibold text-gray-900"></p>
                    <p id="userKabupaten" class="text-sm text-gray-600"></p>
                </div>

                <div class="border-t border-gray-200 pt-4 space-y-3">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-id-card text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500">Sobat ID</p>
                            <p id="userSobatId" class="text-sm font-medium text-gray-900"></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-user-tag text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500">Role</p>
                            <p id="userRoles" class="text-sm font-medium text-gray-900"></p>
                        </div>
                    </div>
                </div>

                <!-- Feedback History -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Riwayat Feedback</h4>
                    <div id="feedbackHistory">
                        <p class="text-sm text-gray-500">Memuat...</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-4" id="placeholderCard">
            <div class="text-center py-8">
                <i class="fas fa-user-circle text-gray-300 text-5xl mb-3"></i>
                <p class="text-gray-500">Pilih pengguna untuk melihat detail</p>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter
const feedbackTextarea = document.getElementById('feedback');
const charCount = document.getElementById('charCount');

if (feedbackTextarea) {
    feedbackTextarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Initialize char count if old value exists
    if (feedbackTextarea.value) {
        charCount.textContent = feedbackTextarea.value.length;
    }
}

// Load user detail
function loadUserDetail() {
    const select = document.getElementById('sobat_id');
    const sobatId = select ? select.value : null;
    
    if (!sobatId) {
        document.getElementById('userInfoCard').style.display = 'none';
        document.getElementById('placeholderCard').style.display = 'block';
        document.getElementById('userInfoPreview').classList.add('hidden');
        return;
    }
    
    // Show user info card
    document.getElementById('userInfoCard').style.display = 'block';
    document.getElementById('placeholderCard').style.display = 'none';
    
    // Get selected option data
    const selectedOption = select.options[select.selectedIndex];
    const userName = selectedOption.dataset.nama_user;
    const userKabupaten = selectedOption.dataset.nama_kabupaten || '-';
    
    // Update preview card
    document.getElementById('previewNama').textContent = userName;
    document.getElementById('previewKabupaten').textContent = userKabupaten;
    document.getElementById('previewSobatId').textContent = sobatId;
    document.getElementById('userInfoPreview').classList.remove('hidden');
    
    // Update initials for preview
    const nameParts = userName.split(' ');
    let initials = '';
    if (nameParts.length >= 2) {
        initials = nameParts[0].charAt(0) + nameParts[1].charAt(0);
    } else {
        initials = userName.substring(0, 2);
    }
    document.getElementById('userInitialsSmall').textContent = initials.toUpperCase();
    
    // Update user info card
    document.getElementById('userName').textContent = userName;
    document.getElementById('userKabupaten').textContent = userKabupaten;
    document.getElementById('userSobatId').textContent = sobatId;
    document.getElementById('userInitials').textContent = initials.toUpperCase();
    
    // Load full user detail via AJAX
    fetch(`<?= base_url('superadmin/feedback/get-user-detail') ?>?sobat_id=${sobatId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('userRoles').textContent = data.data.roles_display || '-';
            }
        })
        .catch(error => {
            console.error('Error loading user detail:', error);
            document.getElementById('userRoles').textContent = '-';
        });
    
    // Load feedback history
    loadFeedbackHistory(sobatId);
}

// Load feedback history
function loadFeedbackHistory(sobatId) {
    const historyDiv = document.getElementById('feedbackHistory');
    historyDiv.innerHTML = '<p class="text-sm text-gray-500">Memuat...</p>';
    
    fetch(`<?= base_url('superadmin/feedback/get-user-feedback-history') ?>?sobat_id=${sobatId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.data.length === 0) {
                    historyDiv.innerHTML = '<p class="text-sm text-gray-500">Belum ada feedback sebelumnya</p>';
                } else {
                    let html = `<p class="text-xs text-gray-500 mb-2">Total: ${data.total} feedback</p>`;
                    html += '<div class="space-y-2 max-h-48 overflow-y-auto">';
                    
                    data.data.slice(0, 5).forEach(fb => {
                        const date = new Date(fb.created_at);
                        const formattedDate = date.toLocaleDateString('id-ID', { 
                            day: 'numeric', 
                            month: 'short', 
                            year: 'numeric' 
                        });
                        
                        html += `
                            <div class="text-sm bg-gray-50 p-2 rounded">
                                <p class="text-gray-700 line-clamp-2">${fb.feedback}</p>
                                <p class="text-xs text-gray-500 mt-1">${formattedDate}</p>
                            </div>
                        `;
                    });
                    
                    html += '</div>';
                    historyDiv.innerHTML = html;
                }
            } else {
                historyDiv.innerHTML = '<p class="text-sm text-red-500">Gagal memuat riwayat</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            historyDiv.innerHTML = '<p class="text-sm text-red-500">Terjadi kesalahan</p>';
        });
}

// Load user detail on page load if sobat_id is selected
document.addEventListener('DOMContentLoaded', function() {
    const sobatIdValue = '<?= old('sobat_id') ?>';
    if (sobatIdValue) {
        const select = document.getElementById('sobat_id');
        if (select) {
            select.value = sobatIdValue;
            loadUserDetail();
        }
    }
});
</script>

<?= $this->endSection() ?>