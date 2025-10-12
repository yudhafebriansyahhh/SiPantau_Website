<?= $this->extend('layouts/adminkab_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center mb-2">
        <a href="<?= base_url('adminsurvei') ?>" class="text-gray-600 hover:text-gray-900 mr-2">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Approval Laporan</h1>
</div>

<!-- Main Content Card -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    
    <!-- Filter Section -->
    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <div class="flex-1">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kegiatan</label>
            <select id="filterKegiatan" onchange="filterTable()" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">-- Pilih Kegiatan --</option>
                <option value="SUSENAS SEPT 2025 (SEPTEMBER)">SUSENAS SEPT 2025 (SEPTEMBER)</option>
                <option value="SAKERNAS 2025">SAKERNAS 2025</option>
                <option value="SUNSENAS 2025">SUNSENAS 2025</option>
            </select>
        </div>
        <div class="flex items-end">
            <button onclick="tampilkanData()" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                <i class="fas fa-filter mr-2"></i>Tampilkan
            </button>
        </div>
    </div>
    
    <!-- Export Buttons -->
    <div class="flex gap-2 mb-4">
        <button class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded transition-colors">
            <i class="far fa-copy mr-1"></i>Copy
        </button>
        <button class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded transition-colors">
            <i class="far fa-file-alt mr-1"></i>CSV
        </button>
        <button class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded transition-colors">
            <i class="far fa-file-excel mr-1"></i>Excel
        </button>
        <button class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded transition-colors">
            <i class="fas fa-print mr-1"></i>Print
        </button>
        
        <!-- Search Box -->
        <div class="ml-auto">
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search..." 
                    class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    onkeyup="searchTable()">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
    </div>
    
    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse" id="approvalTable">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-200">No</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border border-gray-200">Nama PML</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-200">Target</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-200">Progress (%)</th>
                    <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-200">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200" id="tableBody">
                <!-- Data akan dimuat via JavaScript -->
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <div class="flex items-center justify-between mt-4">
        <div class="text-sm text-gray-600">
            Showing <span id="showingInfo">1 to 10 of 21</span> entries
        </div>
        <div class="flex gap-2">
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50 disabled:opacity-50" disabled>
                Previous
            </button>
            <button class="px-3 py-1 text-sm bg-blue-600 text-white rounded">1</button>
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50">2</button>
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50">3</button>
            <button class="px-3 py-1 text-sm border border-gray-300 rounded hover:bg-gray-50">
                Next
            </button>
        </div>
    </div>
</div>

<!-- Modal Detail PCL -->
<div id="pclModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Detail PCL - <span id="modalPMLName"></span></h3>
                <p class="text-sm text-gray-600 mt-1">Daftar PCL dan Status Approval</p>
            </div>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto" style="max-height: calc(90vh - 140px);">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-200">No</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border border-gray-200">Nama PCL</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-200">Target</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-200">Progress</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-200">Status Approval PML</th>
                    </tr>
                </thead>
                <tbody id="modalTableBody">
                    <!-- Data akan dimuat via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Data dummy PML dan PCL
const dataLaporan = {
    "SUSENAS SEPT 2025 (SEPTEMBER)": [
        {
            no: 1,
            nama: "AHMAD SYAHRUL S",
            target: 150,
            progress: 85,
            pcl: [
                { nama: "Siti Aminah", target: 50, progress: 30, status: "Approved" },
                { nama: "Budi Santoso", target: 50, progress: 45, status: "Pending" },
                { nama: "Dewi Lestari", target: 50, progress: 53, status: "Approved" }
            ]
        },
        {
            no: 2,
            nama: "AMIN WAHYU",
            target: 100,
            progress: 75,
            pcl: [
                { nama: "Rizki Ahmad", target: 50, progress: 35, status: "Approved" },
                { nama: "Linda Permata", target: 50, progress: 40, status: "Approved" }
            ]
        },
        {
            no: 3,
            nama: "ARRESTI MAYA SARI",
            target: 100,
            progress: 60,
            pcl: [
                { nama: "Maya Kusuma", target: 50, progress: 28, status: "Pending" },
                { nama: "Hendra Wijaya", target: 50, progress: 32, status: "Rejected" }
            ]
        },
        {
            no: 4,
            nama: "DESRINA GUSRIANTI",
            target: 50,
            progress: 76,
            pcl: [
                { nama: "Fitri Handayani", target: 50, progress: 38, status: "Approved" }
            ]
        },
        {
            no: 5,
            nama: "DESI MAHRANI",
            target: 100,
            progress: 84,
            pcl: [
                { nama: "Agus Widodo", target: 50, progress: 42, status: "Approved" },
                { nama: "Rini Setiawati", target: 50, progress: 42, status: "Pending" }
            ]
        },
        {
            no: 6,
            nama: "DITA MIRANDA",
            target: 50,
            progress: 70,
            pcl: [
                { nama: "Bambang Suryanto", target: 50, progress: 35, status: "Approved" }
            ]
        },
        {
            no: 7,
            nama: "Edimuron SE",
            target: 50,
            progress: 30,
            pcl: [
                { nama: "Eko Prasetyo", target: 50, progress: 15, status: "Pending" }
            ]
        },
        {
            no: 8,
            nama: "GEANGO EKA RAMADHANA",
            target: 50,
            progress: 40,
            pcl: [
                { nama: "Siti Nurhaliza", target: 50, progress: 20, status: "Rejected" }
            ]
        },
        {
            no: 9,
            nama: "INDAH OKTILIANI",
            target: 100,
            progress: 75,
            pcl: [
                { nama: "Muhammad Rizki", target: 50, progress: 40, status: "Approved" },
                { nama: "Dewi Lestari", target: 50, progress: 35, status: "Approved" }
            ]
        },
        {
            no: 10,
            nama: "Irfwan, A.Md",
            target: 50,
            progress: 0,
            pcl: []
        },
        {
            no: 11,
            nama: "RINI SETIAWATI",
            target: 120,
            progress: 92,
            pcl: [
                { nama: "Ahmad Fauzi", target: 60, progress: 55, status: "Approved" },
                { nama: "Siti Khadijah", target: 60, progress: 55, status: "Approved" }
            ]
        },
        {
            no: 12,
            nama: "BAMBANG SURYANTO",
            target: 80,
            progress: 65,
            pcl: [
                { nama: "Rina Wijaya", target: 40, progress: 28, status: "Pending" },
                { nama: "Dedi Hermawan", target: 40, progress: 24, status: "Approved" }
            ]
        }
    ],
    "SAKERNAS 2025": [
        {
            no: 1,
            nama: "HENDRA WIJAYA",
            target: 200,
            progress: 88,
            pcl: [
                { nama: "Lisa Andriani", target: 100, progress: 88, status: "Approved" },
                { nama: "Tono Sutrisno", target: 100, progress: 88, status: "Approved" }
            ]
        },
        {
            no: 2,
            nama: "MAYA KUSUMA",
            target: 150,
            progress: 72,
            pcl: [
                { nama: "Ratna Sari", target: 75, progress: 54, status: "Pending" },
                { nama: "Adi Nugroho", target: 75, progress: 54, status: "Approved" }
            ]
        },
        {
            no: 3,
            nama: "EKO PRASETYO",
            target: 180,
            progress: 55,
            pcl: [
                { nama: "Dian Pertiwi", target: 60, progress: 33, status: "Rejected" },
                { nama: "Rudi Hartono", target: 60, progress: 33, status: "Pending" },
                { nama: "Wati Kusuma", target: 60, progress: 33, status: "Approved" }
            ]
        }
    ],
    "SUNSENAS 2025": [
        {
            no: 1,
            nama: "FITRI HANDAYANI",
            target: 160,
            progress: 90,
            pcl: [
                { nama: "Yoga Pratama", target: 80, progress: 72, status: "Approved" },
                { nama: "Nina Marlina", target: 80, progress: 72, status: "Approved" }
            ]
        },
        {
            no: 2,
            nama: "AGUS WIDODO",
            target: 140,
            progress: 68,
            pcl: [
                { nama: "Budi Santoso", target: 70, progress: 48, status: "Pending" },
                { nama: "Sari Dewi", target: 70, progress: 47, status: "Approved" }
            ]
        }
    ]
};

// Fungsi tampilkan data
function tampilkanData() {
    const kegiatan = document.getElementById('filterKegiatan').value;
    
    if (!kegiatan) {
        alert('Silakan pilih kegiatan terlebih dahulu!');
        return;
    }
    
    const data = dataLaporan[kegiatan];
    const tbody = document.getElementById('tableBody');
    
    if (!data) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-4 py-8 text-center text-gray-500">Data tidak ditemukan</td></tr>';
        return;
    }
    
    let html = '';
    data.forEach((item, index) => {
        const hasPCL = item.pcl && item.pcl.length > 0;
        const rowClass = hasPCL ? 'cursor-pointer hover:bg-blue-50' : '';
        const onclick = hasPCL ? `onclick="showPCLDetail('${item.nama}', ${index})"` : '';
        
        // Warna progress berdasarkan persentase
        let progressColor = 'text-red-600';
        if (item.progress >= 90) {
            progressColor = 'text-green-600';
        } else if (item.progress >= 70) {
            progressColor = 'text-yellow-600';
        }
        
        html += `
            <tr class="${rowClass}" ${onclick}>
                <td class="px-4 py-3 text-center text-sm text-gray-700 border border-gray-200">${item.no}</td>
                <td class="px-4 py-3 text-sm border border-gray-200">
                    <div class="flex items-center justify-between">
                        <span class="${hasPCL ? 'text-blue-600 hover:text-blue-800 font-medium' : 'text-gray-900'}">${item.nama}</span>
                        ${hasPCL ? '<i class="fas fa-chevron-right text-gray-400"></i>' : ''}
                    </div>
                </td>
                <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900 border border-gray-200">${item.target}</td>
                <td class="px-4 py-3 text-center text-sm font-bold ${progressColor} border border-gray-200">${item.progress}%</td>
                <td class="px-4 py-3 text-center border border-gray-200">
                    <button onclick="event.stopPropagation(); approveData(${item.no})" class="px-3 py-1 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded transition-colors">
                        <i class="fas fa-check mr-1"></i>Approve
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
    
    // Update showing info
    document.getElementById('showingInfo').textContent = `1 to ${data.length} of ${data.length}`;
}

// Fungsi tampilkan modal detail PCL
function showPCLDetail(pmlName, index) {
    const kegiatan = document.getElementById('filterKegiatan').value;
    const data = dataLaporan[kegiatan][index];
    
    document.getElementById('modalPMLName').textContent = pmlName;
    
    let html = '';
    data.pcl.forEach((pcl, i) => {
        const progressPercent = (pcl.progress / pcl.target * 100).toFixed(0);
        
        let statusBadge = '';
        if (pcl.status === 'Approved') {
            statusBadge = '<span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-medium rounded-full"><i class="fas fa-check-circle mr-1"></i>Approved</span>';
        } else if (pcl.status === 'Pending') {
            statusBadge = '<span class="px-3 py-1 bg-yellow-100 text-yellow-800 text-xs font-medium rounded-full"><i class="fas fa-clock mr-1"></i>Pending</span>';
        } else {
            statusBadge = '<span class="px-3 py-1 bg-red-100 text-red-800 text-xs font-medium rounded-full"><i class="fas fa-times-circle mr-1"></i>Rejected</span>';
        }
        
        html += `
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-center text-sm text-gray-700 border border-gray-200">${i + 1}</td>
                <td class="px-4 py-3 text-sm text-gray-900 border border-gray-200">${pcl.nama}</td>
                <td class="px-4 py-3 text-center text-sm text-gray-700 border border-gray-200">${pcl.target}</td>
                <td class="px-4 py-3 border border-gray-200">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: ${progressPercent}%"></div>
                        </div>
                        <span class="text-sm font-medium text-gray-700 whitespace-nowrap">${pcl.progress}/${pcl.target}</span>
                    </div>
                </td>
                <td class="px-4 py-3 text-center border border-gray-200">
                    ${statusBadge}
                </td>
            </tr>
        `;
    });
    
    document.getElementById('modalTableBody').innerHTML = html;
    document.getElementById('pclModal').classList.remove('hidden');
}

// Fungsi close modal
function closeModal() {
    document.getElementById('pclModal').classList.add('hidden');
}

// Fungsi search
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('approvalTable');
    const tbody = table.getElementsByTagName('tbody')[0];
    const rows = tbody.getElementsByTagName('tr');
    
    for (let i = 0; i < rows.length; i++) {
        const nameCell = rows[i].getElementsByTagName('td')[1];
        if (nameCell) {
            const txtValue = nameCell.textContent || nameCell.innerText;
            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                rows[i].style.display = '';
            } else {
                rows[i].style.display = 'none';
            }
        }
    }
}

// Fungsi filter table
function filterTable() {
    // Reset table saat ganti filter
    document.getElementById('tableBody').innerHTML = '';
}

// Close modal when clicking outside
document.getElementById('pclModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Fungsi approve data
function approveData(id) {
    if (confirm('Apakah Anda yakin ingin menyetujui laporan ini?')) {
        alert(`Laporan PML ID ${id} telah disetujui!`);
        // TODO: Kirim request ke server untuk approve
        // fetch('/approval-laporan/approve/' + id, { method: 'POST' })
    }
}
</script>

<?= $this->endSection() ?>