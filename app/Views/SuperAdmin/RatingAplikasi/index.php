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
            <h3 class="text-lg font-semibold text-gray-900">Trend Rating</h3>
            <div class="flex gap-2">
                <button onclick="changeTrendView('day')" 
                        class="trend-btn px-3 py-1 text-xs rounded-lg border border-gray-300 hover:bg-gray-100 transition-colors"
                        data-view="day">
                    Hari
                </button>
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
        <form method="GET" action="<?= base_url('superadmin/rating-aplikasi') ?>" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="<?= esc($filters['search'] ?? '') ?>"
                               placeholder="Nama user atau feedback..." 
                               class="input-field pl-10">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>

                <!-- Filter Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                    <select name="rating" class="input-field">
                        <option value="">Semua Rating</option>
                        <?php for($i = 5; $i >= 1; $i--): ?>
                            <option value="<?= $i ?>" <?= ($filters['rating'] ?? '') == $i ? 'selected' : '' ?>>
                                <?= $i ?> Bintang
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <!-- Filter Kabupaten -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kabupaten</label>
                    <select name="id_kabupaten" class="input-field">
                        <option value="">Semua Kabupaten</option>
                        <?php foreach ($kabupatens as $kab): ?>
                            <option value="<?= $kab['id_kabupaten'] ?>" 
                                    <?= ($filters['id_kabupaten'] ?? '') == $kab['id_kabupaten'] ? 'selected' : '' ?>>
                                <?= esc($kab['nama_kabupaten']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                    <div class="flex gap-2">
                        <input type="date" 
                               name="date_from" 
                               value="<?= esc($filters['date_from'] ?? '') ?>"
                               class="input-field"
                               placeholder="Dari">
                        <input type="date" 
                               name="date_to" 
                               value="<?= esc($filters['date_to'] ?? '') ?>"
                               class="input-field"
                               placeholder="Sampai">
                    </div>
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-2">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
                <a href="<?= base_url('superadmin/rating-aplikasi') ?>" class="btn-secondary">
                    <i class="fas fa-redo mr-2"></i>Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        No
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        User
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Rating
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Feedback
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Tanggal
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($feedbacks)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <i class="fas fa-inbox text-gray-300 text-4xl mb-2"></i>
                            <p class="text-gray-500">Belum ada rating</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($feedbacks as $index => $fb): ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 text-sm text-gray-900">
                                <?= $index + 1 ?>
                            </td>
                            <td class="px-4 py-4">
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
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-2xl font-bold text-gray-900"><?= $fb['rating'] ?></span>
                                    <div class="text-yellow-500">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= $fb['rating'] ? '' : '-o' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <p class="text-sm text-gray-900 line-clamp-2"><?= esc($fb['feedback']) ?></p>
                            </td>
                            <td class="px-4 py-4 text-sm text-gray-600">
                                <?= date('d M Y', strtotime($fb['created_at'])) ?><br>
                                <span class="text-xs text-gray-400"><?= date('H:i', strtotime($fb['created_at'])) ?></span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="<?= base_url('superadmin/rating-aplikasi/show/' . $fb['id_feedback']) ?>" 
                                       class="text-blue-600 hover:text-blue-800 transition-colors"
                                       title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button onclick="confirmDelete(<?= $fb['id_feedback'] ?>)" 
                                            class="text-red-600 hover:text-red-800 transition-colors"
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
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
        // Gunakan data asli (per bulan)
        processedData = trendData.map(d => ({
            date: d.bulan,
            total: parseInt(d.total),
            avg: parseFloat(d.avg_rating)
        })).reverse();
    } else if (view === 'year') {
        // Group by year
        const yearMap = {};
        trendData.forEach(d => {
            const year = d.bulan.split('-')[0];
            if (!yearMap[year]) {
                yearMap[year] = { total: 0, sum: 0, count: 0 };
            }
            yearMap[year].total += parseInt(d.total);
            yearMap[year].sum += parseFloat(d.avg_rating) * parseInt(d.total);
            yearMap[year].count += parseInt(d.total);
        });
        
        processedData = Object.keys(yearMap).sort().map(year => ({
            date: year,
            total: yearMap[year].total,
            avg: yearMap[year].count > 0 ? yearMap[year].sum / yearMap[year].count : 0
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
                name: 'Jumlah Rating',
                data: processedData.map(d => d.total)
            },
            {
                name: 'Rata-rata Rating',
                data: processedData.map(d => parseFloat(d.avg).toFixed(2))
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
            width: [3, 3],
            curve: 'smooth'
        },
        colors: ['#3B82F6', '#EAB308'],
        markers: {
            size: [4, 4],
            hover: {
                size: 6
            }
        },
        xaxis: {
            categories: categories,
            labels: {
                rotate: -45,
                style: {
                    fontSize: '11px'
                }
            }
        },
        yaxis: [
            {
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
            {
                opposite: true,
                title: {
                    text: 'Rata-rata (★)',
                    style: {
                        fontSize: '12px'
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
            }
        ],
        legend: {
            position: 'top',
            horizontalAlign: 'left',
            offsetY: 0
        },
        grid: {
            borderColor: '#f3f4f6',
            padding: {
                bottom: 10
            }
        },
        tooltip: {
            shared: true,
            intersect: false,
            y: [
                {
                    formatter: function(value) {
                        return value + ' rating';
                    }
                },
                {
                    formatter: function(value) {
                        return value + ' ★';
                    }
                }
            ]
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

// Confirm delete
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus rating ini?')) {
        fetch(`<?= base_url('superadmin/rating-aplikasi/delete/') ?>${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat menghapus rating');
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    renderDistributionChart();
    renderTrendChart('month');
});
</script>

<?= $this->endSection() ?>