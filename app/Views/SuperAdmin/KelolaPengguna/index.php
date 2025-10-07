<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('admin') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Pengguna</h1>
    <p class="text-gray-600 mt-1">Kelola data pengguna sistem SiPantau</p>
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
                   placeholder="Cari nama, email, atau role..."
                   onkeyup="searchTable()">
        </div>
        
        <!-- Filter and Add Button -->
        <div class="flex gap-2 w-full sm:w-auto">
            <select id="roleFilter" class="input-field" onchange="filterByRole()">
                <option value="">Semua Role</option>
                <option value="Admin Provinsi">Admin Provinsi</option>
                <option value="Admin Kabupaten/Kota">Admin Kabupaten/Kota</option>
                <option value="Operator">Operator</option>
            </select>
            
            <a href="<?= base_url('kelola-pengguna/create') ?>" 
               class="btn-primary whitespace-nowrap text-center">
                <i class="fas fa-plus mr-2"></i>
                Tambah Pengguna
            </a>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="penggunaTable">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">
                        No
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Nama
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Email
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Kab/Kota
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        No HP
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">
                        Role
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <!-- Row 1 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150" data-role="Admin Provinsi">
                    <td class="px-4 py-4 text-sm text-gray-900">1</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">SA</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Super Admin</p>
                                <p class="text-xs text-gray-500">superadmin@bps.go.id</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-900">superadmin@bps.go.id</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Provinsi Riau</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">081234567890</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-success">Admin Provinsi</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('kelola-pengguna/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(1, 'Super Admin')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 2 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150" data-role="Admin Kabupaten/Kota">
                    <td class="px-4 py-4 text-sm text-gray-900">2</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">BP</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Budi Prasetyo</p>
                                <p class="text-xs text-gray-500">budi.prasetyo@bps.go.id</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-900">budi.prasetyo@bps.go.id</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Kab. Kampar</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">082345678901</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-info">Admin Kabupaten/Kota</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('kelola-pengguna/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(2, 'Budi Prasetyo')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 3 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150" data-role="Admin Kabupaten/Kota">
                    <td class="px-4 py-4 text-sm text-gray-900">3</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">SW</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Siti Wahyuni</p>
                                <p class="text-xs text-gray-500">siti.wahyuni@bps.go.id</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-900">siti.wahyuni@bps.go.id</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Kota Pekanbaru</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">083456789012</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-info">Admin Kabupaten/Kota</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('kelola-pengguna/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(3, 'Siti Wahyuni')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 4 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150" data-role="Operator">
                    <td class="px-4 py-4 text-sm text-gray-900">4</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">AR</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Ahmad Rizki</p>
                                <p class="text-xs text-gray-500">ahmad.rizki@bps.go.id</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-900">ahmad.rizki@bps.go.id</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Kab. Bengkalis</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">084567890123</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-warning">Operator</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('kelola-pengguna/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(4, 'Ahmad Rizki')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors duration-200"
                                    title="Hapus">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Row 5 -->
                <tr class="hover:bg-gray-50 transition-colors duration-150" data-role="Operator">
                    <td class="px-4 py-4 text-sm text-gray-900">5</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">DM</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Dewi Maharani</p>
                                <p class="text-xs text-gray-500">dewi.maharani@bps.go.id</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-900">dewi.maharani@bps.go.id</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">Kab. Indragiri Hilir</p>
                    </td>
                    <td class="px-4 py-4">
                        <p class="text-sm text-gray-600">085678901234</p>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-warning">Operator</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-center space-x-2">
                            <a href="<?= base_url('kelola-pengguna/edit') ?>" 
                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors duration-200"
                               title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button onclick="confirmDelete(5, 'Dewi Maharani')"
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
    const table = document.getElementById('penggunaTable');
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

// Filter by role
function filterByRole() {
    const select = document.getElementById('roleFilter');
    const filter = select.value;
    const table = document.getElementById('penggunaTable');
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const role = row.getAttribute('data-role');
        
        if (filter === '' || role === filter) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }
    
    document.getElementById('showingCount').textContent = visibleCount;
}

// Delete confirmation dengan SweetAlert2
function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Pengguna?',
        html: `Apakah Anda yakin ingin menghapus pengguna <strong>"${name}"</strong>?<br><span class="text-sm text-gray-600">Tindakan ini tidak dapat dibatalkan.</span>`,
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
            text: `Pengguna "${name}" telah dihapus.`,
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
                const table = document.getElementById('penggunaTable');
                const visibleRows = Array.from(table.getElementsByTagName('tr')).filter(r => r.style.display !== 'none').length - 1;
                document.getElementById('showingCount').textContent = visibleRows;
            }
        });
    }, 1000);
}
</script>

<?= $this->endSection() ?>