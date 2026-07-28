<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Clients extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Client_model');
    }

    /**
     * Halaman daftar klien
     */
    public function index()
    {
        $search = $this->input->get('q', TRUE) ?: '';
        $page   = $this->input->get('page', TRUE) ? (int)$this->input->get('page', TRUE) : 1;
        if ($page < 1) $page = 1;

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total_rows = $this->Client_model->count_list($this->workshop_id, $search);

        $data['title']       = 'Master Klien - Ridho Interior';
        $data['clients']     = $this->Client_model->get_list($this->workshop_id, $search, $limit, $offset);
        $data['search']      = $search;
        $data['page']        = $page;
        $data['limit']       = $limit;
        $data['total_rows']  = $total_rows;
        $data['total_pages'] = ceil($total_rows / $limit);
        $data['offset']      = $offset;

        $this->load_view('clients/index', $data);
    }

    /**
     * AJAX: Simpan klien baru
     */
    public function store()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('telepon', 'Telepon', 'trim');
        $this->form_validation->set_rules('alamat', 'Alamat', 'trim');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $data = [
            'workshop_id' => $this->workshop_id,
            'nama'        => $this->input->post('nama', TRUE),
            'telepon'     => $this->input->post('telepon', TRUE),
            'alamat'      => $this->input->post('alamat', TRUE),
        ];

        if ($this->Client_model->insert($data)) {
            $this->json_response(['status' => 'success', 'message' => 'Klien berhasil ditambahkan!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menyimpan data.'], 500);
        }
    }

    /**
     * AJAX: Get data klien untuk edit modal
     */
    public function get($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $client = $this->Client_model->get_by_id($id, $this->workshop_id);
        if ($client) {
            $this->json_response(['status' => 'success', 'data' => $client]);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
        }
    }

    /**
     * AJAX: Update klien
     */
    public function update($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $data = [
            'nama'    => $this->input->post('nama', TRUE),
            'telepon' => $this->input->post('telepon', TRUE),
            'alamat'  => $this->input->post('alamat', TRUE),
        ];

        if ($this->Client_model->update($id, $this->workshop_id, $data)) {
            $this->json_response(['status' => 'success', 'message' => 'Data klien diperbarui!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal memperbarui data.'], 500);
        }
    }

    /**
     * AJAX: Hapus klien
     */
    public function destroy($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if ($this->Client_model->delete($id, $this->workshop_id)) {
            $this->json_response(['status' => 'success', 'message' => 'Klien berhasil dihapus!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menghapus data.'], 500);
        }
    }
}
