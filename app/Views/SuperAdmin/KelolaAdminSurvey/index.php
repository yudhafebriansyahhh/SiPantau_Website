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

    input:checked+.toggle-slider {
        background-color: #3b82f6;
    }

    input:checked+.toggle-slider:before {
        transform: translateX(26px);
    }
</style>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('superadmin') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Admin Survei Provinsi</h1>
    <p class="text-gray-600 mt-1">Kelola assignment admin survei untuk setiap kegiatan di tingkat provinsi</p>
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

<!-- Main Card -->
<div class="card">
    <!-- Search, PerPage, dan Assign Button -->
    <div style="display: grid; grid-template-columns: 1fr 200px 200px; gap: 1rem; margin-bottom: 1.5rem;">
        <!-- Search Box -->
        <div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" class="input-field w-full pl-10"
                    placeholder="Cari nama admin, email, atau HP..." onkeyup="handleSearch()"
                    value="<?= esc($search) ?>">
            </div>
        </div>

        <!-- Per Page Selector -->
        <div>
            <div class="relative">
                <select name="perPage" id="perPageSelect"
                    class="input-field w-full pr-10 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer"
                    onchange="updatePerPage()">
                    <option value="5" <?= ($perPage == 5) ? 'selected' : ''; ?>>5</option>
                    <option value="10" <?= ($perPage == 10) ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?= ($perPage == 25) ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?= ($perPage == 50) ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?= ($perPage == 100) ? 'selected' : ''; ?>>100</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i id="perPageChevron"
                        class="fas fa-chevron-down text-gray-400 text-sm transition-transform duration-300"></i>
                </div>
            </div>
        </div>

        <!-- Assign Button -->
        <div>
            <a href="<?= base_url('superadmin/kelola-admin-surveyprov/assign') ?>"
                class="btn-primary whitespace-nowrap w-full text-center inline-block">
                <i class="fas fa-user-plus mr-2"></i>
                Assign Admin
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="adminSurveiTable">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200 w-16">
                        No</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        Nama Admin & Email</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        Kab/Kota</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        No HP</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        Role</th>
                    <th
                        class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        Kegiatan yang Di-assign</th>
                    <th
                        class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200 w-32">
                        Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($admin_list)): ?>
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2 text-gray-300"></i>
                            <p>Belum ada admin survei provinsi</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($admin_list as $index => $admin): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 border-r border-gray-200 text-sm text-gray-900">
                                <?= ($pager->getCurrentPage('admin') - 1) * $pager->getPerPage('admin') + $index + 1 ?>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200">
                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-white text-sm font-medium">
                                            <?= strtoupper(substr($admin['nama_user'], 0, 2)) ?>
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900"><?= esc($admin['nama_user']) ?></p>
                                        <p class="text-xs text-gray-500"><?= esc($admin['email']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200">
                                <p class="text-sm text-gray-600"><?= esc($admin['nama_kabupaten'] ?? '-') ?></p>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200">
                                <p class="text-sm text-gray-600"><?= esc($admin['hp']) ?></p>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200">
                                <div class="flex flex-wrap gap-1">
                                    <?php if (!empty($admin['role_names'])): ?>
                                        <?php
                                        $badgeColors = ['bg-green-100 text-green-700', 'bg-blue-100 text-blue-700', 'bg-yellow-100 text-yellow-700', 'bg-purple-100 text-purple-700'];
                                        foreach ($admin['role_names'] as $idx => $roleName):
                                            $colorClass = $badgeColors[$idx % count($badgeColors)];
                                            ?>
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $colorClass ?>">
                                                <?= esc($roleName) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-400">-</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200">
                                <?php if (empty($admin['kegiatan'])): ?>
                                    <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs">
                                        <i class="fas fa-clipboard-list mr-1"></i>
                                        Belum ada kegiatan
                                    </span>
                                <?php else: ?>
                                    <div class="space-y-1">
                                        <?php
                                        $maxShow = 2;
                                        $colors = ['blue', 'green', 'purple', 'pink', 'indigo'];
                                        $shown = array_slice($admin['kegiatan'], 0, $maxShow);
                                        $remaining = count($admin['kegiatan']) - $maxShow;

                                        foreach ($shown as $idx => $keg):
                                            $color = $colors[$idx % count($colors)];
                                            ?>
                                            <div class="flex items-center gap-2 text-xs">
                                                <span class="w-2 h-2 bg-<?= $color ?>-500 rounded-full"></span>
                                                <span class="text-gray-700"><?= esc($keg['nama_kegiatan_detail']) ?></span>
                                            </div>
                                        <?php endforeach; ?>

                                        <?php if ($remaining > 0): ?>
                                            <div class="text-xs text-blue-600 font-medium">
                                                <i class="fas fa-plus-circle mr-1"></i>
                                                <?= $remaining ?> kegiatan lainnya
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="<?= base_url('superadmin/kelola-admin-surveyprov/detail/' . $admin['id_admin_provinsi']) ?>"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                                        title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= base_url('superadmin/kelola-admin-surveyprov/assign/' . $admin['id_admin_provinsi']) ?>"
                                        class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors duration-200"
                                        title="Edit Assignment">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button
                                        onclick="confirmDelete(<?= $admin['id_admin_provinsi'] ?>, '<?= esc($admin['nama_user']) ?>')"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                        title="Hapus Admin">
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

    <!-- Footer dengan Pagination -->
    <?php if (!empty($admin_list)): ?>
        <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-sm text-gray-600">
                Menampilkan data
                <span
                    class="font-medium"><?= (($pager->getCurrentPage('admin') - 1) * $pager->getPerPage('admin')) + 1 ?></span>-<span
                    class="font-medium"><?= min($pager->getCurrentPage('admin') * $pager->getPerPage('admin'), $pager->getTotal('admin')) ?></span>
                dari <span class="font-medium"><?= $pager->getTotal('admin') ?></span> data
            </p>

            <!-- Custom Pagination -->
            <?php if ($pager->getPageCount('admin') > 1): ?>
                <?= $pager->links('admin', 'tailwind_pager') ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Handle animasi chevron untuk perPage selector
    const perPageSelect = document.getElementById('perPageSelect');
    const perPageChevron = document.getElementById('perPageChevron');

    perPageSelect.addEventListener('focus', function () {
        perPageChevron.classList.add('rotate-180');
    });

    perPageSelect.addEventListener('blur', function () {
        perPageChevron.classList.remove('rotate-180');
    });

    // Function untuk update perPage
    function updatePerPage() {
        const perPage = document.getElementById('perPageSelect').value;
        const search = document.getElementById('searchInput').value;

        const params = new URLSearchParams();
        if (perPage) params.append('perPage', perPage);
        if (search) params.append('search', search);

        window.location.href = '<?= base_url('superadmin/kelola-admin-surveyprov') ?>?' + params.toString();
    }

    // UBAH function handleSearch (bukan searchTable lagi)
    function handleSearch() {
        const searchValue = document.getElementById('searchInput').value;
        const perPage = document.getElementById('perPageSelect').value;

        const params = new URLSearchParams();
        if (searchValue) params.append('search', searchValue);
        if (perPage) params.append('perPage', perPage);

        // Debounce search
        clearTimeout(window.searchTimeout);
        window.searchTimeout = setTimeout(() => {
            window.location.href = '<?= base_url('superadmin/kelola-admin-surveyprov') ?>?' + params.toString();
        }, 500);
    }

    // Delete confirmation
    function confirmDelete(idAdminProvinsi, namaAdmin) {
        Swal.fire({
            title: 'Hapus Admin?',
            html: `Apakah Anda yakin ingin menghapus <strong>"${namaAdmin}"</strong> dari daftar admin survei provinsi?<br><br><small class="text-red-600">Semua assignment kegiatan untuk admin ini akan dihapus.</small>`,
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
            }
        }).then((result) => {
            if (result.isConfirmed) {
                deleteAdmin(idAdminProvinsi);
            }
        });
    }

    // Process delete
    function deleteAdmin(idAdminProvinsi) {
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

        fetch(`<?= base_url('superadmin/kelola-admin-surveyprov/delete') ?>/${idAdminProvinsi}`, {
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
                        title: 'Gagal!',
                        text: data.message,
                        confirmButtonColor: '#3b82f6'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus data',
                    confirmButtonColor: '#3b82f6'
                });
            });
    }
</script>

<?= $this->endSection() ?>