<?= $this->extend('layouts/adminkab_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Data Petugas</h1>
    <p class="text-gray-600 mt-1">Data mitra BPS <?= esc($kabupaten['nama_kabupaten']) ?> yang pernah ditugaskan sebagai PML atau PCL.</p>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">DATA MITRA BPS <?= strtoupper(esc($kabupaten['nama_kabupaten'])) ?></h2>
        
        <!-- Filter dan Search Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <!-- Filter Kegiatan Proses -->
            <div>
                <label for="kegiatanProsesFilter" class="block text-sm font-medium text-gray-700 mb-1">
                    Filter Kegiatan
                </label>
                <select
                    id="kegiatanProsesFilter"
                    class="input-field w-full"
                    onchange="updateFilters()">
                    <option value="">Semua Kegiatan</option>
                    <?php foreach ($kegiatanProsesList as $kegiatan) : ?>
                        <option value="<?= $kegiatan['id_kegiatan_detail_proses'] ?>" 
                            <?= ($selectedKegiatanProses == $kegiatan['id_kegiatan_detail_proses']) ? 'selected' : '' ?>>
                            <?= esc($kegiatan['nama_kegiatan_detail']) ?> - <?= esc($kegiatan['nama_kegiatan_detail_proses']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Search Box -->
            <div>
                <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-1">
                    Pencarian
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input
                        type="text"
                        id="searchInput"
                        class="input-field w-full pl-10"
                        placeholder="Cari nama atau sobat ID..."
                        value="<?= esc($search ?? '') ?>"
                        onkeyup="handleSearch(event)">
                </div>
            </div>

            <!-- Per Page Selector -->
            <div>
                <label for="perPageSelect" class="block text-sm font-medium text-gray-700 mb-1">
                    Data per Halaman
                </label>
                <select
                    id="perPageSelect"
                    class="input-field w-full"
                    onchange="updateFilters()">
                    <option value="5" <?= ($perPage == 5) ? 'selected' : ''; ?>>5</option>
                    <option value="10" <?= ($perPage == 10) ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?= ($perPage == 25) ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?= ($perPage == 50) ? 'selected' : ''; ?>>50</option>
                    <option value="100" <?= ($perPage == 100) ? 'selected' : ''; ?>>100</option>
                </select>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap w-16">
                        No
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        Nama Mitra
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        Sobat ID
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        Role
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border border-gray-300">
                        Kegiatan yang Diikuti
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($dataPetugas)) : ?>
                    <?php foreach ($dataPetugas as $index => $petugas) : ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-center text-sm text-gray-700 border border-gray-300">
                                <?= ($pager->getCurrentPage('dataPetugas') - 1) * $pager->getPerPage('dataPetugas') + $index + 1 ?>
                            </td>
                            <td class="px-4 py-3 text-sm border border-gray-300">
                                <a href="<?= base_url('adminsurvei-kab/data-petugas/detail/' . $petugas['sobat_id']) ?>" 
                                   class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                                    <?= esc($petugas['nama_user']) ?>
                                </a>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 border border-gray-300">
                                <?= esc($petugas['sobat_id']) ?>
                            </td>
                            <td class="px-4 py-3 text-center border border-gray-300">
                                <div class="flex justify-center gap-1 flex-wrap">
                                    <?php foreach ($petugas['roles'] as $role) : ?>
                                        <?php 
                                        $bgColor = $role === 'PML' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800';
                                        ?>
                                        <span class="inline-flex px-3 py-1 <?= $bgColor ?> text-xs font-semibold rounded-full">
                                            <?= $role ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center border border-gray-300">
                                <?php 
                                $statusClass = $petugas['is_active'] == 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                                $statusText = $petugas['is_active'] == 1 ? 'Aktif' : 'Tidak Aktif';
                                ?>
                                <span class="inline-flex px-3 py-1 <?= $statusClass ?> text-xs font-semibold rounded-full">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm border border-gray-300">
                                <?php if (!empty($petugas['kegiatan'])) : ?>
                                    <div class="flex flex-wrap gap-1">
                                        <?php 
                                        $displayCount = min(3, count($petugas['kegiatan']));
                                        $remainingCount = count($petugas['kegiatan']) - $displayCount;
                                        ?>
                                        <?php for ($i = 0; $i < $displayCount; $i++) : ?>
                                            <span class="inline-block px-2 py-1 bg-yellow-400 text-gray-800 text-xs font-medium rounded">
                                                <?= esc($petugas['kegiatan'][$i]['display']) ?>
                                            </span>
                                        <?php endfor; ?>
                                        <?php if ($remainingCount > 0) : ?>
                                            <button 
                                                onclick="showMoreKegiatan('<?= $petugas['sobat_id'] ?>')"
                                                class="inline-flex items-center px-2 py-1 bg-gray-200 hover:bg-gray-300 text-gray-700 text-xs font-medium rounded transition-colors">
                                                <i class="fas fa-plus mr-1"></i> <?= $remainingCount ?> lainnya
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Hidden kegiatan (untuk modal) -->
                                    <div id="allKegiatan-<?= $petugas['sobat_id'] ?>" class="hidden">
                                        <?php foreach ($petugas['kegiatan'] as $kg) : ?>
                                            <span class="inline-block px-2 py-1 bg-yellow-400 text-gray-800 text-xs font-medium rounded mb-1 mr-1">
                                                <?= esc($kg['display']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else : ?>
                                    <span class="text-gray-400 text-xs">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-gray-500 border border-gray-300">
                            Belum ada data petugas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Footer dengan Pagination -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium"><?= count($dataPetugas) ?></span> dari 
            <span class="font-medium"><?= $totalData ?></span> total data
        </p>

        <!-- Pagination -->
        <?php if ($pager->getPageCount('dataPetugas') > 1): ?>
            <?= $pager->links('dataPetugas', 'tailwind_pager') ?>
        <?php endif; ?>
    </div>
</div>

<!-- Modal untuk menampilkan semua kegiatan -->
<div id="kegiatanModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full p-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Semua Kegiatan</h3>
            <button onclick="closeKegiatanModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="kegiatanModalContent" class="space-y-2">
            <!-- Content will be inserted here -->
        </div>
        <div class="mt-6 flex justify-end">
            <button onclick="closeKegiatanModal()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
// Function untuk update filters
function updateFilters() {
    const kegiatanProses = document.getElementById('kegiatanProsesFilter').value;
    const perPage = document.getElementById('perPageSelect').value;
    const search = document.getElementById('searchInput').value;
    
    const params = new URLSearchParams();
    if (kegiatanProses) params.append('kegiatan_proses', kegiatanProses);
    if (perPage) params.append('perPage', perPage);
    if (search) params.append('search', search);
    
    window.location.href = '<?= base_url('adminsurvei-kab/data-petugas') ?>?' + params.toString();
}

// Handle search dengan debounce
let searchTimeout;
function handleSearch(event) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        updateFilters();
    }, 500);
}

// Handle Enter key pada search
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        clearTimeout(searchTimeout);
        updateFilters();
    }
});

// Show more kegiatan modal
function showMoreKegiatan(sobatId) {
    const allKegiatanDiv = document.getElementById('allKegiatan-' + sobatId);
    const modalContent = document.getElementById('kegiatanModalContent');
    const modal = document.getElementById('kegiatanModal');
    
    modalContent.innerHTML = allKegiatanDiv.innerHTML;
    modal.classList.remove('hidden');
}

// Close modal
function closeKegiatanModal() {
    document.getElementById('kegiatanModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('kegiatanModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeKegiatanModal();
    }
});
</script>

<?= $this->endSection() ?>