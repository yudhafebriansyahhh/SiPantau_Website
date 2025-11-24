<?php

namespace App\Controllers\SuperAdmin;

use App\Controllers\BaseController;
use App\Models\FeedbackModel;
use App\Models\UserModel;
use App\Models\MasterKabModel;

class FeedbackController extends BaseController
{
    protected $feedbackModel;
    protected $userModel;
    protected $kabupatenModel;

    public function __construct()
    {
        $this->feedbackModel = new FeedbackModel();
        $this->userModel = new UserModel();
        $this->kabupatenModel = new MasterKabModel();
    }

    /**
     * Halaman utama feedback
     */
    public function index()
    {
        $filters = [
            'id_kabupaten' => $this->request->getGet('id_kabupaten'),
            'search' => $this->request->getGet('search'),
        ];

        $feedbacks = $this->feedbackModel->getFeedbackWithDetails($filters);
        $stats = $this->feedbackModel->getFeedbackStats($filters);
        $kabupatens = $this->kabupatenModel->findAll();

        $data = [
            'title' => 'Kelola Feedback',
            'active_menu' => 'feedback',
            'feedbacks' => $feedbacks,
            'stats' => $stats,
            'kabupatens' => $kabupatens,
            'filters' => $filters
        ];

        return view('SuperAdmin/Feedback/index', $data);
    }

    /**
     * Form create feedback
     */
    public function create()
    {
        // Get users (untuk dipilih sebagai penerima feedback)
        $users = $this->userModel->getUsersWithDetails();
        $kabupatens = $this->kabupatenModel->findAll();

        $data = [
            'title' => 'Buat Feedback Baru',
            'active_menu' => 'feedback',
            'users' => $users,
            'kabupatens' => $kabupatens
        ];

        return view('SuperAdmin/Feedback/create', $data);
    }

    /**
     * Store feedback
     */
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'sobat_id' => 'required|numeric',
            'feedback' => 'required|min_length[10]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $data = [
            'sobat_id' => $this->request->getPost('sobat_id'),
            'feedback' => $this->request->getPost('feedback'),
        ];

        if ($this->feedbackModel->insert($data)) {
            return redirect()->to(base_url('superadmin/feedback'))
                ->with('success', 'Feedback berhasil dikirim!');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal mengirim feedback.');
    }

    /**
     * Form edit feedback
     */
    public function edit($id)
    {
        $feedback = $this->feedbackModel->getFeedbackById($id);

        if (!$feedback) {
            return redirect()->to(base_url('superadmin/feedback'))
                ->with('error', 'Feedback tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Feedback',
            'active_menu' => 'feedback',
            'feedback' => $feedback
        ];

        return view('SuperAdmin/Feedback/edit', $data);
    }

    /**
     * Update feedback
     */
    public function update($id)
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'feedback' => 'required|min_length[10]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        $data = [
            'feedback' => $this->request->getPost('feedback'),
        ];

        if ($this->feedbackModel->update($id, $data)) {
            return redirect()->to(base_url('superadmin/feedback'))
                ->with('success', 'Feedback berhasil diperbarui!');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Gagal memperbarui feedback.');
    }

    /**
     * Delete feedback
     */
    public function delete($id)
    {
        if ($this->feedbackModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Feedback berhasil dihapus!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Gagal menghapus feedback.'
        ]);
    }

    /**
     * Get user by sobat_id (AJAX)
     */
    public function getUserDetail()
    {
        $sobatId = $this->request->getGet('sobat_id');
        
        if (!$sobatId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sobat ID tidak ditemukan'
            ]);
        }

        $user = $this->userModel->getUserWithRoles($sobatId);
        
        if ($user) {
            return $this->response->setJSON([
                'success' => true,
                'data' => $user
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'User tidak ditemukan'
        ]);
    }

    /**
     * Get feedback history by user (AJAX)
     */
    public function getUserFeedbackHistory()
    {
        $sobatId = $this->request->getGet('sobat_id');
        
        if (!$sobatId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sobat ID tidak ditemukan'
            ]);
        }

        $feedbacks = $this->feedbackModel->getLatestFeedbackByUser($sobatId, 10);
        $count = $this->feedbackModel->getFeedbackCountByUser($sobatId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $feedbacks,
            'total' => $count
        ]);
    }
}