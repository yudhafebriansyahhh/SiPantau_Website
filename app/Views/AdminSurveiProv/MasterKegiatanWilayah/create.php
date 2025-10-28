<?= $this->extend('layouts/adminprov_layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminprov/master-kegiatan-wilayah') ?>" class="hover:text-blue-600 transition-colors">
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
                    Lengkapi semua informasi kegiatan dengan benar. Field bertanda
                    <span class="text-red-500 font-semibold">*</span> wajib diisi.
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

        <!-- Pilih Kabupaten -->
        <div class="mb-6">
            <label for="kabupaten" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Kabupaten/Kota <span class="text-red-500">*</span>
            </label>
            <select id="kabupaten" name="kabupaten" class="input-field" required>
                <option value="">-- Pilih Kabupaten/Kota --</option>
                <?php foreach ($Kab as $item): ?>
                    <option value="<?= esc($item['id_kabupaten']) ?>"
                        <?= old('kabupaten') == $item['id_kabupaten'] ? 'selected' : '' ?>>
                        <?= esc($item['nama_kabupaten']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Target Wilayah -->
        <div class="mb-6">
            <label for="target" class="block text-sm font-medium text-gray-700 mb-2">
                Target Wilayah <span class="text-red-500">*</span>
            </label>
            <input type="number" id="target" name="target" class="input-field"
                placeholder="Masukkan target wilayah" min="1" value="<?= old('target') ?>" required>
            <p id="sisaInfo" class="text-sm text-gray-500 mt-1"></p>
        </div>

        <!-- Keterangan -->
        <div class="mb-6">
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                Keterangan <span class="text-red-500">*</span>
            </label>
            <textarea id="keterangan" name="keterangan" rows="3" class="input-field resize-none"
                placeholder="Masukkan keterangan" required><?= old('keterangan') ?></textarea>
        </div>

        <div class="border-t border-gray-200 my-6"></div>

        <div class="flex justify-center">
            <button type="submit" class="btn-primary px-24 py-3 text-base">Simpan</button>
        </div>
    </form>
</div>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const kegiatanSelect = document.getElementById('kegiatan_detail');
    const targetInput = document.getElementById('target');
    const sisaInfo = document.getElementById('sisaInfo');

    kegiatanSelect.addEventListener('change', () => {
        const idKegiatan = kegiatanSelect.value;
        if (!idKegiatan) {
            targetInput.value = '';
            targetInput.placeholder = 'Masukkan target wilayah';
            sisaInfo.textContent = '';
            return;
        }

        fetch(`<?= base_url('adminsurvei/master-kegiatan-wilayah/sisa-target/') ?>${idKegiatan}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    sisaInfo.textContent = data.error;
                    targetInput.value = '';
                    targetInput.placeholder = 'Masukkan target wilayah';
                } else {
                    sisaInfo.innerHTML = `
                        <strong>Target Provinsi:</strong> ${data.target_prov} |
                        <strong>Terpakai:</strong> ${data.terpakai} |
                        <strong>Sisa:</strong> ${data.sisa}
                    `;
                    targetInput.value = data.sisa; // otomatis isi dengan sisa target
                    targetInput.max = data.sisa;   // batasi input maksimal sisa
                    targetInput.placeholder = `Sisa target: ${data.sisa}`;
                }
            })
            .catch(() => {
                sisaInfo.textContent = "⚠️ Gagal memuat data sisa target.";
            });
    });

    // Validasi sebelum submit
    document.getElementById('formMasterKegiatan').addEventListener('submit', function(e) {
        const target = parseInt(targetInput.value);
        const max = parseInt(targetInput.max || 0);

        if (max > 0 && target > max) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Target Melebihi Batas',
                text: `Target yang dimasukkan melebihi sisa target (${max}).`,
                confirmButtonColor: '#ef4444'
            });
        }
    });
});
</script>

<?= $this->endSection() ?>
