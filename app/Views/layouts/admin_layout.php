<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - SiPantau</title>
    <!-- Di <head> section admin_layout.php, tambahkan: -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <!-- Tailwind CSS -->
    <link href="<?= base_url('assets/css/output.css') ?>" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>
<body class="bg-gray-50">
    
    <!-- Sidebar -->
    <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full lg:translate-x-0">
        <div class="h-full flex flex-col bg-white border-r border-gray-200">
            
            <!-- Logo -->
            <div class="h-20 border-b border-gray-200 px-6 py-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                        <img src="<?= base_url('assets/gambar/LOGO_BPS.png') ?>" alt="Logo BPS" class="w-12 h-12 object-contain"/>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-bold text-gray-900 leading-tight">SiPantau</span>
                        <span class="text-xs text-gray-600 leading-tight">Badan Pusat Statistik</span>
                    </div>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 overflow-y-auto scrollbar-thin py-4 px-3">
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a href="<?= base_url('admin') ?>" class="sidebar-link <?= ($active_menu ?? '') == 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-home w-5"></i>
                        <span class="ml-3">Dashboard</span>
                    </a>
                    
                    <!-- Calendar -->
                    <a href="<?= base_url('calendar') ?>" class="sidebar-link <?= ($active_menu ?? '') == 'calendar' ? 'active' : '' ?>">
                        <i class="far fa-calendar w-5"></i>
                        <span class="ml-3">Calendar</span>
                    </a>
                    
                    <!-- User Profile -->
                    <a href="<?= base_url('profile') ?>" class="sidebar-link <?= ($active_menu ?? '') == 'profile' ? 'active' : '' ?>">
                        <i class="far fa-user w-5"></i>
                        <span class="ml-3">User Profile</span>
                    </a>
                    
                    <!-- Divider -->
                    <div class="py-2">
                        <div class="border-t border-gray-200"></div>
                    </div>
                    
                    <!-- Task Menu -->
                    <div class="space-y-1">
                        <button onclick="toggleSubmenu('task')" class="sidebar-link w-full justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-database w-5"></i>
                                <span class="ml-3">Master Data</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="task-icon"></i>
                        </button>
                        <div id="task-submenu" class="hidden ml-8 space-y-1">
                            <a href="<?= base_url('kegiatan') ?>" class="sidebar-link text-sm <?= ($active_menu ?? '') == 'kegiatan' ? 'active' : '' ?>">
                                <span>Kegiatan</span>
                            </a>
                            <a href="<?= base_url('kegiatan-detail') ?>" class="sidebar-link text-sm <?= ($active_menu ?? '') == 'kegiatan-detail' ? 'active' : '' ?>">
                                <span>Detail Proses</span>
                            </a>
                            <a href="<?= base_url('kegiatan-wilayah') ?>" class="sidebar-link text-sm <?= ($active_menu ?? '') == 'kegiatan-wilayah' ? 'active' : '' ?>">
                                <span>Target Wilayah</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Forms Menu -->
                    <div class="space-y-1">
                        <button onclick="toggleSubmenu('forms')" class="sidebar-link w-full justify-between">
                            <div class="flex items-center">
                                <i class="far fa-file-alt w-5"></i>
                                <span class="ml-3">Forms</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="forms-icon"></i>
                        </button>
                        <div id="forms-submenu" class="hidden ml-8 space-y-1">
                            <a href="<?= base_url('petugas') ?>" class="sidebar-link text-sm <?= ($active_menu ?? '') == 'petugas' ? 'active' : '' ?>">
                                <span>Petugas</span>
                            </a>
                            <a href="<?= base_url('assignment') ?>" class="sidebar-link text-sm <?= ($active_menu ?? '') == 'assignment' ? 'active' : '' ?>">
                                <span>Assignment</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Tables Menu -->
                    <div class="space-y-1">
                        <button onclick="toggleSubmenu('tables')" class="sidebar-link w-full justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-table w-5"></i>
                                <span class="ml-3">Tables</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="tables-icon"></i>
                        </button>
                        <div id="tables-submenu" class="hidden ml-8 space-y-1">
                            <a href="<?= base_url('laporan') ?>" class="sidebar-link text-sm <?= ($active_menu ?? '') == 'laporan' ? 'active' : '' ?>">
                                <span>Laporan</span>
                            </a>
                            <a href="<?= base_url('monitoring') ?>" class="sidebar-link text-sm <?= ($active_menu ?? '') == 'monitoring' ? 'active' : '' ?>">
                                <span>Monitoring</span>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Pages Menu -->
                    <div class="space-y-1">
                        <button onclick="toggleSubmenu('pages')" class="sidebar-link w-full justify-between">
                            <div class="flex items-center">
                                <i class="far fa-copy w-5"></i>
                                <span class="ml-3">Pages</span>
                            </div>
                            <i class="fas fa-chevron-down text-xs transition-transform duration-200" id="pages-icon"></i>
                        </button>
                        <div id="pages-submenu" class="hidden ml-8 space-y-1">
                            <a href="<?= base_url('users') ?>" class="sidebar-link text-sm <?= ($active_menu ?? '') == 'users' ? 'active' : '' ?>">
                                <span>Users</span>
                            </a>
                            <a href="<?= base_url('settings') ?>" class="sidebar-link text-sm <?= ($active_menu ?? '') == 'settings' ? 'active' : '' ?>">
                                <span>Settings</span>
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Support Section -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Support</p>
                    <a href="<?= base_url('logout') ?>" class="sidebar-link text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt w-5"></i>
                        <span class="ml-3">Log Out</span>
                    </a>
                </div>
            </nav>
        </div>
    </aside>
    
    <!-- Main Content -->
    <div class="lg:ml-64">
        
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    
                    <!-- Left Section - Mobile Menu Button -->
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                    
                    <!-- Spacer for desktop -->
                    <div class="hidden lg:block"></div>
                    
                    <!-- Right Section -->
                    <div class="flex items-center space-x-4 ml-auto">
                        
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg">
                            <i class="far fa-bell text-xl"></i>
                            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                        </button>
                        
                        <!-- User Menu -->
                        <div class="relative">
                            <button onclick="toggleUserMenu()" class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-100">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white text-sm font-medium">SA</span>
                                </div>
                                <div class="hidden sm:block text-left">
                                    <p class="text-sm font-medium text-gray-900">Super Admin</p>
                                    <p class="text-xs text-gray-500">Admin Provinsi</p>
                                </div>
                                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                            </button>
                            
                            <!-- Dropdown -->
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1">
                                <a href="<?= base_url('profile') ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="far fa-user w-5"></i>
                                    <span class="ml-2">Profile</span>
                                </a>
                                <a href="<?= base_url('settings') ?>" class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-cog w-5"></i>
                                    <span class="ml-2">Settings</span>
                                </a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <a href="<?= base_url('logout') ?>" class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    <i class="fas fa-sign-out-alt w-5"></i>
                                    <span class="ml-2">Logout</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Page Content -->
        <main class="p-4 sm:p-6 lg:p-8">
            <?= $this->renderSection('content') ?>
        </main>
        
        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 mt-8">
            <div class="px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col sm:flex-row justify-between items-center text-sm text-gray-600">
                    <p>&copy; <?= date('Y') ?> SiPantau - BPS Provinsi Riau. All rights reserved.</p>
                    <div class="flex space-x-4 mt-2 sm:mt-0">
                        <a href="#" class="hover:text-blue-600">Documentation</a>
                        <a href="#" class="hover:text-blue-600">Support</a>
                        <a href="#" class="hover:text-blue-600">Privacy Policy</a>
                    </div>
                </div>
            </div>
        </footer>
    </div>
    
    <!-- Sidebar Overlay (Mobile) -->
    <div id="sidebarOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden" onclick="toggleSidebar()"></div>
    
    <!-- Scripts -->
    <script src="<?= base_url('assets/js/admin.js') ?>"></script>
</body>
</html>