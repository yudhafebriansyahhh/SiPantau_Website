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
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_pengguna']) ?></h3>
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
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['total_kegiatan']) ?></h3>
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
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['kegiatan_aktif']) ?></h3>
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
                <h3 class="text-3xl font-bold text-gray-900"><?= $stats['target_tercapai'] ?>%</h3>
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
                <h3 class="text-lg font-semibold text-gray-900">Kurva S - Target vs Realisasi</h3>
                <p class="text-sm text-gray-600">Progres Kumulatif Kegiatan</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <select id="filterKegiatan" class="input-field w-full sm:w-auto text-sm" onchange="loadKegiatanWilayah()">
                    <option value="">-- Pilih Kegiatan --</option>
                    <?php foreach ($kegiatanDetailProses as $proses): ?>
                        <option value="<?= $proses['id_kegiatan_detail_proses'] ?>">
                            <?= esc($proses['nama_kegiatan_detail_proses']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <select id="filterWilayah" class="input-field w-full sm:w-auto text-sm" onchange="updateChart()" disabled>
                    <option value="all">Semua Wilayah</option>
                </select>
            </div>
        </div>        
        
        <!-- Chart Container -->
        <div class="relative -mx-2 sm:mx-0">
            <div id="kegiatanChart" class="w-full"></div>
            <div id="chartPlaceholder" class="flex justify-center items-center py-16">
                <p class="text-gray-400">Pilih kegiatan untuk menampilkan kurva S</p>
            </div>
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
                            <span class="text-sm font-semibold" style="color: <?= $prog['color'] ?>;"><?= $prog['progress'] ?>%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all duration-500" 
                                 style="width: <?= $prog['progress'] ?>%; background-color: <?= $prog['color'] ?>;"></div>
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

// Load kegiatan wilayah ketika kegiatan dipilih
async function loadKegiatanWilayah() {
    const kegiatanId = document.getElementById('filterKegiatan').value;
    const wilayahSelect = document.getElementById('filterWilayah');
    const chartPlaceholder = document.getElementById('chartPlaceholder');
    
    if (!kegiatanId) {
        wilayahSelect.disabled = true;
        wilayahSelect.innerHTML = '<option value="all">Semua Wilayah</option>';
        
        // Reset chart
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
        }
        
        // Show placeholder
        chartPlaceholder.style.display = 'flex';
        
        // Reset petugas table
        document.getElementById('petugasTableBody').innerHTML = `
            <tr>
                <td colspan="3" class="px-4 py-12 text-center">
                    <i class="fas fa-info-circle text-gray-300 text-4xl mb-2"></i>
                    <p class="text-gray-500">Pilih kegiatan untuk menampilkan data petugas</p>
                </td>
            </tr>
        `;
        
        return;
    }
    
    // Hide placeholder immediately when kegiatan selected
    chartPlaceholder.style.display = 'none';
    
    const response = await fetch(`<?= base_url('superadmin/get-kegiatan-wilayah') ?>?id_kegiatan_detail_proses=${kegiatanId}`);
    const result = await response.json();
    
    if (result.success) {
        wilayahSelect.innerHTML = '<option value="all">Semua Wilayah</option>';
        result.data.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id_kegiatan_wilayah;
            option.textContent = item.nama_kabupaten;
            wilayahSelect.appendChild(option);
        });
        wilayahSelect.disabled = false;
        
        // Load chart
        updateChart();
        
        // Load petugas
        loadPetugas();
    }
}

// Update chart
async function updateChart() {
    const kegiatanId = document.getElementById('filterKegiatan').value;
    const wilayahId = document.getElementById('filterWilayah').value;
    const chartPlaceholder = document.getElementById('chartPlaceholder');
    
    if (!kegiatanId) return;
    
    const response = await fetch(`<?= base_url('superadmin/get-kurva-s') ?>?id_kegiatan_detail_proses=${kegiatanId}&id_kegiatan_wilayah=${wilayahId}`);
    const result = await response.json();
    
    if (result.success) {
        chartPlaceholder.style.display = 'none';
        renderChart(result.data);
        loadPetugas(); // Reload petugas when filter changes
    }
}

// Render chart with zoom functionality
function renderChart(data) {
    if (chartInstance) {
        chartInstance.destroy();
    }
    
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
                show: true,
                offsetX: 0,
                offsetY: 0,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                },
                autoSelected: 'zoom'
            },
            zoom: {
                enabled: true,
                type: 'x',
                autoScaleYaxis: true,
                zoomedArea: {
                    fill: {
                        color: '#90CAF9',
                        opacity: 0.4
                    },
                    stroke: {
                        color: '#0D47A1',
                        opacity: 0.4,
                        width: 1
                    }
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
                text: 'Periode',
                style: {
                    fontSize: isMobile ? '11px' : '12px',
                    fontWeight: 600
                }
            },
            labels: {
                rotate: 0,
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

// Load petugas
async function loadPetugas() {
    const kegiatanId = document.getElementById('filterKegiatan').value;
    const wilayahId = document.getElementById('filterWilayah').value;
    
    if (!kegiatanId) return;
    
    const response = await fetch(`<?= base_url('superadmin/get-petugas') ?>?id_kegiatan_detail_proses=${kegiatanId}&id_kegiatan_wilayah=${wilayahId}`);
    const result = await response.json();
    
    if (result.success) {
        renderPetugasTable(result.data);
    }
}

// Render petugas table
function renderPetugasTable(data) {
  const tbody = document.getElementById('petugasTableBody');
  
  if (data.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="4" class="px-4 py-12 text-center">
          <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
          <p class="text-gray-500">Belum ada data petugas</p>
        </td>
      </tr>
    `;
    return;
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
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${p.status_kegiatan_class === 'badge-danger' ? 'bg-red-100 text-red-800' : ''} ${p.status_kegiatan_class === 'badge-success' ? 'bg-green-100 text-green-800' : ''} ${p.status_kegiatan_class === 'badge-warning' ? 'bg-yellow-100 text-yellow-800' : ''} ${p.status_kegiatan_class === 'badge-secondary' ? 'bg-gray-100 text-gray-700' : ''}">
          ${p.status_kegiatan}
        </span>
      </td>
      
      <!-- Status Harian -->
      <td class="px-4 py-4">
        <div class="flex flex-col gap-1 items-start">
          <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${p.status_harian_class === 'badge-danger' ? 'bg-red-100 text-red-800' : ''} ${p.status_harian_class === 'badge-success' ? 'bg-green-100 text-green-800' : ''} ${p.status_harian_class === 'badge-warning' ? 'bg-yellow-100 text-yellow-800' : ''} ${p.status_harian_class === 'badge-info' ? 'bg-blue-100 text-blue-800' : ''} ${p.status_harian_class === 'badge-secondary' ? 'bg-gray-100 text-gray-700' : ''}">
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

// Enhanced resize handler with debounce
let resizeTimer;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        if (chartInstance) {
            const kegiatanId = document.getElementById('filterKegiatan').value;
            if (kegiatanId) {
                updateChart();
            }
        }
    }, 250);
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
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
// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Animate progress bars
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