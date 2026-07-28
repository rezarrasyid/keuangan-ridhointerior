<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Expense_model', 'Project_model']);
    }

    /**
     * Halaman daftar pengeluaran (dengan search & paging)
     */
    public function index()
    {
        $search = $this->input->get('q', TRUE) ?: '';
        $page   = $this->input->get('page', TRUE) ? (int)$this->input->get('page', TRUE) : 1;
        if ($page < 1) $page = 1;

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total_rows = $this->Expense_model->count_expenses($this->workshop_id, $search);

        $data['title']             = 'Manajemen Pengeluaran - Ridho Interior';
        $data['expenses']          = $this->Expense_model->get_list($this->workshop_id, $search, $limit, $offset);
        $data['projects_dropdown'] = $this->Project_model->get_dropdown($this->workshop_id);
        $data['search']            = $search;
        $data['page']              = $page;
        $data['limit']             = $limit;
        $data['total_rows']        = $total_rows;
        $data['total_pages']       = ceil($total_rows / $limit);
        $data['offset']            = $offset;

        $this->load_view('expenses/index', $data);
    }

    /**
     * AJAX: Simpan pengeluaran baru
     */
    public function store()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('kategori', 'Kategori', 'required|trim');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|decimal');
        $this->form_validation->set_rules('tgl', 'Tanggal', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $project_id = $this->input->post('project_id', TRUE);
        $data = [
            'workshop_id' => $this->workshop_id,
            'project_id'  => ($project_id && $project_id != '') ? $project_id : NULL,
            'kategori'    => $this->input->post('kategori', TRUE),
            'jumlah'      => $this->input->post('jumlah', TRUE),
            'tgl'         => $this->input->post('tgl', TRUE),
            'keterangan'  => $this->input->post('keterangan', TRUE),
        ];

        if ($this->Expense_model->insert($data)) {
            $this->json_response(['status' => 'success', 'message' => 'Pengeluaran berhasil dicatat!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menyimpan data.'], 500);
        }
    }

    /**
     * AJAX: Ambil data pengeluaran untuk edit modal
     */
    public function get($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        $expense = $this->Expense_model->get_by_id($id, $this->workshop_id);
        if ($expense) {
            $this->json_response(['status' => 'success', 'data' => $expense]);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
        }
    }

    /**
     * AJAX: Update pengeluaran
     */
    public function update($id)
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('kategori', 'Kategori', 'required|trim');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|decimal');
        $this->form_validation->set_rules('tgl', 'Tanggal', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $project_id = $this->input->post('project_id', TRUE);
        $data = [
            'project_id'  => ($project_id && $project_id != '') ? $project_id : NULL,
            'kategori'    => $this->input->post('kategori', TRUE),
            'jumlah'      => $this->input->post('jumlah', TRUE),
            'tgl'         => $this->input->post('tgl', TRUE),
            'keterangan'  => $this->input->post('keterangan', TRUE),
        ];

        if ($this->Expense_model->update($id, $this->workshop_id, $data)) {
            $this->json_response(['status' => 'success', 'message' => 'Pengeluaran berhasil diperbarui!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal memperbarui.'], 500);
        }
    }

    /**
     * AJAX: Hapus pengeluaran
     */
    public function destroy($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        if ($this->Expense_model->delete($id, $this->workshop_id)) {
            $this->json_response(['status' => 'success', 'message' => 'Pengeluaran berhasil dihapus!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menghapus.'], 500);
        }
    }
}
