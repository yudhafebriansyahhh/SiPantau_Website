<?= $this->extend('layouts/adminkab_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="<?= base_url('adminsurvei-kab/assign-petugas') ?>" class="text-gray-600 hover:text-gray-900 mr-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Detail PML</h1>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    
    <!-- PML Info -->
    <div class="mb-6 pb-6 border-b border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500 mb-1">Nama Survei</p>
                <p class="text-base font-semibold text-gray-900">Survei Angkatan Kerja Nasional 2025</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Nama PML</p>
                <p class="text-base font-semibold text-gray-900">Ahmad Subarjo</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Target</p>
                <p class="text-base font-semibold text-gray-900">150</p>
            </div>
        </div>
    </div>
    
    <!-- PCL Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Nama PCL</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Target</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Progress</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <!-- Sample PCL Data Row 1 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">1</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Siti Aminah</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">50</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 60%"></div>
                            </div>
                            <span class="text-xs font-medium">30/50</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('adminsurvei-kab/assign-petugas/pcl-detail/1') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <button class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </button>
                            <button onclick="confirmDelete(1)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample PCL Data Row 2 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">2</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Muhammad Rizki</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">50</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 80%"></div>
                            </div>
                            <span class="text-xs font-medium">40/50</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </button>
                            <button class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </button>
                            <button onclick="confirmDelete(2)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample PCL Data Row 3 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">3</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Dewi Lestari</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">50</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">
                        <div class="flex items-center">
                            <div class="flex-1 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 40%"></div>
                            </div>
                            <span class="text-xs font-medium">20/50</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <button class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </button>
                            <button class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </button>
                            <button onclick="confirmDelete(3)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Summary Section -->
    <div class="mt-6 pt-6 border-t border-gray-200">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-600 mb-1">Total PCL</p>
                <p class="text-2xl font-bold text-blue-700">3</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm text-green-600 mb-1">Total Progress</p>
                <p class="text-2xl font-bold text-green-700">90 / 150</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-sm text-purple-600 mb-1">Persentase</p>
                <p class="text-2xl font-bold text-purple-700">60%</p>
            </div>
        </div>
    </div>
    
</div>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus PCL ini?')) {
        // Handle delete
        console.log('Delete PCL:', id);
    }
}
</script>

<?= $this->endSection() ?>