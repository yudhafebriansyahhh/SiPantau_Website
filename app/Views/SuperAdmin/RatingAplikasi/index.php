<?= $this->extend('layouts/sadmin_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Rating Aplikasi</h1>
            <p class="text-gray-600 mt-1">Lihat feedback dan rating dari pengguna aplikasi</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="<?= base_url('superadmin/rating-aplikasi/export-csv?' . http_build_query($filters)) ?>" 
               class="btn-primary">
                <i class="fas fa-download mr-2"></i>
                Export CSV
            </a>
        </div>
    </div>
</div>

<!-- Alert Messages -->
<?php if (session()->getFlashdata('success')): ?>
<div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
    <div class="flex items-center">
        <i class="fas fa-check-circle text-green-500 mr-3"></i>
        <p class="text-green-700"><?= session()->getFlashdata('success') ?></p>
    </div>
</div>
<?php endif; ?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Feedback -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Rating</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['total']) ?></h3>
                <p class="text-xs text-gray-500 mt-1">Bulan ini: <?= number_format($stats['bulan_ini']) ?></p>
            </div>
            <div class="w-14 h-14 bg-blue-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-comments text-2xl text-blue-600"></i>
            </div>
        </div>
    </div>

    <!-- Average Rating -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Rata-rata Rating</p>
                <div class="flex items-baseline gap-2">
                    <h3 class="text-3xl font-bold text-gray-900"><?= $stats['avg_rating'] ?></h3>
                    <span class="text-yellow-500">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star<?= $i <= $stats['avg_rating'] ? '' : '-o' ?>"></i>
                        <?php endfor; ?>
                    </span>
                </div>
            </div>
            <div class="w-14 h-14 bg-yellow-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-star text-2xl text-yellow-500"></i>
            </div>
        </div>
    </div>

    <!-- Hari Ini -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Rating Hari Ini</p>
                <h3 class="text-3xl font-bold text-gray-900"><?= number_format($stats['hari_ini']) ?></h3>
                <p class="text-xs text-gray-500 mt-1">Minggu ini: <?= number_format($stats['minggu_ini']) ?></p>
            </div>
            <div class="w-14 h-14 bg-green-50 rounded-lg flex items-center justify-center">
                <i class="fas fa-calendar-day text-2xl text-green-600"></i>
            </div>
        </div>
    </div>

    <!-- Rating Distribution -->
    <div class="card hover:shadow-md transition-shadow duration-200">
        <p class="text-sm text-gray-600 mb-3">Distribusi Rating</p>
        <div class="space-y-2">
            <?php 
            $ratingMap = array_column($stats['rating_distribution'], 'jumlah', 'rating');
            for($i = 5; $i >= 1; $i--): 
                $count = $ratingMap[$i] ?? 0;
                $percentage = $stats['total'] > 0 ? ($count / $stats['total']) * 100 : 0;
            ?>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-600 w-6"><?= $i ?>★</span>
                <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full transition-all duration-500" 
                         style="width: <?= $percentage ?>%"></div>
                </div>
                <span class="text-xs text-gray-600 w-8 text-right"><?= $count ?></span>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Charts Section - UPDATED -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <!-- Rating Distribution Bar Chart -->
    <div class="card">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Distribusi Rating</h3>
        <div id="distributionChart" style="min-height: 350px;"></div>
    </div>

    <!-- Rating Trend Line Chart -->
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">Trend Rating Kumulatif</h3>
            <div class="flex gap-2">
                <button onclick="changeTrendView('month')" 
                        class="trend-btn px-3 py-1 text-xs rounded-lg border border-gray-300 bg-blue-50 border-blue-300 text-blue-600"
                        data-view="month">
                    Bulan
                </button>
                <button onclick="changeTrendView('year')" 
                        class="trend-btn px-3 py-1 text-xs rounded-lg border border-gray-300 hover:bg-gray-100 transition-colors"
                        data-view="year">
                    Tahun
                </button>
            </div>
        </div>
        <div id="trendChart" style="min-height: 350px;"></div>
    </div>
</div>

<!-- Latest Ratings -->
<div class="card mb-8">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Rating Terbaru</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
        <?php if (empty($latestFeedbacks)): ?>
            <div class="col-span-full text-center py-8">
                <p class="text-gray-500">Belum ada rating</p>
            </div>
        <?php else: ?>
            <?php foreach ($latestFeedbacks as $fb): ?>
            <div class="flex gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <?php
                    $nameParts = explode(' ', $fb['nama_user']);
                    $initials = count($nameParts) >= 2 
                        ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                        : strtoupper(substr($fb['nama_user'], 0, 2));
                    ?>
                    <span class="text-white text-sm font-semibold"><?= $initials ?></span>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-sm font-medium text-gray-900 truncate"><?= esc($fb['nama_user']) ?></p>
                        <span class="text-yellow-500 text-sm">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star<?= $i <= $fb['rating'] ? '' : '-o' ?>"></i>
                            <?php endfor; ?>
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 line-clamp-1"><?= esc($fb['feedback']) ?></p>
                    <p class="text-xs text-gray-400 mt-1"><?= date('d M Y H:i', strtotime($fb['created_at'])) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Filters & Table -->
<div class="card">
    <!-- Filters -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row gap-3 items-start sm:items-center">
            <!-- Search -->
            <div class="relative flex-1 w-full sm:w-auto">
                <input type="text" 
                       id="searchInput"
                       placeholder="Nama user atau feedback..." 
                       class="input-field pl-10 w-full"
                       onkeyup="applyFilters()">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>

            <!-- Filter Rating -->
            <div class="w-full sm:w-auto sm:min-w-[180px]">
                <select id="ratingFilter" class="input-field w-full" onchange="applyFilters()">
                    <option value="">Semua Rating</option>
                    <?php for($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>">
                            <?= $i ?> Bintang
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <!-- Filter Kabupaten -->
            <div class="w-full sm:w-auto sm:min-w-[200px]">
                <select id="kabupatenFilter" class="input-field w-full" onchange="applyFilters()">
                    <option value="">Semua Kabupaten</option>
                    <?php foreach ($kabupatens as $kab): ?>
                        <option value="<?= $kab['id_kabupaten'] ?>">
                            <?= esc($kab['nama_kabupaten']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Dari Tanggal -->
            <div class="w-full sm:w-auto sm:min-w-[160px]">
                <input type="date" 
                       id="dateFromFilter"
                       placeholder="Dari Tanggal"
                       class="input-field w-full"
                       onchange="applyFilters()">
            </div>

            <!-- Sampai Tanggal -->
            <div class="w-full sm:w-auto sm:min-w-[160px]">
                <input type="date" 
                       id="dateToFilter"
                       placeholder="Sampai Tanggal"
                       class="input-field w-full"
                       onchange="applyFilters()">
            </div>

            <!-- Reset Button -->
            <div class="w-full sm:w-auto">
                <button onclick="resetFilters()" 
                   class="btn-secondary inline-flex items-center justify-center w-full sm:w-auto whitespace-nowrap">
                    <i class="fas fa-redo mr-2"></i>Reset
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="ratingTable">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        No
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        User
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        Rating
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        Feedback
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        Tanggal
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider border-r border-gray-200">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="tableBody">
                <?php if (empty($feedbacks)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">Belum ada rating</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($feedbacks as $index => $fb): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150" 
                            data-search="<?= strtolower(esc($fb['nama_user']) . ' ' . esc($fb['feedback']) . ' ' . esc($fb['nama_kabupaten'])) ?>"
                            data-rating="<?= $fb['rating'] ?>"
                            data-kabupaten="<?= $fb['id_kabupaten'] ?? '' ?>"
                            data-date="<?= date('Y-m-d', strtotime($fb['created_at'])) ?>">
                            <td class="px-4 py-4 border-r border-gray-200 text-sm text-gray-900">
                                <?= $index + 1 ?>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <?php
                                        $nameParts = explode(' ', $fb['nama_user']);
                                        $initials = count($nameParts) >= 2 
                                            ? strtoupper(substr($nameParts[0], 0, 1) . substr($nameParts[1], 0, 1))
                                            : strtoupper(substr($fb['nama_user'], 0, 2));
                                        ?>
                                        <span class="text-white text-sm font-semibold"><?= $initials ?></span>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate"><?= esc($fb['nama_user']) ?></p>
                                        <p class="text-xs text-gray-500 truncate"><?= esc($fb['nama_kabupaten']) ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200">
                                <div class="flex items-center gap-2">
                                    <!-- <span class="text-2xl font-bold text-gray-900"><?= $fb['rating'] ?></span> -->
                                    <div class="text-yellow-500">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= $fb['rating'] ? '' : '-o' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200">
                                <p class="text-sm text-gray-900 line-clamp-2"><?= esc($fb['feedback']) ?></p>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200 text-sm text-gray-600">
                                <?= date('d M Y', strtotime($fb['created_at'])) ?><br>
                                <span class="text-xs text-gray-400"><?= date('H:i', strtotime($fb['created_at'])) ?></span>
                            </td>
                            <td class="px-4 py-4 border-r border-gray-200">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?= base_url('superadmin/rating-aplikasi/show/' . $fb['id_feedback']) ?>" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination Info -->
    <div class="mt-6 flex items-center justify-between">
        <p class="text-sm text-gray-600" id="paginationInfo">
            Menampilkan data <span class="font-medium" id="startRange">1</span>-<span class="font-medium" id="endRange">0</span> dari <span class="font-medium" id="totalData">0</span> data
        </p>
    </div>
</div>

<!-- ApexCharts CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.44.0/apexcharts.min.js"></script>

<script>
// Data dari PHP
const ratingDistribution = <?= json_encode($stats['rating_distribution']) ?>;
const trendData = <?= json_encode($stats['trend']) ?>;

let trendChart = null;
let currentView = 'month';

// Render Distribution Bar Chart
function renderDistributionChart() {
    // Prepare data untuk semua rating (1-5)
    const ratingMap = {};
    ratingDistribution.forEach(item => {
        ratingMap[item.rating] = parseInt(item.jumlah);
    });
    
    const categories = ['1 ★', '2 ★', '3 ★', '4 ★', '5 ★'];
    const data = [
        ratingMap[1] || 0,
        ratingMap[2] || 0,
        ratingMap[3] || 0,
        ratingMap[4] || 0,
        ratingMap[5] || 0
    ];
    
    const options = {
        series: [{
            name: 'Jumlah Rating',
            data: data
        }],
        chart: {
            type: 'bar',
            height: 350,
            fontFamily: 'Poppins, sans-serif',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    zoom: false,
                    zoomin: false,
                    zoomout: false,
                    pan: false,
                    reset: false
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 8,
                dataLabels: {
                    position: 'top',
                },
                columnWidth: '60%'
            }
        },
        dataLabels: {
            enabled: true,
            offsetY: -25,
            style: {
                fontSize: '12px',
                colors: ['#304758']
            }
        },
        xaxis: {
            categories: categories,
            labels: {
                style: {
                    fontSize: '13px',
                    fontWeight: 500
                }
            }
        },
        yaxis: {
            title: {
                text: 'Jumlah Rating',
                style: {
                    fontSize: '12px'
                }
            },
            labels: {
                formatter: function(value) {
                    return Math.floor(value);
                }
            }
        },
        colors: ['#3B82F6'],
        grid: {
            borderColor: '#f3f4f6',
            padding: {
                top: 10,
                bottom: 0
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return value + ' rating';
                }
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#distributionChart"), options);
    chart.render();
}

// Render Trend Line Chart
function renderTrendChart(view = 'month') {
    // Process data based on view
    let processedData = [];
    
    if (view === 'month') {
        // Gunakan data asli (per bulan) dan hitung kumulatif
        const reversedData = [...trendData].reverse();
        let cumulativeTotal = 0;
        let cumulativeSum = 0;
        
        processedData = reversedData.map(d => {
            cumulativeTotal += parseInt(d.total);
            cumulativeSum += parseFloat(d.avg_rating) * parseInt(d.total);
            
            return {
                date: d.bulan,
                total: cumulativeTotal,
                avg: cumulativeTotal > 0 ? cumulativeSum / cumulativeTotal : 0
            };
        });
    } else if (view === 'year') {
        // Group by year untuk kumulatif
        const yearMap = {};
        const sortedData = [...trendData].sort((a, b) => a.bulan.localeCompare(b.bulan));
        
        let cumulativeTotal = 0;
        let cumulativeSum = 0;
        
        sortedData.forEach(d => {
            const year = d.bulan.split('-')[0];
            cumulativeTotal += parseInt(d.total);
            cumulativeSum += parseFloat(d.avg_rating) * parseInt(d.total);
            
            yearMap[year] = {
                total: cumulativeTotal,
                avg: cumulativeTotal > 0 ? cumulativeSum / cumulativeTotal : 0
            };
        });
        
        processedData = Object.keys(yearMap).sort().map(year => ({
            date: year,
            total: yearMap[year].total,
            avg: yearMap[year].avg
        }));
    }
    
    const categories = processedData.map(d => {
        if (view === 'year') {
            return d.date;
        } else {
            const [year, month] = d.date.split('-');
            return new Date(year, month - 1).toLocaleDateString('id-ID', { 
                month: 'short', 
                year: 'numeric' 
            });
        }
    });
    
    const options = {
        series: [
            {
                name: 'Rata-rata Rating Kumulatif',
                data: processedData.map((d, index) => ({
                    x: categories[index],
                    y: parseFloat(d.avg).toFixed(2),
                    jumlah: d.total
                }))
            }
        ],
        chart: {
            height: 350,
            type: 'line',
            fontFamily: 'Poppins, sans-serif',
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                }
            },
            zoom: {
                enabled: true,
                type: 'x',
                autoScaleYaxis: true
            }
        },
        stroke: {
            width: 3,
            curve: 'smooth'
        },
        colors: ['#EAB308'],
        markers: {
            size: 5,
            hover: {
                size: 7
            }
        },
        xaxis: {
            type: 'category',
            labels: {
                rotate: -45,
                style: {
                    fontSize: '11px'
                }
            }
        },
        yaxis: {
            title: {
                text: 'Rata-rata Rating Kumulatif (★)',
                style: {
                    fontSize: '12px',
                    fontWeight: 500
                }
            },
            min: 0,
            max: 5,
            tickAmount: 5,
            labels: {
                formatter: function(value) {
                    return value.toFixed(1);
                }
            }
        },
        legend: {
            show: false
        },
        grid: {
            borderColor: '#f3f4f6',
            padding: {
                bottom: 10
            }
        },
        tooltip: {
            custom: function({series, seriesIndex, dataPointIndex, w}) {
                const data = w.config.series[0].data[dataPointIndex];
                return '<div class="px-3 py-2">' +
                    '<div class="text-xs font-semibold text-gray-700 mb-1">' + data.x + '</div>' +
                    '<div class="flex items-center gap-2 mb-1">' +
                        '<span class="w-2 h-2 rounded-full bg-yellow-500"></span>' +
                        '<span class="text-xs text-gray-600">Rata-rata Kumulatif:</span>' +
                        '<span class="text-xs font-semibold text-gray-900">' + data.y + ' ★</span>' +
                    '</div>' +
                    '<div class="flex items-center gap-2">' +
                        '<span class="w-2 h-2 rounded-full bg-blue-500"></span>' +
                        '<span class="text-xs text-gray-600">Total Rating s/d periode:</span>' +
                        '<span class="text-xs font-semibold text-gray-900">' + data.jumlah + ' rating</span>' +
                    '</div>' +
                '</div>';
            }
        }
    };

    if (trendChart) {
        trendChart.destroy();
    }
    
    trendChart = new ApexCharts(document.querySelector("#trendChart"), options);
    trendChart.render();
}

// Change trend view
function changeTrendView(view) {
    currentView = view;
    
    // Update button states
    document.querySelectorAll('.trend-btn').forEach(btn => {
        if (btn.dataset.view === view) {
            btn.classList.add('bg-blue-50', 'border-blue-300', 'text-blue-600');
            btn.classList.remove('hover:bg-gray-100');
        } else {
            btn.classList.remove('bg-blue-50', 'border-blue-300', 'text-blue-600');
            btn.classList.add('hover:bg-gray-100');
        }
    });
    
    // Re-render chart
    renderTrendChart(view);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    renderDistributionChart();
    renderTrendChart('month');
    applyFilters(); // Initialize filter on load
});

// Apply filters without page refresh
function applyFilters() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const ratingFilter = document.getElementById('ratingFilter').value;
    const kabupatenFilter = document.getElementById('kabupatenFilter').value;
    const dateFromFilter = document.getElementById('dateFromFilter').value;
    const dateToFilter = document.getElementById('dateToFilter').value;

    const rows = document.querySelectorAll('#tableBody tr');
    let visibleCount = 0;
    let totalRows = rows.length;

    rows.forEach((row, index) => {
        // Skip empty state row
        if (row.querySelector('td[colspan="6"]')) {
            return;
        }

        const searchData = row.getAttribute('data-search') || '';
        const rowRating = row.getAttribute('data-rating') || '';
        const rowKabupaten = row.getAttribute('data-kabupaten') || '';
        const rowDate = row.getAttribute('data-date') || '';

        let showRow = true;

        // Filter by search
        if (searchInput && !searchData.includes(searchInput)) {
            showRow = false;
        }

        // Filter by rating
        if (ratingFilter && rowRating !== ratingFilter) {
            showRow = false;
        }

        // Filter by kabupaten
        if (kabupatenFilter && rowKabupaten !== kabupatenFilter) {
            showRow = false;
        }

        // Filter by date range
        if (dateFromFilter && rowDate < dateFromFilter) {
            showRow = false;
        }

        if (dateToFilter && rowDate > dateToFilter) {
            showRow = false;
        }

        // Show/hide row
        if (showRow) {
            row.style.display = '';
            visibleCount++;
            // Update row number
            row.querySelector('td:first-child').textContent = visibleCount;
        } else {
            row.style.display = 'none';
        }
    });

    // Update pagination info
    updatePaginationInfo(visibleCount, totalRows);
}

// Reset all filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('ratingFilter').value = '';
    document.getElementById('kabupatenFilter').value = '';
    document.getElementById('dateFromFilter').value = '';
    document.getElementById('dateToFilter').value = '';
    applyFilters();
}

// Update pagination info
function updatePaginationInfo(visible, total) {
    const startRange = visible > 0 ? 1 : 0;
    const endRange = visible;
    
    document.getElementById('startRange').textContent = startRange;
    document.getElementById('endRange').textContent = endRange;
    document.getElementById('totalData').textContent = total;
}
</script>

<?= $this->endSection() ?>