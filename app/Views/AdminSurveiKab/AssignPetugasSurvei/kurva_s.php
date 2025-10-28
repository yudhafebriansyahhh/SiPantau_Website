<?= $this->extend('layouts/adminkab_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="<?= base_url('adminsurvei-kab/assign-petugas/detail/1') ?>" class="text-gray-600 hover:text-gray-900 mr-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Detail Progress PCL</h1>
</div>

<!-- PCL Info Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <p class="text-sm text-gray-500 mb-1">Nama PCL</p>
            <p class="text-base font-semibold text-gray-900">Siti Aminah</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">PML</p>
            <p class="text-base font-semibold text-gray-900">Ahmad Subarjo</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">Nama Survei</p>
            <p class="text-base font-semibold text-gray-900">Survei Angkatan Kerja Nasional 2025</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">Wilayah</p>
            <p class="text-base font-semibold text-gray-900">Kota Pekanbaru</p>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <!-- Target -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Target</p>
                <h3 class="text-3xl font-bold text-gray-900" id="statTarget">50</h3>
            </div>
            <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-bullseye text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>
    
    <!-- Aktual -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Aktual</p>
                <h3 class="text-3xl font-bold text-gray-900" id="statAktual">30</h3>
            </div>
            <div class="w-14 h-14 bg-green-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-2xl text-green-600"></i>
            </div>
        </div>
    </div>
    
    <!-- Persentase -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Pencapaian</p>
                <h3 class="text-3xl font-bold text-gray-900" id="statPersentase">60%</h3>
            </div>
            <div class="w-14 h-14 bg-purple-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-pie text-2xl text-purple-600"></i>
            </div>
        </div>
    </div>
    
    <!-- Selisih -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Selisih</p>
                <h3 class="text-3xl font-bold text-gray-900" id="statSelisih">-20</h3>
            </div>
            <div class="w-14 h-14 bg-orange-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-balance-scale text-2xl text-orange-600"></i>
            </div>
        </div>
    </div>
</div>

<!-- Kurva S Chart -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Kurva S - Target vs Aktual PCL</h3>
            <p class="text-sm text-gray-600">Progress Kumulatif Harian</p>
        </div>
        <div class="mt-3 sm:mt-0">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                <i class="fas fa-calendar-alt mr-2"></i>
                September 2025
            </span>
        </div>
    </div>
    
    <div id="pclChart"></div>
</div>

<!-- Daily Report Table -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Laporan Harian</h3>
    
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tanggal</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Target Kumulatif</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Aktual Kumulatif</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Pencapaian</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="dailyReportTable">
                <!-- Data akan diisi oleh JavaScript -->
            </tbody>
        </table>
    </div>
</div>

<!-- ApexCharts CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>

<script>
// ============================================
// KONFIGURASI PCL (Data dari server/database)
// ============================================
const pclConfig = {
    nama: 'Siti Aminah',
    pml: 'Ahmad Subarjo',
    survei: 'Survei Angkatan Kerja Nasional 2025',
    wilayah: 'Kota Pekanbaru',
    targetTotal: 50,
    progressAktual: 30, // Progress saat ini: 30/50 = 60%
    targetAwalPersen: 5,
    tanggalMulai: 2,
    tanggalSelesai: 25
};

// ============================================
// FUNGSI GENERATE KURVA S UNTUK PCL
// ============================================
function generatePCLSCurve(config) {
    const targetTotal = config.targetTotal;
    const targetAwal = Math.round(targetTotal * (config.targetAwalPersen / 100));
    const tanggalMulai = config.tanggalMulai;
    const tanggalSelesai = config.tanggalSelesai;
    
    const targetCurve = [];
    const labels = [];
    
    // Generate untuk 30 hari September
    for (let day = 1; day <= 30; day++) {
        labels.push(`${day} Sep`);
        
        if (day < tanggalMulai) {
            targetCurve.push(0);
        } else if (day >= tanggalSelesai) {
            targetCurve.push(targetTotal);
        } else {
            const totalDays = tanggalSelesai - tanggalMulai;
            const currentDay = day - tanggalMulai;
            const progress = currentDay / totalDays;
            
            // Sigmoid function untuk kurva S
            const k = 8;
            const x0 = 0.5;
            const sigmoid = 1 / (1 + Math.exp(-k * (progress - x0)));
            
            const value = targetAwal + (targetTotal - targetAwal) * sigmoid;
            targetCurve.push(Math.round(value));
        }
    }
    
    return { labels, targetCurve };
}

// ============================================
// FUNGSI GENERATE DATA AKTUAL BERDASARKAN PROGRESS
// ============================================
function generatePCLAktualData(targetCurve, config) {
    const tanggalMulai = config.tanggalMulai;
    const progressAktual = config.progressAktual;
    const targetTotal = config.targetTotal;
    const persentaseProgress = (progressAktual / targetTotal) * 100;
    
    const aktual = [];
    let lastValue = 0;
    
    // Hitung hari ini berdasarkan tanggal sekarang (simulasi)
    const today = 15; // Simulasi hari ke-15 September
    
    for (let day = 1; day <= 30; day++) {
        if (day < tanggalMulai) {
            aktual.push(0);
        } else if (day <= today) {
            // Data sampai hari ini (real data)
            const targetHariIni = targetCurve[day - 1];
            const targetKemarin = day > 1 ? targetCurve[day - 2] : 0;
            const targetIncrement = targetHariIni - targetKemarin;
            
            // Simulasi dengan variasi harian
            const adaLaporan = Math.random() > 0.25;
            
            if (adaLaporan && targetIncrement > 0) {
                const achievementRate = 0.80 + Math.random() * 0.15;
                const increment = Math.round(targetIncrement * achievementRate);
                lastValue = Math.min(lastValue + increment, progressAktual);
            }
            
            // Pastikan pada hari ini mencapai progressAktual
            if (day === today) {
                lastValue = progressAktual;
            }
            
            aktual.push(lastValue);
        } else {
            // Proyeksi ke depan (null untuk tidak ditampilkan)
            aktual.push(null);
        }
    }
    
    return { aktual, today };
}

// ============================================
// GENERATE DATA
// ============================================
const { labels, targetCurve } = generatePCLSCurve(pclConfig);
const { aktual, today } = generatePCLAktualData(targetCurve, pclConfig);

// ============================================
// UPDATE STATISTICS
// ============================================
function updateStats() {
    const target = pclConfig.targetTotal;
    const aktualValue = pclConfig.progressAktual;
    const persentase = Math.round((aktualValue / target) * 100);
    const selisih = aktualValue - target;
    
    document.getElementById('statTarget').textContent = target;
    document.getElementById('statAktual').textContent = aktualValue;
    document.getElementById('statPersentase').textContent = persentase + '%';
    document.getElementById('statSelisih').textContent = (selisih >= 0 ? '+' : '') + selisih;
    
    // Update colors
    const persentaseEl = document.getElementById('statPersentase');
    const selisihEl = document.getElementById('statSelisih');
    
    if (persentase >= 90) {
        persentaseEl.classList.add('text-green-600');
    } else if (persentase >= 70) {
        persentaseEl.classList.add('text-orange-600');
    } else {
        persentaseEl.classList.add('text-red-600');
    }
    
    if (selisih >= 0) {
        selisihEl.classList.add('text-green-600');
    } else {
        selisihEl.classList.add('text-red-600');
    }
}

// ============================================
// RENDER DAILY REPORT TABLE
// ============================================
function renderDailyReport() {
    const tbody = document.getElementById('dailyReportTable');
    let html = '';
    
    // Tampilkan 10 hari terakhir
    const startDay = Math.max(1, today - 9);
    
    for (let i = today; i >= startDay; i--) {
        const targetVal = targetCurve[i - 1];
        const aktualVal = aktual[i - 1];
        const persentase = targetVal > 0 ? Math.round((aktualVal / targetVal) * 100) : 0;
        
        let statusBadge = '';
        if (persentase >= 90) {
            statusBadge = '<span class="px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full">On Track</span>';
        } else if (persentase >= 70) {
            statusBadge = '<span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs font-medium rounded-full">Warning</span>';
        } else {
            statusBadge = '<span class="px-2 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full">Behind</span>';
        }
        
        html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-sm text-gray-900">${i} September 2025</td>
                <td class="px-4 py-3 text-sm text-gray-700 text-center">${targetVal}</td>
                <td class="px-4 py-3 text-sm font-semibold text-gray-900 text-center">${aktualVal || 0}</td>
                <td class="px-4 py-3 text-sm text-center">
                    <span class="font-medium ${persentase >= 90 ? 'text-green-600' : persentase >= 70 ? 'text-orange-600' : 'text-red-600'}">
                        ${persentase}%
                    </span>
                </td>
                <td class="px-4 py-3 text-center">${statusBadge}</td>
            </tr>
        `;
    }
    
    tbody.innerHTML = html;
}

// ============================================
// RENDER CHART
// ============================================
function renderChart() {
    const options = {
        series: [
            {
                name: 'Target (Kurva S)',
                data: targetCurve,
                type: 'area'
            },
            {
                name: 'Aktual (Kumulatif)',
                data: aktual,
                type: 'area'
            }
        ],
        chart: {
            height: 400,
            type: 'area',
            fontFamily: 'Poppins, sans-serif',
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
            width: [3, 3],
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
                opacityTo: 0.1
            }
        },
        markers: {
            size: 0,
            colors: ['#1e88e5', '#e53935'],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
                size: 7
            }
        },
        xaxis: {
            categories: labels,
            title: {
                text: 'Tanggal (September 2025)',
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
                text: 'Jumlah Survei (Kumulatif)',
                style: {
                    fontSize: '12px',
                    fontWeight: 600
                }
            },
            max: pclConfig.targetTotal + 5
        },
        tooltip: {
            shared: true,
            intersect: false,
            custom: function({ series, seriesIndex, dataPointIndex, w }) {
                const target = series[0][dataPointIndex];
                const aktualVal = series[1][dataPointIndex];
                
                if (aktualVal === null) {
                    return `
                        <div class="px-3 py-2 bg-gray-900 text-white rounded-lg shadow-lg">
                            <div class="font-semibold mb-1">${labels[dataPointIndex]}</div>
                            <div class="text-sm">
                                <div>Target: ${target}</div>
                                <div class="text-gray-400 mt-1">Belum ada data</div>
                            </div>
                        </div>
                    `;
                }
                
                const persentase = target > 0 ? ((aktualVal / target) * 100).toFixed(1) : 0;
                const selisih = aktualVal - target;
                
                return `
                    <div class="px-3 py-2 bg-gray-900 text-white rounded-lg shadow-lg">
                        <div class="font-semibold mb-1">${labels[dataPointIndex]}</div>
                        <div class="text-sm space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-[#1e88e5] rounded-full"></span>
                                <span>Target: ${target}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 bg-[#e53935] rounded-full"></span>
                                <span>Aktual: ${aktualVal}</span>
                            </div>
                            <div class="border-t border-gray-700 pt-1 mt-1">
                                <div>Pencapaian: ${persentase}%</div>
                                <div>Selisih: ${selisih >= 0 ? '+' : ''}${selisih}</div>
                            </div>
                        </div>
                    </div>
                `;
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'left'
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 3
        },
        annotations: {
            xaxis: [
                {
                    x: `${pclConfig.tanggalMulai} Sep`,
                    borderColor: '#43a047',
                    label: {
                        text: 'Mulai',
                        style: {
                            color: '#fff',
                            background: '#43a047'
                        }
                    }
                },
                {
                    x: `${pclConfig.tanggalSelesai} Sep`,
                    borderColor: '#e53935',
                    label: {
                        text: 'Target 100%',
                        style: {
                            color: '#fff',
                            background: '#e53935'
                        }
                    }
                },
                {
                    x: `${today} Sep`,
                    borderColor: '#fb8c00',
                    label: {
                        text: 'Hari Ini',
                        style: {
                            color: '#fff',
                            background: '#fb8c00'
                        }
                    }
                }
            ]
        }
    };
    
    const chart = new ApexCharts(document.querySelector("#pclChart"), options);
    chart.render();
}

// ============================================
// INITIALIZE
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
    renderDailyReport();
    renderChart();
});
</script>

<?= $this->endSection() ?>