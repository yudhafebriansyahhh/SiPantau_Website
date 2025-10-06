<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600 mt-1">Selamat datang di SiPantau - Sistem Pelaporan Kegiatan Lapangan</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Total Pengguna -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Pengguna</p>
                <h3 class="text-3xl font-bold text-gray-900">200</h3>
                <p class="text-sm text-green-600 mt-2">
                    <i class="fas fa-arrow-up"></i> 12% dari bulan lalu
                </p>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Kegiatan -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Kegiatan</p>
                <h3 class="text-3xl font-bold text-gray-900">200</h3>
                <p class="text-sm text-blue-600 mt-2">
                    <i class="fas fa-arrow-up"></i> 8% dari bulan lalu
                </p>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clipboard-list text-2xl text-green-600"></i>
            </div>
        </div>
    </div>
    
    <!-- Kegiatan Aktif -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Kegiatan Aktif</p>
                <h3 class="text-3xl font-bold text-gray-900">45</h3>
                <p class="text-sm text-yellow-600 mt-2">
                    <i class="fas fa-minus"></i> Stabil
                </p>
            </div>
            <div class="w-14 h-14 bg-yellow-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-2xl text-yellow-600"></i>
            </div>
        </div>
    </div>
    
    <!-- Target Tercapai -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Target Tercapai</p>
                <h3 class="text-3xl font-bold text-gray-900">78%</h3>
                <p class="text-sm text-green-600 mt-2">
                    <i class="fas fa-arrow-up"></i> 5% dari target
                </p>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-bullseye text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Kegiatan Chart -->
    <div class="lg:col-span-2 card">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Kegiatan</h3>
                <p class="text-sm text-gray-600">Aktual vs Target</p>
            </div>
            <select class="input-field w-auto text-sm">
                <option>Today</option>
                <option>This Week</option>
                <option>This Month</option>
            </select>
        </div>
        <canvas id="kegiatanChart" class="w-full" style="max-height: 300px;"></canvas>
    </div>
    
    <!-- Progress Kegiatan -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Progres Kegiatan Sedang Berjalan</h3>
        <div class="space-y-4">
            <?php for($i = 1; $i <= 6; $i++): ?>
            <div class="pb-4 border-b border-gray-100 last:border-0 last:pb-0">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">SUNSENAS 2025</span>
                    <span class="text-sm font-semibold text-blue-600">20%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 20%"></div>
                </div>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Data Petugas</h3>
            <p class="text-sm text-gray-600 mt-1">Monitoring progres petugas lapangan</p>
        </div>
        <div class="flex flex-wrap gap-2 mt-4 sm:mt-0">
            <select class="input-field w-auto text-sm">
                <option>Month</option>
                <option>January</option>
                <option>February</option>
            </select>
            <select class="input-field w-auto text-sm">
                <option>Kegiatan</option>
                <option>SUNSENAS 2025</option>
            </select>
            <select class="input-field w-auto text-sm">
                <option>Kabupaten/Kota</option>
                <option>Pekanbaru</option>
            </select>
            <select class="input-field w-auto text-sm">
                <option>All</option>
                <option>Active</option>
            </select>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-user mr-2"></i>Petugas
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-list mr-2"></i>Status
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-tasks mr-2"></i>Progress
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <!-- Avatar - Hidden on mobile with md:flex -->
                            <div class="w-10 h-10 bg-blue-600 rounded-full items-center justify-center mr-3 hidden md:flex mobile-hide-avatar">
                                <span class="text-white text-sm font-medium">GM</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">George R.R Martin</p>
                                <p class="text-xs text-gray-500">PCL - Pekanbaru</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-warning">Belum Lapor</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="flex-1 mr-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 50%"></div>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">50</span>
                        </div>
                    </td>
                </tr>
                
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <!-- Avatar - Hidden on mobile with md:flex -->
                            <div class="w-10 h-10 bg-green-600 rounded-full items-center justify-center mr-3 hidden md:flex mobile-hide-avatar">
                                <span class="text-white text-sm font-medium">MS</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Markus Suzak</p>
                                <p class="text-xs text-gray-500">PCL - Kampar</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-success">Sudah Lapor</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="flex-1 mr-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 75%"></div>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">75</span>
                        </div>
                    </td>
                </tr>
                
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <!-- Avatar - Hidden on mobile with md:flex -->
                            <div class="w-10 h-10 bg-purple-600 rounded-full items-center justify-center mr-3 hidden md:flex mobile-hide-avatar">
                                <span class="text-white text-sm font-medium">AW</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Ankur Warikoo</p>
                                <p class="text-xs text-gray-500">PCL - Dumai</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-4">
                        <span class="badge badge-success">Sudah Lapor</span>
                    </td>
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="flex-1 mr-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-600 h-2 rounded-full" style="width: 100%"></div>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900">100</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
// Chart Configuration
const ctx = document.getElementById('kegiatanChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['Kegiatan 1', 'Kegiatan 2', 'Kegiatan 3', 'Kegiatan 4', 'Kegiatan 5'],
        datasets: [
            {
                label: 'Aktual',
                data: [4, 10, 5, 8, 6],
                backgroundColor: '#3b82f6',
                borderRadius: 6,
            },
            {
                label: 'Target',
                data: [8, 12, 9, 11, 10],
                backgroundColor: '#1f2937',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    display: true,
                    color: '#f3f4f6'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});
</script>

<?= $this->endSection() ?>