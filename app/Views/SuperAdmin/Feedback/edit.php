<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center gap-4">
        <a href="<?= base_url('superadmin/feedback') ?>" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Feedback</h1>
            <p class="text-gray-600 mt-1">Perbarui feedback untuk pengguna</p>
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
            <form action="<?= base_url('superadmin/feedback/update/' . $feedback['id_feedback']) ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="_method" value="PUT">

                <!-- User Info (Read Only) -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Penerima Feedback:</p>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center">
                            <?php
                            $nameParts = explode(' ', $feedback['nama_user']);
                            $initials = '';
                            if (count($nameParts) >= 2) {
                                $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
                            } else {
                                $initials = strtoupper(substr($feedback['nama_user'], 0, 2));
                            }
                            ?>
                            <span class="text-white font-semibold"><?= $initials ?></span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900"><?= esc($feedback['nama_user']) ?></p>
                            <p class="text-sm text-gray-600"><?= esc($feedback['sobat_id_user']) ?> - <?= esc($feedback['nama_kabupaten']) ?></p>
                        </div>
                    </div>
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
                              maxlength="255"><?= old('feedback', $feedback['feedback']) ?></textarea>
                    <p class="text-sm text-gray-500 mt-1">
                        <span id="charCount">0</span>/255 karakter
                    </p>
                </div>

                <!-- Info -->
                <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-medium mb-1">Informasi:</p>
                            <p>Feedback ini dibuat pada <strong><?= date('d M Y H:i', strtotime($feedback['created_at'])) ?></strong></p>
                            <?php if ($feedback['updated_at'] != $feedback['created_at']): ?>
                                <p>Terakhir diperbarui: <strong><?= date('d M Y H:i', strtotime($feedback['updated_at'])) ?></strong></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                    <a href="<?= base_url('superadmin/feedback') ?>" class="btn-secondary">
                        <i class="fas fa-times mr-2"></i>Batal
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- User Info & Feedback History -->
    <div class="lg:col-span-1">
        <div class="card">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Pengguna</h3>
            
            <div class="space-y-4">
                <!-- Avatar -->
                <div class="flex justify-center">
                    <div class="w-20 h-20 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-2xl font-bold"><?= $initials ?></span>
                    </div>
                </div>

                <!-- User Details -->
                <div class="text-center">
                    <p class="text-lg font-semibold text-gray-900"><?= esc($feedback['nama_user']) ?></p>
                    <p class="text-sm text-gray-600"><?= esc($feedback['nama_kabupaten']) ?></p>
                </div>

                <div class="border-t border-gray-200 pt-4 space-y-3">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-id-card text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500">Sobat ID</p>
                            <p class="text-sm font-medium text-gray-900"><?= esc($feedback['sobat_id_user']) ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-envelope text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500">Email</p>
                            <p class="text-sm font-medium text-gray-900"><?= esc($feedback['email']) ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-phone text-gray-400 mt-1"></i>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500">No. HP</p>
                            <p class="text-sm font-medium text-gray-900"><?= esc($feedback['hp']) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Feedback History -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Riwayat Feedback Lainnya</h4>
                    <div id="feedbackHistory">
                        <p class="text-sm text-gray-500">Memuat...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter
const feedbackTextarea = document.getElementById('feedback');
const charCount = document.getElementById('charCount');

function updateCharCount() {
    charCount.textContent = feedbackTextarea.value.length;
}

feedbackTextarea.addEventListener('input', updateCharCount);

// Initialize char count
updateCharCount();

// Load feedback history
function loadFeedbackHistory() {
    const sobatId = '<?= $feedback['sobat_id'] ?>';
    const currentFeedbackId = '<?= $feedback['id_feedback'] ?>';
    const historyDiv = document.getElementById('feedbackHistory');
    
    fetch(`<?= base_url('superadmin/feedback/get-user-feedback-history') ?>?sobat_id=${sobatId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Filter out current feedback
                const otherFeedbacks = data.data.filter(fb => fb.id_feedback != currentFeedbackId);
                
                if (otherFeedbacks.length === 0) {
                    historyDiv.innerHTML = '<p class="text-sm text-gray-500">Tidak ada feedback lainnya</p>';
                } else {
                    let html = `<p class="text-xs text-gray-500 mb-2">Total feedback lainnya: ${otherFeedbacks.length}</p>`;
                    html += '<div class="space-y-2 max-h-48 overflow-y-auto">';
                    
                    otherFeedbacks.slice(0, 5).forEach(fb => {
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

// Load history on page load
document.addEventListener('DOMContentLoaded', loadFeedbackHistory);
</script>

<?= $this->endSection() ?>