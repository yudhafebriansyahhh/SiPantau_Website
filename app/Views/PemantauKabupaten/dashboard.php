<?= $this->extend('layouts/pemantau_kabupaten_layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard Pemantau Kabupaten</h1>
    <p class="text-gray-600 mt-1">Selamat datang di SiPantau - <?= esc($kabupaten['nama_kabupaten'] ?? 'Kabupaten') ?>
    </p>
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
                <p class="text-sm text-gray-600">Progres Kumulatif Kegiatan - <?= esc($kabupaten['nama_kabupaten']) ?>
                </p>
            </div>

            <div class="flex flex-col gap-2">
                <select id="filterKegiatan" class="input-field text-sm w-full sm:w-96">
                    <option value="">-- Pilih Kegiatan --</option>
                    <?php foreach ($kegiatanList as $item): ?>
                        <option value="<?= esc($item['id_kegiatan_wilayah']) ?>">
                            <?= esc($item['nama_kegiatan_detail_proses']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div id="chartLoadingState" class="flex justify-center items-center py-16">
            <div class="text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                <p class="text-gray-600">Memuat data kurva S...</p>
            </div>
        </div>

        <div id="kurvaKabChart" class="w-full" style="display: none; min-height: 320px;"></div>
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

<!-- Data Petugas dengan Tabs -->
<div class="card">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Data Petugas</h3>
            <p class="text-sm text-gray-600 mt-1">Monitoring progres petugas lapangan</p>
        </div>
    </div>

    <!-- Tabs Navigation Inside Card -->
    <div class="border-b border-gray-200 mb-6">
        <nav class="-mb-px flex flex-wrap gap-4 sm:gap-8" aria-label="Tabs">
            <button onclick="switchDataTab('petugas')" id="datatab-petugas"
                class="data-tab-button active whitespace-nowrap py-3 px-3 border-b-2 font-medium text-sm">
                <i class="fas fa-users mr-2"></i>Data Petugas
            </button>
            <button onclick="switchDataTab('kepatuhan')" id="datatab-kepatuhan"
                class="data-tab-button whitespace-nowrap py-3 px-3 border-b-2 font-medium text-sm text-gray-600">
                <i class="fas fa-chart-bar mr-2"></i>Tingkat Kepatuhan
            </button>
        </nav>
    </div>

    <!-- Tab Content: Data Petugas -->
    <div id="datacontent-petugas" class="data-tab-content">
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
                            <button onclick="toggleProgressSort()"
                                class="flex items-center gap-2 hover:text-blue-600 transition-colors">
                                <i class="fas fa-tasks"></i>
                                <span>Progress</span>
                                <i id="sortIcon" class="fas fa-sort text-gray-400"></i>
                            </button>
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

    <!-- Tab Content: Tingkat Kepatuhan -->
    <div id="datacontent-kepatuhan" class="data-tab-content hidden">
        <!-- Statistik Cards Kepatuhan -->
        <div id="kepatuhanStatsCards" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6"></div>

        <!-- Chart Section Kepatuhan -->
        <div class="mb-6">
            <h4 class="text-base font-semibold text-gray-900 mb-4">
                <i class="fas fa-chart-line mr-2"></i>Grafik Kepatuhan Harian
            </h4>
            <div id="kepatuhanChartContainer" class="relative">
                <div id="kepatuhanChart"></div>
                <div id="kepatuhanChartPlaceholder" class="flex justify-center items-center py-16">
                    <p class="text-gray-400">Pilih kegiatan untuk menampilkan grafik kepatuhan</p>
                </div>
            </div>
        </div>

        <!-- Content Grid: Leaderboard & Tidak Patuh -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Leaderboard Kepatuhan -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-semibold text-gray-900">
                        <i class="fas fa-trophy text-yellow-500 mr-2"></i>Leaderboard Kepatuhan
                    </h4>
                </div>
                <div id="leaderboardContainer" class="space-y-3 max-h-[600px] overflow-y-auto custom-scrollbar pr-2">
                    <p class="text-center text-gray-400 py-8">Pilih kegiatan untuk melihat data</p>
                </div>
            </div>

            <!-- Petugas Tidak Patuh -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h4 class="text-base font-semibold text-gray-900">
                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Petugas Tidak Patuh
                    </h4>
                </div>
                <div id="tidakPatuhContainer" class="space-y-3 max-h-[600px] overflow-y-auto custom-scrollbar pr-2">
                    <p class="text-center text-gray-400 py-8">Pilih kegiatan untuk melihat data</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ApexCharts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>
<script>
    let chartInstance = null;
    const defaultKegiatan = "<?= esc($latestKegiatanWilayahId) ?>";
    const baseKurva = "<?= base_url('pemantau-kabupaten/get-kurva-s-with-realisasi') ?>";
    const basePetugas = "<?= base_url('pemantau-kabupaten/get-petugas') ?>";

    let kepatuhanChartInstance = null;
    let currentKegiatanWilayahId = null;
    let currentSortOrder = 'none';
    let petugasDataCache = [];

    // Load dan Render Kurva S dengan Realisasi
    async function loadAndRenderKurvaS(idWilayah = "") {
        if (!idWilayah) {
            if (chartInstance) {
                chartInstance.destroy();
                chartInstance = null;
            }
            document.getElementById('chartLoadingState').style.display = 'none';
            document.getElementById('chartPlaceholder').style.display = 'flex';
            document.getElementById('kurvaKabChart').style.display = 'none';
            document.getElementById('chartErrorState').style.display = 'none';
            return;
        }

        try {
            const url = `${baseKurva}?id_kegiatan_wilayah=${idWilayah}&nocache=${Date.now()}`;

            document.getElementById('chartLoadingState').style.display = 'flex';
            document.getElementById('kurvaKabChart').style.display = 'none';
            document.getElementById('chartErrorState').style.display = 'none';
            document.getElementById('chartPlaceholder').style.display = 'none';

            const response = await fetch(url, { cache: "no-store" });
            const result = await response.json();

            if (!result.success) throw new Error(result.message || 'Failed to load data');

            await new Promise(res => setTimeout(res, 200));
            document.getElementById('chartLoadingState').style.display = 'none';
            document.getElementById('kurvaKabChart').style.display = 'block';
            renderChart(result.data);
        } catch (e) {
            console.error(e);
            document.getElementById('chartLoadingState').style.display = 'none';
            document.getElementById('chartErrorState').style.display = 'block';
        }
    }

    // Render Chart dengan Target dan Realisasi
    function renderChart(data) {
        if (chartInstance) {
            chartInstance.destroy();
            chartInstance = null;
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
                type: 'area',
                height: isMobile ? 300 : 420,
                fontFamily: 'Poppins, sans-serif',
                toolbar: {
                    show: true,
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
                        fill: { color: '#90CAF9', opacity: 0.4 },
                        stroke: { color: '#0D47A1', opacity: 0.4, width: 1 }
                    }
                },
                animations: { enabled: true, speed: 600 }
            },
            colors: ['#1e88e5', '#e53935'],
            dataLabels: { enabled: false },
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
                    opacityFrom: 0.5,
                    opacityTo: 0.05
                }
            },
            markers: { size: 0, hover: { size: isMobile ? 5 : 7 } },
            xaxis: {
                categories: data.labels,
                title: { text: 'Tanggal', style: { fontSize: isMobile ? '11px' : '12px' } },
                labels: {
                    rotate: isMobile ? -45 : 0,
                    style: { fontSize: isMobile ? '10px' : '11px' }
                }
            },
            yaxis: {
                title: { text: isMobile ? '' : 'Jumlah', style: { fontSize: '12px' } },
                labels: {
                    style: { fontSize: isMobile ? '9px' : '11px' },
                    formatter: value => Math.round(value).toLocaleString('id-ID')
                }
            },
            tooltip: {
                shared: true,
                intersect: false,
                y: {
                    formatter: value => value ? value.toLocaleString('id-ID') : '0'
                }
            },
            legend: {
                position: isMobile ? 'bottom' : 'top',
                horizontalAlign: isMobile ? 'center' : 'left',
                fontSize: isMobile ? '11px' : '13px'
            },
            grid: { borderColor: '#f3f4f6', strokeDashArray: 3 },
            annotations: {
                xaxis: isMobile ? [] : [
                    {
                        x: data.config.tanggal_mulai,
                        borderColor: '#43a047',
                        label: {
                            text: 'Mulai',
                            style: { color: '#fff', background: '#43a047', fontSize: '10px' }
                        }
                    },
                    {
                        x: data.config.tanggal_selesai,
                        borderColor: '#e53935',
                        label: {
                            text: 'Selesai',
                            style: { color: '#fff', background: '#e53935', fontSize: '10px' }
                        }
                    }
                ]
            }
        };

        chartInstance = new ApexCharts(document.querySelector("#kurvaKabChart"), options);
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
            petugasDataCache = [];
            return;
        }

        try {
            const response = await fetch(`${basePetugas}?id_kegiatan_wilayah=${kegiatanId}`);
            const result = await response.json();

            if (result.success) {
                petugasDataCache = result.data;
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
            <tr class="hover:bg-gray-50 transition-colors duration-150" data-progress="${p.progress}">
                <td class="px-4 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" 
                             style="background-color: ${colors[index % colors.length]};">
                            <span class="text-white text-sm font-semibold">${p.nama_user.substring(0, 2).toUpperCase()}</span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">${p.nama_user}</p>
                            <p class="text-xs text-gray-500 truncate">${p.role}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${p.status_kegiatan_class === 'badge-danger' ? 'bg-red-100 text-red-800' : ''} ${p.status_kegiatan_class === 'badge-success' ? 'bg-green-100 text-green-800' : ''} ${p.status_kegiatan_class === 'badge-warning' ? 'bg-yellow-100 text-yellow-800' : ''} ${p.status_kegiatan_class === 'badge-secondary' ? 'bg-gray-100 text-gray-700' : ''}">
                        ${p.status_kegiatan}
                    </span>
                </td>
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

        // Apply current sorting if active
        if (currentSortOrder !== 'none') {
            applySorting();
        }
    }

    // Toggle Progress Sort
    function toggleProgressSort() {
        const sortIcon = document.getElementById('sortIcon');

        if (currentSortOrder === 'none') {
            currentSortOrder = 'desc';
            sortIcon.className = 'fas fa-sort-down text-blue-600';
        } else if (currentSortOrder === 'desc') {
            currentSortOrder = 'asc';
            sortIcon.className = 'fas fa-sort-up text-blue-600';
        } else {
            currentSortOrder = 'none';
            sortIcon.className = 'fas fa-sort text-gray-400';
        }

        applySorting();
    }

    // Apply Sorting
    function applySorting() {
        const tbody = document.getElementById('petugasTableBody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        if (rows.length === 0 || rows[0].cells.length === 1) {
            return;
        }

        if (currentSortOrder === 'none') {
            renderPetugasTable(petugasDataCache);
            return;
        }

        rows.sort((a, b) => {
            const progressA = parseFloat(a.getAttribute('data-progress'));
            const progressB = parseFloat(b.getAttribute('data-progress'));

            if (currentSortOrder === 'asc') {
                return progressA - progressB;
            } else {
                return progressB - progressA;
            }
        });

        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));
    }

    // Switch Data Tab
    function switchDataTab(tabName) {
        const tabs = document.querySelectorAll('.data-tab-button');
        tabs.forEach(tab => {
            tab.classList.remove('active', 'border-[#1e88e5]', 'text-[#1e88e5]');
            tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        });

        const activeTab = document.getElementById(`datatab-${tabName}`);
        activeTab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        activeTab.classList.add('active', 'border-[#1e88e5]', 'text-[#1e88e5]');

        const contents = document.querySelectorAll('.data-tab-content');
        contents.forEach(content => content.classList.add('hidden'));
        document.getElementById(`datacontent-${tabName}`).classList.remove('hidden');

        if (tabName === 'kepatuhan') {
            const kegiatanId = document.getElementById('filterKegiatan').value;
            if (kegiatanId) {
                loadKepatuhanData(kegiatanId);
            }
        }
    }

    // Load Kepatuhan Data
    async function loadKepatuhanData(kegiatanWilayahId = null) {
        if (!kegiatanWilayahId) {
            kegiatanWilayahId = document.getElementById('filterKegiatan').value;
        }

        if (!kegiatanWilayahId) {
            resetKepatuhanDisplay();
            return;
        }

        // PENTING: Simpan ke variable global untuk validasi
        const requestKegiatanId = kegiatanWilayahId;

        try {
            const response = await fetch(
                `<?= base_url('pemantau-kabupaten/get-kepatuhan-data') ?>?id_kegiatan_wilayah=${kegiatanWilayahId}`
            );
            const result = await response.json();

            // VALIDASI: Pastikan filter belum berubah
            if (currentKegiatanWilayahId !== requestKegiatanId) {
                console.log('Kepatuhan filter changed, ignoring old data');
                return;
            }

            if (result.success) {
                if (result.data.stats.total_pcl === 0) {
                    showNoDataKepatuhan();
                    return;
                }

                const noDataMsg = document.querySelector('.no-data-kepatuhan-message');
                if (noDataMsg) noDataMsg.style.display = 'none';

                // PENTING: Tampilkan semua container
                document.getElementById('kepatuhanStatsCards').style.display = 'grid';
                document.getElementById('kepatuhanChartContainer').style.display = 'block';
                const leaderboardParent = document.getElementById('leaderboardContainer').parentElement.parentElement;
                leaderboardParent.style.display = 'grid';

                renderKepatuhanStats(result.data.stats);
                renderKepatuhanChart(result.data.chart);
                renderLeaderboard(result.data.leaderboard);
                renderTidakPatuh(result.data.tidak_patuh);
            } else {
                console.error('Error loading kepatuhan data:', result.message);
                showNoDataKepatuhan();
            }
        } catch (error) {
            console.error('Error loading kepatuhan data:', error);
            showNoDataKepatuhan();
        }
    }

    function showNoDataKepatuhan() {
        document.getElementById('leaderboardContainer').innerHTML = '<p class="text-center text-gray-400 py-8">Belum ada data</p>';
        document.getElementById('tidakPatuhContainer').innerHTML = '<p class="text-center text-gray-400 py-8">Belum ada data</p>';
        document.getElementById('kepatuhanStatsCards').innerHTML = '';

        if (kepatuhanChartInstance) {
            kepatuhanChartInstance.destroy();
            kepatuhanChartInstance = null;
        }

        document.getElementById('kepatuhanChartPlaceholder').style.display = 'flex';
        document.getElementById('kepatuhanChartPlaceholder').innerHTML = '<p class="text-gray-400">Pilih kegiatan untuk menampilkan grafik kepatuhan</p>';
        document.getElementById('kepatuhanStatsCards').style.display = 'none';
        document.getElementById('kepatuhanChartContainer').style.display = 'none';
        const leaderboardParent = document.getElementById('leaderboardContainer').parentElement.parentElement;
        leaderboardParent.style.display = 'none';

        const contentDiv = document.getElementById('datacontent-kepatuhan');
        let existingMessage = contentDiv.querySelector('.no-data-kepatuhan-message');

        if (!existingMessage) {
            const messageDiv = document.createElement('div');
            messageDiv.className = 'no-data-kepatuhan-message flex flex-col items-center justify-center py-20';
            messageDiv.innerHTML = `
            <i class="fas fa-inbox text-gray-300 text-6xl mb-4"></i>
            <p class="text-gray-500 text-lg">Belum ada data petugas</p>
        `;
            contentDiv.appendChild(messageDiv);
        } else {
            existingMessage.style.display = 'flex';
        }
    }

    function resetKepatuhanDisplay() {
        document.getElementById('kepatuhanStatsCards').innerHTML = '';
        document.getElementById('leaderboardContainer').innerHTML = '<p class="text-center text-gray-400 py-8">Pilih kegiatan untuk melihat data</p>';
        document.getElementById('tidakPatuhContainer').innerHTML = '<p class="text-center text-gray-400 py-8">Pilih kegiatan untuk melihat data</p>';
        if (kepatuhanChartInstance) {
            kepatuhanChartInstance.destroy();
            kepatuhanChartInstance = null;
        }
        document.getElementById('kepatuhanChartPlaceholder').style.display = 'flex';
        document.getElementById('kepatuhanChartPlaceholder').innerHTML = '<p class="text-gray-400">Pilih kegiatan untuk menampilkan grafik kepatuhan</p>';
    }

    function renderKepatuhanStats(stats) {
        const container = document.getElementById('kepatuhanStatsCards');
        container.style.display = 'grid';
        const persentasePatuh = stats.persentase_patuh || 0;
        const persentaseKurangPatuh = stats.persentase_kurang_patuh || 0;
        const persentaseTidakPatuh = stats.persentase_tidak_patuh || 0;
        const rataRata = stats.rata_rata_kepatuhan || 0;

        const statsHtml = `
        <div class="card hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Total PCL</p>
                    <h3 class="text-3xl font-bold text-gray-900">${stats.total_pcl || 0}</h3>
                </div>
                <div class="w-14 h-14 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-friends text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Patuh</p>
                    <h3 class="text-3xl font-bold text-gray-900">${stats.patuh || 0}</h3>
                    <p class="text-xs text-gray-500 mt-1">${persentasePatuh.toFixed(1)}% dari total</p>
                </div>
                <div class="w-14 h-14 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Kurang Patuh</p>
                    <h3 class="text-3xl font-bold text-gray-900">${stats.kurang_patuh || 0}</h3>
                    <p class="text-xs text-gray-500 mt-1">${persentaseKurangPatuh.toFixed(1)}% dari total</p>
                </div>
                <div class="w-14 h-14 bg-amber-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-exclamation-circle text-2xl text-amber-600"></i>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Tidak Patuh</p>
                    <h3 class="text-3xl font-bold text-gray-900">${stats.tidak_patuh || 0}</h3>
                    <p class="text-xs text-gray-500 mt-1">${persentaseTidakPatuh.toFixed(1)}% dari total</p>
                </div>
                <div class="w-14 h-14 bg-red-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
            </div>
        </div>
        <div class="card hover:shadow-lg transition-shadow duration-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Rata-rata</p>
                    <h3 class="text-3xl font-bold text-gray-900">${rataRata.toFixed(1)}%</h3>
                    <p class="text-xs text-gray-500 mt-1">Tingkat kepatuhan</p>
                </div>
                <div class="w-14 h-14 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-bar text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
    `;

        container.innerHTML = statsHtml;
    }

    function renderKepatuhanChart(chartData) {
        // PENTING: Pastikan container ditampilkan
        document.getElementById('kepatuhanChartContainer').style.display = 'block';
        document.getElementById('kepatuhanChartPlaceholder').style.display = 'none';

        if (kepatuhanChartInstance) {
            kepatuhanChartInstance.destroy();
        }

        // Validasi data chart
        if (!chartData || !chartData.data || chartData.data.length === 0) {
            document.getElementById('kepatuhanChartPlaceholder').style.display = 'flex';
            document.getElementById('kepatuhanChartPlaceholder').innerHTML = '<p class="text-gray-400">Belum ada data laporan</p>';
            return;
        }

        const isMobile = window.innerWidth < 640;

        const options = {
            series: [{
                name: 'Kepatuhan Harian',
                data: chartData.data.map(item => parseFloat(item.persentase) || 0)
            }],
            chart: {
                type: 'area',
                height: 380,
                fontFamily: 'Poppins, sans-serif',
                toolbar: {
                    show: true,
                    tools: {
                        download: true,
                        selection: true,
                        zoom: true,
                        zoomin: true,
                        zoomout: true,
                        pan: true,
                        reset: true
                    }
                },
                zoom: { enabled: true }
            },
            colors: ['#10b981'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2,
                    stops: [0, 100]
                }
            },
            xaxis: {
                categories: chartData.data.map(item => item.label || item.tanggal),
                labels: {
                    style: {
                        fontSize: isMobile ? '10px' : '11px'
                    },
                    rotate: isMobile ? -45 : 0
                }
            },
            yaxis: {
                max: 100,
                labels: {
                    formatter: val => val.toFixed(0) + '%'
                }
            },
            tooltip: {
                y: {
                    formatter: function (val, opts) {
                        const dataPoint = chartData.data[opts.dataPointIndex];
                        return val.toFixed(1) + '% (' + dataPoint.jumlah_lapor + '/' + dataPoint.total_pcl + ' PCL)';
                    }
                }
            },
            markers: { size: 4, hover: { size: 6 } },
            grid: { borderColor: '#f3f4f6' }
        };

        kepatuhanChartInstance = new ApexCharts(document.querySelector("#kepatuhanChart"), options);
        kepatuhanChartInstance.render();

        console.log('Chart rendered successfully with', chartData.data.length, 'data points');
    }

    function renderLeaderboard(data) {
        const container = document.getElementById('leaderboardContainer');
        if (!data || data.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-400 py-8">Belum ada data</p>';
            return;
        }
        const html = data.map((item, index) => {
            const rankColor = index === 0 ? 'bg-yellow-100 text-yellow-800' : index === 1 ? 'bg-gray-100 text-gray-700' : index === 2 ? 'bg-orange-100 text-orange-700' : 'bg-blue-50 text-blue-700';
            const persentase = parseFloat(item.persentase_kepatuhan || 0);
            const statusClass = persentase >= 80 ? 'bg-green-100 text-green-800' : persentase >= 50 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800';
            return `
            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-200 hover:shadow-sm transition-shadow">
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-8 h-8 rounded-full ${rankColor} flex items-center justify-center font-bold text-sm flex-shrink-0">${index + 1}</div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate">${item.nama_pcl || '-'}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold text-gray-900">${item.jumlah_laporan || 0} / ${item.total_hari_kerja || 0}</p>
                        <p class="text-xs text-gray-500">hari</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium ${statusClass}">${persentase.toFixed(1)}%</span>
                </div>
            </div>
        `;
        }).join('');
        container.innerHTML = html;
    }

    function renderTidakPatuh(data) {
        const container = document.getElementById('tidakPatuhContainer');
        if (!data || data.length === 0) {
            container.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-check-circle text-green-300 text-4xl mb-2"></i>
                <p class="text-gray-500">Semua petugas patuh!</p>
            </div>
        `;
            return;
        }

        const html = data.map(item => {
            const persentase = parseFloat(item.persentase_kepatuhan || 0);
            return `
            <div class="p-4 rounded-lg border-2 border-red-200 bg-red-50">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex-1">
                        <p class="font-medium text-gray-900">${item.nama_pcl || '-'}</p>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">${persentase.toFixed(1)}%</span>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center text-sm text-gray-700">
                        <i class="fas fa-calendar-times text-red-500 mr-2 w-4"></i>
                        <span>Terakhir lapor: ${item.terakhir_lapor || 'Belum pernah lapor'}</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-700">
                        <i class="fas fa-clock text-red-500 mr-2 w-4"></i>
                        <span>Tidak lapor: ${item.hari_tidak_lapor || 0} hari</span>
                    </div>
                    <div class="flex items-center text-sm text-gray-700">
                        <i class="fas fa-chart-line text-red-500 mr-2 w-4"></i>
                        <span>Laporan: ${item.jumlah_laporan || 0} / ${item.total_hari_kerja || 0} hari</span>
                    </div>
                </div>
            </div>
        `;
        }).join('');

        container.innerHTML = html;
    }

    // Event Listeners
    document.addEventListener("DOMContentLoaded", function () {
        if (defaultKegiatan) {
            document.getElementById('filterKegiatan').value = defaultKegiatan;
            currentKegiatanWilayahId = defaultKegiatan;
            loadAndRenderKurvaS(defaultKegiatan);
            loadPetugas();
        } else {
            document.getElementById('chartLoadingState').style.display = 'none';
            document.getElementById('chartPlaceholder').style.display = 'flex';
        }

        document.getElementById('filterKegiatan').addEventListener('change', function () {
            const idWilayah = this.value;
            currentKegiatanWilayahId = idWilayah;
            currentSortOrder = 'none';
            const sortIcon = document.getElementById('sortIcon');
            if (sortIcon) {
                sortIcon.className = 'fas fa-sort text-gray-400';
            }

            loadAndRenderKurvaS(idWilayah);
            loadPetugas();

            // PENTING: Load kepatuhan jika tab aktif
            const activeTab = document.querySelector('.data-tab-button.active');
            if (activeTab && activeTab.id === 'datatab-kepatuhan') {
                if (idWilayah) {
                    loadKepatuhanData(idWilayah);
                } else {
                    resetKepatuhanDisplay();
                }
            }
        });

        setTimeout(() => {
            document.querySelectorAll('[data-width]').forEach(bar => {
                bar.style.width = bar.dataset.width + '%';
            });
        }, 400);
    });

    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            const idWilayah = document.getElementById('filterKegiatan').value;
            if (chartInstance && idWilayah) {
                loadAndRenderKurvaS(idWilayah);
            }
        }, 250);
    });
</script>
<style>
    .data-tab-button {
        transition: all 0.2s ease;
        min-width: 140px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .data-tab-button.active {
        border-color: #1e88e5 !important;
        color: #1e88e5 !important;
    }

    #kepatuhanStatsCards {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
    }

    #kepatuhanStatsCards>div {
        transition: all 0.3s ease;
    }

    #kepatuhanStatsCards>div:hover {
        transform: translateY(-4px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 1024px) {
        #kepatuhanStatsCards {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 640px) {
        .data-tab-button {
            min-width: 120px;
            font-size: 0.813rem;
        }

        #kepatuhanStatsCards {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }

    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }

    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f1f1f1;
    }
</style>

<?= $this->endSection() ?>