<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('admin') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Master Output</h1>
    <p class="text-gray-600 mt-1">Kelola data master output kegiatan survei/sensus</p>
</div>

<!-- Main Card -->
<div class="card">
    <!-- Search and Add Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Search Box -->
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" id="searchInput" 
                   class="input-field w-full pl-10" 
                   placeholder="Cari nama, fungsi, atau alias..."
                   onkeyup="searchTable()">
        </div>
        
        <!-- Add Button -->
        <a href="<?= base_url('master-output/create') ?>" 
           class="btn-primary whitespace-nowrap w-full sm:w-auto text-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Output
        </a>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="masterOutputTable">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">
                        No
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nama
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Fungsi
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Alias
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <!-- Static Data untuk Demo -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">1</td>
                    <td class="px-4 py-4">
                        <p class="text-sm font-medium text-gray-900">SUNSENAS 2025</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Survei Sosial Ekonomi Nasional</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">SUSENAS</p>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-output/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(1, 'SUNSENAS 2025')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">2</td>
                    <td class="px-4 py-4">
                        <p class="text-sm font-medium text-gray-900">Sensus Pertanian 2025</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Pendataan Usaha Pertanian</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">ST2025</p>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-output/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(2, 'Sensus Pertanian 2025')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">3</td>
                    <td class="px-4 py-4">
                        <p class="text-sm font-medium text-gray-900">Survei Angkatan Kerja Nasional</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Pendataan Ketenagakerjaan</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">SAKERNAS</p>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('master-output/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(3, 'Survei Angkatan Kerja Nasional')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium">3</span> data
        </p>
        
        <div class="flex items-center space-x-2">
            <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg">1</button>
            <button class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">2</button>
            <button class="px-4 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">3</button>
            <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Search functionality
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('masterOutputTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        for (let j = 1; j < cells.length - 1; j++) {
            const cell = cells[j];
            if (cell) {
                const textValue = cell.textContent || cell.innerText;
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        row.style.display = found ? '' : 'none';
    }
}

// Delete confirmation dengan SweetAlert2
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Data Output?',
        html: `Apakah Anda yakin ingin menghapus data <strong>"${name}"</strong>?<br><span class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan.</span>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash mr-2"></i>Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-6 py-2.5 rounded-lg font-medium'
        },
        buttonsStyling: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Simulasi proses delete (karena static)
            deleteData(id, name);
        }
    });
}

// Fungsi untuk proses delete (simulasi)
function deleteData(id, name) {
    // Tampilkan loading
    Swal.fire({
        title: 'Menghapus...',
        html: 'Mohon tunggu sebentar',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        willOpen: () => {
            Swal.showLoading();
        }
    });

    // Simulasi API call dengan setTimeout
    setTimeout(() => {
        // Sukses delete
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Dihapus!',
            text: `Data "${name}" telah dihapus.`,
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        }).then(() => {
            // Redirect atau refresh halaman
            // window.location.reload();
            
            // Untuk demo, hapus row dari tabel
            const row = event.target.closest('tr');
            if (row) {
                row.remove();
            }
        });

        // Jika error, gunakan ini:
        // Swal.fire({
        //     icon: 'error',
        //     title: 'Gagal Menghapus',
        //     text: 'Terjadi kesalahan saat menghapus data.',
        //     confirmButtonColor: '#3b82f6'
        // });
    }, 1000);
}
</script>

<?= $this->endSection() ?>