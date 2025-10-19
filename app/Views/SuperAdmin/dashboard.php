<?= $this->extend('layouts/sadmin_layout') ?>

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
            </div>
            <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-users text-2xl text-[#1e88e5]"></i>
            </div>
        </div>
    </div>
    
    <!-- Total Kegiatan -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Kegiatan</p>
                <h3 class="text-3xl font-bold text-gray-900">200</h3>
            </div>
            <div class="w-14 h-14 bg-green-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-clipboard-list text-2xl text-[#43a047]"></i>
            </div>
        </div>
    </div>
    
    <!-- Kegiatan Aktif -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Kegiatan Aktif</p>
                <h3 class="text-3xl font-bold text-gray-900">45</h3>
            </div>
            <div class="w-14 h-14 bg-orange-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-chart-line text-2xl text-[#fb8c00]"></i>
            </div>
        </div>
    </div>
    
    <!-- Target Tercapai -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Target Tercapai</p>
                <h3 class="text-3xl font-bold text-gray-900">78%</h3>
            </div>
            <div class="w-14 h-14 bg-purple-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-bullseye text-2xl text-[#8e24aa]"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    
    <!-- Kurva S Chart -->
    <div class="lg:col-span-2 card">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Kurva S - Target vs Aktual</h3>
                <p class="text-sm text-gray-600">Progres Kumulatif Kegiatan</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <select id="filterKegiatan" class="input-field w-full sm:w-auto text-sm" onchange="updateChart()">
                    <option value="sunsenas">SUNSENAS 2025</option>
                    <option value="sakernas">SAKERNAS 2025</option>
                    <option value="susenas">SUSENAS 2025</option>
                </select>
                <select id="filterWilayah" class="input-field w-full sm:w-auto text-sm" onchange="updateChart()">
                    <option value="all">Semua Wilayah</option>
                    <option value="pekanbaru">Kota Pekanbaru</option>
                    <option value="dumai">Kota Dumai</option>
                    <option value="kampar">Kab. Kampar</option>
                    <option value="rohul">Kab. Rokan Hulu</option>
                    <option value="rohil">Kab. Rokan Hilir</option>
                    <option value="siak">Kab. Siak</option>
                    <option value="bengkalis">Kab. Bengkalis</option>
                    <option value="indragiri_hulu">Kab. Indragiri Hulu</option>
                    <option value="indragiri_hilir">Kab. Indragiri Hilir</option>
                    <option value="pelalawan">Kab. Pelalawan</option>
                    <option value="kuansing">Kab. Kuantan Singingi</option>
                    <option value="kepulauan_meranti">Kab. Kepulauan Meranti</option>
                </select>
            </div>
        </div>
        
        <!-- Chart Info Stats -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
            <div class="bg-blue-50 rounded-lg p-3">
                <p class="text-xs text-[#1e88e5] mb-1">Target</p>
                <p class="text-lg font-bold text-[#1565c0]" id="statTarget">1000</p>
            </div>
            <div class="bg-red-50 rounded-lg p-3">
                <p class="text-xs text-[#e53935] mb-1">Aktual</p>
                <p class="text-lg font-bold text-[#c62828]" id="statAktual">950</p>
            </div>
            <div class="bg-green-50 rounded-lg p-3">
                <p class="text-xs text-[#43a047] mb-1">Pencapaian</p>
                <p class="text-lg font-bold text-[#2e7d32]" id="statPersentase">95%</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-3">
                <p class="text-xs text-[#fb8c00] mb-1">Selisih</p>
                <p class="text-lg font-bold text-[#ef6c00]" id="statSelisih">-50</p>
            </div>
        </div>
        
        <!-- Chart Container -->
        <div class="relative -mx-2 sm:mx-0">
            <div id="kegiatanChart" class="w-full"></div>
        </div>
    </div>
    
    <!-- Progress Kegiatan -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Progres Kegiatan Sedang Berjalan</h3>
        <div class="space-y-4">
            <!-- SUNSENAS 2025 -->
            <div class="pb-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">SUNSENAS 2025</span>
                    <span class="text-sm font-semibold" style="color: #1e88e5;">95%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500" style="width: 95%; background-color: #1e88e5;"></div>
                </div>
            </div>

            <!-- SAKERNAS 2025 -->
            <div class="pb-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">SAKERNAS 2025</span>
                    <span class="text-sm font-semibold" style="color: #43a047;">78%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500" style="width: 78%; background-color: #43a047;"></div>
                </div>
            </div>

            <!-- SUSENAS 2025 -->
            <div class="pb-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">SUSENAS 2025</span>
                    <span class="text-sm font-semibold" style="color: #fdd835;">62%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500" style="width: 62%; background-color: #fdd835;"></div>
                </div>
            </div>

            <!-- SP2025 -->
            <div class="pb-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">SP2025</span>
                    <span class="text-sm font-semibold" style="color: #8e24aa;">45%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500" style="width: 45%; background-color: #8e24aa;"></div>
                </div>
            </div>

            <!-- VHTL 2025 -->
            <div class="pb-4 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">VHTL 2025</span>
                    <span class="text-sm font-semibold" style="color: #e53935;">30%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500" style="width: 30%; background-color: #e53935;"></div>
                </div>
            </div>

            <!-- SKDI 2025 -->
            <div class="pb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-900">SKDI 2025</span>
                    <span class="text-sm font-semibold" style="color: #5e35b1;">20%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="h-2 rounded-full transition-all duration-500" style="width: 20%; background-color: #5e35b1;"></div>
                </div>
            </div>
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
            <select class="input-field w-full sm:w-auto text-sm">
                <option>Month</option>
                <option>January</option>
                <option>February</option>
            </select>
            <select class="input-field w-full sm:w-auto text-sm">
                <option>Kegiatan</option>
                <option>SUNSENAS 2025</option>
            </select>
            <select class="input-field w-full sm:w-auto text-sm">
                <option>Kabupaten/Kota</option>
                <option>Pekanbaru</option>
            </select>
            <select class="input-field w-full sm:w-auto text-sm">
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
                            <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">GM</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Dadang Sunandar</p>
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
                                    <div class="bg-yellow-500 h-2 rounded-full transition-all duration-300" style="width: 50%"></div>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 min-w-[2rem] text-right">50</span>
                        </div>
                    </td>
                </tr>
                
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">MS</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Muhammad Indra Mahfuzzak</p>
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
                                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 75%"></div>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 min-w-[2rem] text-right">75</span>
                        </div>
                    </td>
                </tr>
                
                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="px-4 py-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">AW</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">T. Muhammad Alkhadafi</p>
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
                                    <div class="bg-green-600 h-2 rounded-full transition-all duration-300" style="width: 100%"></div>
                                </div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 min-w-[2rem] text-right">100</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ApexCharts CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>

<script>
// ============================================
// KONFIGURASI KEGIATAN (Input dari Admin)
// ============================================
const kegiatanConfig = {
    sunsenas: {
        nama: 'SUNSENAS 2025',
        targetTotal: 1000,
        targetAwalPersen: 5,
        tanggalMulai: 2,
        tanggalSelesai: 25,
        wilayah: {
            all: 1000,
            pekanbaru: 120,
            dumai: 80,
            kampar: 100,
            rohul: 70,
            rohil: 65,
            siak: 75,
            bengkalis: 90,
            indragiri_hulu: 60,
            indragiri_hilir: 70,
            pelalawan: 50,
            kuansing: 45,
            kepulauan_meranti: 40
        }
    },
    sakernas: {
        nama: 'SAKERNAS 2025',
        targetTotal: 1500,
        targetAwalPersen: 3,
        tanggalMulai: 1,
        tanggalSelesai: 28,
        wilayah: {
            all: 1500,
            pekanbaru: 180,
            dumai: 120,
            kampar: 150,
            rohul: 105,
            rohil: 98,
            siak: 113,
            bengkalis: 135,
            indragiri_hulu: 90,
            indragiri_hilir: 105,
            pelalawan: 75,
            kuansing: 68,
            kepulauan_meranti: 60
        }
    },
    susenas: {
        nama: 'SUSENAS 2025',
        targetTotal: 1200,
        targetAwalPersen: 4,
        tanggalMulai: 3,
        tanggalSelesai: 27,
        wilayah: {
            all: 1200,
            pekanbaru: 144,
            dumai: 96,
            kampar: 120,
            rohul: 84,
            rohil: 78,
            siak: 90,
            bengkalis: 108,
            indragiri_hulu: 72,
            indragiri_hilir: 84,
            pelalawan: 60,
            kuansing: 54,
            kepulauan_meranti: 48
        }
    }
};

// ============================================
// FUNGSI GENERATE KURVA S DINAMIS
// ============================================
function generateDynamicSCurve(config, wilayah) {
    const targetTotal = config.wilayah[wilayah];
    const targetAwal = Math.round(targetTotal * (config.targetAwalPersen / 100));
    const tanggalMulai = config.tanggalMulai;
    const tanggalSelesai = config.tanggalSelesai;
    
    const curve = [];
    const labels = [];
    
    // Generate labels tanpa "Sep"
    for (let day = 1; day <= 30; day++) {
        labels.push(day);
        
        if (day < tanggalMulai) {
            curve.push(0);
        } else if (day >= tanggalSelesai) {
            curve.push(targetTotal);
        } else {
            const totalDays = tanggalSelesai - tanggalMulai;
            const currentDay = day - tanggalMulai;
            const progress = currentDay / totalDays;
            
            const k = 8;
            const x0 = 0.5;
            const sigmoid = 1 / (1 + Math.exp(-k * (progress - x0)));
            
            const value = targetAwal + (targetTotal - targetAwal) * sigmoid;
            curve.push(Math.round(value));
        }
    }
    
    return { labels, target: curve };
}

// ============================================
// FUNGSI GENERATE DATA AKTUAL (Kumulatif)
// ============================================
function generateAktualData(targetCurve, config, wilayah) {
    const tanggalMulai = config.tanggalMulai;
    const aktual = [];
    let lastValue = 0;
    
    for (let day = 1; day <= 30; day++) {
        if (day < tanggalMulai) {
            aktual.push(0);
        } else {
            const targetHariIni = targetCurve[day - 1];
            const targetKemarin = day > 1 ? targetCurve[day - 2] : 0;
            const targetIncrement = targetHariIni - targetKemarin;
            
            const adaLaporan = Math.random() > 0.3;
            
            if (adaLaporan && targetIncrement > 0) {
                const achievementRate = 0.85 + Math.random() * 0.13;
                const increment = Math.round(targetIncrement * achievementRate);
                lastValue = lastValue + increment;
            }
            
            lastValue = Math.min(lastValue, targetHariIni);
            aktual.push(lastValue);
        }
    }
    
    return aktual;
}

// ============================================
// GENERATE DATA UNTUK SEMUA KEGIATAN
// ============================================
const dataKegiatan = {};

Object.keys(kegiatanConfig).forEach(kegiatanKey => {
    const config = kegiatanConfig[kegiatanKey];
    dataKegiatan[kegiatanKey] = {};
    
    Object.keys(config.wilayah).forEach(wilayah => {
        const { labels, target } = generateDynamicSCurve(config, wilayah);
        const aktual = generateAktualData(target, config, wilayah);
        
        dataKegiatan[kegiatanKey][wilayah] = {
            labels,
            target,
            aktual
        };
    });
});

// ============================================
// APEXCHARTS INSTANCE
// ============================================
let chartInstance = null;

// Update statistics
function updateStats(targetData, aktualData) {
    const target = targetData[targetData.length - 1];
    const aktual = aktualData[aktualData.length - 1];
    const persentase = target > 0 ? Math.round((aktual / target) * 100) : 0;
    const selisih = aktual - target;
    
    document.getElementById('statTarget').textContent = target.toLocaleString('id-ID');
    document.getElementById('statAktual').textContent = aktual.toLocaleString('id-ID');
    document.getElementById('statPersentase').textContent = persentase + '%';
    document.getElementById('statSelisih').textContent = (selisih >= 0 ? '+' : '') + selisih.toLocaleString('id-ID');
    
    const persentaseEl = document.getElementById('statPersentase');
    const selisihEl = document.getElementById('statSelisih');
    
    if (persentase >= 90) {
        persentaseEl.className = 'text-lg font-bold text-[#2e7d32]';
    } else if (persentase >= 70) {
        persentaseEl.className = 'text-lg font-bold text-[#ef6c00]';
    } else {
        persentaseEl.className = 'text-lg font-bold text-[#c62828]';
    }
    
    if (selisih >= 0) {
        selisihEl.className = 'text-lg font-bold text-[#2e7d32]';
    } else {
        selisihEl.className = 'text-lg font-bold text-[#c62828]';
    }
}

// ============================================
// UPDATE CHART WITH RESPONSIVE SETTINGS
// ============================================
function updateChart() {
    const kegiatan = document.getElementById('filterKegiatan').value;
    const wilayah = document.getElementById('filterWilayah').value;
    
    const data = dataKegiatan[kegiatan][wilayah];
    const config = kegiatanConfig[kegiatan];
    
    updateStats(data.target, data.aktual);
    
    const isMobile = window.innerWidth < 640;
    const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
    
    const options = {
        series: [
            {
                name: 'Target (Kurva S)',
                data: data.target,
                type: 'area'
            },
            {
                name: 'Aktual (Kumulatif)',
                data: data.aktual,
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
                speed: 800,
                animateGradually: {
                    enabled: true,
                    delay: 150
                }
            },
            dropShadow: {
                enabled: !isMobile,
                top: 2,
                left: 0,
                blur: 4,
                opacity: 0.1,
                color: ['#1e88e5', '#e53935']
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
            colors: ['#1e88e5', '#e53935'],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: {
                size: isMobile ? 5 : 7,
                sizeOffset: 3
            }
        },
        xaxis: {
            categories: data.labels,
            title: {
                text: 'Periode September 2025',
                style: {
                    fontSize: isMobile ? '11px' : '12px',
                    fontWeight: 600,
                    color: '#4b5563'
                }
            },
            labels: {
                rotate: 0,
                rotateAlways: false,
                hideOverlappingLabels: true,
                trim: false,
                style: {
                    fontSize: isMobile ? '10px' : '11px',
                    colors: '#6b7280'
                },
                formatter: function(value) {
                    // value adalah angka tanggal (1-30)
                    if (isMobile) {
                        // Mobile: tampilkan setiap 5 hari
                        if (value === 1 || value === 5 || value === 10 || value === 15 || value === 20 || value === 25 || value === 30) {
                            return value;
                        }
                        return '';
                    } else if (isTablet) {
                        // Tablet: tampilkan setiap 3 hari
                        if (value % 3 === 0 || value === 1) {
                            return value;
                        }
                        return '';
                    } else {
                        // Desktop: tampilkan semua tanggal
                        return value;
                    }
                }
            },
            axisBorder: {
                show: true,
                color: '#e5e7eb',
                height: 1
            },
            axisTicks: {
                show: !isMobile,
                height: 4,
                color: '#e5e7eb'
            },
            crosshairs: {
                show: true,
                stroke: {
                    color: '#cbd5e0',
                    width: 1,
                    dashArray: 3
                }
            },
            tooltip: {
                enabled: false
            }
        },
        yaxis: {
            title: {
                text: isMobile ? '' : 'Jumlah Survei (Kumulatif)',
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
                    if (isMobile && value >= 1000) {
                        return (value / 1000).toFixed(1) + 'k';
                    }
                    return Math.round(value).toLocaleString('id-ID');
                }
            },
            tickAmount: isMobile ? 4 : 6
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: {
                formatter: function(value) {
                    return value.toLocaleString('id-ID');
                }
            },
            custom: function({ series, seriesIndex, dataPointIndex, w }) {
                const target = series[0][dataPointIndex];
                const aktual = series[1][dataPointIndex];
                const persentase = target > 0 ? ((aktual / target) * 100).toFixed(1) : 0;
                const selisih = aktual - target;
                const tanggal = data.labels[dataPointIndex];
                
                return `
                    <div class="px-3 py-2 bg-gray-900 text-white rounded-lg shadow-lg" style="min-width: ${isMobile ? '200px' : '220px'}">
                        <div class="font-semibold mb-1 text-${isMobile ? 'xs' : 'sm'}">${tanggal} September 2025</div>
                        <div class="text-xs space-y-1">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-[#1e88e5] rounded-full"></span>
                                <span>Target: ${target.toLocaleString('id-ID')}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 bg-[#e53935] rounded-full"></span>
                                <span>Aktual: ${aktual.toLocaleString('id-ID')}</span>
                            </div>
                            <div class="border-t border-gray-700 pt-1 mt-1">
                                <div>Pencapaian: ${persentase}%</div>
                                <div>Selisih: ${selisih >= 0 ? '+' : ''}${selisih.toLocaleString('id-ID')}</div>
                            </div>
                        </div>
                    </div>
                `;
            }
        },
        legend: {
            position: isMobile ? 'bottom' : 'top',
            horizontalAlign: isMobile ? 'center' : 'left',
            fontSize: isMobile ? '11px' : '13px',
            markers: {
                width: isMobile ? 10 : 12,
                height: isMobile ? 10 : 12,
                radius: 12
            },
            itemMargin: {
                horizontal: isMobile ? 8 : 10,
                vertical: isMobile ? 4 : 5
            }
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 3,
            padding: {
                left: isMobile ? 0 : 10,
                right: isMobile ? 0 : 10
            },
            xaxis: {
                lines: {
                    show: false
                }
            },
            yaxis: {
                lines: {
                    show: true
                }
            }
        },
        annotations: {
            xaxis: isMobile ? [] : [
                {
                    x: config.tanggalMulai,
                    borderColor: '#43a047',
                    strokeDashArray: 0,
                    label: {
                        text: 'Mulai',
                        style: {
                            color: '#fff',
                            background: '#43a047',
                            fontSize: '10px',
                            fontWeight: 600,
                            padding: {
                                left: 8,
                                right: 8,
                                top: 4,
                                bottom: 4
                            }
                        }
                    }
                },
                {
                    x: config.tanggalSelesai,
                    borderColor: '#e53935',
                    strokeDashArray: 0,
                    label: {
                        text: 'Target 100%',
                        style: {
                            color: '#fff',
                            background: '#e53935',
                            fontSize: '10px',
                            fontWeight: 600,
                            padding: {
                                left: 8,
                                right: 8,
                                top: 4,
                                bottom: 4
                            }
                        }
                    }
                }
            ]
        }
    };
    
    if (chartInstance) {
        chartInstance.destroy();
    }
    
    chartInstance = new ApexCharts(document.querySelector("#kegiatanChart"), options);
    chartInstance.render();
}

// ============================================
// WINDOW RESIZE LISTENER
// ============================================
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        updateChart();
    }, 250);
});

// ============================================
// INITIALIZE ON PAGE LOAD
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    updateChart();
    
    // Animate progress bars on load
    setTimeout(function() {
        const progressBars = document.querySelectorAll('.card .h-2.rounded-full[style*="width"]');
        progressBars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    }, 100);
});
</script>

<?= $this->endSection() ?>