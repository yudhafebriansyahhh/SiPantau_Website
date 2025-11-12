<?= $this->extend('layouts/pemantau_layout') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('pemantau') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Master Kegiatan Wilayah</h1>
    <p class="text-gray-600 mt-1">Kelola data target survei untuk setiap kab/kota.</p>
</div>

<div class="card">
    <!-- Search dan Filter Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Search Box -->
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input
                type="text"
                id="searchInput"
                class="input-field w-full pl-10"
                placeholder="Cari kegiatan detail atau satuan..."
                onkeyup="searchTable()">
        </div>

        <!-- Filter Kabupaten -->
        <div class="w-full sm:w-64">
            <label for="kabupatenFilter" class="block text-sm font-medium text-gray-700 mb-1">
                Filter Kabupaten
            </label>
            <form id="filterForm" method="get" class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-filter text-gray-400"></i>
                </div>
                <select
                    name="kabupaten"
                    id="kabupatenFilter"
                    class="input-field w-full pl-10 pr-8 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                    <option value="">-- Semua Kabupaten --</option>
                    <?php foreach ($kabupatenList as $kab) : ?>
                        <option value="<?= $kab['id_kabupaten']; ?>" <?= ($selectedKabupaten == $kab['id_kabupaten']) ? 'selected' : ''; ?>>
                            <?= esc($kab['nama_kabupaten']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="kegiatanDetailTable">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-16">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Nama Kegiatan Detail Proses</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Kab/Kota</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-32">Tanggal Mulai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-32">Tanggal Selesai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-32">Keterangan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-40">Target Wilayah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($kegiatanWilayah)) : ?>
                    <?php foreach ($kegiatanWilayah as $index => $kg) : ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <?= ($pager->getCurrentPage('kegiatanWilayah') - 1) * $pager->getPerPage('kegiatanWilayah') + $index + 1 ?>
                            </td>
                            <td class="px-4 py-4 text-sm"><?= esc($kg['nama_kegiatan_detail_proses']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($kg['nama_kabupaten']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($kg['tanggal_mulai']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($kg['tanggal_selesai']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($kg['keterangan']) ?></td>
                            <td class="px-4 py-4 text-center text-sm font-medium"><?= esc($kg['target_wilayah']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                            Belum ada data kegiatan.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer dengan Pagination -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium"><?= count($kegiatanWilayah) ?></span> dari 
            <span class="font-medium"><?= $pager->getTotal('kegiatanWilayah') ?></span> total data
        </p>

        <!-- ✅ Custom Pagination -->
        <?php if ($pager->getPageCount('kegiatanWilayah') > 1): ?>
            <?= $pager->links('kegiatanWilayah', 'tailwind_pager') ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('kabupatenFilter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    function searchTable() {
        const input = document.getElementById('searchInput').value.toLowerCase();
        const rows = document.querySelectorAll('#kegiatanDetailTable tbody tr');
        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(input) ? '' : 'none';
        });
    }
</script>

<?= $this->endSection() ?>
