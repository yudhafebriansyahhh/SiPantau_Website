<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('admin') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Admin Survei Provinsi</h1>
    <p class="text-gray-600 mt-1">Kelola assignment admin survei untuk setiap kegiatan di tingkat provinsi</p>
</div>

<!-- Main Card -->
<div class="card">
    <!-- Search and Assign Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Search Box -->
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" id="searchInput" 
                   class="input-field w-full pl-10" 
                   placeholder="Cari nama atau sobat ID..."
                   onkeyup="searchTable()">
        </div>
        
        <!-- Assign Button -->
        <a href="<?= base_url('kelola-admin-surveyprov/assign') ?>" 
           class="btn-primary whitespace-nowrap w-full sm:w-auto text-center">
            <i class="fas fa-user-plus mr-2"></i>
            Assign Admin Survei Provinsi
        </a>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="adminSurveiTable">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">
                        No
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nama
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Sobat ID
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <!-- Row 1 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">1</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">AS</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Ahmad Suryana</p>
                                <p class="text-xs text-gray-500">ahmad.suryana@bps.go.id</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                            340057021
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('kelola-admin-surveyprov/edit/1') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit Assignment">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(1, 'Ahmad Suryana')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">2</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-pink-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">RA</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Rina Anggraini</p>
                                <p class="text-xs text-gray-500">rina.anggraini@bps.go.id</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                            340057022
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('kelola-admin-surveyprov/edit/2') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit Assignment">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(2, 'Rina Anggraini')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">3</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-indigo-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">DW</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Dedi Wijaya</p>
                                <p class="text-xs text-gray-500">dedi.wijaya@bps.go.id</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                            340057023
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('kelola-admin-surveyprov/edit/3') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit Assignment">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(3, 'Dedi Wijaya')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 4 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">4</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-teal-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">LH</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Lina Handayani</p>
                                <p class="text-xs text-gray-500">lina.handayani@bps.go.id</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                            340057024
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('kelola-admin-surveyprov/edit/4') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit Assignment">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(4, 'Lina Handayani')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 5 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">5</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-orange-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">FN</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Fadli Nugroho</p>
                                <p class="text-xs text-gray-500">fadli.nugroho@bps.go.id</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-medium">
                            340057025
                        </span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('kelola-admin-surveyprov/edit/5') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit Assignment">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(5, 'Fadli Nugroho')"
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
            Menampilkan <span class="font-medium" id="showingCount">5</span> dari <span class="font-medium">5</span> data
        </p>
        
        <div class="flex items-center space-x-2">
            <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg">1</button>
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
    const table = document.getElementById('adminSurveiTable');
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;
    
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
        
        if (found) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }
    
    document.getElementById('showingCount').textContent = visibleCount;
}

// Show more activities
function showMoreActivities(id) {
    const activities = {
        1: ['Sensus Penduduk Daerah Terpencil', 'Pendataan Usaha Mikro Kecil Menengah'],
        3: ['Survey Indeks Harga Konsumen'],
        4: ['Survey Konsumsi Rumah Tangga']
    };
    
    if (activities[id]) {
        const list = activities[id].map(item => `<li class="text-left">• ${item}</li>`).join('');
        
        Swal.fire({
            title: 'Kegiatan Lainnya',
            html: `<ul class="text-sm text-gray-700 space-y-1">${list}</ul>`,
            icon: 'info',
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        });
    }
}

// Delete confirmation dengan SweetAlert2
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Assignment?',
        html: `Apakah Anda yakin ingin menghapus assignment untuk <strong>"${name}"</strong>?<br><span class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan.</span>`,
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
            deleteData(id, name);
        }
    });
}

// Fungsi untuk proses delete (simulasi)
function deleteData(id, name) {
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

    setTimeout(() => {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil Dihapus!',
            text: `Assignment "${name}" telah dihapus.`,
            confirmButtonColor: '#3b82f6',
            customClass: {
                popup: 'rounded-xl',
                confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
            }
        }).then(() => {
            const row = event.target.closest('tr');
            if (row) {
                row.remove();
                // Update showing count
                const table = document.getElementById('adminSurveiTable');
                const visibleRows = Array.from(table.getElementsByTagName('tr')).filter(r => r.style.display !== 'none').length - 1;
                document.getElementById('showingCount').textContent = visibleRows;
            }
        });
    }, 1000);
}
</script>

<?= $this->endSection() ?>