<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Total pemasukan (project_payments) berdasarkan rentang tanggal
     */
    public function get_total_pemasukan_range($workshop_id, $start_date, $end_date)
    {
        $sql = "
            SELECT COALESCE(SUM(pp.jumlah), 0) AS total
            FROM project_payments pp
            JOIN projects p ON p.id = pp.project_id
            WHERE p.workshop_id = ?
              AND pp.tgl >= ? 
              AND pp.tgl <= ?
        ";
        $row = $this->db->query($sql, [$workshop_id, $start_date, $end_date])->row();
        return $row ? $row->total : 0;
    }

    /**
     * Total pengeluaran berdasarkan rentang tanggal
     */
    public function get_total_pengeluaran_range($workshop_id, $start_date, $end_date)
    {
        $sql = "
            SELECT COALESCE(SUM(jumlah), 0) AS total
            FROM expenses
            WHERE workshop_id = ?
              AND tgl >= ? 
              AND tgl <= ?
        ";
        $row = $this->db->query($sql, [$workshop_id, $start_date, $end_date])->row();
        return $row ? $row->total : 0;
    }

    /**
     * Total saldo upah tukang yang belum diambil (Tetap Kumulatif, tidak terpengaruh filter)
     */
    public function get_total_saldo_tukang($workshop_id)
    {
        $sql = "
            SELECT
                COALESCE(SUM(CASE WHEN jenis = 'Hak_Upah' THEN jumlah ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN jenis = 'Tarik_Tunai' THEN jumlah ELSE 0 END), 0) AS total_saldo
            FROM worker_ledgers
            WHERE workshop_id = ?
        ";
        $row = $this->db->query($sql, [$workshop_id])->row();
        return $row ? $row->total_saldo : 0;
    }

    /**
     * Jumlah proyek aktif (Tetap berdasarkan status, tidak terpengaruh filter)
     */
    public function get_total_proyek_aktif($workshop_id)
    {
        return $this->db
            ->where('workshop_id', $workshop_id)
            ->where('status_project', 'Aktif')
            ->count_all_results('projects');
    }

    /**
     * Data pemasukan harian untuk Chart
     */
    public function get_daily_income($workshop_id, $start_date, $end_date)
    {
        $sql = "
            SELECT DATE(pp.tgl) AS tgl, SUM(pp.jumlah) AS total
            FROM project_payments pp
            JOIN projects p ON p.id = pp.project_id
            WHERE p.workshop_id = ? AND pp.tgl >= ? AND pp.tgl <= ?
            GROUP BY DATE(pp.tgl)
            ORDER BY DATE(pp.tgl) ASC
        ";
        return $this->db->query($sql, [$workshop_id, $start_date, $end_date])->result();
    }

    /**
     * Data pengeluaran harian untuk Chart
     */
    public function get_daily_expense($workshop_id, $start_date, $end_date)
    {
        $sql = "
            SELECT DATE(tgl) AS tgl, SUM(jumlah) AS total
            FROM expenses
            WHERE workshop_id = ? AND tgl >= ? AND tgl <= ?
            GROUP BY DATE(tgl)
            ORDER BY DATE(tgl) ASC
        ";
        return $this->db->query($sql, [$workshop_id, $start_date, $end_date])->result();
    }

    // ==========================================================
    // FUNGSI KHUSUS SUPERADMIN (DASHBOARD PUSAT / GLOBAL)
    // ==========================================================

    public function get_global_pemasukan_range($start_date, $end_date)
    {
        $sql = "SELECT COALESCE(SUM(pp.jumlah), 0) AS total 
                FROM project_payments pp 
                WHERE pp.tgl >= ? AND pp.tgl <= ?";
        $row = $this->db->query($sql, [$start_date, $end_date])->row();
        return $row ? $row->total : 0;
    }

    public function get_global_pengeluaran_range($start_date, $end_date)
    {
        $sql = "SELECT COALESCE(SUM(jumlah), 0) AS total 
                FROM expenses 
                WHERE tgl >= ? AND tgl <= ?";
        $row = $this->db->query($sql, [$start_date, $end_date])->row();
        return $row ? $row->total : 0;
    }

    public function get_global_saldo_tukang()
    {
        $sql = "SELECT 
                    COALESCE(SUM(CASE WHEN jenis = 'Hak_Upah' THEN jumlah ELSE 0 END), 0) -
                    COALESCE(SUM(CASE WHEN jenis = 'Tarik_Tunai' THEN jumlah ELSE 0 END), 0) AS total_saldo
                FROM worker_ledgers";
        $row = $this->db->query($sql)->row();
        return $row ? $row->total_saldo : 0;
    }

    public function get_global_proyek_aktif()
    {
        return $this->db->where('status_project', 'Aktif')->count_all_results('projects');
    }

    public function get_daily_income_global($start_date, $end_date)
    {
        $sql = "SELECT DATE(tgl) AS tgl, SUM(jumlah) AS total 
                FROM project_payments 
                WHERE tgl >= ? AND tgl <= ? 
                GROUP BY DATE(tgl) ORDER BY DATE(tgl) ASC";
        return $this->db->query($sql, [$start_date, $end_date])->result();
    }

    public function get_daily_expense_global($start_date, $end_date)
    {
        $sql = "SELECT DATE(tgl) AS tgl, SUM(jumlah) AS total 
                FROM expenses 
                WHERE tgl >= ? AND tgl <= ? 
                GROUP BY DATE(tgl) ORDER BY DATE(tgl) ASC";
        return $this->db->query($sql, [$start_date, $end_date])->result();
    }

    // Breakdown Pemasukan & Pengeluaran per Cabang (Untuk Tabel Analisis)
    public function get_performa_cabang($start_date, $end_date)
    {
        $sql = "SELECT w.id, w.nama_workshop,
                    (SELECT COALESCE(SUM(pp.jumlah), 0) FROM project_payments pp JOIN projects p ON p.id = pp.project_id WHERE p.workshop_id = w.id AND pp.tgl >= ? AND pp.tgl <= ?) as total_pemasukan,
                    (SELECT COALESCE(SUM(e.jumlah), 0) FROM expenses e WHERE e.workshop_id = w.id AND e.tgl >= ? AND e.tgl <= ?) as total_pengeluaran
                FROM workshops w";
        return $this->db->query($sql, [$start_date, $end_date, $start_date, $end_date])->result();
    }
}