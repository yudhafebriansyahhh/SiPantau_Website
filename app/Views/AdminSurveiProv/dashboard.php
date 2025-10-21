<?= $this->extend('layouts/adminprov_layout') ?>
<?= $this->section('content') ?>

<!-- =======================
 DASHBOARD ADMIN PROVINSI
======================= -->

<!-- Header -->
<div class="mb-6">
  <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
  <p class="text-gray-600 mt-1">
    Selamat datang di SiPantau – Sistem Pelaporan Kegiatan Lapangan
  </p>
</div>

<!-- Statistik -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
  <?php
    $stats = [
      ['Total Pengguna', '200', 'fas fa-users', 'bg-blue-50', '#1e88e5'],
      ['Total Kegiatan', '150', 'fas fa-clipboard-list', 'bg-green-50', '#43a047'],
      ['Kegiatan Aktif', '45', 'fas fa-chart-line', 'bg-orange-50', '#fb8c00'],
      ['Target Tercapai', '78%', 'fas fa-bullseye', 'bg-purple-50', '#8e24aa'],
    ];
    foreach ($stats as [$label, $value, $icon, $bg, $color]): ?>
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
  
  <!-- Grafik Kurva S -->
  <div class="lg:col-span-2 card">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Kurva S – Target Kumulatif Provinsi</h3>
        <p class="text-sm text-gray-600">Progres realisasi target provinsi</p>
      </div>
    </div>
    
    <!-- Loading State -->
    <div id="chartLoadingState" class="flex justify-center items-center py-16">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Memuat data kurva S...</p>
      </div>
    </div>
    
    <!-- Chart Container -->
    <div id="kurvaSProvChart" class="w-full" style="display: none;"></div>
    
    <!-- Error State -->
    <div id="chartErrorState" class="text-center py-16" style="display: none;">
      <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
      <p class="text-gray-600">Gagal memuat data. Silakan refresh halaman.</p>
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
          <div class="h-2 rounded-full transition-all duration-500" style="width: 0%; background-color: #1e88e5;" data-width="95"></div>
        </div>
      </div>

      <!-- SAKERNAS 2025 -->
      <div class="pb-4 border-b border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-900">SAKERNAS 2025</span>
          <span class="text-sm font-semibold" style="color: #43a047;">78%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="h-2 rounded-full transition-all duration-500" style="width: 0%; background-color: #43a047;" data-width="78"></div>
        </div>
      </div>

      <!-- SUSENAS 2025 -->
      <div class="pb-4 border-b border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-900">SUSENAS 2025</span>
          <span class="text-sm font-semibold" style="color: #fdd835;">62%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="h-2 rounded-full transition-all duration-500" style="width: 0%; background-color: #fdd835;" data-width="62"></div>
        </div>
      </div>

      <!-- SP2025 -->
      <div class="pb-4 border-b border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-900">SP2025</span>
          <span class="text-sm font-semibold" style="color: #8e24aa;">45%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="h-2 rounded-full transition-all duration-500" style="width: 0%; background-color: #8e24aa;" data-width="45"></div>
        </div>
      </div>

      <!-- VHTL 2025 -->
      <div class="pb-4 border-b border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-900">VHTL 2025</span>
          <span class="text-sm font-semibold" style="color: #e53935;">30%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="h-2 rounded-full transition-all duration-500" style="width: 0%; background-color: #e53935;" data-width="30"></div>
        </div>
      </div>

      <!-- SKDI 2025 -->
      <div class="pb-4">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-900">SKDI 2025</span>
          <span class="text-sm font-semibold" style="color: #5e35b1;">20%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="h-2 rounded-full transition-all duration-500" style="width: 0%; background-color: #5e35b1;" data-width="20"></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Tabel Petugas -->
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
        <option>March</option>
      </select>
      <select class="input-field w-auto text-sm">
        <option>Kegiatan</option>
        <option>SUNSENAS 2025</option>
        <option>SAKERNAS 2025</option>
      </select>
      <select class="input-field w-auto text-sm">
        <option>Kabupaten/Kota</option>
        <option>Kota Pekanbaru</option>
        <option>Kab. Kampar</option>
      </select>
      <select class="input-field w-auto text-sm">
        <option>Status</option>
        <option>Sudah Lapor</option>
        <option>Belum Lapor</option>
      </select>
    </div>
  </div>

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
                <p class="text-sm font-medium text-gray-900">George Martin</p>
                <p class="text-xs text-gray-500">PCL – Pekanbaru</p>
              </div>
            </div>
          </td>
          <td class="px-4 py-4"><span class="badge badge-warning">Belum Lapor</span></td>
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
                <p class="text-sm font-medium text-gray-900">Markus Suzak</p>
                <p class="text-xs text-gray-500">PCL – Kampar</p>
              </div>
            </div>
          </td>
          <td class="px-4 py-4"><span class="badge badge-success">Sudah Lapor</span></td>
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
                <p class="text-sm font-medium text-gray-900">Ankur Warikoo</p>
                <p class="text-xs text-gray-500">PCL – Dumai</p>
              </div>
            </div>
          </td>
          <td class="px-4 py-4"><span class="badge badge-success">Sudah Lapor</span></td>
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

<!-- ApexCharts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>

<script>
let chartInstance = null;

// ============================================
// LOAD DAN RENDER KURVA S
// ============================================
async function loadAndRenderKurvaS() {
  try {
    // Fetch data dari endpoint
    const response = await fetch("<?= base_url('adminsurvei/kurva-provinsi') ?>");
    
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    
    const data = await response.json();
    
    // Validasi data
    if (!data.labels || !data.targetAbsolut || data.labels.length === 0) {
      throw new Error('Data tidak valid atau kosong');
    }
    
    // Sembunyikan loading state
    document.getElementById('chartLoadingState').style.display = 'none';
    document.getElementById('kurvaSProvChart').style.display = 'block';
    
    // Render chart
    renderChart(data);
    
  } catch (error) {
    console.error('Error loading kurva S:', error);
    
    // Tampilkan error state
    document.getElementById('chartLoadingState').style.display = 'none';
    document.getElementById('chartErrorState').style.display = 'block';
  }
}

// ============================================
// RENDER CHART DENGAN DATA REAL
// ============================================
function renderChart(data) {
  const isMobile = window.innerWidth < 640;
  const isTablet = window.innerWidth >= 640 && window.innerWidth < 1024;
  
  const options = {
    series: [
      { 
        name: "Target Kumulatif (Absolut)", 
        data: data.targetAbsolut,
        type: 'area'
      }
    ],
    chart: {
      type: "area",
      height: isMobile ? 300 : 400,
      fontFamily: "Poppins, sans-serif",
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
        color: ['#1e88e5']
      }
    },
    colors: ["#1e88e5"],
    stroke: { 
      curve: "smooth", 
      width: isMobile ? 2 : 3 
    },
    fill: {
      type: "gradient",
      gradient: {
        shade: 'light',
        type: 'vertical',
        shadeIntensity: 0.3,
        opacityFrom: 0.5,
        opacityTo: 0.1,
        stops: [0, 100],
      },
    },
    dataLabels: { enabled: false },
    markers: {
      size: 0,
      colors: ['#1e88e5'],
      strokeColors: '#fff',
      strokeWidth: 2,
      hover: {
        size: isMobile ? 5 : 7,
        sizeOffset: 3
      }
    },
    grid: { 
      borderColor: "#e5e7eb", 
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
    xaxis: {
      categories: data.labels,
      title: { 
        text: "Tanggal", 
        style: { 
          fontSize: isMobile ? "11px" : "12px", 
          fontWeight: 600,
          color: '#4b5563'
        } 
      },
      labels: { 
        rotate: isMobile ? -45 : 0,
        rotateAlways: false,
        hideOverlappingLabels: true,
        trim: false,
        style: {
          fontSize: isMobile ? "10px" : "11px",
          colors: '#6b7280'
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
      }
    },
    yaxis: {
      title: { 
        text: isMobile ? "" : "Target Kumulatif", 
        style: { 
          fontSize: "12px", 
          fontWeight: 600,
          color: '#4b5563'
        } 
      },
      labels: { 
        style: {
          fontSize: isMobile ? "9px" : "11px",
          colors: '#6b7280'
        },
        formatter: (val) => {
          if (isMobile && val >= 1000) {
            return (val / 1000).toFixed(1) + 'k';
          }
          return Math.round(val).toLocaleString("id-ID");
        }
      },
      tickAmount: isMobile ? 4 : 6
    },
    tooltip: {
      shared: true,
      intersect: false,
      y: { 
        formatter: (val) => val.toLocaleString("id-ID") + " target"
      },
      custom: function({ series, seriesIndex, dataPointIndex, w }) {
        const target = series[0][dataPointIndex];
        const tanggal = data.labels[dataPointIndex];
        const persen = data.targetPersen ? data.targetPersen[dataPointIndex] : null;
        
        return `
          <div class="px-3 py-2 bg-gray-900 text-white rounded-lg shadow-lg" style="min-width: ${isMobile ? '180px' : '200px'}">
            <div class="font-semibold mb-1 text-${isMobile ? 'xs' : 'sm'}">${tanggal}</div>
            <div class="text-xs space-y-1">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 bg-[#1e88e5] rounded-full"></span>
                <span>Target: ${target.toLocaleString('id-ID')}</span>
              </div>
              ${persen !== null ? `
              <div class="border-t border-gray-700 pt-1 mt-1">
                <div>Persentase: ${persen}%</div>
              </div>
              ` : ''}
            </div>
          </div>
        `;
      }
    },
    legend: {
      position: isMobile ? 'bottom' : 'top',
      horizontalAlign: 'left',
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
    }
  };

  // Destroy existing chart if any
  if (chartInstance) {
    chartInstance.destroy();
  }

  // Create new chart
  chartInstance = new ApexCharts(document.querySelector("#kurvaSProvChart"), options);
  chartInstance.render();
}

// ============================================
// RESPONSIVE CHART - RESIZE HANDLER
// ============================================
let resizeTimer;
window.addEventListener('resize', function() {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(async function() {
    if (chartInstance) {
      // Reload chart dengan ukuran baru
      try {
        const response = await fetch("<?= base_url('adminsurvei/kurva-provinsi') ?>");
        const data = await response.json();
        renderChart(data);
      } catch (error) {
        console.error('Error reloading chart on resize:', error);
      }
    }
  }, 250);
});

// ============================================
// INITIALIZE ON PAGE LOAD
// ============================================
document.addEventListener("DOMContentLoaded", function() {
  // Load kurva S data
  loadAndRenderKurvaS();
  
  // Animate progress bars
  setTimeout(function() {
    const progressBars = document.querySelectorAll('.bg-yellow-500, .bg-green-600');
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