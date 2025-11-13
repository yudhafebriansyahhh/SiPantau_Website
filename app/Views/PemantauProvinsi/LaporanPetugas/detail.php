<?= $this->extend('layouts/pemantau_provinsi_layout') ?>

<?= $this->section('content') ?>

<!-- Back Button & Title -->
<div class="mb-6">
    <a href="<?= base_url('pemantau-provinsi/laporan-petugas') ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
        <i class="fas fa-arrow-left mr-2"></i>
        <span>Kembali</span>
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Detail Progress PCL</h1>
</div>

<!-- Info Cards -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div>
            <p class="text-sm text-gray-500 mb-1">Nama PCL</p>
            <p class="text-base font-bold text-gray-900"><?= esc($pcl['nama_pcl']) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">PML</p>
            <p class="text-base font-semibold text-gray-900"><?= esc($pcl['nama_pml']) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">Nama Survei</p>
            <p class="text-base font-semibold text-gray-900"><?= esc($pcl['nama_kegiatan_detail_proses']) ?></p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">Wilayah</p>
            <p class="text-base font-semibold text-gray-900"><?= esc($pcl['nama_kabupaten']) ?></p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <!-- Target Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-2">Target</p>
                <p class="text-4xl font-bold text-gray-900"><?= number_format($target) ?></p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-bullseye text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Aktual Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-2">Aktual</p>
                <p class="text-4xl font-bold text-gray-900"><?= number_format($realisasi) ?></p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Pencapaian Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-2">Pencapaian</p>
                <p class="text-4xl font-bold <?= $persentase >= 100 ? 'text-green-600' : 'text-red-600' ?>">
                    <?= number_format($persentase, 1) ?>%
                </p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Selisih Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-2">Selisih</p>
                <p class="text-4xl font-bold <?= $selisih <= 0 ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $selisih > 0 ? '-' : '+' ?><?= number_format(abs($selisih)) ?>
                </p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-balance-scale text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-900 mb-1">Kurva S - Target vs Aktual PCL</h2>
            <p class="text-sm text-gray-600">Progress Kumulatif Harian</p>
        </div>
        <div class="text-sm text-gray-600">
            <i class="far fa-calendar-alt mr-2"></i>
            <?= date('d M Y', strtotime($pcl['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($pcl['tanggal_selesai'])) ?>
        </div>
    </div>
    
    <div class="relative">
        <div id="kurvaChart"></div>
    </div>
</div>

<!-- Tabs Section -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Tab Headers -->
    <div class="border-b border-gray-200">
        <nav class="flex -mb-px">
            <button onclick="switchTab('pantau')" id="tabPantau" class="tab-button active px-6 py-4 text-sm font-medium border-b-2 border-blue-600 text-blue-600">
                <i class="fas fa-chart-line mr-2"></i>Pantau Progress
            </button>
            <button onclick="switchTab('transaksi')" id="tabTransaksi" class="tab-button px-6 py-4 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                <i class="fas fa-clipboard-list mr-2"></i>Laporan Transaksi
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="p-6">
        <!-- Pantau Progress Tab -->
        <div id="contentPantau" class="tab-content">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Pantau Progress</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-semibold border-r border-gray-200 text-gray-700">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold border-r border-gray-200 text-gray-700">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold border-r border-gray-200 text-gray-700">Waktu</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold border-r border-gray-200 text-gray-700">Realisasi</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold border-r border-gray-200 text-gray-700">Realisasi Kumulatif</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold border-r border-gray-200 text-gray-700">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="pantauTableBody">
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                                <p class="text-gray-600">Memuat data...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Pantau -->
            <div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4" id="pantauPagination"></div>
        </div>

        <!-- Laporan Transaksi Tab -->
        <div id="contentTransaksi" class="tab-content hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Data Laporan Transaksi</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 text-left text-xs font-semibold border border-gray-200 text-gray-700">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold border border-gray-200 text-gray-700">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold border border-gray-200 text-gray-700">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold border border-gray-200 text-gray-700">Kecamatan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold border border-gray-200 text-gray-700">Desa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold border border-gray-200 text-gray-700">Lokasi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold border border-gray-200 text-gray-700">Foto</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold border border-gray-200 text-gray-700">Resume</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="transaksiTableBody">
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                                <p class="text-gray-600">Memuat data...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination Transaksi -->
            <div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4" id="transaksiPagination"></div>
        </div>
    </div>
</div>

<!-- ApexCharts CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>

<script>
const idPCL = <?= $idPCL ?>;
const baseUrl = '<?= base_url('pemantau-provinsi/detail-petugas') ?>';

// Kurva Data dari PHP
const kurvaData = <?= json_encode($kurvaData) ?>;

// Initialize Chart
let chartInstance = null;

function renderChart() {
    const isMobile = window.innerWidth < 640;
    
    const options = {
        series: [
            {
                name: 'Target (Kurva S)',
                data: kurvaData.target,
                type: 'area'
            },
            {
                name: 'Realisasi (Kumulatif)',
                data: kurvaData.realisasi,
                type: 'area'
            }
        ],
        chart: {
            height: isMobile ? 300 : 380,
            type: 'area',
            fontFamily: 'Poppins, sans-serif',
            toolbar: {
                show: !isMobile,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            },
            animations: {
                enabled: true,
                speed: 800
            }
        },
        colors: ['#1e88e5', '#e53935'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            width: isMobile ? [2, 2] : [3, 3],
            curve: 'smooth',
            dashArray: [0, 5]
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'vertical',
                shadeIntensity: 0.3,
                gradientToColors: ['#bbdefb', '#ffcdd2'],
                inverseColors: false,
                opacityFrom: 0.5,
                opacityTo: 0.1,
                stops: [0, 100]
            }
        },
        markers: {
            size: 0,
            hover: {
                size: isMobile ? 5 : 7
            }
        },
        xaxis: {
            categories: kurvaData.labels,
            title: {
                text: 'Tanggal',
                style: {
                    fontSize: isMobile ? '11px' : '12px',
                    fontWeight: 600
                }
            },
            labels: {
                rotate: isMobile ? -45 : 0,
                style: {
                    fontSize: isMobile ? '10px' : '11px'
                }
            }
        },
        yaxis: {
            title: {
                text: isMobile ? '' : 'Jumlah',
                style: {
                    fontSize: '12px',
                    fontWeight: 600
                }
            },
            labels: {
                style: {
                    fontSize: isMobile ? '9px' : '11px'
                },
                formatter: function(value) {
                    return Math.round(value).toLocaleString('id-ID');
                }
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(value) {
                    return value ? value.toLocaleString('id-ID') : '0';
                }
            }
        },
        legend: {
            position: isMobile ? 'bottom' : 'top',
            horizontalAlign: isMobile ? 'center' : 'left',
            fontSize: isMobile ? '11px' : '13px'
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 3
        },
        annotations: {
            xaxis: !isMobile ? [
                {
                    x: kurvaData.config.tanggal_mulai,
                    borderColor: '#43a047',
                    label: {
                        text: 'Mulai',
                        style: {
                            color: '#fff',
                            background: '#43a047',
                            fontSize: '10px'
                        }
                    }
                },
                {
                    x: kurvaData.config.tanggal_selesai,
                    borderColor: '#e53935',
                    label: {
                        text: 'Selesai',
                        style: {
                            color: '#fff',
                            background: '#e53935',
                            fontSize: '10px'
                        }
                    }
                }
            ] : []
        }
    };
    
    if (chartInstance) {
        chartInstance.destroy();
    }
    
    chartInstance = new ApexCharts(document.querySelector("#kurvaChart"), options);
    chartInstance.render();
}

// Switch Tab - OPTIMIZED with caching
let tabDataCache = {
    pantau: null,
    transaksi: null
};

function switchTab(tab) {
    // Update button styles
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.classList.remove('active', 'border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show active tab
    if (tab === 'pantau') {
        document.getElementById('tabPantau').classList.add('active', 'border-blue-600', 'text-blue-600');
        document.getElementById('contentPantau').classList.remove('hidden');
        document.getElementById('contentTransaksi').classList.add('hidden');
        
        // Load data only if not cached
        if (!tabDataCache.pantau) {
            loadPantauProgress(1);
        }
    } else {
        document.getElementById('tabTransaksi').classList.add('active', 'border-blue-600', 'text-blue-600');
        document.getElementById('contentTransaksi').classList.remove('hidden');
        document.getElementById('contentPantau').classList.add('hidden');
        
        // Load data only if not cached
        if (!tabDataCache.transaksi) {
            loadLaporanTransaksi(1);
        }
    }
}

// Load Pantau Progress - OPTIMIZED
async function loadPantauProgress(page = 1) {
    const tbody = document.getElementById('pantauTableBody');
    
    // Show loading
    tbody.innerHTML = `
        <tr>
            <td colspan="6" class="px-4 py-12 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Memuat data...</p>
            </td>
        </tr>
    `;
    
    try {
        const response = await fetch(`${baseUrl}/get-pantau-progress?id_pcl=${idPCL}&page=${page}`);
        const result = await response.json();
        
        if (result.success) {
            renderPantauTable(result.data, result.pagination);
            // Cache the data
            if (page === 1) {
                tabDataCache.pantau = true;
            }
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="px-4 py-12 text-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-2"></i>
                        <p class="text-gray-600">Gagal memuat data</p>
                    </td>
                </tr>
            `;
        }
    } catch (e) {
        console.error('Error loading pantau progress:', e);
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-12 text-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-2"></i>
                    <p class="text-gray-600">Terjadi kesalahan saat memuat data</p>
                </td>
            </tr>
        `;
    }
}

// Render Pantau Table
function renderPantauTable(data, pagination) {
    const tbody = document.getElementById('pantauTableBody');
    
    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="px-4 py-12 text-center border">
                    <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                    <p class="text-gray-500">Belum ada data</p>
                </td>
            </tr>
        `;
        return;
    }
    
    const startNo = (pagination.currentPage - 1) * pagination.perPage + 1;
    
    tbody.innerHTML = data.map((item, index) => `
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700">${startNo + index}</td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700">${formatDate(item.created_at)}</td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700">${formatTime(item.created_at)}</td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-center font-semibold text-gray-900">${item.jumlah_realisasi_absolut || 0}</td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-center font-semibold text-blue-600">${item.jumlah_realisasi_kumulatif || 0}</td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700">${item.catatan_aktivitas || '-'}</td>
        </tr>
    `).join('');
    
    renderPagination('pantau', pagination);
}

// Load Laporan Transaksi - OPTIMIZED
async function loadLaporanTransaksi(page = 1) {
    const tbody = document.getElementById('transaksiTableBody');
    
    // Show loading
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="px-4 py-12 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Memuat data...</p>
            </td>
        </tr>
    `;
    
    try {
        const response = await fetch(`${baseUrl}/get-laporan-transaksi?id_pcl=${idPCL}&page=${page}`);
        const result = await response.json();
        
        if (result.success) {
            renderTransaksiTable(result.data, result.pagination);
            // Cache the data
            if (page === 1) {
                tabDataCache.transaksi = true;
            }
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center">
                        <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-2"></i>
                        <p class="text-gray-600">${result.message || 'Gagal memuat data'}</p>
                    </td>
                </tr>
            `;
        }
    } catch (e) {
        console.error('Error loading laporan transaksi:', e);
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-12 text-center">
                    <i class="fas fa-exclamation-triangle text-red-500 text-4xl mb-2"></i>
                    <p class="text-gray-600">Terjadi kesalahan saat memuat data</p>
                </td>
            </tr>
        `;
    }
}

// Render Transaksi Table - OPTIMIZED with Lazy Loading Images
function renderTransaksiTable(data, pagination) {
    const tbody = document.getElementById('transaksiTableBody');
    
    if (data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="px-4 py-12 text-center border">
                    <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                    <p class="text-gray-500">Belum ada data</p>
                </td>
            </tr>
        `;
        return;
    }
    
    const startNo = (pagination.currentPage - 1) * pagination.perPage + 1;
    
    tbody.innerHTML = data.map((item, index) => `
        <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700 ">${startNo + index}</td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700">${formatDate(item.created_at)}</td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700">${formatTime(item.created_at)}</td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700">${item.nama_kecamatan || '-'}</td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700">${item.nama_desa || '-'}</td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700">
                ${item.latitude && item.longitude ? 
                    `<a href="https://www.google.com/maps?q=${item.latitude},${item.longitude}" target="_blank" class="text-blue-600 hover:underline inline-flex items-center">
                        <i class="fas fa-map-marker-alt mr-1"></i>Lihat
                    </a>` : '-'}
            </td>
            <td class="px-4 py-3 border border-gray-200">
                ${item.imagepath ? 
                    `<img src="<?= base_url() ?>${item.imagepath}" 
                         alt="Foto" 
                         loading="lazy"
                         class="w-16 h-16 object-cover rounded border border-gray-200 cursor-pointer hover:opacity-80 transition-opacity" 
                         onclick="showImage('<?= base_url() ?>${item.imagepath}')"
                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2264%22 height=%2264%22%3E%3Crect fill=%22%23ddd%22 width=%2264%22 height=%2264%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 font-size=%2212%22 text-anchor=%22middle%22 dy=%22.3em%22 fill=%22%23999%22%3ENo Image%3C/text%3E%3C/svg%3E'">` 
                    : '<span class="text-gray-400 text-xs">Tidak ada foto</span>'}
            </td>
            <td class="px-4 py-3 border border-gray-200 text-sm text-gray-700">
                <div class="max-w-xs truncate" title="${item.resume || '-'}">${item.resume || '-'}</div>
            </td>
        </tr>
    `).join('');
    
    renderPagination('transaksi', pagination);
}

// Render Pagination
function renderPagination(type, pagination) {
    const container = document.getElementById(`${type}Pagination`);
    
    if (pagination.totalPages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    const showing = `Menampilkan ${(pagination.currentPage - 1) * pagination.perPage + 1} - ${Math.min(pagination.currentPage * pagination.perPage, pagination.total)} dari ${pagination.total} data`;
    
    let buttons = '';
    for (let i = 1; i <= pagination.totalPages; i++) {
        if (i === pagination.currentPage) {
            buttons += `<button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">${i}</button>`;
        } else {
            buttons += `<button onclick="load${type === 'pantau' ? 'PantauProgress' : 'LaporanTransaksi'}(${i})" class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50">${i}</button>`;
        }
    }
    
    container.innerHTML = `
        <p class="text-sm text-gray-600">${showing}</p>
        <div class="flex gap-1">${buttons}</div>
    `;
}

// Utility functions
function formatDate(datetime) {
    if (!datetime) return '-';
    const date = new Date(datetime);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function formatTime(datetime) {
    if (!datetime) return '-';
    const date = new Date(datetime);
    return date.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

function showImage(url) {
    window.open(url, '_blank');
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    renderChart();
    loadPantauProgress(1);
});

// Handle window resize
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        if (chartInstance) {
            renderChart();
        }
    }, 250);
});
</script>

<style>
.tab-button.active {
    border-bottom: 2px solid #2563eb;
    color: #2563eb;
}
</style>

<?= $this->endSection() ?>