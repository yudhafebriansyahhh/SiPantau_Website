<?= $this->extend('layouts/pemantau_layout') ?>

<?= $this->section('content') ?>

<!-- Back Button & Title -->
<div class="mb-6">
    <a href="<?= base_url('pemantau') ?>" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-3">
        <i class="fas fa-arrow-left mr-2"></i>
        <span>Back</span>
    </a>
    <h1 class="text-3xl font-bold text-gray-900">Laporan Petugas</h1>
</div>

<!-- Main Card -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="p-6">
        <!-- Filter Section -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between">
                <div class="flex items-center gap-2">
                    <label class="text-sm font-medium text-gray-700">Pilih Kegiatan:</label>
                    <select id="filterKegiatan" class="border border-gray-300 rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>SUSENAS SEPT 2025 (SEPTEMBER)</option>
                        <option>SAKERNAS AGUSTUS 2025</option>
                        <option>SURVEI PERTANIAN 2025</option>
                    </select>
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                        Tampilkan
                    </button>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-2 mb-4">
            <button class="px-4 py-2 bg-gray-600 text-white rounded text-sm hover:bg-gray-700 transition-colors">
                Copy
            </button>
            <button class="px-4 py-2 bg-green-600 text-white rounded text-sm hover:bg-green-700 transition-colors">
                CSV
            </button>
            <button class="px-4 py-2 bg-emerald-600 text-white rounded text-sm hover:bg-emerald-700 transition-colors">
                Excel
            </button>
            <button class="px-4 py-2 bg-red-600 text-white rounded text-sm hover:bg-red-700 transition-colors">
                Print
            </button>
        </div>

        <!-- Search Box -->
        <div class="flex justify-end mb-4">
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-700">Search:</label>
                <input type="text" id="searchInput" placeholder="" class="border border-gray-300 rounded px-3 py-1 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="petugasTable">
                <thead>
                    <tr class="border-b border-gray-200 bg-gray-50">
                        <th class="px-4 py-3 text-left text-gray-700 font-semibold cursor-pointer hover:bg-gray-100">
                            No <i class="fas fa-sort text-xs ml-1"></i>
                        </th>
                        <th class="px-4 py-3 text-left text-gray-700 font-semibold cursor-pointer hover:bg-gray-100">
                            Nama
                        </th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">06/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">07/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">08/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">09/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">10/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">11/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">12/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">13/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">14/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">15/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">16/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">17/09</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-semibold">TOTAL</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data will be inserted here by JavaScript -->
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row items-center justify-between mt-4 gap-4">
            <div class="text-sm text-gray-600">
                Showing <span id="showingInfo">1 to 10 of 21</span> entries
            </div>
            <div class="flex gap-1">
                <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 transition-colors disabled:opacity-50" id="prevBtn">
                    Previous
                </button>
                <button class="px-3 py-1 bg-blue-600 text-white rounded text-sm">1</button>
                <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 transition-colors">2</button>
                <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 transition-colors">3</button>
                <button class="px-3 py-1 border border-gray-300 rounded text-sm hover:bg-gray-50 transition-colors" id="nextBtn">
                    Next
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Dummy Data
const petugasData = [
    { id: 1, nama: "AHMAD NURHOLIS", data: [4, 2, 3, 3, 0, 0, 0, 0, 0, 0, 0, 0], total: 12, status: true },
    { id: 2, nama: "ADRI YANTI", data: [0, 2, 2, 0, 1, 0, 2, 0, 0, 3, 2, 0], total: 12, status: false },
    { id: 3, nama: "ARIYESTI MAYA SARI", data: [0, 2, 1, 2, 1, 2, 1, 0, 0, 1, 0, 0], total: 10, status: false },
    { id: 4, nama: "DESRIKA GUSRIANTI", data: [4, 1, 3, 1, 0, 2, 0, 0, 0, 0, 0, 0], total: 11, status: false },
    { id: 5, nama: "DILA ANDRIANI", data: [1, 0, 5, 0, 0, 0, 0, 0, 0, 0, 6, 0], total: 12, status: false },
    { id: 6, nama: "DIVA AMANDA", data: [2, 2, 2, 1, 0, 0, 2, 0, 1, 0, 0, 0], total: 10, status: false },
    { id: 7, nama: "Edisman SE", data: [0, 0, 0, 0, 2, 0, 0, 0, 0, 0, 0, 0], total: 2, status: false },
    { id: 8, nama: "GILANG EKA RAMADHANA", data: [2, 0, 2, 0, 0, 0, 0, 3, 0, 0, 0, 0], total: 7, status: false },
    { id: 9, nama: "INDAH OKTILIANI", data: [0, 4, 2, 5, 0, 0, 0, 0, 0, 0, 1, 0], total: 12, status: false },
    { id: 10, nama: "Hidayat, A.Md", data: [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], total: 0, status: false },
    { id: 11, nama: "JOKO PURNOMO", data: [3, 1, 2, 1, 0, 1, 0, 0, 0, 2, 0, 0], total: 10, status: false },
    { id: 12, nama: "KHAIRUL AMRI", data: [0, 2, 3, 0, 1, 0, 0, 0, 0, 1, 0, 0], total: 7, status: false },
    { id: 13, nama: "LISA ARMITA", data: [1, 0, 2, 2, 0, 0, 1, 0, 0, 0, 4, 0], total: 10, status: false },
    { id: 14, nama: "M. RIFKI FADHILLAH", data: [0, 3, 1, 0, 2, 0, 0, 0, 1, 0, 0, 0], total: 7, status: false },
    { id: 15, nama: "MARIANA", data: [2, 0, 3, 1, 0, 1, 0, 0, 0, 0, 0, 3], total: 10, status: false },
    { id: 16, nama: "MONA LIZA", data: [0, 1, 2, 3, 0, 0, 2, 0, 0, 2, 0, 0], total: 10, status: false },
    { id: 17, nama: "NANDA PRATAMA", data: [1, 2, 1, 0, 1, 2, 0, 1, 0, 0, 0, 0], total: 8, status: false },
    { id: 18, nama: "PUTRA WIJAYA", data: [0, 0, 4, 0, 0, 0, 3, 0, 2, 0, 1, 0], total: 10, status: false },
    { id: 19, nama: "RINA SAFITRI", data: [3, 1, 0, 2, 0, 1, 0, 0, 0, 3, 0, 0], total: 10, status: false },
    { id: 20, nama: "SARI RAHAYU", data: [0, 2, 2, 0, 1, 0, 1, 2, 0, 0, 0, 2], total: 10, status: false },
    { id: 21, nama: "YOGA PRATAMA", data: [1, 0, 3, 1, 0, 2, 0, 0, 1, 0, 0, 0], total: 8, status: false }
];

let currentPage = 1;
let itemsPerPage = 10;
let filteredData = [...petugasData];

function renderTable() {
    const tbody = document.getElementById('tableBody');
    const start = (currentPage - 1) * itemsPerPage;
    const end = start + itemsPerPage;
    const pageData = filteredData.slice(start, end);
    
    tbody.innerHTML = '';
    
    pageData.forEach((petugas, index) => {
        const row = document.createElement('tr');
        row.className = 'border-b border-gray-100 hover:bg-gray-50';
        
        let cells = `
            <td class="px-4 py-3 text-gray-900">${start + index + 1}</td>
            <td class="px-4 py-3">
                <a href="<?= base_url('laporan-petugas/detail/') ?>${petugas.id}" class="text-blue-600 hover:text-blue-800 hover:underline font-medium">
                    ${petugas.nama}
                </a>
            </td>
        `;
        
        petugas.data.forEach(val => {
            cells += `<td class="px-4 py-3 text-center text-gray-700">${val}</td>`;
        });
        
        cells += `
            <td class="px-4 py-3 text-center font-semibold text-gray-900">
                ${petugas.total}
                ${petugas.status ? '<i class="fas fa-check-circle text-green-500 ml-2"></i>' : ''}
            </td>
        `;
        
        row.innerHTML = cells;
        tbody.appendChild(row);
    });
    
    document.getElementById('showingInfo').textContent = 
        `${start + 1} to ${Math.min(end, filteredData.length)} of ${filteredData.length}`;
}

// Search functionality
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    filteredData = petugasData.filter(petugas => 
        petugas.nama.toLowerCase().includes(searchTerm)
    );
    currentPage = 1;
    renderTable();
});

// Initial render
renderTable();
</script>

<?= $this->endSection() ?>