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
        $year = $this->input->get('year') ? (int)$this->input->get('year') : (int)date('Y');

        $data['title']             = 'Dashboard - Ridho Interior';
        $data['total_pemasukan']   = $this->Dashboard_model->get_total_pemasukan_month($wid);
        $data['total_pengeluaran'] = $this->Dashboard_model->get_total_pengeluaran_month($wid);
        $data['saldo_tukang']      = $this->Dashboard_model->get_total_saldo_tukang($wid);
        $data['proyek_aktif']      = $this->Dashboard_model->get_total_proyek_aktif($wid);
        $data['top_clients']       = $this->Client_model->get_top_clients($wid, 5);
        $data['year']              = $year;
        $data['yearly_recap']      = $this->Dashboard_model->get_yearly_recap($wid, $year);

        $this->load_view('dashboard/index', $data);
    }

    /**
     * Endpoint AJAX untuk data Chart.js
     * Returns JSON: { labels, pemasukan, pengeluaran }
     */
    public function chart_data()
    {
        $wid  = $this->workshop_id;
        $year = $this->input->get('year') ? (int)$this->input->get('year') : (int)date('Y');

        $months_id = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        // Init array 12 bulan dengan 0
        $pemasukan   = array_fill(1, 12, 0);
        $pengeluaran = array_fill(1, 12, 0);

        $raw_income  = $this->Dashboard_model->get_monthly_income($wid, $year);
        $raw_expense = $this->Dashboard_model->get_monthly_expense($wid, $year);

        foreach ($raw_income as $row) {
            $pemasukan[(int)$row->bulan] = (float)$row->total;
        }
        foreach ($raw_expense as $row) {
            $pengeluaran[(int)$row->bulan] = (float)$row->total;
        }

        $response = [
            'labels'      => $months_id,
            'pemasukan'   => array_values($pemasukan),
            'pengeluaran' => array_values($pengeluaran),
            'year'        => $year,
        ];

        $this->json_response($response);
    }

    /**
     * Cetak Laporan Rekap Tahunan (PDF)
     */
    public function print_pdf($year)
    {
        $wid  = $this->workshop_id;
        $year = (int)$year;
        
        $ws = $this->db->where('id', $wid)->get('workshops')->row();
        $data['workshop_name']   = $ws ? $ws->nama_workshop : 'Ridho Interior';
        $data['workshop_alamat'] = $ws ? $ws->alamat : '';
        $data['year']            = $year;
        $data['recap']           = $this->Dashboard_model->get_yearly_recap($wid, $year);
        $data['title']           = 'Laporan Tahunan Keuangan - ' . $year;
        
        $this->load->view('dashboard/print_pdf', $data);
    }

    /**
     * Cetak Laporan Rekap Bulanan Detail (PDF)
     */
    public function print_monthly_pdf($month, $year)
    {
        $wid   = $this->workshop_id;
        $month = (int)$month;
        $year  = (int)$year;
        
        $ws = $this->db->where('id', $wid)->get('workshops')->row();
        $data['workshop_name']   = $ws ? $ws->nama_workshop : 'Ridho Interior';
        $data['workshop_alamat'] = $ws ? $ws->alamat : '';
        $data['month']           = $month;
        $data['year']            = $year;
        
        // Data Pemasukan Detail (project_payments)
        $sql_income = "
            SELECT pp.tgl, pp.nama_pembayaran, pp.jumlah, pp.jenis, p.nama_project, c.nama AS nama_client
            FROM project_payments pp
            JOIN projects p ON p.id = pp.project_id
            JOIN clients c ON c.id = p.client_id
            WHERE p.workshop_id = ? AND MONTH(pp.tgl) = ? AND YEAR(pp.tgl) = ?
            ORDER BY pp.tgl ASC
        ";
        $data['incomes'] = $this->db->query($sql_income, [$wid, $month, $year])->result();
        
        // Data Pengeluaran Detail (expenses)
        $sql_expense = "
            SELECT e.tgl, e.kategori, e.keterangan, e.jumlah, p.nama_project
            FROM expenses e
            LEFT JOIN projects p ON p.id = e.project_id
            WHERE e.workshop_id = ? AND MONTH(e.tgl) = ? AND YEAR(e.tgl) = ?
            ORDER BY e.tgl ASC
        ";
        $data['expenses'] = $this->db->query($sql_expense, [$wid, $month, $year])->result();
        
        $months_id = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        $data['month_name'] = $months_id[$month - 1];
        $data['title'] = 'Laporan Bulanan Keuangan - ' . $data['month_name'] . ' ' . $year;
        
        $this->load->view('dashboard/print_monthly_pdf', $data);
    }
}
