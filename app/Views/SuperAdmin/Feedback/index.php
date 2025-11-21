<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Feedback</h1>
            <p class="text-gray-600 mt-1">Berikan feedback kepada pengguna terkait kinerja mereka</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= base_url('superadmin/feedback/create') ?>" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                Buat Feedback Baru
            </a>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-green-500 mr-3"></i>
        <p class="text-green-700"><?= session()->getFlashdata('success') ?></p>
    </div>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
    <div class="flex items-center">
        <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
        <p class="text-red-700"><?= session()->getFlashdata('error') ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <!-- Total Feedback -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Feedback</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['total']) ?></h3>
            </div>
            <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-comments text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Feedback Hari Ini -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Hari Ini</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['hari_ini']) ?></h3>
            </div>
            <div class="w-14 h-14 bg-green-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-day text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Feedback Minggu Ini -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Minggu Ini</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['minggu_ini']) ?></h3>
            </div>
            <div class="w-14 h-14 bg-purple-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-week text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Filters & Table -->
<div class="card">
    <!-- Filters -->
    <div class="mb-6">
        <form method="GET" action="<?= base_url('superadmin/feedback') ?>" class="flex flex-col md:flex-row gap-4">
            <!-- Search -->
            <div class="flex-1">
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="<?= esc($filters['search'] ?? '') ?>"
                           placeholder="Cari nama pengguna atau feedback..." 
                           class="input-field pl-10">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>

            <!-- Filter Kabupaten -->
            <select name="id_kabupaten" class="input-field">
                <option value="">Semua Kabupaten</option>
                <?php foreach ($kabupatens as $kab): ?>
                    <option value="<?= $kab['id_kabupaten'] ?>" 
                            <?= ($filters['id_kabupaten'] ?? '') == $kab['id_kabupaten'] ? 'selected' : '' ?>>
                        <?= esc($kab['nama_kabupaten']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <!-- Buttons -->
            <button type="submit" class="btn-primary">
                <i class="fas fa-filter mr-2"></i>Filter
            </button>
            <a href="<?= base_url('superadmin/feedback') ?>" class="btn-secondary">
                <i class="fas fa-redo mr-2"></i>Reset
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        No
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Penerima
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Feedback
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($feedbacks)): ?>
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">Belum ada feedback</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($feedbacks as $index => $fb): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <?= $index + 1 ?>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <?php
                                        $initials = '';
                                        $nameParts = explode(' ', $fb['nama_user']);
                                        if (count($nameParts) >= 2) {
                                            $initials = strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1));
                                        } else {
                                            $initials = strtoupper(substr($fb['nama_user'], 0, 2));
                                        }
                                        ?>
                                        <span class="text-white text-sm font-semibold"><?= $initials ?></span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate"><?= esc($fb['nama_user']) ?></p>
                                        <p class="text-xs text-gray-500 truncate"><?= esc($fb['nama_kabupaten']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-900 line-clamp-2"><?= esc($fb['feedback']) ?></p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600">
                                <?= date('d M Y H:i', strtotime($fb['created_at'])) ?>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?= base_url('superadmin/feedback/edit/' . $fb['id_feedback']) ?>" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors"
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?= $fb['id_feedback'] ?>)" 
                                            class="text-red-600 hover:text-red-800 transition-colors"
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
</div>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus feedback ini?')) {
        fetch(`<?= base_url('superadmin/feedback/delete/') ?>${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus feedback');
        });
    }
}
</script>

<?= $this->endSection() ?>