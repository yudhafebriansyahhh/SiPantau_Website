<?= $this->extend('layouts/adminkab_layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
  <div class="flex items-center mb-2">
    <a href="<?= base_url('adminsurvei-kab/assign-petugas/detail/' . $pcl['id_pml']) ?>" 
       class="text-gray-600 hover:text-gray-900 mr-2">
      <i class="fas fa-arrow-left"></i> Kembali
    </a>
  </div>
  <h1 class="text-2xl font-bold text-gray-900">Detail Progress PCL</h1>
</div>

<!-- Info PCL -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
  <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
    <div>
      <p class="text-sm text-gray-500 mb-1">Nama PCL</p>
      <p class="text-base font-semibold text-gray-900"><?= esc($pcl['nama_pcl']) ?></p>
    </div>
    <div>
      <p class="text-sm text-gray-500 mb-1">PML</p>
      <p class="text-base font-semibold text-gray-900"><?= esc($pcl['nama_pml']) ?></p>
    </div>
    <div>
      <p class="text-sm text-gray-500 mb-1">Nama Survei</p>
      <p class="text-base font-semibold text-gray-900">
        <?= esc($pcl['nama_kegiatan']) ?> – <?= esc($pcl['nama_kegiatan_detail_proses']) ?>
      </p>
    </div>
    <div>
      <p class="text-sm text-gray-500 mb-1">Wilayah</p>
      <p class="text-base font-semibold text-gray-900"><?= esc($pcl['nama_kabupaten'] ?? '-') ?></p>
    </div>
  </div>
</div>

<!-- Statistik Target -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="flex items-center justify-between">
      <div>
        <p class="text-sm text-gray-600 mb-1">Target</p>
        <h3 class="text-3xl font-bold text-gray-900"><?= esc($pcl['target']) ?></h3>
      </div>
      <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center">
        <i class="fas fa-bullseye text-2xl text-blue-600"></i>
      </div>
    </div>
  </div>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <p class="text-sm text-gray-600 mb-1">Aktual</p>
    <h3 class="text-3xl font-bold text-gray-400">–</h3>
  </div>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <p class="text-sm text-gray-600 mb-1">Pencapaian</p>
    <h3 class="text-3xl font-bold text-gray-400">–</h3>
  </div>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <p class="text-sm text-gray-600 mb-1">Selisih</p>
    <h3 class="text-3xl font-bold text-gray-400">–</h3>
  </div>
</div>

<!-- Kurva S Chart -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
  <h3 class="text-lg font-semibold text-gray-900 mb-4">Kurva S – Target vs Aktual</h3>
  <div id="pclChart"></div>
</div>

<!-- Laporan Harian -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
  <h3 class="text-lg font-semibold text-gray-900 mb-4">Laporan Harian (Kurva Petugas)</h3>
  <div class="overflow-x-auto">
    <table class="w-full border-collapse">
      <thead>
        <tr class="bg-gray-50 border-b border-gray-200">
          <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Tanggal</th>
          <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Target Harian</th>
          <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Target Kumulatif</th>
          <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Pencapaian</th>
          <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Status</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-200">
        <?php if (empty($kurvaData)): ?>
          <tr><td colspan="5" class="text-center text-gray-500 py-4">Belum ada data kurva petugas</td></tr>
        <?php else: ?>
          <?php foreach ($kurvaData as $row): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 text-sm text-gray-900"><?= date('d M Y', strtotime($row['tanggal_target'])) ?></td>
              <td class="px-4 py-3 text-center text-sm text-gray-700"><?= esc($row['target_harian_absolut']) ?></td>
              <td class="px-4 py-3 text-center text-sm text-gray-700"><?= esc($row['target_kumulatif_absolut']) ?></td>
              <td class="px-4 py-3 text-center text-sm text-gray-400">–</td>
              <td class="px-4 py-3 text-center text-sm text-gray-400">–</td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- ApexCharts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>
<script>
const labels = <?= json_encode($labels) ?>;
const targetData = <?= json_encode($targetKumulatif) ?>;
const aktualData = <?= json_encode($aktualKumulatif) ?>;
const kurvaData = <?= json_encode($kurvaData) ?>;

const options = {
  series: [
    { name: '', data: targetData },
    { name: 'Aktual (Kumulatif)', data: aktualData }
  ],
  chart: { height: 400, type: 'area', fontFamily: 'Poppins, sans-serif' },
  colors: ['#1e88e5', '#e53935'],
  dataLabels: { enabled: false },
  stroke: { curve: 'smooth', width: 3 },
  xaxis: { categories: labels },
  yaxis: { title: { text: 'Jumlah (Kumulatif)' } },
  fill: { opacity: 0.4 },
  legend: { position: 'top', horizontalAlign: 'left' },
  tooltip: {
    shared: true,
    intersect: false,
    y: {
      formatter: function (val, { seriesIndex, dataPointIndex }) {
        const data = kurvaData[dataPointIndex] || {};
        const targetHarian = data.target_harian_absolut ?? '-';
        const persenKum = data.target_persen_kumulatif ?? '-';

        if (seriesIndex === 0) {
          return `
            <div style="
              font-size:12px; 
              line-height:1.5; 
              background:#f9fafb; 
              padding:6px 12px; 
              border-radius:6px;
              border:1px solid #e5e7eb;
              box-shadow:inset 0 0 3px rgba(0,0,0,0.05);
            ">
              <div style="font-weight:600; font-size:13px; color:#1e293b; margin-bottom:4px;">
                Target Kumulatif: <span style="float:right; color:#2563eb;">${val}</span>
              </div>
              <hr style="border:none; border-top:1px dashed #d1d5db; margin:4px 0;">
              <div style="display:flex; justify-content:space-between;">
                <span>🎯 Target Harian</span>
                <span style="font-weight:600; color:#0f172a;">${targetHarian}</span>
              </div>
              <div style="display:flex; justify-content:space-between;">
                <span>📈 Persen Kumulatif </span>
                <span style="font-weight:600; color:#0f172a;">${persenKum}%</span>
              </div>
            </div>`;
        } else {
          return `
            <div style="
              font-size:12px; 
              line-height:1.4;
              color:#0f172a;
              font-weight:600;
            ">
              Aktual Kumulatif: <span style="float:right;">${val}</span>
            </div>`;
        }
      }
    }
  }
};

new ApexCharts(document.querySelector("#pclChart"), options).render();
</script>



<?= $this->endSection() ?>
