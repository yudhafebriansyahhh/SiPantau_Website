<?= $this->extend('layouts/pemantau_kabupaten_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="<?= base_url('pemantau-kabupaten') ?>" class="text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Data Petugas</h1>
    <p class="text-gray-600 mt-1">Data mitra BPS <?= esc($kabupaten['nama_kabupaten'] ?? 'Kabupaten') ?> yang pernah ditugaskan sebagai PML atau PCL.</p>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">DATA MITRA BPS <?= strtoupper(esc($kabupaten['nama_kabupaten'] ?? 'KABUPATEN')) ?></h2>
        
        <!-- Search dan Per Page Section -->
        <div style="display: grid; grid-template-columns: 1fr 200px; gap: 1rem; margin-bottom: 1.5rem;">
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
                <div class="relative">
                    <select
                        name="perPage"
                        id="perPageSelect"
                        class="input-field w-full pr-10 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 appearance-none cursor-pointer"
                        onchange="updateFilters()">
                        <option value="5" <?= ($perPage == 5) ? 'selected' : ''; ?>>5</option>
                        <option value="10" <?= ($perPage == 10) ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?= ($perPage == 25) ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?= ($perPage == 50) ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?= ($perPage == 100) ? 'selected' : ''; ?>>100</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <i id="perPageChevron" class="fas fa-chevron-down text-gray-400 text-sm transition-transform duration-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse" id="petugasTable">
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
                                <span class="text-gray-900 font-medium"><?= esc($petugas['nama_user']) ?></span>
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
                                        <?php foreach ($petugas['kegiatan'] as $kegiatan) : ?>
                                            <span class="inline-block px-2 py-1 bg-yellow-400 text-gray-800 text-xs font-medium rounded">
                                                <?= esc($kegiatan['display']) ?>
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

        <!-- Custom Pagination -->
        <?php if ($pager->getPageCount('dataPetugas') > 1): ?>
            <?= $pager->links('dataPetugas', 'tailwind_pager') ?>
        <?php endif; ?>
    </div>
</div>

<script>
// Handle animasi chevron untuk perPage selector
const perPageSelect = document.getElementById('perPageSelect');
const perPageChevron = document.getElementById('perPageChevron');

perPageSelect.addEventListener('focus', function() {
    perPageChevron.classList.add('rotate-180');
});

perPageSelect.addEventListener('blur', function() {
    perPageChevron.classList.remove('rotate-180');
});

// Function untuk update filters dengan mempertahankan parameter yang ada
function updateFilters() {
    const perPage = document.getElementById('perPageSelect').value;
    const search = document.getElementById('searchInput').value;
    
    // Build URL dengan parameter
    const params = new URLSearchParams();
    if (perPage) params.append('perPage', perPage);
    if (search) params.append('search', search);
    
    // Redirect ke URL dengan parameter baru
    window.location.href = '<?= base_url('pemantau-kabupaten/data-petugas') ?>?' + params.toString();
}

// Handle search dengan debounce
let searchTimeout;
function handleSearch(event) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(function() {
        updateFilters();
    }, 500); // Tunggu 500ms setelah user berhenti mengetik
}

// Handle Enter key pada search
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        clearTimeout(searchTimeout);
        updateFilters();
    }
});
</script>

<?= $this->endSection() ?>