<?= $this->extend('layouts/adminprov_layout') ?>
<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei/master-kegiatan-detail-proses') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Kegiatan Detail Proses
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Edit Master Kegiatan Detail Proses</h1>
    <p class="text-gray-600 mt-1">Ubah data master kegiatan detail proses</p>
</div>

<!-- Form Card -->
<div class="card max-w-5xl">
    <form id="formMasterKegiatan" method="POST" action="<?= base_url('adminsurvei/master-kegiatan-detail-proses/update/' . $detailProses['id_kegiatan_detail_proses']) ?>">
        <?= csrf_field() ?>

        <!-- Pilih Kegiatan Detail -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Kegiatan Detail<span class="text-red-500">*</span></label>
            <select name="kegiatan_detail" class="input-field" required>
                <option value="">-- Pilih Kegiatan Detail --</option>
                <?php foreach ($kegiatanDetailList as $item): ?>
                    <option value="<?= esc($item['id_kegiatan_detail']) ?>"
                        <?= $detailProses['id_kegiatan_detail'] == $item['id_kegiatan_detail'] ? 'selected' : '' ?>>
                        <?= esc($item['nama_kegiatan_detail']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Nama Kegiatan Detail Proses -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kegiatan Detail Proses <span class="text-red-500">*</span></label>
            <input type="text" name="nama_proses" value="<?= esc($detailProses['nama_kegiatan_detail_proses']) ?>" class="input-field" required>
        </div>

        <!-- Tanggal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai<span class="text-red-500">*</span></label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?= esc($detailProses['tanggal_mulai']) ?>" class="input-field" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai<span class="text-red-500">*</span></label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="<?= esc($detailProses['tanggal_selesai']) ?>" class="input-field" required>
            </div>
        </div>

        <!-- Satuan -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Satuan<span class="text-red-500">*</span></label>
            <select name="satuan" class="input-field" required>
                <option value="">-- Pilih Satuan --</option>
                <?php 
                    $satuanList = ['unit', 'orang', 'dokumen', 'paket', 'kegiatan'];
                    foreach ($satuanList as $satuan):
                ?>
                    <option value="<?= $satuan ?>" <?= $detailProses['satuan'] == $satuan ? 'selected' : '' ?>><?= ucfirst($satuan) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Keterangan -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan<span class="text-red-500">*</span></label>
            <textarea name="keterangan" rows="3" class="input-field resize-none" required><?= esc($detailProses['keterangan']) ?></textarea>
        </div>

        <!-- Periode -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Periode<span class="text-red-500">*</span></label>
            <input type="text" name="periode" value="<?= esc($detailProses['periode']) ?>" class="input-field" required>
        </div>

        <!-- Target -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Target<span class="text-red-500">*</span></label>
            <input type="number" name="target" value="<?= esc($detailProses['target']) ?>" class="input-field" min="0" required>
        </div>

        <!-- Target Hari Pertama & Selesai -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Hari Pertama<span class="text-red-500">*</span></label>
                <input type="number" name="persentase_target_awal" value="<?= esc($detailProses['persentase_target_awal']) ?>" class="input-field" min="0" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Target Tanggal Selesai<span class="text-red-500">*</span></label>
                <input type="date" id="tanggal_selesai_target" name="tanggal_selesai_target" value="<?= esc($detailProses['tanggal_selesai_target']) ?>" class="input-field" required>
            </div>
        </div>

        <div class="flex justify-center mt-8">
            <button type="submit" class="btn-primary px-24 py-3 text-base">Perbarui</button>
        </div>
    </form>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('formMasterKegiatan').addEventListener('submit', function(e) {
    e.preventDefault();

    const tanggalMulai = new Date(document.getElementById('tanggal_mulai').value);
    const tanggalSelesai = new Date(document.getElementById('tanggal_selesai').value);
    const tanggal100 = new Date(document.getElementById('tanggal_selesai_target').value);

    // === Validasi tanggal selesai tidak boleh < tanggal mulai ===
    if (tanggalSelesai < tanggalMulai) {
        Swal.fire({
            icon: 'error',
            title: 'Tanggal Tidak Valid',
            text: 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai!',
            confirmButtonColor: '#3b82f6',
        });
        return;
    }

    // === Validasi target 100% tidak boleh < tanggal mulai ===
    if (tanggal100 < tanggalMulai) {
        Swal.fire({
            icon: 'error',
            title: 'Target 100% Tidak Valid',
            text: 'Tanggal target 100% tidak boleh lebih awal dari tanggal mulai!',
            confirmButtonColor: '#3b82f6',
        });
        return;
    }

    // === Validasi target 100% tidak boleh > tanggal selesai ===
    if (tanggal100 > tanggalSelesai) {
        Swal.fire({
            icon: 'error',
            title: 'Target 100% Tidak Valid',
            text: 'Tanggal target 100% tidak boleh melebihi tanggal selesai!',
            confirmButtonColor: '#3b82f6',
        });
        return;
    }

    // === Konfirmasi jika semua valid ===
    Swal.fire({
        title: 'Konfirmasi Perubahan',
        text: 'Apakah data yang diubah sudah benar?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Simpan Perubahan',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
    }).then((result) => {
        if (result.isConfirmed) this.submit();
    });
});
</script>

<?= $this->endSection() ?>
