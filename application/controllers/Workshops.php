<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Workshops extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        
        // Blokir akses jika bukan superadmin
        if (!isset($this->session->userdata['user']) || $this->session->userdata['user']['role'] !== 'superadmin') {
            show_error('Akses Ditolak. Halaman ini hanya untuk Superadmin.', 403);
        }
        
        $this->load->model('Workshop_model');
    }

    public function index()
    {
        $data['title']     = 'Manajemen Workshop - Ridho Interior';
        $data['workshops'] = $this->Workshop_model->get_all();
        
        $this->load_view('workshops/index', $data);
    }

    public function get($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        
        $workshop = $this->Workshop_model->get_by_id($id);
        if ($workshop) {
            $this->json_response(['status' => 'success', 'data' => $workshop]);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
        }
    }

    public function store()
    {
        if (!$this->input->is_ajax_request()) show_404();

        // Validasi Workshop
        $this->form_validation->set_rules('nama_workshop', 'Nama Workshop', 'required|trim');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required|trim');
        // Validasi User
        $this->form_validation->set_rules('username', 'Username', 'required|trim|is_unique[users.username]', [
            'is_unique' => 'Username ini sudah digunakan. Pilih yang lain.'
        ]);
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap Admin', 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|min_length[5]');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $workshop_data = [
            'nama_workshop' => $this->input->post('nama_workshop', TRUE),
            'alamat'        => $this->input->post('alamat', TRUE)
        ];

        $user_data = [
            'username'     => $this->input->post('username', TRUE),
            'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
            'password'     => password_hash($this->input->post('password'), PASSWORD_DEFAULT)
        ];

        if ($this->Workshop_model->insert_with_admin($workshop_data, $user_data)) {
            $this->json_response(['status' => 'success', 'message' => 'Workshop & Akun Admin berhasil dibuat!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menyimpan data.'], 500);
        }
    }

    public function update($id)
    {
        if (!$this->input->is_ajax_request()) show_404();

        $workshop = $this->Workshop_model->get_by_id($id);
        if (!$workshop) {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            return;
        }

        // Cek apakah username berubah (untuk validasi is_unique)
        $username_post = $this->input->post('username', TRUE);
        $is_unique = ($username_post !== $workshop->username) ? '|is_unique[users.username]' : '';

        $this->form_validation->set_rules('nama_workshop', 'Nama Workshop', 'required|trim');
        $this->form_validation->set_rules('alamat', 'Alamat', 'required|trim');
        $this->form_validation->set_rules('username', 'Username', 'required|trim' . $is_unique, [
            'is_unique' => 'Username ini sudah digunakan.'
        ]);
        $this->form_validation->set_rules('nama_lengkap', 'Nama Lengkap Admin', 'required|trim');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $workshop_data = [
            'nama_workshop' => $this->input->post('nama_workshop', TRUE),
            'alamat'        => $this->input->post('alamat', TRUE)
        ];

        $user_data = [
            'username'     => $username_post,
            'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
        ];

        // Jika password diisi, maka update passwordnya
        $password = $this->input->post('password');
        if (!empty($password)) {
            if (strlen($password) < 5) {
                $this->json_response(['status' => 'error', 'message' => 'Password minimal 5 karakter.'], 422);
                return;
            }
            $user_data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($this->Workshop_model->update_with_admin($id, $workshop_data, $workshop->user_id, $user_data)) {
            $this->json_response(['status' => 'success', 'message' => 'Data Workshop berhasil diperbarui!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal memperbarui data.'], 500);
        }
    }

    public function destroy($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        
        if ($this->Workshop_model->delete($id)) {
            $this->json_response(['status' => 'success', 'message' => 'Workshop berhasil dihapus!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menghapus workshop.'], 500);
        }
    }
}