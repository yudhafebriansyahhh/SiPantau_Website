<?= $this->extend('layouts/adminprov_layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
  <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
  <p class="text-gray-600 mt-1">Selamat datang di SiPantau - Sistem Pelaporan Kegiatan Lapangan</p>
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

<!-- Kurva S dan Progres -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
  <div class="lg:col-span-2 card">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
      <div>
        <h3 class="text-lg font-semibold text-gray-900">Kurva S – Target Kumulatif</h3>
        <p class="text-sm text-gray-600">Pilih kegiatan dan kabupaten untuk melihat grafik</p>
      </div>

      <div class="flex flex-col sm:flex-row gap-2">
        <!-- Dropdown Kegiatan -->
        <select id="filterKegiatanProses" class="input-field text-sm w-full sm:w-64">
          <option value="">-- Semua Kegiatan --</option>
          <?php foreach ($kegiatanList as $item): ?>
            <option value="<?= esc($item['id_kegiatan_detail_proses']) ?>">
              <?= esc($item['nama_kegiatan_detail_proses']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <!-- Dropdown Kabupaten -->
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
  </div>

  <!-- Dummy progress -->
  <div class="card">
    <h3 class="text-lg font-semibold text-gray-900 mb-6">Progres Kegiatan Sedang Berjalan</h3>
    <?php
    $progressData = [
      ['SUNSENAS 2025', 95, '#1e88e5'],
      ['SAKERNAS 2025', 78, '#43a047'],
      ['SUSENAS 2025', 62, '#fdd835'],
      ['SP2025', 45, '#8e24aa'],
    ];
    foreach ($progressData as [$name, $percent, $color]): ?>
      <div class="pb-4 border-b border-gray-100">
        <div class="flex items-center justify-between mb-2">
          <span class="text-sm font-medium text-gray-900"><?= $name ?></span>
          <span class="text-sm font-semibold" style="color: <?= $color ?>;"><?= $percent ?>%</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
          <div class="h-2 rounded-full transition-all duration-500"
            style="width: 0%; background-color: <?= $color ?>;" data-width="<?= $percent ?>"></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

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

<!-- ========================= -->
<!-- ✅ SCRIPT APEXCHARTS -->
<!-- ========================= -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>
<script>
let chartInstance = null;
const defaultKegiatan = "<?= esc($latestKegiatanId) ?>";
const baseProv = "<?= base_url('adminsurvei/kurva-provinsi') ?>";
const baseKab = "<?= base_url('adminsurvei/kurva-kabupaten') ?>";
const baseWilayah = "<?= base_url('adminsurvei/kegiatan-wilayah') ?>";

async function loadKabupatenDropdown(idProses) {
  const kabSelect = document.getElementById("filterKabupaten");
  kabSelect.innerHTML = `<option value="">-- Pilih Kabupaten (opsional) --</option>`;
  kabSelect.disabled = true;
  if (!idProses) return;
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
}

async function loadAndRenderKurvaS(idProses = "", idWilayah = "") {
  try {
    const url = idWilayah
      ? `${baseKab}?id_kegiatan_wilayah=${idWilayah}&nocache=${Date.now()}`
      : `${baseProv}?id_kegiatan_detail_proses=${idProses}&nocache=${Date.now()}`;

    document.getElementById('chartLoadingState').style.display = 'flex';
    document.getElementById('kurvaSProvChart').style.display = 'none';
    document.getElementById('chartErrorState').style.display = 'none';

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

function renderChart(data) {
  if (chartInstance) { chartInstance.destroy(); chartInstance = null; }
  const options = {
    chart: { type: 'area', height: 420, animations: { enabled: true, speed: 600 }, toolbar: { show: false } },
    series: [{ name: 'Target Kumulatif Absolut', data: data.targetAbsolut }],
    xaxis: { categories: data.labels, title: { text: 'Tanggal' }, labels: { rotate: -45 } },
    yaxis: { title: { text: 'Target Kumulatif Absolut' } },
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
    stroke: { curve: 'smooth', width: 3 },
    fill: { type: 'gradient', gradient: { opacityFrom: 0.5, opacityTo: 0.05 } },
    dataLabels: { enabled: false }
  };
  chartInstance = new ApexCharts(document.querySelector("#kurvaSProvChart"), options);
  chartInstance.render();
}

document.addEventListener("DOMContentLoaded", function() {
  loadAndRenderKurvaS(defaultKegiatan);

  document.getElementById('filterKegiatanProses').addEventListener('change', function() {
    const idProses = this.value || defaultKegiatan;
    loadKabupatenDropdown(idProses);
    loadAndRenderKurvaS(idProses);
  });

  document.getElementById('filterKabupaten').addEventListener('change', function() {
    const idKab = this.value;
    const idProses = document.getElementById('filterKegiatanProses').value || defaultKegiatan;
    loadAndRenderKurvaS(idProses, idKab);
  });

  setTimeout(() => {
    document.querySelectorAll('[data-width]').forEach(bar => {
      bar.style.width = bar.dataset.width + '%';
    });
  }, 400);
});
</script>

<?= $this->endSection() ?>
