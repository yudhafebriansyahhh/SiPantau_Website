<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Role - SiPantau</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            
            <!-- Card -->
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 p-6 text-center">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-user-circle text-4xl text-blue-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white mb-2">Pilih Role</h1>
                    <p class="text-blue-100 text-sm">
                        Selamat datang, <strong><?= esc($user['nama_user']) ?></strong>
                    </p>
                </div>

                <!-- Content -->
                <div class="p-6">
                    
                    <!-- Info Alert -->
                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-blue-600 mt-0.5 mr-3"></i>
                            <div class="text-sm text-blue-700">
                                <p class="font-medium mb-1">Anda memiliki beberapa role</p>
                                <p>Silakan pilih role yang ingin Anda gunakan untuk sesi ini</p>
                            </div>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('error')): ?>
                    <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                            <p class="text-sm text-red-700"><?= session()->getFlashdata('error') ?></p>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Form -->
                    <form action="<?= base_url('login/process-role-selection') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="space-y-3 mb-6">
                            <?php 
                            $roleIcons = [
                                1 => ['icon' => 'fa-crown', 'color' => 'blue', 'desc' => 'Akses penuh sistem'],
                                2 => ['icon' => 'fa-user-shield', 'color' => 'indigo', 'desc' => 'Admin tingkat provinsi'],
                                3 => ['icon' => 'fa-user-tie', 'color' => 'purple', 'desc' => 'Admin kabupaten/kota'],
                                4 => ['icon' => 'fa-eye', 'color' => 'green', 'desc' => 'Pemantau lapangan']
                            ];
                            
                            foreach ($roles as $role): 
                                $roleId = $role['id_roleuser'];
                                $iconData = $roleIcons[$roleId] ?? ['icon' => 'fa-user', 'color' => 'gray', 'desc' => 'Role user'];
                            ?>
                            <label class="flex items-center p-4 border-2 border-gray-200 rounded-xl cursor-pointer hover:border-<?= $iconData['color'] ?>-500 hover:bg-<?= $iconData['color'] ?>-50 transition-all duration-200 group">
                                <input type="radio" 
                                       name="selected_role" 
                                       value="<?= $roleId ?>" 
                                       class="w-5 h-5 text-<?= $iconData['color'] ?>-600 focus:ring-<?= $iconData['color'] ?>-500"
                                       required>
                                
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-<?= $iconData['color'] ?>-100 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas <?= $iconData['icon'] ?> text-<?= $iconData['color'] ?>-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900"><?= esc($role['roleuser']) ?></p>
                                            <p class="text-xs text-gray-500"><?= $iconData['desc'] ?></p>
                                        </div>
                                    </div>
                                </div>
                                
                                <i class="fas fa-chevron-right text-gray-400 group-hover:text-<?= $iconData['color'] ?>-600 transition-colors"></i>
                            </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-3">
                            <a href="<?= base_url('login/logout') ?>" 
                               class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors text-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Batal
                            </a>
                            <button type="submit" 
                                    class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg hover:shadow-xl">
                                Lanjutkan
                                <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </form>

                </div>
            </div>

            <!-- Footer -->
            <p class="text-center text-gray-600 text-sm mt-6">
                © <?= date('Y') ?> SiPantau - Sistem Informasi Pemantauan
            </p>

        </div>
    </div>

</body>
</html>