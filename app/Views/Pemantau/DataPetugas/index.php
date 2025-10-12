<?= $this->extend('layouts/pemantau_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="<?= base_url('pemantau') ?>" class="text-gray-600 hover:text-gray-900 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Data Petugas</h1>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    
    <!-- Header Section -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">DATA MITRA BPS 1401</h2>
        
        <!-- Export Buttons and Search -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Export Buttons -->
            <div class="flex flex-wrap gap-2">
                <button class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors">
                    Copy
                </button>
                <button class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded transition-colors">
                    Excel
                </button>
                <button class="px-3 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-xs font-medium rounded transition-colors">
                    CSV
                </button>
                <button class="px-3 py-2 bg-orange-600 hover:bg-orange-700 text-white text-xs font-medium rounded transition-colors">
                    PDF
                </button>
                <button class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                    Print
                </button>
            </div>
            
            <!-- Search Box -->
            <div class="relative w-full sm:w-64">
                <input type="text" 
                    id="searchInput" 
                    placeholder="Search..." 
                    class="w-full pl-4 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    onkeyup="searchTable()">
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse" id="petugasTable">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        No
                        <i class="fas fa-sort text-gray-400 ml-1"></i>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        Kode Kabkot
                        <i class="fas fa-sort text-gray-400 ml-1"></i>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        Nama Mitra
                        <i class="fas fa-sort text-gray-400 ml-1"></i>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        Sobat ID
                        <i class="fas fa-sort text-gray-400 ml-1"></i>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        Role
                        <i class="fas fa-sort text-gray-400 ml-1"></i>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        Status
                        <i class="fas fa-sort text-gray-400 ml-1"></i>
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 border border-gray-300 whitespace-nowrap">
                        Kegiatan yang diikuti
                        <i class="fas fa-sort text-gray-400 ml-1"></i>
                    </th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Data akan dimuat via JavaScript -->
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mt-4">
        <div class="text-sm text-gray-600">
            Showing <span id="showingInfo">1 to 15 of 902</span> entries
        </div>
        <div class="flex flex-wrap gap-2">
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                Previous
            </button>
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">1</button>
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">2</button>
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">3</button>
            <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded">4</button>
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">5</button>
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">...</button>
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">91</button>
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                Next
            </button>
        </div>
    </div>
</div>

<script>
// Data dummy petugas
const dataPetugas = [
    {
        no: 31,
        kodeKabkot: 1401,
        nama: "HARIRI IHSB",
        sobatId: "140122020049",
        role: "PML",
        status: "Aktif",
        kegiatan: []
    },
    {
        no: 32,
        kodeKabkot: 1401,
        nama: "RIDHO RAHMAN",
        sobatId: "140122020050",
        role: "PCL",
        status: "Aktif",
        kegiatan: []
    },
    {
        no: 33,
        kodeKabkot: 1401,
        nama: "TAUFIK HERMAN",
        sobatId: "140122020052",
        role: "PCL",
        status: "Tidak Aktif",
        kegiatan: ["wilkerstat (2025)"]
    },
    {
        no: 34,
        kodeKabkot: 1401,
        nama: "RIKA HERMATI",
        sobatId: "140122020054",
        role: "PML",
        status: "Aktif",
        kegiatan: ["wilkerstat (2025)"]
    },
    {
        no: 35,
        kodeKabkot: 1401,
        nama: "APRIQEN S, PD",
        sobatId: "140122020055",
        role: "PCL",
        status: "Aktif",
        kegiatan: [
            "VHTL (MARET)", "VHTL (APRIL)", "VHTL (MEI)", "VHTL (JUNI)", "VHTL (JULI)", 
            "VHTL (AGUSTUS)", "VHTL (SEPTEMBER)", "VHTL (OKTOBER)", "VHTL (DESEMBER)", 
            "VHTL (JANUARI 2025)", "VHTL (APRIL 2025)", "VHTL (MEI 2025)", "VHTL (MARET 2025)", 
            "VHTL (JUNI 2025)", "SKPD (JUNI 2025)", "SKPD (JULI 2025)", "SHP (AGUSTUS 2025)", 
            "SHP (JANUARI 2025)", "SHP (DESEMBER 2025)"
        ]
    },
    {
        no: 36,
        kodeKabkot: 1401,
        nama: "INDIRA, S, PD.I",
        sobatId: "140122020056",
        role: "PML",
        status: "Aktif",
        kegiatan: [
            "SHP (JANUARI 2025)", "SHP (MARET 2025)", "SHP (APRIL 2025)", 
            "SHP (MEI 2025)", "SHP (JUNI 2025)", "SHP (JULI 2025)", 
            "SHP (AGUSTUS 2025)", "SHP (SEPTEMBER 2025)"
        ]
    },
    {
        no: 37,
        kodeKabkot: 1401,
        nama: "M ISNAINI",
        sobatId: "140122020057",
        role: "PCL",
        status: "Tidak Aktif",
        kegiatan: []
    },
    {
        no: 38,
        kodeKabkot: 1401,
        nama: "WANDA YANIS",
        sobatId: "140122020058",
        role: "PCL",
        status: "Aktif",
        kegiatan: []
    },
    {
        no: 39,
        kodeKabkot: 1401,
        nama: "HARIANTO",
        sobatId: "140122020060",
        role: "PML",
        status: "Aktif",
        kegiatan: []
    },
    {
        no: 40,
        kodeKabkot: 1401,
        nama: "ROHAN RIZKI BAIHARUN",
        sobatId: "140122020063",
        role: "PCL",
        status: "Aktif",
        kegiatan: []
    },
    {
        no: 41,
        kodeKabkot: 1401,
        nama: "SITI AMINAH",
        sobatId: "140122020065",
        role: "PCL",
        status: "Aktif",
        kegiatan: ["SUSENAS SEPT 2025", "SAKERNAS 2025"]
    },
    {
        no: 42,
        kodeKabkot: 1401,
        nama: "BUDI SANTOSO",
        sobatId: "140122020067",
        role: "PCL",
        status: "Tidak Aktif",
        kegiatan: ["SUSENAS SEPT 2025"]
    },
    {
        no: 43,
        kodeKabkot: 1401,
        nama: "DEWI LESTARI",
        sobatId: "140122020069",
        role: "PML",
        status: "Aktif",
        kegiatan: ["SUNSENAS 2025", "VHTL 2025"]
    },
    {
        no: 44,
        kodeKabkot: 1401,
        nama: "AHMAD FAUZI",
        sobatId: "140122020071",
        role: "PCL",
        status: "Aktif",
        kegiatan: ["SAKERNAS 2025"]
    },
    {
        no: 45,
        kodeKabkot: 1401,
        nama: "RIZKI AHMAD",
        sobatId: "140122020073",
        role: "PML",
        status: "Aktif",
        kegiatan: ["SUSENAS SEPT 2025", "SUNSENAS 2025"]
    }
];

/**
 * Fungsi untuk membuat badge status
 * @param {string} status - Status petugas
 * @returns {string} HTML badge status
 */
function createStatusBadge(status) {
    const isActive = status === 'Aktif';
    const bgColor = isActive ? 'bg-green-100' : 'bg-red-100';
    const textColor = isActive ? 'text-green-800' : 'text-red-800';
    
    return `<span class="inline-flex px-3 py-1 ${bgColor} ${textColor} text-xs font-semibold rounded-full">${status}</span>`;
}

/**
 * Fungsi untuk membuat badge role
 * @param {string} role - Role petugas
 * @returns {string} HTML badge role
 */
function createRoleBadge(role) {
    const isPML = role === 'PML';
    const bgColor = isPML ? 'bg-blue-100' : 'bg-purple-100';
    const textColor = isPML ? 'text-blue-800' : 'text-purple-800';
    
    return `<span class="inline-flex px-3 py-1 ${bgColor} ${textColor} text-xs font-semibold rounded-full">${role}</span>`;
}

/**
 * Fungsi untuk membuat badges kegiatan
 * @param {Array} kegiatan - Array kegiatan
 * @returns {string} HTML badges kegiatan
 */
function createKegiatanBadges(kegiatan) {
    if (kegiatan.length === 0) {
        return '<span class="text-gray-400 text-xs">-</span>';
    }
    
    return kegiatan.map(k => 
        `<span class="inline-block px-2 py-1 bg-yellow-400 text-gray-800 text-xs font-medium rounded mr-1 mb-1">${k}</span>`
    ).join('');
}

/**
 * Fungsi render table
 * @param {Array} data - Array data petugas
 */
function renderTable(data = dataPetugas) {
    const tbody = document.getElementById('tableBody');
    
    if (!tbody) {
        console.error('Element tableBody tidak ditemukan');
        return;
    }
    
    const rows = data.map(item => `
        <tr class="hover:bg-gray-50 transition-colors">
            <td class="px-4 py-3 text-center text-sm text-gray-700 border border-gray-300">${item.no}</td>
            <td class="px-4 py-3 text-sm text-gray-700 border border-gray-300">${item.kodeKabkot}</td>
            <td class="px-4 py-3 text-sm border border-gray-300">
                <a href="#" class="text-blue-600 hover:text-blue-800 font-medium hover:underline">${item.nama}</a>
            </td>
            <td class="px-4 py-3 text-sm text-gray-700 border border-gray-300">${item.sobatId}</td>
            <td class="px-4 py-3 text-center border border-gray-300">
                ${createRoleBadge(item.role)}
            </td>
            <td class="px-4 py-3 text-center border border-gray-300">
                ${createStatusBadge(item.status)}
            </td>
            <td class="px-4 py-3 text-sm border border-gray-300">
                <div class="flex flex-wrap gap-1">
                    ${createKegiatanBadges(item.kegiatan)}
                </div>
            </td>
        </tr>
    `).join('');
    
    tbody.innerHTML = rows;
    updateShowingInfo(data.length);
}

/**
 * Fungsi untuk update informasi showing entries
 * @param {number} count - Jumlah data yang ditampilkan
 */
function updateShowingInfo(count) {
    const showingInfo = document.getElementById('showingInfo');
    if (showingInfo) {
        const start = count > 0 ? 1 : 0;
        showingInfo.textContent = `${start} to ${count} of ${dataPetugas.length}`;
    }
}

/**
 * Fungsi search table
 */
function searchTable() {
    const input = document.getElementById('searchInput');
    if (!input) return;
    
    const filter = input.value.toLowerCase().trim();
    
    if (filter === '') {
        renderTable(dataPetugas);
        return;
    }
    
    const filteredData = dataPetugas.filter(item => {
        const searchableText = [
            item.nama,
            item.sobatId,
            item.role,
            item.status,
            ...item.kegiatan
        ].join(' ').toLowerCase();
        
        return searchableText.includes(filter);
    });
    
    renderTable(filteredData);
}

// Initialize table on page load
document.addEventListener('DOMContentLoaded', function() {
    renderTable();
    
    // Setup search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', searchTable);
    }
});
</script>

<?= $this->endSection() ?>