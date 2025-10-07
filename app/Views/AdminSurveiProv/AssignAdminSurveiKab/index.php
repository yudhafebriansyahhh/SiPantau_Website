<?= $this->extend('layouts/adminprov_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Admin Survei Kab</h1>
    <p class="text-gray-600 mt-1">Kelola data Admin survei setiap kab/kota</p>
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
                   placeholder="Cari nama admin atau kab/kota..."
                   onkeyup="searchTable()">
        </div>
        
        <!-- Add Button -->
        <a href="<?= base_url('assign-admin-kab/create') ?>" 
           class="btn-primary whitespace-nowrap w-full sm:w-auto text-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Admin
        </a>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="adminSurveiTable">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kabupaten/Kota</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <!-- Data Dummy -->
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">1</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Ahmad Ridwan</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Pekanbaru</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('admin-survei-kab/edit/1') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(1, 'Ahmad Ridwan')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">2</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Siti Nurhaliza</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Kampar</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('admin-survei-kab/edit/2') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(2, 'Siti Nurhaliza')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">3</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Budi Santoso</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Bengkalis</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('admin-survei-kab/edit/3') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(3, 'Budi Santoso')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">4</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Dewi Lestari</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Dumai</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('admin-survei-kab/edit/4') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(4, 'Dewi Lestari')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">5</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Eko Prasetyo</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Siak</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('admin-survei-kab/edit/5') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(5, 'Eko Prasetyo')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">6</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Fitri Handayani</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Indragiri Hulu</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('admin-survei-kab/edit/6') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(6, 'Fitri Handayani')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">7</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Guntur Wijaya</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Indragiri Hilir</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('admin-survei-kab/edit/7') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(7, 'Guntur Wijaya')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">8</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Hendra Kusuma</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Pelalawan</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('admin-survei-kab/edit/8') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(8, 'Hendra Kusuma')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">9</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Indah Permata</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Rokan Hulu</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('admin-survei-kab/edit/9') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(9, 'Indah Permata')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4 text-sm text-gray-900">10</td>
                    <td class="px-4 py-4 text-sm text-gray-900">Joko Widodo</td>
                    <td class="px-4 py-4 text-sm text-gray-600">Rokan Hilir</td>
                    <td class="px-4 py-4 text-center">
                        <a href="<?= base_url('admin-survei-kab/edit/10') ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button onclick="confirmDelete(10, 'Joko Widodo')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium">10</span> data admin survei kabupaten/kota.
        </p>
        <div class="flex items-center space-x-2">
            <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg" disabled>
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="px-4 py-2 text-sm bg-blue-600 text-white rounded-lg">1</button>
            <button class="px-3 py-2 text-sm border border-gray-300 rounded-lg">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('adminSurveiTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        for (let j = 1; j < cells.length - 1; j++) {
            if (cells[j] && cells[j].innerText.toLowerCase().includes(filter)) {
                found = true;
                break;
            }
        }
        row.style.display = found ? '' : 'none';
    }
}

function confirmDelete(id, name) {
    Swal.fire({
        title: 'Hapus Data Admin?',
        html: `Yakin ingin menghapus admin <strong>${name}</strong>?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true,
        customClass: {
            popup: 'rounded-xl',
            confirmButton: 'px-6 py-2.5 rounded-lg font-medium',
            cancelButton: 'px-6 py-2.5 rounded-lg font-medium'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Simulasi proses hapus
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: `Admin "${name}" telah dihapus.`,
                confirmButtonColor: '#3b82f6',
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'px-6 py-2.5 rounded-lg font-medium'
                }
            }).then(() => {
                // Reload halaman atau update tabel
                // window.location.reload();
            });
        }
    });
}
</script>

<?= $this->endSection() ?>