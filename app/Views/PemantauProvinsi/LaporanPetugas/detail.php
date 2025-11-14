<?= $this->extend('layouts/pemantau_provinsi_layout') ?>

<?= $this->section('content') ?>

<!-- Back Button & Title -->
<div class="mb-6">
    <a href="<?= base_url('laporan-petugas') ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4">
        <i class="fas fa-arrow-left mr-2"></i>
        <span>Back</span>
    </a>
    <h1 class="text-2xl font-bold text-gray-900">Detail Progress PCL</h1>
</div>

<!-- Info Cards -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div>
            <p class="text-sm text-gray-500 mb-1">Nama PCL</p>
            <p class="text-base font-bold text-gray-900" id="namaPCL">AHMAD NURHOLIS</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">PML</p>
            <p class="text-base font-semibold text-gray-900">Ahmad Subarjo</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">Nama Survei</p>
            <p class="text-base font-semibold text-gray-900">SUSENAS SEPT (September) 2025</p>
        </div>
        <div>
            <p class="text-sm text-gray-500 mb-1">Wilayah</p>
            <p class="text-base font-semibold text-gray-900">Kota Pekanbaru</p>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <!-- Target Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-2">Target</p>
                <p class="text-4xl font-bold text-gray-900">50</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                <i class="fas fa-bullseye text-blue-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Aktual Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-2">Aktual</p>
                <p class="text-4xl font-bold text-gray-900">30</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Pencapaian Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-2">Pencapaian</p>
                <p class="text-4xl font-bold text-red-600">60%</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                <i class="fas fa-chart-pie text-purple-600 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Selisih Card -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-2">Selisih</p>
                <p class="text-4xl font-bold text-red-600">-20</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                <i class="fas fa-balance-scale text-orange-600 text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Chart Section -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-lg font-bold text-gray-900 mb-1">Kurva S - Target vs Aktual PCL</h2>
            <p class="text-sm text-gray-600">Progress Kumulatif Harian</p>
        </div>
        <button class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
            <i class="far fa-calendar-alt mr-2"></i>
            September 2025
        </button>
    </div>
    
    <div class="relative">
        <canvas id="kurvaChart"></canvas>
    </div>
</div>

<!-- Table Section -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Laporan Detail</h2>
    
    <div class="overflow-x-auto">
        <table class="w-full border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Tanggal</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Waktu</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Kabupaten</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Nama Kegiatan</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Periode</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Posisi</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border-r border-gray-200">Foto</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Resume</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="laporanTableBody">
                <!-- Data will be inserted here by JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
        <div class="text-sm text-gray-600">
            Showing <span id="showingInfo">1 to 10 of 12</span> entries
        </div>
        <div class="flex gap-1">
            <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 transition-colors disabled:opacity-50" id="prevBtn">
                Previous
            </button>
            <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">1</button>
            <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 transition-colors">2</button>
            <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 transition-colors" id="nextBtn">
                Next
            </button>
        </div>
    </div>
</div>

<script>
// Dummy Data untuk Laporan
const laporanData = [
    {
        tanggal: "2025-09-01",
        waktu: "07:22:52",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan1.jpg') ?>",
        resume: "Pencacahan sampel ruta"
    },
    {
        tanggal: "2025-09-02",
        waktu: "10:30:56",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan2.jpg') ?>",
        resume: "Pencacahan ruta sampel"
    },
    {
        tanggal: "2025-09-03",
        waktu: "09:01:21",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan3.jpg') ?>",
        resume: "Pencacahan sampel ruta"
    },
    {
        tanggal: "2025-09-04",
        waktu: "14:15:10",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan4.jpg') ?>",
        resume: "Pencacahan sampel ruta"
    },
    {
        tanggal: "2025-09-05",
        waktu: "11:14:44",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan5.jpg') ?>",
        resume: "Pencacahan sampel ruta"
    },
    {
        tanggal: "2025-09-06",
        waktu: "03:12:06",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan6.jpg') ?>",
        resume: "Keterdarhan Pendarar Survei Sidh"
    },
    {
        tanggal: "2025-09-07",
        waktu: "08:45:30",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan7.jpg') ?>",
        resume: "Pencacahan sampel ruta"
    },
    {
        tanggal: "2025-09-08",
        waktu: "13:20:15",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan8.jpg') ?>",
        resume: "Pencacahan ruta sampel"
    },
    {
        tanggal: "2025-09-09",
        waktu: "09:30:22",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan9.jpg') ?>",
        resume: "Pencacahan sampel ruta"
    },
    {
        tanggal: "2025-09-10",
        waktu: "15:10:45",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan10.jpg') ?>",
        resume: "Pencacahan sampel ruta"
    },
    {
        tanggal: "2025-09-11",
        waktu: "11:55:33",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan11.jpg') ?>",
        resume: "Pencacahan ruta sampel"
    },
    {
        tanggal: "2025-09-12",
        waktu: "07:40:18",
        kabupaten: "1401",
        namaKegiatan: "SUSENAS SEPT 2025",
        periode: "SEPTEMBER",
        posisi: "PCL",
        foto: "<?= base_url('assets/gambar/foto-laporan12.jpg') ?>",
        resume: "Keterdarhan Pendarar Survei Sidh"
    }
];

// Generate data untuk September 2025 (30 hari)
const generateSeptemberDates = () => {
    const dates = [];
    for (let i = 1; i <= 30; i++) {
        dates.push(`${i} Sep`);
    }
    return dates;
};

// Generate Target Data (Kurva S Logistik)
const generateTargetData = () => {
    const data = [];
    const totalTarget = 50;
    const k = 0.25; // Growth rate
    const x0 = 15; // Midpoint
    
    for (let day = 1; day <= 30; day++) {
        const value = totalTarget / (1 + Math.exp(-k * (day - x0)));
        data.push(value);
    }
    return data;
};

// Generate Aktual Data (sampai hari ini dengan variasi)
const generateAktualData = () => {
    const data = [];
    const today = 15; // Simulasi hari ke-15 September
    const targetData = generateTargetData();
    
    for (let day = 1; day <= 30; day++) {
        if (day <= today) {
            // Data aktual dengan sedikit variasi dari target
            const variance = (Math.random() - 0.5) * 4;
            let value = targetData[day - 1] * 0.6 + variance; // 60% dari target
            if (value < 0) value = 0;
            data.push(value);
        } else {
            data.push(null); // Tidak ada data untuk hari-hari mendatang
        }
    }
    return data;
};

const dates = generateSeptemberDates();
const targetData = generateTargetData();
const aktualData = generateAktualData();

// Find current day index
const currentDay = 15;

// Render Chart
const ctx = document.getElementById('kurvaChart').getContext('2d');
const kurvaChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: dates,
        datasets: [
            {
                label: 'Target (Kurva S)',
                data: targetData,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#3b82f6',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3,
                fill: true,
                order: 2
            },
            {
                label: 'Aktual (Kumulatif)',
                data: aktualData,
                borderColor: '#ef4444',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBackgroundColor: '#ef4444',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3,
                borderDash: [8, 4],
                fill: true,
                order: 1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 2.8,
        interaction: {
            mode: 'index',
            intersect: false
        },
        plugins: {
            legend: {
                display: true,
                position: 'top',
                align: 'start',
                labels: {
                    usePointStyle: true,
                    pointStyle: 'line',
                    padding: 20,
                    font: {
                        size: 13,
                        family: 'Poppins, sans-serif',
                        weight: '500'
                    },
                    color: '#374151',
                    boxWidth: 40,
                    boxHeight: 3
                }
            },
            tooltip: {
                backgroundColor: 'rgba(255, 255, 255, 0.98)',
                titleColor: '#1f2937',
                bodyColor: '#374151',
                borderColor: '#e5e7eb',
                borderWidth: 1,
                padding: 12,
                boxPadding: 6,
                usePointStyle: true,
                titleFont: {
                    size: 13,
                    weight: 'bold',
                    family: 'Poppins, sans-serif'
                },
                bodyFont: {
                    size: 12,
                    family: 'Poppins, sans-serif'
                },
                callbacks: {
                    title: function(context) {
                        return context[0].label;
                    },
                    label: function(context) {
                        if (context.parsed.y === null) return null;
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += Math.round(context.parsed.y);
                        return label;
                    }
                }
            },
            annotation: {
                annotations: {
                    line1: {
                        type: 'line',
                        xMin: currentDay - 1,
                        xMax: currentDay - 1,
                        borderColor: '#f59e0b',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        label: {
                            display: true,
                            content: 'Hari Ini',
                            position: 'start',
                            backgroundColor: '#f59e0b',
                            color: 'white',
                            font: {
                                size: 11,
                                weight: 'bold',
                                family: 'Poppins, sans-serif'
                            },
                            padding: 4,
                            rotation: 90,
                            yAdjust: -20
                        }
                    }
                }
            }
        },
        scales: {
            x: {
                title: {
                    display: true,
                    text: 'Tanggal (September 2025)',
                    font: {
                        size: 13,
                        weight: '600',
                        family: 'Poppins, sans-serif'
                    },
                    color: '#374151',
                    padding: {top: 15}
                },
                grid: {
                    display: true,
                    color: 'rgba(0, 0, 0, 0.04)',
                    drawBorder: true,
                    borderColor: '#d1d5db',
                    borderWidth: 1.5
                },
                ticks: {
                    font: {
                        size: 10,
                        family: 'Poppins, sans-serif'
                    },
                    color: '#6b7280',
                    padding: 8,
                    maxRotation: 45,
                    minRotation: 0
                }
            },
            y: {
                title: {
                    display: true,
                    text: 'Jumlah Survei (Kumulatif)',
                    font: {
                        size: 13,
                        weight: '600',
                        family: 'Poppins, sans-serif'
                    },
                    color: '#374151',
                    padding: {bottom: 15}
                },
                grid: {
                    display: true,
                    color: 'rgba(0, 0, 0, 0.04)',
                    drawBorder: true,
                    borderColor: '#d1d5db',
                    borderWidth: 1.5
                },
                ticks: {
                    font: {
                        size: 11,
                        family: 'Poppins, sans-serif'
                    },
                    color: '#6b7280',
                    padding: 10,
                    stepSize: 5
                },
                beginAtZero: true,
                max: 60
            }
        },
        layout: {
            padding: {
                top: 20,
                bottom: 10,
                left: 10,
                right: 20
            }
        }
    }
});

// Render Table
let currentPage = 1;
const itemsPerPage = 10;

function renderTable() {
    const tbody = document.getElementById('laporanTableBody');
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageData = laporanData.slice(start, end);
    
    tbody.innerHTML = '';
    
    pageData.forEach((laporan, index) => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50';
        
        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">${start + index + 1}</td>
            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">${laporan.tanggal}</td>
            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">${laporan.waktu}</td>
            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">${laporan.kabupaten}</td>
            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">${laporan.namaKegiatan}</td>
            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">${laporan.periode}</td>
            <td class="px-4 py-3 text-sm text-gray-700 border-r border-gray-200">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    ${laporan.posisi}
                </span>
            </td>
            <td class="px-4 py-3 border-r border-gray-200">
                <img src="https://via.placeholder.com/60x60?text=Foto" alt="Foto" class="w-16 h-16 object-cover rounded border border-gray-200">
            </td>
            <td class="px-4 py-3 text-sm text-gray-700">${laporan.resume}</td>
        `;
        
        tbody.appendChild(row);
    });
    
    document.getElementById('showingInfo').textContent = 
        `${start + 1} to ${Math.min(end, laporanData.length)} of ${laporanData.length}`;
}

// Pagination handlers
document.getElementById('prevBtn').addEventListener('click', function() {
    if (currentPage > 1) {
        currentPage--;
        renderTable();
    }
});

document.getElementById('nextBtn').addEventListener('click', function() {
    const totalPages = Math.ceil(laporanData.length / itemsPerPage);
    if (currentPage < totalPages) {
        currentPage++;
        renderTable();
    }
});

// Initial render
renderTable();
</script>

<?= $this->endSection() ?>