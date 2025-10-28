<?= $this->extend('layouts/adminprov_layout') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Kelola Master Kegiatan Detail Proses</h1>
    <p class="text-gray-600 mt-1">
        Kelola data detail kegiatan survei/sensus beserta satuan, periode, dan target pelaksanaan
    </p>
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
        <a href="<?= base_url('adminsurvei/master-kegiatan-detail-proses/create') ?>" 
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
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Master Kegiatan Detail</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Nama Kegiatan Detail Proses</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Satuan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Tanggal Mulai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Tanggal Selesai</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-40">Keterangan</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Periode</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-24">Target</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-32">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (!empty($kegiatanDetails)) : ?>
                    <?php foreach ($kegiatanDetails as $index => $detail) : ?>
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-4 py-4 text-sm text-gray-900"><?= $index + 1 ?></td>
                            <td class="px-4 py-4 text-sm text-gray-900"><?= esc($detail['nama_kegiatan_detail']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($detail['nama_kegiatan_detail_proses']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($detail['satuan']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($detail['tanggal_mulai']) ?></td>
                            <td class="px-4 py-4 text-sm text-gray-600"><?= esc($detail['tanggal_selesai']) ?></td>
                            <td class="px-4 py-4 text-sm text-center text-gray-600"><?= esc($detail['keterangan']) ?></td>
                            <td class="px-4 py-4 text-center"><span class="badge badge-info"><?= esc($detail['periode']) ?></span></td>
                            <td class="px-4 py-4 text-center text-gray-900 font-medium"><?= esc($detail['target']) ?></td>
                            <td class="px-4 py-4 text-center">
                                <a href="<?= base_url('adminsurvei/master-kegiatan-detail-proses/edit/' . $detail['id_kegiatan_detail_proses']) ?>" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"><i class="fas fa-edit"></i></a>
                                <button onclick="confirmDelete(<?= $detail['id_kegiatan_detail_proses'] ?>, '<?= esc($detail['nama_kegiatan_detail_proses']) ?>')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg"><i class="fas fa-trash"></i></button>
                                    <input type="hidden" name="_method" value="DELETE">

                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="10" class="px-4 py-6 text-center text-gray-500">Belum ada data kegiatan detail proses.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <form id="deleteForm" method="post" style="display:none;">
    <?= csrf_field() ?>
    <input type="hidden" name="_method" value="DELETE">
</form>

    </div>

    <!-- Footer -->
    <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <p class="text-sm text-gray-600">
            Menampilkan <span class="font-medium"><?= isset($kegiatanDetails) ? count($kegiatanDetails) : 0 ?></span> data kegiatan detail proses.
        </p>
    </div>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function searchTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase();
    const rows = document.querySelectorAll('#kegiatanDetailTable tbody tr');

    rows.forEach(row => {
        const text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
}

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
            const form = document.getElementById('deleteForm');
            form.action = `<?= base_url('adminsurvei/master-kegiatan-detail-proses/delete/') ?>${id}`;
            form.submit();
        }
    });
}


// Alert sukses setelah tambah/edit/hapus
<?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: '<?= session()->getFlashdata('success') ?>',
        showConfirmButton: false,
        timer: 2000
    });
<?php endif; ?>
</script>

<?= $this->endSection() ?>
