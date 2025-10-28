<?= $this->extend('layouts/adminprov_layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminprov/master-kegiatan-wilayah') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Kegiatan Wilayah
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Edit Master Kegiatan Wilayah</h1>
    <p class="text-gray-600 mt-1">Perbarui data kegiatan wilayah</p>
</div>

<div class="card max-w-5xl">
    <form id="formMasterKegiatan" method="POST"
        action="<?= base_url('adminsurvei/master-kegiatan-wilayah/update/' . $wilayah['id_kegiatan_wilayah']) ?>">
        <?= csrf_field() ?>

        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                <p class="text-sm text-blue-700">
                    Ubah informasi kegiatan dengan benar. Field bertanda
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
                        <?= $wilayah['id_kegiatan_detail_proses'] == $item['id_kegiatan_detail_proses'] ? 'selected' : '' ?>>
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
                        <?= $wilayah['id_kabupaten'] == $item['id_kabupaten'] ? 'selected' : '' ?>>
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
                placeholder="Masukkan target wilayah" min="1"
                value="<?= esc($wilayah['target_wilayah']) ?>" required>
            <p id="sisaInfo" class="text-sm text-gray-500 mt-1"></p>
        </div>

        <!-- Keterangan -->
        <div class="mb-6">
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                Keterangan <span class="text-red-500">*</span>
            </label>
            <textarea id="keterangan" name="keterangan" rows="3" class="input-field resize-none" required><?= esc($wilayah['keterangan']) ?></textarea>
        </div>

        <div class="border-t border-gray-200 my-6"></div>

        <div class="flex justify-center">
            <button type="submit" class="btn-primary px-24 py-3 text-base">Update</button>
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

    // 🔹 Fungsi untuk ambil dan tampilkan info target
    const loadTargetInfo = (idKegiatan) => {
        if (!idKegiatan) {
            sisaInfo.textContent = '';
            return;
        }

        fetch(`<?= base_url('adminsurvei/master-kegiatan-wilayah/sisa-target/') ?>${idKegiatan}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    sisaInfo.textContent = data.error;
                    return;
                }

                sisaInfo.innerHTML = `
                    🎯 <strong>Target Provinsi:</strong> ${data.target_prov} |
                    <strong>Terpakai:</strong> ${data.terpakai} |
                    <strong>Sisa:</strong> ${data.sisa}
                `;

                targetInput.max = data.sisa + parseInt("<?= $wilayah['target_wilayah'] ?>");
            })
            .catch(() => {
                sisaInfo.textContent = "⚠️ Gagal memuat data sisa target.";
            });
    };

    // Jalankan saat pertama kali halaman dimuat (untuk kegiatan yang sudah terpilih)
    loadTargetInfo(kegiatanSelect.value);

    // Jalankan setiap kali dropdown berubah
    kegiatanSelect.addEventListener('change', () => {
        loadTargetInfo(kegiatanSelect.value);
    });

    // 🔹 Validasi sebelum submit
    document.getElementById('formMasterKegiatan').addEventListener('submit', function(e) {
        e.preventDefault();
        const target = parseInt(targetInput.value);
        const max = parseInt(targetInput.max || 0);

        if (max > 0 && target > max) {
            Swal.fire({
                icon: 'error',
                title: 'Target Melebihi Batas',
                text: `Target yang dimasukkan melebihi batas maksimal (${max}).`,
                confirmButtonColor: '#ef4444'
            });
            return;
        }

        Swal.fire({
            title: 'Update Data?',
            text: 'Apakah Anda yakin ingin memperbarui data kegiatan wilayah ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Update',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) e.target.submit();
        });
    });
});
</script>

<?= $this->endSection() ?>
