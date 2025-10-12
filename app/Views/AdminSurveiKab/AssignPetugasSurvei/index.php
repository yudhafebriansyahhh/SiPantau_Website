<?= $this->extend('layouts/adminkab_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="<?= base_url('adminsurvei') ?>" class="text-gray-600 hover:text-gray-900 mr-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Assign Petugas Survey</h1>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    
    <!-- Search and Add Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Search Box -->
        <div class="w-full sm:w-96">
            <div class="relative">
                <input type="text" id="searchInput" 
                    placeholder="Cari nama survei atau PML..." 
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    onkeyup="searchTable()">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
        
        <!-- Add Button -->
        <a href="<?= base_url('assign-petugas/create') ?>" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors whitespace-nowrap">
            <i class="fas fa-plus mr-2"></i>
            Tambah Petugas Survei
        </a>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse" id="assignTable">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Nama Survei</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Nama PML</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Target</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <!-- Sample Data Row 1 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">1</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Angkatan Kerja Nasional 2025</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Ahmad Subarjo</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">150</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/1') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/1') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(1)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 2 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">2</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Sosial Ekonomi Nasional</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Budi Santoso</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">200</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/2') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/2') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(2)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 3 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">3</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Pertanian Terintegrasi</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Siti Nurhaliza</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">180</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/3') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/3') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(3)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 4 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">4</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Industri Besar Sedang</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Eko Prasetyo</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">120</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/4') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/4') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(4)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 5 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">5</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Konstruksi</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Dewi Lestari</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">95</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/5') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/5') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(5)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 6 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">6</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Perdagangan</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Rini Setiawati</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">160</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/6') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/6') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(6)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 7 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">7</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Ketenagakerjaan</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Agus Widodo</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">175</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/7') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/7') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(7)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 8 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">8</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Harga Konsumen</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Linda Permatasari</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">140</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/8') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/8') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(8)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 9 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">9</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Perikanan</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Hendra Wijaya</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">110</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/9') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/9') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(9)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 10 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">10</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Pariwisata</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Maya Kusuma</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">130</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/10') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/10') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(10)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 11 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">11</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Transportasi</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Bambang Suryanto</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">145</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/11') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/11') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(11)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Sample Data Row 12 -->
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">12</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Survei Kesehatan</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">Fitri Handayani</td>
                    <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">165</td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('assign-petugas/detail/12') ?>" class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                                Detail
                            </a>
                            <a href="<?= base_url('assign-petugas/edit/12') ?>" class="px-3 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition-colors">
                                Edit
                            </a>
                            <button onclick="confirmDelete(12)" class="px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
                
                <!-- Empty State (Uncomment if no data) -->
                <!-- <tr>
                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>Belum ada data assignment</p>
                    </td>
                </tr> -->
            </tbody>
        </table>
    </div>
    
    <!-- No Results Message (Hidden by default) -->
    <div id="noResults" class="hidden text-center py-8">
        <i class="fas fa-search text-gray-400 text-4xl mb-3"></i>
        <p class="text-gray-500">Tidak ada data yang ditemukan</p>
    </div>
</div>

<script>
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

// Delete Confirmation
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        window.location.href = '<?= base_url('assign-petugas/delete/') ?>' + id;
    }
}
</script>

<?= $this->endSection() ?>