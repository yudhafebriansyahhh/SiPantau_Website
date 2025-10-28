<?= $this->extend('layouts/adminprov_layout') ?>
<?= $this->section('content') ?>

<div class="mb-6">
    <div class="flex items-center text-sm text-gray-600 mb-4">
        <a href="<?= base_url('adminsurvei/master-kegiatan-wilayah') ?>" class="hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Master Kegiatan Wilayah
        </a>
    </div>
    <h1 class="text-2xl font-bold text-gray-900">Edit Master Kegiatan Wilayah</h1>
    <p class="text-gray-600 mt-1">Perbarui data kegiatan wilayah</p>
</div>

<div class="card max-w-5xl">
    <!-- Flash Messages Error dari Session -->
    <?php if (session()->getFlashdata('error')): ?>
    <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-600 mr-3 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-red-800">Error!</p>
                <p class="text-sm text-red-700 mt-1"><?= session()->getFlashdata('error') ?></p>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Display Validation Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
    <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start">
            <i class="fas fa-exclamation-triangle text-red-600 mr-3 mt-0.5"></i>
            <div class="flex-1">
                <p class="text-sm font-medium text-red-800 mb-2">Terdapat kesalahan pada form:</p>
                <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                        <li><?= esc($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

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
            <select id="kegiatan_detail" name="kegiatan_detail" 
                class="input-field <?= (isset($validation) && $validation->hasError('kegiatan_detail')) ? 'border-red-500' : '' ?>" required>
                <option value="">-- Pilih Kegiatan Detail Proses --</option>
                <?php foreach ($kegiatanDetailProses as $item): ?>
                    <option value="<?= esc($item['id_kegiatan_detail_proses']) ?>"
                        <?= (old('kegiatan_detail', $wilayah['id_kegiatan_detail_proses']) == $item['id_kegiatan_detail_proses']) ? 'selected' : '' ?>>
                        <?= esc($item['nama_kegiatan_detail_proses']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($validation) && $validation->hasError('kegiatan_detail')): ?>
                <p class="text-red-500 text-xs mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <?= $validation->getError('kegiatan_detail') ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Pilih Kabupaten -->
        <div class="mb-6">
            <label for="kabupaten" class="block text-sm font-medium text-gray-700 mb-2">
                Pilih Kabupaten/Kota <span class="text-red-500">*</span>
            </label>
            <select id="kabupaten" name="kabupaten" 
                class="input-field <?= (isset($validation) && $validation->hasError('kabupaten')) ? 'border-red-500' : '' ?>" required>
                <option value="">-- Pilih Kabupaten/Kota --</option>
                <?php foreach ($Kab as $item): ?>
                    <option value="<?= esc($item['id_kabupaten']) ?>"
                        <?= (old('kabupaten', $wilayah['id_kabupaten']) == $item['id_kabupaten']) ? 'selected' : '' ?>>
                        <?= esc($item['nama_kabupaten']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($validation) && $validation->hasError('kabupaten')): ?>
                <p class="text-red-500 text-xs mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <?= $validation->getError('kabupaten') ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Target Wilayah -->
        <div class="mb-6">
            <label for="target" class="block text-sm font-medium text-gray-700 mb-2">
                Target Wilayah <span class="text-red-500">*</span>
            </label>
            <input type="number" id="target" name="target" 
                class="input-field <?= (isset($validation) && $validation->hasError('target')) ? 'border-red-500' : '' ?>"
                placeholder="Masukkan target wilayah" min="1"
                value="<?= old('target', esc($wilayah['target_wilayah'])) ?>" required>
            <p id="sisaInfo" class="text-sm text-gray-500 mt-1"></p>
            <?php if (isset($validation) && $validation->hasError('target')): ?>
                <p class="text-red-500 text-xs mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <?= $validation->getError('target') ?>
                </p>
            <?php endif; ?>
        </div>

        <!-- Keterangan -->
        <div class="mb-6">
            <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">
                Keterangan <span class="text-red-500">*</span>
            </label>
            <textarea id="keterangan" name="keterangan" rows="3" 
                class="input-field resize-none <?= (isset($validation) && $validation->hasError('keterangan')) ? 'border-red-500' : '' ?>" 
                placeholder="Masukkan keterangan" required><?= old('keterangan', esc($wilayah['keterangan'])) ?></textarea>
            <?php if (isset($validation) && $validation->hasError('keterangan')): ?>
                <p class="text-red-500 text-xs mt-1">
                    <i class="fas fa-exclamation-circle mr-1"></i>
                    <?= $validation->getError('keterangan') ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="border-t border-gray-200 my-6"></div>

        <!-- Action Buttons -->
        <div class="flex gap-3 justify-center">
            <a href="<?= base_url('adminsurvei/master-kegiatan-wilayah') ?>" 
               class="btn-secondary px-12 py-3 text-base text-center">
                <i class="fas fa-times mr-2"></i>
                Batal
            </a>
            <button type="submit" class="btn-primary px-12 py-3 text-base">
                <i class="fas fa-save mr-2"></i>
                Update
            </button>
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
    const originalTarget = parseInt("<?= $wilayah['target_wilayah'] ?>");

    // 🔹 Fungsi untuk ambil dan tampilkan info target
    const loadTargetInfo = (idKegiatan) => {
        if (!idKegiatan) {
            sisaInfo.textContent = '';
            targetInput.removeAttribute('max');
            return;
        }

        fetch(`<?= base_url('adminsurvei/master-kegiatan-wilayah/sisa-target/') ?>${idKegiatan}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    sisaInfo.innerHTML = `<span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>${data.error}</span>`;
                    targetInput.removeAttribute('max');
                    return;
                }

                // Calculate available target (sisa + original target since we're editing)
                const availableTarget = data.sisa + originalTarget;

                sisaInfo.innerHTML = `
                    <span class="inline-flex items-center gap-3 text-xs">
                        <span><strong>Target Provinsi:</strong> ${data.target_prov.toLocaleString()}</span>
                        <span class="text-gray-300">|</span>
                        <span><strong>Terpakai:</strong> ${data.terpakai.toLocaleString()}</span>
                        <span class="text-gray-300">|</span>
                        <span><strong>Sisa:</strong> ${data.sisa.toLocaleString()}</span>
                        <span class="text-gray-300">|</span>
                        <span class="text-blue-600 font-semibold"><strong>Tersedia untuk Edit:</strong> ${availableTarget.toLocaleString()}</span>
                    </span>
                `;

                targetInput.max = availableTarget;
                targetInput.placeholder = `Maksimal: ${availableTarget.toLocaleString()}`;
            })
            .catch(() => {
                sisaInfo.innerHTML = '<span class="text-red-600"><i class="fas fa-exclamation-triangle mr-1"></i>Gagal memuat data sisa target.</span>';
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

        if (!kegiatanSelect.value) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Silakan pilih kegiatan detail proses terlebih dahulu.',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        if (!document.getElementById('kabupaten').value) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Silakan pilih kabupaten/kota terlebih dahulu.',
                confirmButtonColor: '#3b82f6'
            });
            return;
        }

        if (max > 0 && target > max) {
            Swal.fire({
                icon: 'error',
                title: 'Target Melebihi Batas',
                text: `Target yang dimasukkan (${target.toLocaleString()}) melebihi batas maksimal yang tersedia (${max.toLocaleString()}).`,
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
            if (result.isConfirmed) {
                e.target.submit();
            }
        });
    });
});

// Alert untuk form validation errors
<?php if (session()->getFlashdata('errors')): ?>
    Swal.fire({
        icon: 'error',
        title: 'Validasi Gagal',
        html: 'Silakan periksa kembali form Anda dan lengkapi semua field yang diperlukan.',
        confirmButtonColor: '#ef4444'
    });
<?php endif; ?>
</script>

<?= $this->endSection() ?>