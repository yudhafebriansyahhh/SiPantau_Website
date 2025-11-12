<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('superadmin/master-kegiatan-detail/show/' . $detailProses['id_kegiatan_detail']) ?>" 
           class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Detail Kegiatan
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Daftar Kegiatan Wilayah</h1>
    <p class="text-gray-600 mt-1">Wilayah yang terkait dengan proses kegiatan ini</p>
</div>

<!-- Info Card - Kegiatan Detail Proses -->
<div class="card mb-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200">
    <div class="flex items-start justify-between">
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                <?= esc($detailProses['nama_kegiatan_detail_proses']) ?>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-600">Kegiatan Detail:</span>
                    <p class="font-medium text-gray-900"><?= esc($detailProses['nama_kegiatan_detail']) ?></p>
                </div>
                <div>
                    <span class="text-gray-600">Satuan:</span>
                    <p class="font-medium text-gray-900"><?= esc($detailProses['satuan']) ?></p>
                </div>
                <div>
                    <span class="text-gray-600">Target:</span>
                    <p class="font-medium text-gray-900"><?= number_format($detailProses['target']) ?></p>
                </div>
            </div>
            <?php if (!empty($detailProses['keterangan'])): ?>
            <div class="mt-3 pt-3 border-t border-blue-200">
                <span class="text-gray-600 text-sm">Keterangan:</span>
                <p class="text-sm text-gray-700 mt-1"><?= esc($detailProses['keterangan']) ?></p>
            </div>
            <?php endif; ?>
        </div>
        <div class="ml-4">
            <span class="badge badge-info text-base px-4 py-2">
                <?= esc($detailProses['periode']) ?>
            </span>
        </div>
    </div>
</div>

<!-- Summary Cards dengan warna sesuai contoh -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <!-- Total Wilayah - Biru -->
    <div class="card bg-white border-l-4 border-blue-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Wilayah</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= count($kegiatanWilayah) ?></h3>
            </div>
            <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-map-marked-alt text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Total Target - Hijau -->
    <div class="card bg-white border-l-4 border-green-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Target</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($totalTarget) ?></h3>
            </div>
            <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clipboard-list text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Total Realisasi - Orange -->
    <div class="card bg-white border-l-4 border-orange-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Realisasi</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($totalRealisasi) ?></h3>
            </div>
            <div class="w-14 h-14 bg-orange-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-2xl text-orange-600"></i>
            </div>
        </div>
    </div>

    <!-- Target Tercapai - Ungu -->
    <div class="card bg-white border-l-4 border-purple-500">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Target Tercapai</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($avgProgress, 0) ?>%</h3>
            </div>
            <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-bullseye text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Kurva S Chart -->
<div class="card mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Kurva S – Target vs Realisasi</h3>
            <p class="text-sm text-gray-600 mt-1">Perbandingan pencapaian target kumulatif dengan realisasi aktual</p>
        </div>
        <div class="flex gap-3 text-sm">
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-blue-500 mr-2"></div>
                <span class="text-gray-600">Target</span>
            </div>
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                <span class="text-gray-600">Realisasi</span>
            </div>
        </div>
    </div>
    <div id="kurvaProvinsiChart"></div>
</div>

<!-- Main Card -->
<div class="card">
    <!-- Search Box -->
    <div class="mb-6">
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" id="searchInput"
                class="input-field w-full pl-10"
                placeholder="Cari kabupaten..."
                onkeyup="searchTable()">
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="kegiatanWilayahTable">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-12">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kabupaten/Kota</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Keterangan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Target</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Realisasi</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Progress</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($kegiatanWilayah)): ?>
                    <?php foreach ($kegiatanWilayah as $index => $kg): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 text-sm text-gray-900 text-center"><?= $index + 1 ?></td>
                            <td class="px-4 py-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-900"><?= esc($kg['nama_kabupaten']) ?></p>
                                    <p class="text-xs text-gray-500">Kode: <?= esc($kg['id_kabupaten']) ?></p>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-600 line-clamp-2">
                                    <?= !empty($kg['keterangan']) ? esc($kg['keterangan']) : '-' ?>
                                </p>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="text-sm font-semibold text-gray-900">
                                    <?= number_format($kg['target_wilayah']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="text-sm font-semibold text-gray-900">
                                    <?= number_format($kg['realisasi']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex flex-col items-center">
                                    <!-- Circular Progress -->
                                    <div class="relative inline-flex items-center justify-center">
                                        <svg class="transform -rotate-90" width="50" height="50">
                                            <circle cx="25" cy="25" r="20" stroke="#e5e7eb" stroke-width="4" fill="none"/>
                                            <circle cx="25" cy="25" r="20" 
                                                    stroke="<?= $kg['progress_color'] ?>" 
                                                    stroke-width="4" 
                                                    fill="none"
                                                    stroke-dasharray="<?= (2 * 3.14159 * 20) ?>"
                                                    stroke-dashoffset="<?= (2 * 3.14159 * 20) * (1 - ($kg['progress'] / 100)) ?>"
                                                    stroke-linecap="round"/>
                                        </svg>
                                        <span class="absolute text-xs font-semibold" 
                                              style="color: <?= $kg['progress_color'] ?>">
                                            <?= number_format($kg['progress'], 1) ?>%
                                        </span>
                                    </div>
                                    <!-- Status Label -->
                                    <div class="mt-1 text-xs text-center">
                                        <?php if ($kg['progress'] >= 80): ?>
                                            <span class="text-green-600 font-medium">Sangat Baik</span>
                                        <?php elseif ($kg['progress'] >= 50): ?>
                                            <span class="text-blue-600 font-medium">Baik</span>
                                        <?php elseif ($kg['progress'] >= 25): ?>
                                            <span class="text-orange-600 font-medium">Sedang</span>
                                        <?php else: ?>
                                            <span class="text-red-600 font-medium">Rendah</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-4 py-12 text-center">
                            <i class="fas fa-inbox text-gray-300 text-5xl mb-4"></i>
                            <p class="text-gray-500">Belum ada kegiatan wilayah untuk proses ini</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Info -->
    <?php if (!empty($kegiatanWilayah)): ?>
    <div class="mt-6 flex items-center justify-between border-t border-gray-200 pt-4">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium"><?= count($kegiatanWilayah) ?></span> wilayah
        </p>
        <a href="<?= base_url('superadmin/master-kegiatan-detail/show/' . $detailProses['id_kegiatan_detail']) ?>" 
           class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- ApexCharts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>

<script>
let chartInstance = null;

// Search function
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('kegiatanWilayahTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        // Skip empty state row
        if (cells.length === 1 && cells[0].getAttribute('colspan')) {
            continue;
        }
        
        // Search in kabupaten column (index 1)
        if (cells[1]) {
            const textValue = cells[1].textContent || cells[1].innerText;
            if (textValue.toLowerCase().indexOf(filter) > -1) {
                found = true;
            }
        }
        
        row.style.display = found ? '' : 'none';
    }
}

// Load Kurva S Provinsi
document.addEventListener('DOMContentLoaded', function() {
    loadKurvaProvinsi();
});

async function loadKurvaProvinsi() {
    const chartContainer = document.getElementById('kurvaProvinsiChart');
    
    try {
        const idProses = <?= $detailProses['id_kegiatan_detail_proses'] ?>;
        
        // Show loading state
        chartContainer.innerHTML = `
            <div class="flex justify-center items-center py-16">
                <div class="text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-gray-600">Memuat data kurva S...</p>
                </div>
            </div>
        `;
        
        const response = await fetch(`<?= base_url('superadmin/master-kegiatan-detail/kurva-provinsi') ?>?id_kegiatan_detail_proses=${idProses}&nocache=${Date.now()}`, {
            cache: "no-store"
        });
        
        const data = await response.json();
        
        if (data && data.labels && data.labels.length > 0) {
            // Clear loading state
            chartContainer.innerHTML = '';
            await new Promise(resolve => setTimeout(resolve, 200));
            renderKurvaProvinsi(data);
        } else {
            chartContainer.innerHTML = `
                <div class="text-center py-16">
                    <i class="fas fa-chart-line text-5xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500">Belum ada data kurva S untuk kegiatan ini</p>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading kurva provinsi:', error);
        chartContainer.innerHTML = `
            <div class="text-center py-16">
                <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
                <p class="text-gray-600">Gagal memuat data. Silakan refresh halaman.</p>
            </div>
        `;
    }
}

function renderKurvaProvinsi(data) {
    // Destroy existing chart instance
    if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
    }
    
    const options = {
        series: [
            {
                name: 'Target Kumulatif',
                data: data.targetAbsolut
            },
            {
                name: 'Realisasi Kumulatif',
                data: data.realisasiAbsolut
            }
        ],
        chart: {
            height: 420,
            type: 'area',
            fontFamily: 'Inter, sans-serif',
            animations: {
                enabled: true,
                speed: 600,
                animateGradually: {
                    enabled: true,
                    delay: 150
                }
            },
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: false,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            }
        },
        colors: ['#1e88e5', '#43a047'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        xaxis: {
            categories: data.labels,
            title: {
                text: 'Tanggal',
                style: {
                    fontSize: '12px',
                    fontWeight: 600
                }
            },
            labels: {
                rotate: -45,
                style: {
                    fontSize: '11px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Jumlah Kumulatif',
                style: {
                    fontSize: '12px',
                    fontWeight: 600
                }
            },
            labels: {
                formatter: function(val) {
                    return Math.floor(val).toLocaleString('id-ID');
                }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.5,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        legend: {
            show: true,
            position: 'top',
            horizontalAlign: 'left',
            fontSize: '13px',
            markers: {
                width: 12,
                height: 12,
                radius: 2
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            custom: function({ seriesIndex, dataPointIndex, w }) {
                const targetKum = data.targetAbsolut[dataPointIndex];
                const targetHarian = data.targetHarian ? data.targetHarian[dataPointIndex] : 0;
                const targetPersen = data.targetPersen ? data.targetPersen[dataPointIndex] : 0;
                const realisasiKum = data.realisasiAbsolut[dataPointIndex];
                const realisasiPersen = data.realisasiPersen ? data.realisasiPersen[dataPointIndex] : 0;
                const selisih = realisasiKum - targetKum;
                const selisihClass = selisih >= 0 ? 'color: #43a047;' : 'color: #ef4444;';
                const selisihIcon = selisih >= 0 ? '📈' : '📉';
                
                return `
                    <div style="padding: 14px; min-width: 240px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                        <div style="font-weight: 600; margin-bottom: 10px; color: #1e293b; font-size: 13px; border-bottom: 2px solid #e5e7eb; padding-bottom: 6px;">
                            📅 ${data.labels[dataPointIndex]}
                        </div>
                        
                        <div style="margin-bottom: 10px;">
                            <div style="font-weight: 600; color: #64748b; font-size: 11px; margin-bottom: 6px;">TARGET</div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                                <span style="color: #64748b; font-size: 12px;">Kumulatif:</span>
                                <span style="font-weight: 600; color: #1e88e5; font-size: 12px;">${targetKum.toLocaleString('id-ID')}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                                <span style="color: #64748b; font-size: 12px;">Harian:</span>
                                <span style="font-weight: 600; font-size: 12px;">${targetHarian.toLocaleString('id-ID')}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b; font-size: 12px;">Persen:</span>
                                <span style="font-weight: 600; font-size: 12px;">${targetPersen.toFixed(2)}%</span>
                            </div>
                        </div>
                        
                        <div style="margin-bottom: 10px; padding-top: 6px; border-top: 1px dashed #e5e7eb;">
                            <div style="font-weight: 600; color: #64748b; font-size: 11px; margin-bottom: 6px;">REALISASI</div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 3px;">
                                <span style="color: #64748b; font-size: 12px;">Kumulatif:</span>
                                <span style="font-weight: 600; color: #43a047; font-size: 12px;">${realisasiKum.toLocaleString('id-ID')}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span style="color: #64748b; font-size: 12px;">Persen:</span>
                                <span style="font-weight: 600; font-size: 12px;">${realisasiPersen.toFixed(2)}%</span>
                            </div>
                        </div>
                        
                        <div style="padding-top: 8px; border-top: 2px solid #e5e7eb;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="color: #64748b; font-size: 12px; font-weight: 600;">${selisihIcon} Selisih:</span>
                                <span style="font-weight: 700; font-size: 13px; ${selisihClass}">${selisih >= 0 ? '+' : ''}${selisih.toLocaleString('id-ID')}</span>
                            </div>
                        </div>
                    </div>
                `;
            }
        },
        grid: {
            borderColor: '#e5e7eb',
            strokeDashArray: 3
        }
    };

    chartInstance = new ApexCharts(document.querySelector("#kurvaProvinsiChart"), options);
    chartInstance.render();
}
</script>

<?= $this->endSection() ?>