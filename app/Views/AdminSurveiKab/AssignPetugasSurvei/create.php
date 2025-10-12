<?= $this->extend('layouts/adminkab_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="<?= base_url('assign-admin-kab') ?>" class="text-gray-600 hover:text-gray-900 mr-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Assign Petugas Survey</h1>
</div>

<!-- Main Form Card -->
<div class="max-w-4xl">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
        
        <form action="<?= base_url('assign-admin-kab/store') ?>" method="POST" id="assignForm">
            
            <!-- Pilih Kegiatan Survei -->
            <div class="mb-6">
                <label for="kegiatan_survei" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih Kegiatan Survei
                </label>
                <select id="kegiatan_survei" name="kegiatan_survei" required 
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    <option value="">-- Pilih Survei --</option>
                    <option value="1">Survei Angkatan Kerja Nasional 2025</option>
                    <option value="2">Survei Sosial Ekonomi Nasional</option>
                    <option value="3">Survei Pertanian</option>
                    <option value="4">Survei Industri Besar Sedang</option>
                </select>
            </div>
            
            <!-- Pilih PML -->
            <div class="mb-6">
                <label for="pml" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih PML
                </label>
                <input type="text" id="pml" name="pml" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Masukkan nama PML">
            </div>
            
            <!-- Tambah PCL Button -->
            <div class="mb-6">
                <button type="button" onclick="addPCL()" 
                    class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>
                    Tambah PCL
                </button>
            </div>
            
            <!-- PCL List Container -->
            <div id="pclContainer" class="space-y-4 mb-6">
                <!-- PCL items will be added here dynamically -->
            </div>
            
            <!-- Submit Button -->
            <div class="pt-6 border-t border-gray-200">
                <button type="submit" 
                    class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    Simpan
                </button>
            </div>
            
        </form>
        
    </div>
</div>

<script>
let pclCount = 0;

function addPCL() {
    pclCount++;
    const container = document.getElementById('pclContainer');
    
    const pclItem = document.createElement('div');
    pclItem.className = 'bg-gray-100 rounded-lg p-6 relative';
    pclItem.id = `pcl-${pclCount}`;
    
    pclItem.innerHTML = `
        <button type="button" onclick="removePCL(${pclCount})" 
            class="absolute top-4 right-4 w-8 h-8 flex items-center justify-center text-red-600 hover:bg-red-100 rounded-full transition-colors">
            <i class="fas fa-times"></i>
        </button>
        
        <div class="pr-10">
            <h3 class="text-sm font-semibold text-gray-700 mb-4">PCL</h3>
            
            <div class="mb-4">
                <label for="pcl_nama_${pclCount}" class="block text-sm font-medium text-gray-700 mb-2">
                    Pilih PCL
                </label>
                <input type="text" id="pcl_nama_${pclCount}" name="pcl[${pclCount}][nama]" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Masukkan nama PCL">
            </div>
            
            <div>
                <label for="pcl_target_${pclCount}" class="block text-sm font-medium text-gray-700 mb-2">
                    Target
                </label>
                <input type="number" id="pcl_target_${pclCount}" name="pcl[${pclCount}][target]" required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                    placeholder="Masukkan target">
            </div>
        </div>
    `;
    
    container.appendChild(pclItem);
}

function removePCL(id) {
    const element = document.getElementById(`pcl-${id}`);
    if (element) {
        element.remove();
    }
}

// Add first PCL by default
document.addEventListener('DOMContentLoaded', function() {
    addPCL();
});
</script>

<?= $this->endSection() ?>