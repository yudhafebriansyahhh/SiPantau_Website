<?= $this->extend('layouts/adminkab_layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="<?= base_url('adminsurvei-kab/assign-petugas') ?>" class="text-gray-600 hover:text-gray-900 mr-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Detail PML</h1>
</div>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <!-- Info PML -->
    <div class="mb-6 pb-6 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">Nama Survei</p>
                <p class="text-base font-semibold text-gray-900">
                    <?= esc($pml['nama_kegiatan_detail']) ?> - <?= esc($pml['nama_kegiatan_detail_proses']) ?>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Nama PML</p>
                <p class="text-base font-semibold text-gray-900"><?= esc($pml['nama_pml']) ?></p>
                <p class="text-xs text-gray-500"><?= esc($pml['email_pml']) ?></p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Target</p>
                <p class="text-base font-semibold text-gray-900"><?= number_format($pml['target']) ?></p>
            </div>
        </div>
    </div>

    <!-- Table PCL -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r">Nama PCL</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r">Target</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (empty($pclList)): ?>
                    <tr>
                        <td colspan="4" class="text-center text-gray-500 py-6">Belum ada PCL yang ditugaskan</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($pclList as $i => $pcl): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3"><?= $i + 1 ?></td>
                        <td class="px-4 py-3">
                            <p class="font-semibold"><?= esc($pcl['nama_pcl']) ?></p>
                            <p class="text-xs text-gray-500"><?= esc($pcl['email_pcl']) ?></p>
                        </td>
                        <td class="px-4 py-3"><?= number_format($pcl['target']) ?></td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="<?= base_url('adminsurvei-kab/data-petugas/detail-pcl/' . $pcl['id_pcl']) ?>"
                                   class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs rounded">
                                   Detail
                                </a>
                                
                                <button onclick="confirmDelete(<?= $pcl['id_pcl'] ?>)" 
                                   class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded">
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

    <!-- Summary -->
    <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-600 mb-1">Total PCL</p>
                <p class="text-2xl font-bold text-blue-700"><?= $summary['total_pcl'] ?></p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm text-green-600 mb-1">Total Target PCL</p>
                <p class="text-2xl font-bold text-green-700"><?= $summary['total_target'] ?></p>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus PCL ini?')) {
        window.location.href = '<?= base_url('adminsurvei-kab/assign-petugas/delete-pcl/') ?>' + id;
    }
}
</script>

<?= $this->endSection() ?>
