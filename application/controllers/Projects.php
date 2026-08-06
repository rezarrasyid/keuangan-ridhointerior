<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Projects extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Project_model', 'Client_model']);
    }

    /**
     * Daftar semua proyek (dengan search, paging, dan filter tanggal/bulan)
     */
    public function index()
    {
        $search     = $this->input->get('q', TRUE) ?: '';
        $start_date = $this->input->get('start_date', TRUE) ?: '';
        $end_date   = $this->input->get('end_date', TRUE) ?: '';
        $month      = $this->input->get('month', TRUE) ?: ''; // YYYY-MM
        
        $page       = $this->input->get('page', TRUE) ? (int)$this->input->get('page', TRUE) : 1;
        if ($page < 1) $page = 1;

        $limit  = 10;
        $offset = ($page - 1) * $limit;

        $total_rows = $this->Project_model->count_projects($this->workshop_id, $search, $start_date, $end_date, $month);

        $data['title']             = 'Manajemen Proyek - Ridho Interior';
        $data['projects']          = $this->Project_model->get_list($this->workshop_id, $search, $start_date, $end_date, $month, $limit, $offset);
        $data['clients_dropdown']  = $this->Client_model->get_all($this->workshop_id);
        $data['search']            = $search;
        $data['start_date']        = $start_date;
        $data['end_date']          = $end_date;
        $data['month']             = $month;
        $data['page']              = $page;
        $data['limit']             = $limit;
        $data['total_rows']        = $total_rows;
        $data['total_pages']       = ceil($total_rows / $limit);
        $data['offset']            = $offset;

        $this->load_view('projects/index', $data);
    }

    /**
     * Detail proyek (dengan daftar termin)
     */
    public function detail($id)
    {
        $project = $this->Project_model->get_by_id($id, $this->workshop_id);
        if (!$project) {
            redirect('projects');
        }
        $data['title']    = 'Detail Proyek: ' . $project->nama_project;
        $data['project']  = $project;
        $data['payments'] = $this->Project_model->get_payments($id);
        $this->load_view('projects/detail', $data);
    }

    /**
     * AJAX: Simpan proyek baru
     */
    public function store()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('nama_project', 'Nama Proyek', 'required|trim');
        $this->form_validation->set_rules('client_id', 'Klien', 'required|integer');
        $this->form_validation->set_rules('biaya_total', 'Biaya Total', 'required|numeric');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $data = [
            'workshop_id'       => $this->workshop_id,
            'client_id'         => $this->input->post('client_id', TRUE),
            'nama_project'      => $this->input->post('nama_project', TRUE),
            'deskripsi'         => $this->input->post('deskripsi', TRUE),
            'biaya_total'       => $this->input->post('biaya_total', TRUE),
            'status_pembayaran' => 'Belum Lunas',
            'status_project'    => $this->input->post('status_project', TRUE) ?: 'Aktif',
            'tgl_mulai'         => $this->input->post('tgl_mulai', TRUE),
        ];

        $id = $this->Project_model->insert($data);
        if ($id) {
            $this->json_response(['status' => 'success', 'message' => 'Proyek berhasil ditambahkan!', 'id' => $id]);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menyimpan proyek.'], 500);
        }
    }

    /**
     * AJAX: Update proyek
     */
    public function update($id)
    {
        if (!$this->input->is_ajax_request()) show_404();

        $data = [
            'nama_project'   => $this->input->post('nama_project', TRUE),
            'client_id'      => $this->input->post('client_id', TRUE),
            'biaya_total'    => $this->input->post('biaya_total', TRUE),
            'deskripsi'      => $this->input->post('deskripsi', TRUE),
            'status_project' => $this->input->post('status_project', TRUE),
            'tgl_mulai'      => $this->input->post('tgl_mulai', TRUE),
            'tgl_selesai'    => $this->input->post('tgl_selesai', TRUE),
        ];

        if ($this->Project_model->update($id, $this->workshop_id, $data)) {
            $this->Project_model->update_payment_status($id);
            $this->json_response(['status' => 'success', 'message' => 'Proyek berhasil diperbarui!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal memperbarui.'], 500);
        }
    }

    /**
     * AJAX: Hapus proyek
     */
    public function destroy($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        if ($this->Project_model->delete($id, $this->workshop_id)) {
            $this->json_response(['status' => 'success', 'message' => 'Proyek berhasil dihapus!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menghapus.'], 500);
        }
    }

    /**
     * AJAX: Tambah Termin/Pembayaran
     */
    public function add_payment()
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('project_id', 'Proyek', 'required|integer');
        $this->form_validation->set_rules('nama_pembayaran', 'Nama Pembayaran', 'required|trim');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric');
        $this->form_validation->set_rules('tgl', 'Tanggal', 'required');
        $this->form_validation->set_rules('jenis', 'Jenis', 'required|in_list[DP,Termin]');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $project_id = $this->input->post('project_id', TRUE);

        // Validasi apakah melebihi sisa tagihan
        $sisa = $this->Project_model->get_sisa_tagihan($project_id);
        $jumlah = (float)$this->input->post('jumlah', TRUE);
        if ($sisa && $jumlah > $sisa->sisa_tagihan) {
            $this->json_response([
                'status'  => 'error',
                'message' => 'Jumlah melebihi sisa tagihan! Sisa: Rp ' . number_format($sisa->sisa_tagihan, 0, ',', '.')
            ], 422);
            return;
        }

        $data = [
            'project_id'      => $project_id,
            'jenis'           => $this->input->post('jenis', TRUE),
            'nama_pembayaran' => $this->input->post('nama_pembayaran', TRUE),
            'jumlah'          => $jumlah,
            'tgl'             => $this->input->post('tgl', TRUE),
            'keterangan'      => $this->input->post('keterangan', TRUE),
        ];

        if ($this->Project_model->add_payment($data)) {
            $this->Project_model->update_payment_status($project_id);
            $this->json_response(['status' => 'success', 'message' => 'Termin berhasil dicatat!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal menyimpan termin.'], 500);
        }
    }

    /**
     * AJAX: Hapus termin
     */
    public function delete_payment($id)
    {
        if (!$this->input->is_ajax_request()) show_404();

        // Cari project_id sebelum hapus
        $payment = $this->db->where('id', $id)->get('project_payments')->row();
        if ($payment) {
            $this->Project_model->delete_payment($id);
            $this->Project_model->update_payment_status($payment->project_id);
            $this->json_response(['status' => 'success', 'message' => 'Termin berhasil dihapus!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
        }
    }

    /**
     * AJAX: Ambil detail termin untuk edit modal
     */
    public function get_payment($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        $payment = $this->Project_model->get_payment_by_id($id);
        if ($payment) {
            $this->json_response(['status' => 'success', 'data' => $payment]);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
        }
    }

    /**
     * AJAX: Update termin
     */
    public function update_payment($id)
    {
        if (!$this->input->is_ajax_request()) show_404();

        $this->form_validation->set_rules('nama_pembayaran', 'Nama Pembayaran', 'required|trim');
        $this->form_validation->set_rules('jumlah', 'Jumlah', 'required|numeric');
        $this->form_validation->set_rules('tgl', 'Tanggal', 'required');
        $this->form_validation->set_rules('jenis', 'Jenis', 'required|in_list[DP,Termin]');

        if ($this->form_validation->run() === FALSE) {
            $this->json_response(['status' => 'error', 'message' => validation_errors()], 422);
            return;
        }

        $payment = $this->Project_model->get_payment_by_id($id);
        if (!$payment) {
            $this->json_response(['status' => 'error', 'message' => 'Data tidak ditemukan.'], 404);
            return;
        }

        $project_id = $payment->project_id;
        $new_jumlah = (float)$this->input->post('jumlah', TRUE);

        // Validasi apakah melebihi sisa tagihan (tanpa nilai pembayaran yang diedit saat ini)
        $sisa_info = $this->Project_model->get_sisa_tagihan($project_id);
        $sisa_ex = (float)$sisa_info->sisa_tagihan + (float)$payment->jumlah;

        if ($new_jumlah > $sisa_ex) {
            $this->json_response([
                'status'  => 'error',
                'message' => 'Jumlah melebihi sisa tagihan! Batas maksimum: Rp ' . number_format($sisa_ex, 0, ',', '.')
            ], 422);
            return;
        }

        $data = [
            'jenis'           => $this->input->post('jenis', TRUE),
            'nama_pembayaran' => $this->input->post('nama_pembayaran', TRUE),
            'jumlah'          => $new_jumlah,
            'tgl'             => $this->input->post('tgl', TRUE),
            'keterangan'      => $this->input->post('keterangan', TRUE),
        ];

        if ($this->Project_model->update_payment($id, $data)) {
            $this->Project_model->update_payment_status($project_id);
            $this->json_response(['status' => 'success', 'message' => 'Termin berhasil diperbarui!']);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Gagal memperbarui termin.'], 500);
        }
    }

    public function get_project($id)
    {
        if (!$this->input->is_ajax_request()) show_404();
        
        $project = $this->Project_model->get_by_id($id, $this->workshop_id);
        
        if ($project) {
            $this->json_response(['status' => 'success', 'data' => $project]);
        } else {
            $this->json_response(['status' => 'error', 'message' => 'Proyek tidak ditemukan.'], 404);
        }
    }
}
