<?= $this->extend('layouts/pemantau_provinsi_layout') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('pemantau') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Master Kegiatan Detail Proses</h1>
    <p class="text-gray-600 mt-1">
        Lihat data detail kegiatan survei.
    </p>
</div>

<!-- Main Card -->
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

        <!-- Filter Kegiatan Detail -->
        <div class="w-full sm:w-64">
            <label for="kegiatanDetailFilter" class="block text-sm font-medium text-gray-700 mb-1">
                Filter Kegiatan Detail
            </label>
            <form id="filterForm" method="get" class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-filter text-gray-400"></i>
                </div>

                <select
                    name="kegiatan_detail"
                    id="kegiatanDetailFilter"
                    class="input-field w-fulborder-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none">
                    <option value="" class="text-center">Semua Kegiatan Detail</option>
                    <?php foreach ($kegiatanDetailList as $kd) : ?>
                        <option value="<?= $kd['id_kegiatan_detail']; ?>"
                            <?= ($selectedKegiatanDetail == $kd['id_kegiatan_detail']) ? 'selected' : ''; ?>>
                            <?= esc($kd['nama_kegiatan_detail']); ?>
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
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Master Kegiatan Detail</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kegiatan Detail Proses</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Satuan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Tanggal Mulai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Tanggal Selesai</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Keterangan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Periode</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Target</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($kegiatanDetails)) : ?>
                    <?php foreach ($kegiatanDetails as $index => $detail) : ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <?= ($pager->getCurrentPage() - 1) * $pager->getPerPage() + $index + 1 ?>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-900"><?= esc($detail['nama_kegiatan_detail']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($detail['nama_kegiatan_detail_proses']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($detail['satuan']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($detail['tanggal_mulai']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($detail['tanggal_selesai']) ?></td>
                            <td class="px-4 py-4 text-sm text-center text-gray-600"><?= esc($detail['keterangan']) ?></td>
                            <td class="px-4 py-4 text-center"><span class="badge badge-info"><?= esc($detail['periode']) ?></span></td>
                            <td class="px-4 py-4 text-center text-gray-900 font-medium"><?= esc($detail['target']) ?></td>  
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="9" class="px-4 py-6 text-center text-gray-500">Belum ada data kegiatan detail proses.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Footer dengan Pagination -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium"><?= count($kegiatanDetails) ?></span> dari 
            <span class="font-medium"><?= $pager->getTotal() ?></span> total data
        </p>

        <!-- ✅ Custom Pagination -->
        <?php if ($pager->getPageCount() > 1): ?>
            <?= $pager->links('default', 'tailwind_pager') ?>
        <?php endif; ?>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 🔄 Auto submit filter kegiatan detail
    document.getElementById('kegiatanDetailFilter').addEventListener('change', function() {
        document.getElementById('filterForm').submit();
    });

    // 🔍 Fitur pencarian tabel
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const rows = document.querySelectorAll('#kegiatanDetailTable tbody tr');

        rows.forEach(row => {
            const text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }

    // ✅ Alert sukses
    <?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= session()->getFlashdata('success') ?>',
        showConfirmButton: false,
        timer: 2000
    });
    <?php endif; ?>
</script>

<?= $this->endSection() ?>
