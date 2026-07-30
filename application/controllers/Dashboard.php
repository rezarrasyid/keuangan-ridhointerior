<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model(['Dashboard_model', 'Client_model']);
    }

    public function index()
    {
        $wid  = $this->workshop_id;
        
        // Default: Tanggal 1 bulan ini s/d Hari ini
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : date('Y-m-01');
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : date('Y-m-t');

        $data['title']             = 'Dashboard - Ridho Interior';
        $data['start_date']        = $start_date;
        $data['end_date']          = $end_date;
        
        $data['total_pemasukan']   = $this->Dashboard_model->get_total_pemasukan_range($wid, $start_date, $end_date);
        $data['total_pengeluaran'] = $this->Dashboard_model->get_total_pengeluaran_range($wid, $start_date, $end_date);
        $data['saldo_tukang']      = $this->Dashboard_model->get_total_saldo_tukang($wid);
        $data['proyek_aktif']      = $this->Dashboard_model->get_total_proyek_aktif($wid);
        $data['top_clients']       = $this->Client_model->get_top_clients($wid, 5);

        $this->load_view('dashboard/index', $data);
    }

    /**
     * Endpoint AJAX untuk data Chart.js (Format Harian)
     */
    public function chart_data()
    {
        $wid = $this->workshop_id;
        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : date('Y-m-01');
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : date('Y-m-t');

        $raw_income  = $this->Dashboard_model->get_daily_income($wid, $start_date, $end_date);
        $raw_expense = $this->Dashboard_model->get_daily_expense($wid, $start_date, $end_date);

        // Buat lookup array agar mapping tanggal lebih cepat
        $inc_lookup = [];
        foreach ($raw_income as $row) {
            $inc_lookup[$row->tgl] = (float)$row->total;
        }

        $exp_lookup = [];
        foreach ($raw_expense as $row) {
            $exp_lookup[$row->tgl] = (float)$row->total;
        }

        $labels      = [];
        $pemasukan   = [];
        $pengeluaran = [];

        // Loop dari start_date sampai end_date untuk sumbu X
        $current_time = strtotime($start_date);
        $end_time     = strtotime($end_date);

        while ($current_time <= $end_time) {
            $date_str = date('Y-m-d', $current_time);
            
            // Format label: "01 Jul"
            $labels[] = date('d M', $current_time); 
            
            $pemasukan[]   = isset($inc_lookup[$date_str]) ? $inc_lookup[$date_str] : 0;
            $pengeluaran[] = isset($exp_lookup[$date_str]) ? $exp_lookup[$date_str] : 0;
            
            // Tambah 1 hari
            $current_time = strtotime('+1 day', $current_time);
        }

        $this->json_response([
            'labels'      => $labels,
            'pemasukan'   => $pemasukan,
            'pengeluaran' => $pengeluaran
        ]);
    }

    /**
     * DASHBOARD PUSAT (Global) - Hanya untuk Superadmin
     */
    public function pusat()
    {
        if (!isset($this->session->userdata['user']) || $this->session->userdata['user']['role'] !== 'superadmin') {
            show_error('Akses Ditolak. Halaman ini hanya untuk Superadmin.', 403);
        }

        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : date('Y-m-01');
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : date('Y-m-t');

        $data['title']             = 'Dashboard Pusat - Semua Cabang';
        $data['start_date']        = $start_date;
        $data['end_date']          = $end_date;
        
        $data['total_pemasukan']   = $this->Dashboard_model->get_global_pemasukan_range($start_date, $end_date);
        $data['total_pengeluaran'] = $this->Dashboard_model->get_global_pengeluaran_range($start_date, $end_date);
        $data['saldo_tukang']      = $this->Dashboard_model->get_global_saldo_tukang();
        $data['proyek_aktif']      = $this->Dashboard_model->get_global_proyek_aktif();
        
        // Data breakdown performa tiap cabang
        $data['performa_cabang']   = $this->Dashboard_model->get_performa_cabang($start_date, $end_date);

        $this->load_view('dashboard/pusat', $data);
    }

    /**
     * Endpoint AJAX Chart untuk Dashboard Pusat
     */
    public function chart_data_pusat()
    {
        if (!isset($this->session->userdata['user']) || $this->session->userdata['user']['role'] !== 'superadmin') {
            $this->json_response(['status' => 'error'], 403);
            return;
        }

        $start_date = $this->input->get('start_date') ? $this->input->get('start_date') : date('Y-m-01');
        $end_date   = $this->input->get('end_date') ? $this->input->get('end_date') : date('Y-m-t');

        $raw_income  = $this->Dashboard_model->get_daily_income_global($start_date, $end_date);
        $raw_expense = $this->Dashboard_model->get_daily_expense_global($start_date, $end_date);

        $inc_lookup = []; foreach ($raw_income as $row) { $inc_lookup[$row->tgl] = (float)$row->total; }
        $exp_lookup = []; foreach ($raw_expense as $row) { $exp_lookup[$row->tgl] = (float)$row->total; }

        $labels = []; $pemasukan = []; $pengeluaran = [];
        $current_time = strtotime($start_date);
        $end_time     = strtotime($end_date);

        while ($current_time <= $end_time) {
            $date_str = date('Y-m-d', $current_time);
            $labels[] = date('d M', $current_time); 
            $pemasukan[]   = isset($inc_lookup[$date_str]) ? $inc_lookup[$date_str] : 0;
            $pengeluaran[] = isset($exp_lookup[$date_str]) ? $exp_lookup[$date_str] : 0;
            $current_time = strtotime('+1 day', $current_time);
        }

        $this->json_response(['labels' => $labels, 'pemasukan' => $pemasukan, 'pengeluaran' => $pengeluaran]);
    }
}