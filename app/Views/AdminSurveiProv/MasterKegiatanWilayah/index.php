<?= $this->extend('layouts/adminprov_layout') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Master Kegiatan Wilayah</h1>
    <p class="text-gray-600 mt-1">Kelola data target survei untuk setiap kab/kota</p>
</div>

<!-- Main Card -->
<div class="card">
    <!-- Search and Add Button -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <!-- Search Box -->
        <div class="relative w-full sm:w-96">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" id="searchInput"
                class="input-field w-full pl-10"
                placeholder="Cari kegiatan detail atau satuan..."
                onkeyup="searchTable()">
        </div>

        <!-- Add Button -->
        <a href="<?= base_url('adminsurvei/master-kegiatan-wilayah/create') ?>"
            class="btn-primary whitespace-nowrap w-full sm:w-auto text-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Kegiatan
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full" id="kegiatanDetailTable">
            <thead>
                <tr class="border-b border-gray-200 bg-gray-50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-16">No</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kegiatan Detail Proses</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Kab/Kota</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Tanggal Mulai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Tanggal Selesai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Keterangan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Target Wilayah</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($kegiatanWilayah)) : ?>
                    <?php foreach ($kegiatanWilayah as $index => $kg) : ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 text-sm text-gray-900"><?= $index + 1 ?></td>
                            <td class="px-4 py-4 text-sm text-gray-900"><?= esc($kg['nama_kegiatan_detail_proses']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($kg['nama_kabupaten']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($kg['tanggal_mulai']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($kg['tanggal_selesai']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($kg['keterangan']) ?></td>
                            <td class="px-4 py-4 text-center text-gray-900 font-medium"><?= esc($kg['target_wilayah']); ?></td>
                            <td class="px-4 py-4 text-center">
                                <a href="<?= base_url('adminsurvei/master-kegiatan-wilayah/edit/' . $kg['id_kegiatan_wilayah']) ?>"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button onclick="confirmDelete(<?= $kg['id_kegiatan_wilayah'] ?>, '<?= esc($kg['keterangan']) ?>')"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="10" class="px-4 py-6 text-center text-gray-500">
                            Belum ada data kegiatan detail proses.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // 🔍 Fitur pencarian tabel
    function searchTable() {
        const input = document.getElementById('searchInput');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('kegiatanDetailTable');
        const rows = table.getElementsByTagName('tr');

        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            const cells = row.getElementsByTagName('td');
            let found = false;
            for (let j = 1; j < cells.length - 1; j++) {
                if (cells[j] && cells[j].innerText.toLowerCase().includes(filter)) {
                    found = true;
                    break;
                }
            }
            row.style.display = found ? '' : 'none';
        }
    }

    // 🗑️ Konfirmasi hapus
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Data?',
            html: `Yakin ingin menghapus <strong>${name}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal',
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `<?= base_url('adminsurvei/master-kegiatan-wilayah/delete/') ?>${id}`;
                const hiddenMethod = document.createElement('input');
                hiddenMethod.type = 'hidden';
                hiddenMethod.name = '_method';
                hiddenMethod.value = 'DELETE';
                const csrf = document.createElement('input');
                csrf.type = 'hidden';
                csrf.name = '<?= csrf_token() ?>';
                csrf.value = '<?= csrf_hash() ?>';
                form.appendChild(hiddenMethod);
                form.appendChild(csrf);
                document.body.appendChild(form);
                form.submit();
            }
        });
    }

    //Alert sukses dari session flashdata
    <?php if (session()->getFlashdata('success')) : ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "<?= session()->getFlashdata('success') ?>",
            confirmButtonColor: '#3b82f6'
        });
    <?php endif; ?>

    // ⚠️ Alert error dari session flashdata
    <?php if (session()->getFlashdata('error')) : ?>
        Swal.fire({
            icon: 'error',
            title: 'Gagal!',
            text: "<?= session()->getFlashdata('error') ?>",
            confirmButtonColor: '#ef4444'
        });
    <?php endif; ?>
</script>

<?= $this->endSection() ?>
