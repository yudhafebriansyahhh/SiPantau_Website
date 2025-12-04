<?= $this->extend('layouts/adminkab_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="<?= base_url('adminsurvei-kab') ?>" class="text-gray-600 hover:text-gray-900 mr-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Assign Petugas Survey</h1>
    <p class="text-sm text-gray-600 mt-1">Kelola assignment PML dan PCL untuk <?= esc($admin['nama_kabupaten']) ?></p>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
        <i class="fas fa-check-circle mr-3"></i>
        <span><?= session()->getFlashdata('success') ?></span>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
        <i class="fas fa-exclamation-circle mr-3"></i>
        <span><?= session()->getFlashdata('error') ?></span>
    </div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Total PML -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total PML</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= count($dataPML) ?></h3>
            </div>
            <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-users text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Total PCL -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total PCL</p>
                <h3 class="text-3xl font-bold text-gray-900">
                    <?= array_sum(array_column($dataPML, 'jumlah_pcl')) ?>
                </h3>
            </div>
            <div class="w-14 h-14 bg-green-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-friends text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Total Target PML -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Target PML</p>
                <h3 class="text-3xl font-bold text-gray-900">
                    <?= number_format(array_sum(array_column($dataPML, 'target'))) ?>
                </h3>
            </div>
            <div class="w-14 h-14 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-bullseye text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>

    <!-- Total Target PCL -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Target PCL</p>
                <h3 class="text-3xl font-bold text-gray-900">
                    <?= number_format(array_sum(array_column($dataPML, 'total_target_pcl'))) ?>
                </h3>
            </div>
            <div class="w-14 h-14 bg-orange-50 rounded-lg flex items-center justify-center flex-shrink-0">
                <i class="fas fa-chart-line text-2xl text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">

    <!-- Search, Filter, PerPage and Add Button -->
    <div class="flex flex-wrap gap-3 mb-6 items-end">
        <!-- Search Box -->
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-1">Pencarian</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Cari nama survei atau PML..."
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    onkeyup="searchTable()">
            </div>
        </div>

        <!-- Filter Kegiatan -->
        <div class="flex-1 min-w-[250px]">
            <?= view('components/select_component', [
                'label' => 'Filter Kegiatan',
                'name' => 'filter_kegiatan',
                'id' => 'filterKegiatan',
                'placeholder' => 'Semua Kegiatan',
                'options' => $kegiatanList,
                'optionValue' => 'id_kegiatan_wilayah',
                'optionText' => function ($item) {
                                return $item['nama_kegiatan_detail'] . ' - ' . $item['nama_kegiatan_detail_proses'] . ' (' . date('Y', strtotime($item['tanggal_mulai'])) . ')';
                            },
                'value' => $selectedKegiatan,
                'enableSearch' => true,
                'allowClear' => true
            ]) ?>
        </div>

        <!-- Per Page Selector -->
        <div class="w-32">
            <label for="perPageSelect" class="block text-sm font-medium text-gray-700 mb-1">
                Per Halaman
            </label>
            <div class="relative">
                <select name="perPage" id="perPageSelect"
                    class="w-full pl-3 pr-8 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer text-sm"
                    onchange="updatePerPage()">
                    <option value="5" <?= ($perPage == 5) ? 'selected' : ''; ?>>5</option>
                    <option value="10" <?= ($perPage == 10) ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?= ($perPage == 25) ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?= ($perPage == 50) ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?= ($perPage == 100) ? 'selected' : ''; ?>>100</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none">
                    <i id="perPageChevron"
                        class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300"></i>
                </div>
            </div>
        </div>

        <!-- Add Button -->
        <div class="ml-auto">
            <label class="block text-sm font-medium text-gray-700 mb-1">&nbsp;</label>
            <a href="<?= base_url('adminsurvei-kab/assign-petugas/create') ?>"
                class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i>
                Tambah Petugas Survei
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse" id="assignTable">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Nama
                        Survei</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Nama
                        PML</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Target
                        PML</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border-r border-gray-200">
                        Jumlah PCL</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($dataPML)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>Belum ada data assignment</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($dataPML as $index => $pml): ?>
                        <tr class="hover:bg-gray-50" data-kegiatan="<?= $pml['id_kegiatan_wilayah'] ?>">
                            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">
                                <?= ($pager->getCurrentPage('pml_list') - 1) * $pager->getPerPage('pml_list') + $index + 1 ?>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= esc($pml['nama_kegiatan_detail']) ?></p>
                                    <p class="text-xs text-gray-500"><?= esc($pml['nama_kegiatan_detail_proses']) ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-3 border-r border-gray-200">
                                <div>
                                    <p class="text-sm font-semibold text-gray-900"><?= esc($pml['nama_pml']) ?></p>
                                    <p class="text-xs text-gray-500"><?= esc($pml['email']) ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">
                                <span class="font-semibold"><?= number_format($pml['target']) ?></span>
                            </td>
                            <td class="px-4 py-3 text-center border-r border-gray-200">
                                <span
                                    class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                    <i class="fas fa-users mr-1"></i>
                                    <?= $pml['jumlah_pcl'] ?> PCL
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="<?= base_url('adminsurvei-kab/assign-petugas/detail/' . $pml['id_pml']) ?>"
                                        class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors"
                                        title="Detail">
                                        Detail
                                    </a>
                                    <a href="<?= base_url('adminsurvei-kab/assign-petugas/edit/' . $pml['id_pml']) ?>"
                                        class="px-3 py-1 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium rounded transition-colors"
                                        title="Detail">
                                        Edit
                                    </a>
                                    <button
                                        onclick="confirmDelete(<?= $pml['id_pml'] ?>, '<?= esc($pml['nama_pml'], 'js') ?>', <?= $pml['jumlah_pcl'] ?>)"
                                        class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors"
                                        title="Hapus">
                                        Delete
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
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Menampilkan data
            <span
                class="font-medium"><?= (($pager->getCurrentPage('pml_list') - 1) * $pager->getPerPage('pml_list')) + 1 ?></span>-<span
                class="font-medium"><?= min($pager->getCurrentPage('pml_list') * $pager->getPerPage('pml_list'), $pager->getTotal('pml_list')) ?></span>
            dari <span class="font-medium"><?= $pager->getTotal('pml_list') ?></span> data
            <?php if ($selectedKegiatan): ?>
                <span class="text-blue-600">(terfilter)</span>
            <?php endif; ?>
        </p>

        <!-- Custom Pagination -->
        <?php if ($pager->getPageCount('pml_list') > 1): ?>
            <?= $pager->links('pml_list', 'tailwind_pager') ?>
        <?php endif; ?>
    </div>

    <!-- No Results Message (Hidden by default) -->
    <div id="noResults" class="hidden text-center py-8">
        <i class="fas fa-search text-gray-400 text-4xl mb-3"></i>
        <p class="text-gray-500">Tidak ada data yang ditemukan</p>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Prevent icon animation/flicker on stats cards */
    .fa-users,
    .fa-user-friends,
    .fa-bullseye,
    .fa-chart-line {
        -webkit-backface-visibility: hidden;
        backface-visibility: hidden;
        -webkit-transform: translateZ(0);
        transform: translateZ(0);
    }

    /* Smooth transition for select2 without page jump */
    .select2-container {
        transition: none !important;
    }

    .select2-container--default .select2-selection--single {
        height: 2.80rem !important;
        /* sama dengan h-11 */
    }
</style>

<script>
    // Handle animasi chevron untuk perPage selector
    const perPageSelect = document.getElementById('perPageSelect');
    const perPageChevron = document.getElementById('perPageChevron');

    if (perPageSelect && perPageChevron) {
        perPageSelect.addEventListener('focus', function () {
            perPageChevron.classList.add('rotate-180');
        });

        perPageSelect.addEventListener('blur', function () {
            perPageChevron.classList.remove('rotate-180');
        });
    }

    // Function untuk update perPage dengan mempertahankan filter
    function updatePerPage() {
        const perPage = document.getElementById('perPageSelect').value;
        const params = new URLSearchParams(window.location.search);

        // Set perPage baru
        params.set('perPage', perPage);

        // Redirect dengan parameter yang sudah ada
        window.location.href = '<?= base_url('adminsurvei-kab/assign-petugas') ?>?' + params.toString();
    }

    // Prevent page jump/glitch when changing filter
    let isChangingFilter = false;

    // Handle filter kegiatan change
    document.addEventListener('DOMContentLoaded', function () {
        const filterKegiatan = document.getElementById('filterKegiatan');

        if (filterKegiatan) {
            // Listen to Select2 change event
            // Listen to Select2 change event
            $(filterKegiatan).on('change', function () {
                if (isChangingFilter) return;
                isChangingFilter = true;

                const selectedValue = this.value;
                const params = new URLSearchParams(window.location.search);

                // Update parameter kegiatan
                if (selectedValue) {
                    params.set('kegiatan', selectedValue);
                } else {
                    params.delete('kegiatan');
                }

                // Redirect dengan parameter yang sudah ada termasuk perPage
                window.location.href = '<?= base_url('adminsurvei-kab/assign-petugas') ?>?' + params.toString();
            });
        }
    });

    // Search Function
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('assignTable');
        const tbody = table.getElementsByTagName('tbody')[0];
        const rows = tbody.getElementsByTagName('tr');
        const noResults = document.getElementById('noResults');

        let visibleRows = 0;

        // Loop through all table rows
        for (let i = 0; i < rows.length; i++) {
            // Skip if it's the "no data" row
            if (rows[i].cells.length === 1) continue;

            const surveiCell = rows[i].getElementsByTagName('td')[1]; // Nama Survei column
            const pmlCell = rows[i].getElementsByTagName('td')[2]; // Nama PML column

            if (surveiCell && pmlCell) {
                const surveiText = surveiCell.textContent || surveiCell.innerText;
                const pmlText = pmlCell.textContent || pmlCell.innerText;

                // Check if search term exists in Survei or PML name
                if (surveiText.toLowerCase().indexOf(filter) > -1 ||
                    pmlText.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = '';
                    visibleRows++;
                } else {
                    rows[i].style.display = 'none';
                }
            }
        }

        // Show/hide no results message
        if (visibleRows === 0 && filter !== '') {
            table.style.display = 'none';
            noResults.classList.remove('hidden');
        } else {
            table.style.display = 'table';
            noResults.classList.add('hidden');
        }
    }

    // Delete Confirmation with SweetAlert2
    function confirmDelete(idPML, namaPML, jumlahPCL) {
        Swal.fire({
            title: 'Konfirmasi Hapus',
            html: `
            <div class="text-left">
                <p class="text-gray-700 mb-3">
                    Apakah Anda yakin ingin menghapus assignment PML 
                    <strong class="text-gray-900">${namaPML}</strong>?
                </p>
                <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                    <p class="text-sm text-red-800">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <strong>Perhatian:</strong>
                    </p>
                    <ul class="text-sm text-red-700 mt-2 ml-6 list-disc space-y-1">
                        <li>Semua <strong>${jumlahPCL} PCL</strong> yang terkait akan dihapus</li>
                        <li>Data progress dan laporan akan hilang</li>
                        <li>Tindakan ini tidak dapat dibatalkan</li>
                    </ul>
                </div>
            </div>
        `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fas fa-trash mr-2"></i>Ya, Hapus',
            cancelButtonText: '<i class="fas fa-times mr-2"></i>Batal',
            customClass: {
                popup: 'rounded-lg',
                confirmButton: 'px-4 py-2 rounded-lg',
                cancelButton: 'px-4 py-2 rounded-lg'
            },
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Menghapus...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Create form and submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?= base_url('adminsurvei-kab/assign-petugas/delete/') ?>' + idPML;

                // Add CSRF token
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '<?= csrf_token() ?>';
                csrfInput.value = '<?= csrf_hash() ?>';
                form.appendChild(csrfInput);

                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // Auto-hide flash messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('.bg-green-50, .bg-red-50');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 5000);
        });
    });
</script>

<?= $this->endSection() ?>