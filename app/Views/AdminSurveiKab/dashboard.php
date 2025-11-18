<?= $this->extend('layouts/adminkab_layout') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-600 mt-1">Selamat datang di SiPantau - Sistem Pelaporan Kegiatan Lapangan</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <?php
    $statsConfig = [
        ['Total Kegiatan', $stats['total_kegiatan'], 'fas fa-clipboard-list', 'bg-green-50', '#43a047'],
        ['Kegiatan Aktif', $stats['kegiatan_aktif'], 'fas fa-chart-line', 'bg-orange-50', '#fb8c00'],
        ['Target Tercapai', $stats['target_tercapai'] . '%', 'fas fa-bullseye', 'bg-purple-50', '#8e24aa'],
    ];
    foreach ($statsConfig as [$label, $value, $icon, $bg, $color]): ?>
        <div class="card hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1"><?= esc($label) ?></p>
                    <h3 class="text-3xl font-bold text-gray-900"><?= esc($value) ?></h3>
                </div>
                <div class="w-14 h-14 <?= $bg ?> rounded-lg flex items-center justify-center">
                    <i class="<?= $icon ?> text-2xl" style="color: <?= $color ?>;"></i>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    <!-- Kurva S Chart -->
    <div class="lg:col-span-2 card">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Kurva S - Target vs Realisasi</h3>
                <p class="text-sm text-gray-600">Progres Kumulatif Kegiatan</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <select id="filterKegiatan" class="input-field w-full sm:w-auto text-sm" onchange="updateChart()">
                    <option value="">-- Pilih Kegiatan --</option>
                    <?php foreach ($kegiatanList as $item): ?>
                        <option value="<?= esc($item['id_kegiatan_detail_proses']) ?>">
                            <?= esc($item['nama_kegiatan_detail_proses']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div id="chartLoadingState" class="flex justify-center items-center py-16">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Memuat data...</p>
            </div>
        </div>

        <div id="kegiatanChart" style="display: none;"></div>

        <div id="chartPlaceholder" class="flex justify-center items-center py-16" style="display: none;">
            <p class="text-gray-400">Pilih kegiatan untuk menampilkan kurva S</p>
        </div>

        <div id="chartErrorState" class="text-center py-16" style="display: none;">
            <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
            <p class="text-gray-600">Gagal memuat data. Silakan refresh halaman.</p>
        </div>
    </div>

    <!-- Progress Kegiatan -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Progres Kegiatan</h3>
        <div class="space-y-4">
            <?php if (!empty($progressKegiatan)): ?>
                <?php foreach ($progressKegiatan as $index => $prog): ?>
                    <div class="<?= $index < count($progressKegiatan) - 1 ? 'pb-4 border-b border-gray-100' : 'pb-4' ?>">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-900"><?= esc($prog['nama']) ?></span>
                            <span class="text-sm font-semibold"
                                style="color: <?= $prog['color'] ?>;"><?= $prog['progress'] ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-500"
                                style="width: 0%; background-color: <?= $prog['color'] ?>;"
                                data-width="<?= $prog['progress'] ?>"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                    <p class="text-gray-500 text-sm">Belum ada data kegiatan</p>
                </div>
            <?php endif; ?>
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
        <div id="kegiatanStatusInfo" class="mt-2 sm:mt-0" style="display: none;">
            <span class="badge" id="badgeStatusKegiatan"></span>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="petugasTable">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-user mr-2"></i>Petugas
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-calendar-check mr-2"></i>Status Kegiatan
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-clipboard-check mr-2"></i>Status Harian
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        <i class="fas fa-tasks mr-2"></i>Progress
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="petugasTableBody">
                <tr>
                    <td colspan="4" class="px-4 py-12 text-center">
                        <i class="fas fa-info-circle text-gray-300 text-4xl mb-2"></i>
                        <p class="text-gray-500">Pilih kegiatan untuk menampilkan data petugas</p>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ApexCharts CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>

<script>
    let chartInstance = null;
    const defaultKegiatan = "<?= esc($latestKegiatanId) ?>";
    const baseKurvaS = "<?= base_url('adminsurvei-kab/get-kurva-s') ?>";
    const basePetugas = "<?= base_url('adminsurvei-kab/get-petugas') ?>";

    // Update Chart
    async function updateChart() {
        const kegiatan = document.getElementById('filterKegiatan').value;

        if (!kegiatan) {
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }
            document.getElementById('chartLoadingState').style.display = 'none';
            document.getElementById('chartPlaceholder').style.display = 'flex';
            document.getElementById('kegiatanChart').style.display = 'none';
            document.getElementById('chartErrorState').style.display = 'none';

            loadPetugas();
            return;
        }

        document.getElementById('chartLoadingState').style.display = 'flex';
        document.getElementById('chartPlaceholder').style.display = 'none';
        document.getElementById('kegiatanChart').style.display = 'none';
        document.getElementById('chartErrorState').style.display = 'none';

        const response = await fetch(`${baseKurvaS}?id_kegiatan_detail_proses=${kegiatan}`);
        const result = await response.json();

        if (result.success) {
            document.getElementById('chartLoadingState').style.display = 'none';
            document.getElementById('kegiatanChart').style.display = 'block';

            renderChart(result.data);
            loadPetugas();
        } else {
            document.getElementById('chartLoadingState').style.display = 'none';
            document.getElementById('chartErrorState').style.display = 'block';
        }
    }

    // Render Chart
    function renderChart(data) {
        if (chartInstance) {
            chartInstance.destroy();
        }

        const isMobile = window.innerWidth < 640;

        const options = {
            series: [
                {
                    name: 'Target (Kurva S)',
                    data: data.target,
                    type: 'area'
                },
                {
                    name: 'Realisasi (Kumulatif)',
                    data: data.realisasi,
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
                categories: data.labels,
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
                    formatter: function (value) {
                        return Math.round(value).toLocaleString('id-ID');
                    }
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: function (value) {
                        return value.toLocaleString('id-ID');
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
                xaxis: isMobile ? [] : [
                    {
                        x: data.config.tanggal_mulai,
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
                        x: data.config.tanggal_selesai,
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
                ]
            }
        };

        chartInstance = new ApexCharts(document.querySelector("#kegiatanChart"), options);
        chartInstance.render();
    }

    // Load Petugas
    async function loadPetugas() {
        const kegiatanId = document.getElementById('filterKegiatan').value;

        if (!kegiatanId) {
            document.getElementById('petugasTableBody').innerHTML = `
            <tr>
                <td colspan="4" class="px-4 py-12 text-center">
                    <i class="fas fa-info-circle text-gray-300 text-4xl mb-2"></i>
                    <p class="text-gray-500">Pilih kegiatan untuk menampilkan data petugas</p>
                </td>
            </tr>
        `;
            document.getElementById('kegiatanStatusInfo').style.display = 'none';
            return;
        }

        const response = await fetch(`${basePetugas}?id_kegiatan_detail_proses=${kegiatanId}`);
        const result = await response.json();

        if (result.success) {
            renderPetugasTable(result.data);
        }
    }

    // Render Petugas Table
    function renderPetugasTable(data) {
        const tbody = document.getElementById('petugasTableBody');
        const statusInfo = document.getElementById('kegiatanStatusInfo');
        const badgeStatus = document.getElementById('badgeStatusKegiatan');

        // DEBUG: Cek data yang diterima
        console.log('=== DEBUG PETUGAS DATA ===');
        console.log('Total data:', data.length);
        if (data.length > 0) {
            console.log('Sample data pertama:', data[0]);
            console.log('Status Harian:', data[0].status_harian);
            console.log('Status Harian Class:', data[0].status_harian_class);
            console.log('Target Harian:', data[0].target_harian);
            console.log('Realisasi Hari Ini:', data[0].realisasi_hari_ini);
        }
        console.log('========================');

        if (data.length === 0) {
            tbody.innerHTML = `
            <tr>
                <td colspan="4" class="px-4 py-12 text-center">
                    <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                    <p class="text-gray-500">Belum ada data petugas</p>
                </td>
            </tr>
        `;
            statusInfo.style.display = 'none';
            return;
        }

        // Show status kegiatan info
        if (data.length > 0) {
            const statusKegiatan = data[0].status_kegiatan;
            const statusClass = data[0].status_kegiatan_class;
            badgeStatus.textContent = statusKegiatan;
            badgeStatus.className = `badge ${statusClass}`;
            statusInfo.style.display = 'block';
        }

        const colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444'];

        tbody.innerHTML = data.map((p, index) => `
        <tr class="hover:bg-gray-50 transition-colors duration-150">
            <!-- Petugas Info -->
            <td class="px-4 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" 
                         style="background-color: ${colors[index % colors.length]};">
                        <span class="text-white text-sm font-semibold">${p.nama_user.substring(0, 2).toUpperCase()}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">${p.nama_user}</p>
                        <p class="text-xs text-gray-500 truncate">${p.role} - ${p.nama_kabupaten}</p>
                    </div>
                </div>
            </td>
            
            <!-- Status Kegiatan -->
            <td class="px-4 py-4">
                <span class="badge ${p.status_kegiatan_class}">
                    ${p.status_kegiatan}
                </span>
            </td>
            
            <!-- Status Harian -->
            <td class="px-4 py-4">
                <div class="flex flex-col gap-1 items-start">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${p.status_harian_class === 'badge-danger' ? 'bg-red-100 text-red-800' : ''} ${p.status_harian_class === 'badge-success' ? 'bg-green-100 text-green-800' : ''} ${p.status_harian_class === 'badge-warning' ? 'bg-yellow-100 text-yellow-800' : ''} ${p.status_harian_class === 'badge-secondary' ? 'bg-gray-100 text-gray-700' : ''}">
                        ${p.status_harian}
                    </span>
                    ${p.status_harian === 'Belum Lapor' && p.target_harian > 0 ? `
                        <span class="text-xs text-gray-600">
                            <i class="fas fa-bullseye mr-1"></i>
                            Target: ${p.target_harian}
                        </span>
                    ` : ''}
                    ${p.status_harian !== 'Tidak Perlu Lapor' && p.status_harian !== 'Belum Lapor' && (p.realisasi_hari_ini > 0 || p.target_harian > 0) ? `
                        <span class="text-xs text-gray-600">
                            <i class="fas fa-check-circle mr-1"></i>
                            ${p.realisasi_hari_ini}${p.target_harian > 0 ? ' / ' + p.target_harian : ''}
                        </span>
                    ` : ''}
                </div>
            </td>
            
            <!-- Progress Keseluruhan -->
            <td class="px-4 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex-1">
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-300" 
                                 style="width: ${p.progress}%; background-color: ${colors[index % colors.length]};"></div>
                        </div>
                    </div>
                    <span class="text-sm font-semibold text-gray-900 min-w-[3rem] text-right">${p.progress}%</span>
                </div>
            </td>
        </tr>
    `).join('');
    }

    // Window resize handler
    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            if (chartInstance) {
                updateChart();
            }
        }, 250);
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        if (defaultKegiatan) {
            document.getElementById('filterKegiatan').value = defaultKegiatan;
            updateChart();
        } else {
            document.getElementById('chartLoadingState').style.display = 'none';
            document.getElementById('chartPlaceholder').style.display = 'flex';
        }

        // Animate progress bars
        setTimeout(() => {
            document.querySelectorAll('[data-width]').forEach(bar => {
                bar.style.width = bar.dataset.width + '%';
            });
        }, 400);
    });
</script>

<?= $this->endSection() ?>