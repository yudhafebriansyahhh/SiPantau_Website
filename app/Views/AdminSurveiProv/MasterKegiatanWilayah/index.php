<?= $this->extend('layouts/adminprov_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Master Kegiatan Wilayah</h1>
    <p class="text-gray-600 mt-1">Kelola data target survei untuk setiap kab/kota</p>
</div>

<!-- Main Card -->
<div class="card">
    <!-- Filter Section with Better Layout -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
        <div class="flex items-center gap-2 mb-3">
            <i class="fas fa-filter text-gray-600"></i>
            <h3 class="text-sm font-semibold text-gray-700">Filter Data</h3>
        </div>
        
        <form method="GET" action="<?= base_url('adminsurvei/master-kegiatan-wilayah') ?>" id="filterForm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Filter Kegiatan Detail -->
                <div>
                    <?= view('components/select_component', [
                        'label' => 'Filter Kegiatan',
                        'name' => 'kegiatan_detail',
                        'id' => 'filterKegiatan',
                        'required' => false,
                        'placeholder' => 'Semua Kegiatan',
                        'options' => $allKegiatanDetail,
                        'optionValue' => 'id_kegiatan_detail',
                        'optionText' => function($kd) {
                            return esc($kd['nama_kegiatan']) . ' - ' . esc($kd['nama_kegiatan_detail']);
                        },
                        'selectedValue' => $filterKegiatan,
                        'onchange' => 'applyFilter()',
                        'enableSearch' => true
                    ]) ?>
                </div>

                <!-- Filter Kabupaten -->
                <div>
                    <?= view('components/select_component', [
                        'label' => 'Filter Kabupaten',
                        'name' => 'kabupaten',
                        'id' => 'filterKabupaten',
                        'required' => false,
                        'placeholder' => 'Semua Kabupaten',
                        'options' => $allKabupaten,
                        'optionValue' => 'id_kabupaten',
                        'optionText' => 'nama_kabupaten',
                        'selectedValue' => $filterKabupaten,
                        'onchange' => 'applyFilter()',
                        'enableSearch' => true
                    ]) ?>
                </div>
            </div>

            <!-- Reset Filter Button -->
            <?php if (!empty($filterKegiatan) || !empty($filterKabupaten)): ?>
            <div class="mt-3 pt-3 border-t border-gray-200">
                <a href="<?= base_url('adminsurvei/master-kegiatan-wilayah') ?>" 
                   class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 hover:underline">
                    <i class="fas fa-times-circle mr-1.5"></i>
                    Reset Semua Filter
                </a>
            </div>
            <?php endif; ?>
        </form>
    </div>

    <!-- Search and Add Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Search Box -->
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" id="searchInput"
                class="input-field w-full pl-10"
                placeholder="Cari kegiatan detail atau satuan..."
                onkeyup="searchTable()">
        </div>

        <!-- Add Button -->
        <a href="<?= base_url('adminsurvei/master-kegiatan-wilayah/create') ?>"
            class="btn-primary whitespace-nowrap w-full sm:w-auto text-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Kegiatan
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="kegiatanDetailTable">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kegiatan Detail Proses</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kab/Kota</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Tanggal Mulai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Tanggal Selesai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Keterangan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-28">Target</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Progress</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($kegiatanWilayah)) : ?>
                    <?php foreach ($kegiatanWilayah as $index => $kg) : ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-3 text-sm text-gray-900"><?= $index + 1 ?></td>
                            <td class="px-4 py-3 text-sm text-gray-900"><?= esc($kg['nama_kegiatan_detail_proses']) ?></td>
                            <td class="px-4 py-3 text-sm text-gray-600"><?= esc($kg['nama_kabupaten']) ?></td>
                            <td class="px-4 py-3 text-xs text-gray-600"><?= esc($kg['tanggal_mulai']) ?></td>
                            <td class="px-4 py-3 text-xs text-gray-600"><?= esc($kg['tanggal_selesai']) ?></td>
                            <td class="px-4 py-3 text-xs text-gray-600"><?= esc($kg['keterangan']) ?></td>
                            <td class="px-4 py-3 text-center">
                                <div class="text-sm font-semibold text-gray-900"><?= number_format($kg['target_wilayah']) ?></div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    <span class="text-gray-400">Realisasi:</span> <?= number_format($kg['realisasi']) ?>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-col items-center">
                                    <!-- Circle Progress Chart -->
                                    <div class="relative inline-flex items-center justify-center">
                                        <svg class="transform -rotate-90" width="50" height="50">
                                            <!-- Background circle -->
                                            <circle cx="25" cy="25" r="20" stroke="#e5e7eb" stroke-width="4" fill="none"/>
                                            <!-- Progress circle -->
                                            <circle cx="25" cy="25" r="20" 
                                                    stroke="<?= $kg['progress_color'] ?>" 
                                                    stroke-width="4" 
                                                    fill="none"
                                                    stroke-dasharray="<?= (2 * 3.14159 * 20) ?>"
                                                    stroke-dashoffset="<?= (2 * 3.14159 * 20) * (1 - ($kg['progress'] / 100)) ?>"
                                                    stroke-linecap="round"/>
                                        </svg>
                                        <!-- Percentage text in center -->
                                        <span class="absolute text-xs font-semibold" 
                                              style="color: <?= $kg['progress_color'] ?>">
                                            <?= number_format($kg['progress'], 1) ?>%
                                        </span>
                                    </div>
                                    <!-- Progress label -->
                                    <div class="mt-1 text-xs text-center">
                                        <?php if ($kg['progress'] >= 80): ?>
                                            <span class="text-green-600 font-medium">Sangat Baik</span>
                                        <?php elseif ($kg['progress'] >= 50): ?>
                                            <span class="text-blue-600 font-medium">Baik</span>
                                        <?php elseif ($kg['progress'] >= 25): ?>
                                            <span class="text-orange-600 font-medium">Sedang</span>
                                        <?php else: ?>
                                            <span class="text-red-600 font-medium">Rendah</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="<?= base_url('adminsurvei/master-kegiatan-wilayah/edit/' . $kg['id_kegiatan_wilayah']) ?>"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg inline-block"
                                        title="Edit">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?= $kg['id_kegiatan_wilayah'] ?>, '<?= esc($kg['keterangan']) ?>')"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg"
                                        title="Hapus">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9" class="px-4 py-8 text-center text-gray-500">
                            <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                            <p>Belum ada data kegiatan detail proses.</p>
                            <?php if (!empty($filterKegiatan) || !empty($filterKabupaten)): ?>
                                <a href="<?= base_url('adminsurvei/master-kegiatan-wilayah') ?>" 
                                   class="text-blue-600 hover:text-blue-700 text-sm mt-2 inline-block">
                                    Reset filter untuk melihat semua data
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Function to apply filter - Preserve selected values after page reload
    function applyFilter() {
        const form = document.getElementById('filterForm');
        if (form) {
            form.submit();
        }
    }

    // Restore select2 values after page load
    document.addEventListener('DOMContentLoaded', function() {
        // Get current URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const kegiatanDetail = urlParams.get('kegiatan_detail');
        const kabupaten = urlParams.get('kabupaten');

        // Set values if they exist in URL
        if (kegiatanDetail) {
            const selectKegiatan = document.getElementById('filterKegiatan');
            if (selectKegiatan) {
                selectKegiatan.value = kegiatanDetail;
                // Trigger Select2 update if using Select2
                if (window.jQuery && jQuery(selectKegiatan).hasClass('select2-hidden-accessible')) {
                    jQuery(selectKegiatan).trigger('change.select2');
                }
            }
        }

        if (kabupaten) {
            const selectKabupaten = document.getElementById('filterKabupaten');
            if (selectKabupaten) {
                selectKabupaten.value = kabupaten;
                // Trigger Select2 update if using Select2
                if (window.jQuery && jQuery(selectKabupaten).hasClass('select2-hidden-accessible')) {
                    jQuery(selectKabupaten).trigger('change.select2');
                }
            }
        }
    });

    // 🔍 Fitur pencarian tabel
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('kegiatanDetailTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            for (let j = 1; j < cells.length - 1; j++) {
                if (cells[j] && cells[j].innerText.toLowerCase().includes(filter)) {
                    found = true;
                    break;
                }
            }
            row.style.display = found ? '' : 'none';
        }
    }

    // 🗑️ Konfirmasi hapus
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Data?',
            html: `Yakin ingin menghapus <strong>${name}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `<?= base_url('adminsurvei/master-kegiatan-wilayah/delete/') ?>${id}`;
                const hiddenMethod = document.createElement('input');
                hiddenMethod.type = 'hidden';
                hiddenMethod.name = '_method';
                hiddenMethod.value = 'DELETE';
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '<?= csrf_token() ?>';
                csrf.value = '<?= csrf_hash() ?>';
                form.appendChild(hiddenMethod);
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    // ✅ Alert sukses dari session flashdata
    <?php if (session()->getFlashdata('success')) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "<?= session()->getFlashdata('success') ?>",
            confirmButtonColor: '#3b82f6'
        });
    <?php endif; ?>

    // ⚠️ Alert error dari session flashdata
    <?php if (session()->getFlashdata('error')) : ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "<?= session()->getFlashdata('error') ?>",
            confirmButtonColor: '#ef4444'
        });
    <?php endif; ?>
</script>

<?= $this->endSection() ?>