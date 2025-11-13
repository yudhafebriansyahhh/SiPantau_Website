<?= $this->extend('layouts/pemantau_provinsi_layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
  <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
  <p class="text-gray-600 mt-1">Selamat datang di SiPantau - Sistem Pelaporan Kegiatan Lapangan</p>
</div>

<!-- Statistik -->
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

<!-- Kurva S dan Progres -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
  <div class="lg:col-span-2 card">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Kurva S - Target vs Realisasi</h3>
        <p class="text-sm text-gray-600">Progres Kumulatif Kegiatan</p>
      </div>

      <div class="flex flex-col sm:flex-row gap-2">
        <select id="filterKegiatanProses" class="input-field text-sm w-full sm:w-64">
          <option value="">-- Pilih Kegiatan --</option>
          <?php foreach ($kegiatanList as $item): ?>
            <option value="<?= esc($item['id_kegiatan_detail_proses']) ?>">
              <?= esc($item['nama_kegiatan_detail_proses']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <select id="filterKabupaten" class="input-field text-sm w-full sm:w-64" disabled>
          <option value="">-- Pilih Kabupaten (opsional) --</option>
        </select>
      </div>
    </div>

    <div id="chartLoadingState" class="flex justify-center items-center py-16">
      <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
        <p class="text-gray-600">Memuat data kurva S...</p>
      </div>
    </div>

    <div id="kurvaSProvChart" class="w-full" style="display: none; min-height: 320px;"></div>
    <div id="chartErrorState" class="text-center py-16" style="display: none;">
      <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
      <p class="text-gray-600">Gagal memuat data. Silakan refresh halaman.</p>
    </div>
    <div id="chartPlaceholder" class="flex justify-center items-center py-16" style="display: none;">
      <p class="text-gray-400">Pilih kegiatan untuk menampilkan kurva S</p>
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

<!-- Data Petugas -->
<div class="card">
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
    <div>
      <h3 class="text-lg font-semibold text-gray-900">Data Petugas</h3>
      <p class="text-sm text-gray-600 mt-1">Monitoring progres petugas lapangan</p>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full" id="petugasTable">
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
      <tbody class="divide-y divide-gray-100" id="petugasTableBody">
        <tr>
          <td colspan="3" class="px-4 py-12 text-center">
            <i class="fas fa-info-circle text-gray-300 text-4xl mb-2"></i>
            <p class="text-gray-500">Pilih kegiatan untuk menampilkan data petugas</p>
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
const defaultKegiatan = "<?= esc($latestKegiatanId) ?>";
const baseProv = "<?= base_url('pemantau-provinsi/kurva-provinsi') ?>";
const baseKab = "<?= base_url('pemantau-provinsi/kurva-kabupaten') ?>";
const baseWilayah = "<?= base_url('pemantau-provinsi/kegiatan-wilayah') ?>";
const basePetugas = "<?= base_url('pemantau-provinsi/get-petugas') ?>";

// Load Kabupaten Dropdown
async function loadKabupatenDropdown(idProses) {
  const kabSelect = document.getElementById("filterKabupaten");
  kabSelect.innerHTML = `<option value="">-- Pilih Kabupaten (opsional) --</option>`;
  kabSelect.disabled = true;
  
  if (!idProses) return;
  
  try {
    const res = await fetch(`${baseWilayah}?id_kegiatan_detail_proses=${idProses}`);
    const data = await res.json();
    
    if (data.length) {
      data.forEach(item => {
        const opt = document.createElement("option");
        opt.value = item.id_kegiatan_wilayah;
        opt.textContent = item.nama_kabupaten;
        kabSelect.appendChild(opt);
      });
      kabSelect.disabled = false;
    }
  } catch (e) {
    console.error('Error loading kabupaten:', e);
  }
}

// Load dan Render Kurva S
async function loadAndRenderKurvaS(idProses = "", idWilayah = "") {
  try {
    const url = idWilayah
      ? `${baseKab}?id_kegiatan_wilayah=${idWilayah}&nocache=${Date.now()}`
      : `${baseProv}?id_kegiatan_detail_proses=${idProses}&nocache=${Date.now()}`;

    document.getElementById('chartLoadingState').style.display = 'flex';
    document.getElementById('kurvaSProvChart').style.display = 'none';
    document.getElementById('chartErrorState').style.display = 'none';
    document.getElementById('chartPlaceholder').style.display = 'none';

    const response = await fetch(url, { cache: "no-store" });
    const data = await response.json();
    
    if (!data.labels.length) throw new Error('Empty data');

    await new Promise(res => setTimeout(res, 200));
    document.getElementById('chartLoadingState').style.display = 'none';
    document.getElementById('kurvaSProvChart').style.display = 'block';
    renderChart(data);
  } catch (e) {
    console.error(e);
    document.getElementById('chartLoadingState').style.display = 'none';
    document.getElementById('chartErrorState').style.display = 'block';
  }
}

// Render Chart
function renderChart(data) {
  if (chartInstance) { 
    chartInstance.destroy(); 
    chartInstance = null; 
  }
  
  const isMobile = window.innerWidth < 640;
  
  const options = {
    chart: { 
      type: 'area', 
      height: isMobile ? 300 : 420, 
      animations: { enabled: true, speed: 600 }, 
      toolbar: { show: !isMobile },
      fontFamily: 'Poppins, sans-serif'
    },
    series: [{ 
      name: 'Target Kumulatif', 
      data: data.targetAbsolut 
    }],
    xaxis: { 
      categories: data.labels, 
      title: { text: 'Tanggal', style: { fontSize: isMobile ? '11px' : '12px' } }, 
      labels: { 
        rotate: isMobile ? -45 : 0,
        style: { fontSize: isMobile ? '10px' : '11px' }
      } 
    },
    yaxis: { 
      title: { 
        text: isMobile ? '' : 'Target Kumulatif', 
        style: { fontSize: '12px' } 
      },
      labels: {
        style: { fontSize: isMobile ? '9px' : '11px' },
        formatter: value => Math.round(value).toLocaleString('id-ID')
      }
    },
    tooltip: {
      shared: true,
      custom: ({ dataPointIndex }) => `
        <div class="p-2">
          <strong>${data.labels[dataPointIndex]}</strong><br>
          Target Kumulatif: <b>${data.targetAbsolut[dataPointIndex].toLocaleString('id-ID')}</b><br>
          Target Harian: <b>${data.targetHarian[dataPointIndex].toLocaleString('id-ID')}</b><br>
          Persen Kumulatif: <b>${data.targetPersen[dataPointIndex].toFixed(2)}%</b>
        </div>`
    },
    colors: ['#1e88e5'],
    stroke: { curve: 'smooth', width: isMobile ? 2 : 3 },
    fill: { 
      type: 'gradient', 
      gradient: { 
        opacityFrom: 0.5, 
        opacityTo: 0.05 
      } 
    },
    dataLabels: { enabled: false },
    legend: {
      position: isMobile ? 'bottom' : 'top',
      horizontalAlign: isMobile ? 'center' : 'left',
      fontSize: isMobile ? '11px' : '13px'
    }
  };
  
  chartInstance = new ApexCharts(document.querySelector("#kurvaSProvChart"), options);
  chartInstance.render();
}

// Load Petugas
async function loadPetugas() {
  const kegiatanId = document.getElementById('filterKegiatanProses').value;
  const wilayahId = document.getElementById('filterKabupaten').value;
  
  if (!kegiatanId) {
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
  
  try {
    const response = await fetch(`${basePetugas}?id_kegiatan_detail_proses=${kegiatanId}&id_kegiatan_wilayah=${wilayahId || 'all'}`);
    const result = await response.json();
    
    if (result.success) {
      renderPetugasTable(result.data);
    }
  } catch (e) {
    console.error('Error loading petugas:', e);
  }
}

// Render Petugas Table
function renderPetugasTable(data) {
  const tbody = document.getElementById('petugasTableBody');
  
  if (data.length === 0) {
    tbody.innerHTML = `
      <tr>
        <td colspan="3" class="px-4 py-12 text-center">
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
      <td class="px-4 py-4">
        <div class="flex items-center">
          <div class="w-10 h-10 rounded-full flex items-center justify-center mr-3" 
               style="background-color: ${colors[index % colors.length]};">
            <span class="text-white text-sm font-medium">${p.nama_user.substring(0, 2).toUpperCase()}</span>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-900">${p.nama_user}</p>
            <p class="text-xs text-gray-500">${p.role} - ${p.nama_kabupaten}</p>
          </div>
        </div>
      </td>
      <td class="px-4 py-4">
        <span class="badge ${p.status_class}">${p.status}</span>
      </td>
      <td class="px-4 py-4">
        <div class="flex items-center">
          <div class="flex-1 mr-3">
            <div class="w-full bg-gray-200 rounded-full h-2">
              <div class="h-2 rounded-full transition-all duration-300" 
                   style="width: ${p.progress}%; background-color: ${colors[index % colors.length]};"></div>
            </div>
          </div>
          <span class="text-sm font-semibold text-gray-900 min-w-[2rem] text-right">${p.progress}</span>
        </div>
      </td>
    </tr>
  `).join('');
}

// Event Listeners
document.addEventListener("DOMContentLoaded", function() {
  if (defaultKegiatan) {
    document.getElementById('filterKegiatanProses').value = defaultKegiatan;
    loadAndRenderKurvaS(defaultKegiatan);
    loadKabupatenDropdown(defaultKegiatan);
    loadPetugas();
  } else {
    document.getElementById('chartLoadingState').style.display = 'none';
    document.getElementById('chartPlaceholder').style.display = 'flex';
  }

  document.getElementById('filterKegiatanProses').addEventListener('change', function() {
    const idProses = this.value;
    
    if (!idProses) {
      if (chartInstance) {
        chartInstance.destroy();
        chartInstance = null;
      }
      document.getElementById('chartLoadingState').style.display = 'none';
      document.getElementById('chartPlaceholder').style.display = 'flex';
      document.getElementById('filterKabupaten').disabled = true;
      document.getElementById('filterKabupaten').innerHTML = '<option value="">-- Pilih Kabupaten (opsional) --</option>';
      loadPetugas();
      return;
    }
    
    loadKabupatenDropdown(idProses);
    loadAndRenderKurvaS(idProses);
    loadPetugas();
  });

  document.getElementById('filterKabupaten').addEventListener('change', function() {
    const idKab = this.value;
    const idProses = document.getElementById('filterKegiatanProses').value;
    loadAndRenderKurvaS(idProses, idKab);
    loadPetugas();
  });

  // Animate progress bars
  setTimeout(() => {
    document.querySelectorAll('[data-width]').forEach(bar => {
      bar.style.width = bar.dataset.width + '%';
    });
  }, 400);
});

// Window resize handler
let resizeTimer;
window.addEventListener('resize', function() {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(function() {
    const idProses = document.getElementById('filterKegiatanProses').value;
    const idKab = document.getElementById('filterKabupaten').value;
    if (chartInstance && idProses) {
      loadAndRenderKurvaS(idProses, idKab);
    }
  }, 250);
});
</script>

<?= $this->endSection() ?>