<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Workers extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Worker_model', 'Project_model']);
    }

    /**
     * Halaman daftar tukang beserta saldo upah (dengan search & paging)
     */
    public function index()
    {
        $search = $this->input->get('q', TRUE) ?: '';
        $page   = $this->input->get('page', TRUE) ? (int)$this->input->get('page', TRUE) : 1;
        if ($page < 1) $page = 1;

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total_rows = $this->Worker_model->count_workers($this->workshop_id, $search);

        $data['title']             = 'Upah Tukang - Ridho Interior';
        $data['workers']           = $this->Worker_model->get_list_with_saldo($this->workshop_id, $search, $limit, $offset);
        $data['projects_dropdown'] = $this->Project_model->get_dropdown($this->workshop_id);
        $data['search']            = $search;
        $data['page']              = $page;
        $data['limit']             = $limit;
        $data['total_rows']        = $total_rows;
        $data['total_pages']       = ceil($total_rows / $limit);
        $data['offset']            = $offset;

        $this->load_view('workers/index', $data);
    }

    /**
     * AJAX: Simpan tukang baru
     */
    public function store()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('nama', 'Nama', 'required|trim');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required|in_list[Senior,Junior,Baru]');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $data = [
            'workshop_id' => $this->workshop_id,
            'nama'        => $this->input->post('nama', TRUE),
            'telepon'     => $this->input->post('telepon', TRUE),
            'kategori'    => $this->input->post('kategori', TRUE),
        ];

        if ($this->Worker_model->insert($data)) {
            $this->json_response(['status' => 'success', 'message' => 'Tukang berhasil ditambahkan!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menyimpan data.'], 500);
        }
    }

    /**
     * AJAX: Get data tukang untuk edit
     */
    public function get($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        $worker = $this->Worker_model->get_by_id($id, $this->workshop_id);
        if ($worker) {
            $this->json_response(['status' => 'success', 'data' => $worker]);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
        }
    }

    /**
     * AJAX: Update tukang
     */
    public function update($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        $data = [
            'nama'     => $this->input->post('nama', TRUE),
            'telepon'  => $this->input->post('telepon', TRUE),
            'kategori' => $this->input->post('kategori', TRUE),
        ];
        if ($this->Worker_model->update($id, $this->workshop_id, $data)) {
            $this->json_response(['status' => 'success', 'message' => 'Data tukang diperbarui!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal memperbarui.'], 500);
        }
    }

    /**
     * AJAX: Hapus tukang
     */
    public function destroy($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        if ($this->Worker_model->delete($id, $this->workshop_id)) {
            $this->json_response(['status' => 'success', 'message' => 'Tukang berhasil dihapus!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menghapus.'], 500);
        }
    }

    /**
     * Halaman detail ledger tukang (dengan search & paging)
     */
    public function detail($id)
    {
        $worker = $this->Worker_model->get_by_id($id, $this->workshop_id);
        if (!$worker) {
            redirect('workers');
        }
        
        $search = $this->input->get('q', TRUE) ?: '';
        $page   = $this->input->get('page', TRUE) ? (int)$this->input->get('page', TRUE) : 1;
        if ($page < 1) $page = 1;

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total_rows = $this->Worker_model->count_ledger($id, $this->workshop_id, $search);

        $data['title']             = 'Detail Upah: ' . $worker->nama;
        $data['worker']            = $worker;
        $data['saldo']             = $this->Worker_model->get_saldo($id, $this->workshop_id);
        $data['ledger']            = $this->Worker_model->get_ledger_list($id, $this->workshop_id, $search, $limit, $offset);
        $data['projects_dropdown'] = $this->Project_model->get_dropdown($this->workshop_id);
        $data['search']            = $search;
        $data['page']              = $page;
        $data['limit']             = $limit;
        $data['total_rows']        = $total_rows;
        $data['total_pages']       = ceil($total_rows / $limit);
        $data['offset']            = $offset;

        $this->load_view('workers/detail', $data);
    }

    /**
     * AJAX: Tambah entri ledger (Hak Upah / Tarik Tunai)
     */
    public function add_ledger()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('worker_id', 'Tukang', 'required|integer');
        $this->form_validation->set_rules('jenis', 'Jenis', 'required|in_list[Hak_Upah,Tarik_Tunai]');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric');
        $this->form_validation->set_rules('tgl', 'Tanggal', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $project_id = $this->input->post('project_id', TRUE);
        $data = [
            'workshop_id' => $this->workshop_id,
            'worker_id'   => $this->input->post('worker_id', TRUE),
            'project_id'  => ($project_id && $project_id != '') ? $project_id : NULL,
            'jenis'       => $this->input->post('jenis', TRUE),
            'keterangan'  => $this->input->post('keterangan', TRUE),
            'jumlah'      => $this->input->post('jumlah', TRUE),
            'tgl'         => $this->input->post('tgl', TRUE),
        ];

        // Cek saldo tidak minus untuk Tarik Tunai
        if ($data['jenis'] === 'Tarik_Tunai') {
            $saldo = $this->Worker_model->get_saldo($data['worker_id'], $this->workshop_id);
            if ($saldo->sisa_saldo < $data['jumlah']) {
                $this->json_response([
                    'status'  => 'error',
                    'message' => 'Saldo tukang tidak mencukupi! Sisa saldo: Rp ' . number_format($saldo->sisa_saldo, 0, ',', '.')
                ], 422);
                return;
            }
        }

        if ($this->Worker_model->add_ledger($data)) {
            $this->json_response(['status' => 'success', 'message' => 'Transaksi berhasil dicatat!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menyimpan transaksi.'], 500);
        }
    }

    /**
     * AJAX: Ambil detail transaksi upah untuk edit modal
     */
    public function get_ledger($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        $ledger = $this->Worker_model->get_ledger_by_id($id, $this->workshop_id);
        if ($ledger) {
            $this->json_response(['status' => 'success', 'data' => $ledger]);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
        }
    }

    /**
     * AJAX: Update transaksi upah
     */
    public function update_ledger($id)
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('jenis', 'Jenis', 'required|in_list[Hak_Upah,Tarik_Tunai]');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|decimal');
        $this->form_validation->set_rules('tgl', 'Tanggal', 'required');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $ledger = $this->Worker_model->get_ledger_by_id($id, $this->workshop_id);
        if (!$ledger) {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            return;
        }

        $new_jenis = $this->input->post('jenis', TRUE);
        $new_jumlah = (float)$this->input->post('jumlah', TRUE);
        $project_id = $this->input->post('project_id', TRUE);

        // Hitung saldo tanpa transaksi ini untuk validasi limit
        $saldo = $this->Worker_model->get_saldo($ledger->worker_id, $this->workshop_id);
        $current_val = (float)$ledger->jumlah;
        $balance_ex = (float)$saldo->sisa_saldo;
        
        if ($ledger->jenis === 'Hak_Upah') {
            $balance_ex -= $current_val;
        } else {
            $balance_ex += $current_val;
        }

        if ($new_jenis === 'Tarik_Tunai' && $new_jumlah > $balance_ex) {
            $this->json_response([
                'status'  => 'error',
                'message' => 'Saldo tidak mencukupi! Batas penarikan maksimum: Rp ' . number_format($balance_ex, 0, ',', '.')
            ], 422);
            return;
        }

        $data = [
            'project_id'  => ($project_id && $project_id != '') ? $project_id : NULL,
            'jenis'       => $new_jenis,
            'keterangan'  => $this->input->post('keterangan', TRUE),
            'jumlah'      => $new_jumlah,
            'tgl'         => $this->input->post('tgl', TRUE),
        ];

        if ($this->Worker_model->update_ledger($id, $this->workshop_id, $data)) {
            $this->json_response(['status' => 'success', 'message' => 'Transaksi berhasil diperbarui!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal memperbarui transaksi.'], 500);
        }
    }

    /**
     * AJAX: Hapus transaksi upah
     */
    public function destroy_ledger($id)
    {
        if (!$this->input->is_ajax_request()) show_404();

        $ledger = $this->Worker_model->get_ledger_by_id($id, $this->workshop_id);
        if (!$ledger) {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            return;
        }

        // Jika Hak Upah dihapus, pastikan tidak membuat saldo sisa menjadi minus
        if ($ledger->jenis === 'Hak_Upah') {
            $saldo = $this->Worker_model->get_saldo($ledger->worker_id, $this->workshop_id);
            if ($saldo->sisa_saldo < $ledger->jumlah) {
                $this->json_response([
                    'status'  => 'error',
                    'message' => 'Gagal menghapus! Saldo sisa tukang akan menjadi minus.'
                ], 422);
                return;
            }
        }

        if ($this->Worker_model->delete_ledger($id, $this->workshop_id)) {
            $this->json_response(['status' => 'success', 'message' => 'Transaksi berhasil dihapus!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menghapus.'], 500);
        }
    }
}
