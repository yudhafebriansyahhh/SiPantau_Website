<?= $this->extend('layouts/adminprov_layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei/master-kegiatan-wilayah') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Kegiatan Wilayah
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Tambah Master Kegiatan Wilayah</h1>
    <p class="text-gray-600 mt-1">Buat data master kegiatan wilayah baru</p>
</div>

<div class="card max-w-5xl">    
    <form id="formMasterKegiatan" method="POST" action="<?= base_url('adminsurvei/master-kegiatan-wilayah/store') ?>">
        <?= csrf_field() ?>

        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                <p class="text-sm text-blue-700">
                    Lengkapi semua informasi kegiatan dengan benar. Field bertanda <span class="text-red-500 font-semibold">*</span> wajib diisi.
                </p>
            </div>
        </div>

        <!-- Pilih Kegiatan Detail -->
        <div class="mb-6">
            <label for="kegiatan_detail" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Kegiatan Detail Proses <span class="text-red-500">*</span>
            </label>
            <select id="kegiatan_detail" name="kegiatan_detail" class="input-field" required>
                <option value="">-- Pilih Kegiatan Detail Proses --</option>
                <?php foreach ($kegiatanDetailProses as $item): ?>
                    <option value="<?= esc($item['id_kegiatan_detail_proses']) ?>" 
                        <?= old('kegiatan_detail') == $item['id_kegiatan_detail_proses'] ? 'selected' : '' ?>>
                        <?= esc($item['nama_kegiatan_detail_proses']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Pilih Kab/Kota -->
        <div class="mb-6">
            <label for="kab" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Kab/Kota <span class="text-red-500">*</span>
            </label>
            <select id="kab" name="kab" class="input-field" required>
                <option value="">-- Pilih Kab/Kota --</option>
                <?php foreach ($Kab as $item): ?>
                    <option value="<?= esc($item['idkab']) ?>" 
                        <?= old('kab') == $item['idkab'] ? 'selected' : '' ?>>
                        <?= esc($item['nmkab']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Keterangan -->
        <div class="mb-6">
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                Keterangan <span class="text-red-500">*</span>
            </label>
            <textarea id="keterangan" name="keterangan" rows="3" class="input-field resize-none" placeholder="Masukkan keterangan" required><?= old('keterangan') ?></textarea>
        </div>

        <!-- Target -->
        <div class="mb-6">
            <label for="target" class="block text-sm font-medium text-gray-700 mb-2">
                Target Wilayah <span class="text-red-500">*</span>
            </label>
            <input type="number" id="target" name="target" class="input-field" placeholder="Masukkan target" min="1" value="<?= old('target') ?>" required>
        </div>

        <div class="border-t border-gray-200 my-6"></div>

        <div class="flex justify-center">
            <button type="submit" class="btn-primary px-24 py-3 text-base">Simpan</button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.getElementById('formMasterKegiatan').addEventListener('submit', function(e) {
    e.preventDefault();

    const kegiatanDetail = document.getElementById('kegiatan_detail').value;
    const kab = document.getElementById('kab').value;
    const target = document.getElementById('target').value;
    const keterangan = document.getElementById('keterangan').value.trim();

    if (!kegiatanDetail || !kab || !target || !keterangan) {
        Swal.fire({
            icon: 'error',
            title: 'Form Tidak Lengkap',
            text: 'Harap lengkapi semua field yang wajib diisi!',
            confirmButtonColor: '#3b82f6'
        });
        return;
    }

    Swal.fire({
        title: 'Simpan Data?',
        text: 'Apakah Anda yakin ingin menambahkan kegiatan wilayah ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3b82f6',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Simpan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) e.target.submit();
    });
});
</script>

<?= $this->endSection() ?>
