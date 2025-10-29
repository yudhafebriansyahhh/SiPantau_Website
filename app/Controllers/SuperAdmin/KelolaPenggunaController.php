<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\AdminSurveiProvinsiModel;
use App\Models\AdminSurveiKabupatenModel;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class KelolaPenggunaController extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $adminProvinsiModel;
    protected $adminKabupatenModel;
    protected $db;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->adminProvinsiModel = new AdminSurveiProvinsiModel();
        $this->adminKabupatenModel = new AdminSurveiKabupatenModel();
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $search = $this->request->getGet('search') ?? '';
        $roleFilter = $this->request->getGet('role') ?? '';

        // Get users with details
        $users = $this->userModel->getUsersWithDetails($search, $roleFilter);
        
        // Tambahkan role tambahan untuk setiap user
        foreach ($users as &$user) {
            $user['role_names'] = $this->getUserRoleNames($user['sobat_id'], $user['role']);
        }

        $data = [
            'title' => 'Kelola Pengguna',
            'active_menu' => 'kelola-pengguna',
            'users' => $users,
            'roles' => $this->roleModel->findAll(),
            'search' => $search,
            'roleFilter' => $roleFilter
        ];

        return view('SuperAdmin/KelolaPengguna/index', $data);
    }

    /**
     * Get user role names termasuk role tambahan dari tabel admin
     */
    private function getUserRoleNames($sobatId, $roleJson)
    {
        $roleNames = [];
        
        // Decode role dari tabel user
        $userRoles = [];
        if (is_string($roleJson) && (str_starts_with($roleJson, '[') || str_starts_with($roleJson, '{'))) {
            $decoded = json_decode($roleJson, true);
            if (is_array($decoded)) {
                $userRoles = array_map('intval', $decoded);
            }
        } else {
            $userRoles = [(int)$roleJson];
        }
        
        // Check admin status
        $isAdminProvinsi = $this->adminProvinsiModel->isAdminProvinsi($sobatId);
        $isAdminKabupaten = $this->adminKabupatenModel->isAdminKabupaten($sobatId);
        
        // Build available roles dengan nama yang sesuai
        foreach ($userRoles as $roleId) {
            $roleInfo = $this->roleModel->find($roleId);
            if ($roleInfo) {
                if ($roleId == 2) {
                    // Role Pemantau Provinsi (role asli)
                    $roleNames[] = 'Pemantau Provinsi';
                } elseif ($roleId == 3) {
                    // Role Pemantau Kabupaten (role asli)
                    $roleNames[] = 'Pemantau Kabupaten';
                } else {
                    // Role lain
                    $roleNames[] = $roleInfo['roleuser'];
                }
            }
        }
        
        // Tambahkan role admin jika terdaftar
        if ($isAdminProvinsi) {
            $roleNames[] = 'Admin Survei Provinsi';
        }
        
        if ($isAdminKabupaten) {
            $roleNames[] = 'Admin Survei Kabupaten';
        }
        
        return $roleNames;
    }

    public function create()
    {
        $data = [
            'title' => 'Tambah Pengguna',
            'active_menu' => 'kelola-pengguna',
            'roles' => $this->roleModel->findAll(),
            'kabupaten' => $this->getKabupatenList()
        ];

        return view('SuperAdmin/KelolaPengguna/create', $data);
    }

    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'sobat_id' => 'required|numeric|is_unique[sipantau_user.sobat_id]',
            'nama_user' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[sipantau_user.email]',
            'hp' => 'required|numeric|min_length[10]|max_length[20]',
            'id_kabupaten' => 'required|numeric',
            'roles' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $sobatId = $this->request->getPost('sobat_id');
        $roleIds = $this->request->getPost('roles');
        
        if (is_string($roleIds)) {
            $roleIds = explode(',', $roleIds);
        }
        
        $roleIds = array_map('intval', $roleIds);
        
        $userData = [
            'sobat_id' => $sobatId,
            'nama_user' => $this->request->getPost('nama_user'),
            'email' => $this->request->getPost('email'),
            'hp' => $this->request->getPost('hp'),
            'id_kabupaten' => $this->request->getPost('id_kabupaten'),
            'role' => json_encode($roleIds), // Sesuai nama kolom
            'password' => $sobatId, // Password = Sobat ID
            'is_active' => 1
        ];

        $this->userModel->insert($userData);

        return redirect()->to(base_url('superadmin/kelola-pengguna'))
            ->with('success', 'Data pengguna berhasil ditambahkan');
    }

    public function edit($id)
    {
        $user = $this->userModel->getUserWithRoles($id);

        if (!$user) {
            return redirect()->to(base_url('superadmin/kelola-pengguna'))
                ->with('error', 'Data pengguna tidak ditemukan');
        }

        $data = [
            'title' => 'Edit Pengguna',
            'active_menu' => 'kelola-pengguna',
            'user' => $user,
            'roles' => $this->roleModel->findAll(),
            'kabupaten' => $this->getKabupatenList()
        ];

        return view('SuperAdmin/KelolaPengguna/edit', $data);
    }

    public function update($id)
    {
        $rules = [
            'nama_user' => 'required|min_length[3]|max_length[100]',
            'email' => "required|valid_email|is_unique[sipantau_user.email,sobat_id,{$id}]",
            'hp' => 'required|numeric|min_length[10]|max_length[20]',
            'id_kabupaten' => 'required|numeric',
            'roles' => 'required'
        ];

        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = 'min_length[8]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleIds = $this->request->getPost('roles');
        
        if (is_string($roleIds)) {
            $roleIds = explode(',', $roleIds);
        }
        
        $roleIds = array_map('intval', $roleIds);

        $userData = [
            'nama_user' => $this->request->getPost('nama_user'),
            'email' => $this->request->getPost('email'),
            'hp' => $this->request->getPost('hp'),
            'id_kabupaten' => $this->request->getPost('id_kabupaten'),
            'role' => json_encode($roleIds) // Sesuai nama kolom
        ];

        if (!empty($password)) {
            $userData['password'] = $password;
        }

        $this->userModel->update($id, $userData);

        return redirect()->to(base_url('superadmin/kelola-pengguna'))
            ->with('success', 'Data pengguna berhasil diperbarui');
    }

    public function delete($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan'
            ]);
        }

        $this->userModel->delete($id);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Data pengguna berhasil dihapus'
        ]);
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ];

        $headers = ['A1' => 'Sobat ID', 'B1' => 'Nama Lengkap', 'C1' => 'Email', 'D1' => 'No HP', 'E1' => 'Kabupaten/Kota', 'F1' => 'Roles (pisahkan dengan koma)'];

        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        $sheet->getStyle('A1:F1')->applyFromArray($headerStyle);

        foreach (['A' => 15, 'B' => 25, 'C' => 30, 'D' => 15, 'E' => 25, 'F' => 35] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $sheet->setCellValue('A2', '3201010101');
        $sheet->setCellValue('B2', 'Ahmad Hidayat');
        $sheet->setCellValue('C2', 'ahmad@bps.go.id');
        $sheet->setCellValue('D2', '081234567890');
        $sheet->setCellValue('E2', 'Kota Pekanbaru');
        $sheet->setCellValue('F2', 'Admin Kabupaten,Pemantau Kabupaten');

        $filename = 'Template_Import_Pengguna_' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function import()
    {
        $file = $this->request->getFile('file');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'File tidak valid');
        }

        $extension = $file->getClientExtension();
        if (!in_array($extension, ['xlsx', 'xls'])) {
            return redirect()->back()->with('error', 'Format file harus Excel (.xlsx atau .xls)');
        }

        try {
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            array_shift($data);

            $imported = 0;
            $skipped = 0;
            $errors = [];

            $kabupatenMap = $this->getKabupatenMap();
            $roleMap = $this->getRoleMap();

            $this->db->transStart();

            foreach ($data as $index => $row) {
                if (empty(array_filter($row))) continue;

                $rowNumber = $index + 2;

                if (empty($row[0]) || empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[4]) || empty($row[5])) {
                    $errors[] = "Baris {$rowNumber}: Data tidak lengkap";
                    $skipped++;
                    continue;
                }

                $kabupatenName = trim($row[4]);
                $idKabupaten = $kabupatenMap[strtolower($kabupatenName)] ?? null;
                
                if (!$idKabupaten) {
                    $errors[] = "Baris {$rowNumber}: Kabupaten '{$kabupatenName}' tidak ditemukan";
                    $skipped++;
                    continue;
                }

                // Process roles - remove spaces after commas
                $roleNames = array_map('trim', explode(',', str_replace(', ', ',', $row[5])));
                $roleIds = [];
                $invalidRoles = [];
                
                foreach ($roleNames as $roleName) {
                    $roleId = $roleMap[strtolower($roleName)] ?? null;
                    if ($roleId) {
                        $roleIds[] = $roleId;
                    } else {
                        $invalidRoles[] = $roleName;
                    }
                }
                
                if (!empty($invalidRoles)) {
                    $errors[] = "Baris {$rowNumber}: Role tidak ditemukan: " . implode(', ', $invalidRoles);
                    $skipped++;
                    continue;
                }

                $existingUser = $this->userModel->where('email', $row[2])->first();
                if ($existingUser) {
                    $errors[] = "Baris {$rowNumber}: Email {$row[2]} sudah terdaftar";
                    $skipped++;
                    continue;
                }

                $existingSobatId = $this->userModel->where('sobat_id', $row[0])->first();
                if ($existingSobatId) {
                    $errors[] = "Baris {$rowNumber}: Sobat ID {$row[0]} sudah terdaftar";
                    $skipped++;
                    continue;
                }

                $userData = [
                    'sobat_id' => $row[0],
                    'nama_user' => $row[1],
                    'email' => $row[2],
                    'hp' => $row[3],
                    'id_kabupaten' => $idKabupaten,
                    'role' => json_encode($roleIds),
                    'password' => $row[0],
                    'is_active' => 1
                ];

                $this->userModel->insert($userData);
                $imported++;
            }

            $this->db->transComplete();

            $message = "Import selesai! Berhasil: {$imported} data, Dilewati: {$skipped} data";

            if (!empty($errors)) {
                $errorMessage = implode("\n", array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $errorMessage .= "\n... dan " . (count($errors) - 10) . " error lainnya";
                }
                session()->setFlashdata('import_errors', $errorMessage);
            }

            return redirect()->to(base_url('superadmin/kelola-pengguna'))
                ->with($imported > 0 ? 'success' : 'warning', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function export()
    {
        // Get users with details
        $users = $this->userModel->getUsersWithDetails();
        
        // Tambahkan role tambahan untuk setiap user
        foreach ($users as &$user) {
            $roleNames = $this->getUserRoleNames($user['sobat_id'], $user['role']);
            $user['roles_display'] = implode(', ', $roleNames);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]
        ];

        $headers = ['No', 'Sobat ID', 'Nama Lengkap', 'Email', 'No HP', 'Kabupaten/Kota', 'Roles', 'Status', 'Tanggal Dibuat'];
        $column = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($column . '1', $header);
            $column++;
        }

        $sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

        foreach (['A' => 5, 'B' => 15, 'C' => 25, 'D' => 30, 'E' => 15, 'F' => 20, 'G' => 35, 'H' => 10, 'I' => 20] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        $row = 2;
        $no = 1;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $user['sobat_id']);
            $sheet->setCellValue('C' . $row, $user['nama_user']);
            $sheet->setCellValue('D' . $row, $user['email']);
            $sheet->setCellValue('E' . $row, $user['hp']);
            $sheet->setCellValue('F' . $row, $user['nama_kabupaten'] ?? '-');
            $sheet->setCellValue('G' . $row, $user['roles_display'] ?? '-');
            $sheet->setCellValue('H' . $row, $user['is_active'] ? 'Aktif' : 'Nonaktif');
            $sheet->setCellValue('I' . $row, $user['created_at']);
            
            $row++;
            $no++;
        }

        $filename = 'Data_Pengguna_' . date('YmdHis') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    private function getKabupatenList()
    {
        return $this->db->table('master_kabupaten')
            ->select('id_kabupaten, nama_kabupaten')
            ->orderBy('nama_kabupaten', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function getKabupatenMap()
    {
        $kabupaten = $this->getKabupatenList();
        $map = [];
        foreach ($kabupaten as $kab) {
            $map[strtolower($kab['nama_kabupaten'])] = $kab['id_kabupaten'];
        }
        return $map;
    }

    private function getRoleMap()
    {
        $roles = $this->roleModel->findAll();
        $map = [];
        foreach ($roles as $role) {
            $map[strtolower($role['roleuser'])] = $role['id_roleuser'];
        }
        return $map;
    }

    public function toggleStatus($id)
    {
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data pengguna tidak ditemukan'
            ]);
        }

        $newStatus = $user['is_active'] ? 0 : 1;
        $this->userModel->update($id, ['is_active' => $newStatus]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Status pengguna berhasil diubah',
            'status' => $newStatus
        ]);
    }
}