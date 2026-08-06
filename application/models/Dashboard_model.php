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

    // ==========================================================
    // FUNGSI BARU: KPI TAMBAHAN & GRAFIK SESUAI PERMINTAAN KLIEN
    // ==========================================================

    public function get_kpi_tambahan($workshop_id = null)
    {
        $where_workshop = $workshop_id ? " AND p.workshop_id = " . $this->db->escape($workshop_id) : "";
        $where_client   = $workshop_id ? " AND workshop_id = " . $this->db->escape($workshop_id) : "";

        // 1. Total Klien
        $total_klien = $this->db->query("SELECT COUNT(DISTINCT id) as total FROM clients WHERE 1=1 $where_client")->row()->total;
        
        // 2 & 3. Total DP vs Pelunasan (Memanfaatkan kolom enum 'jenis')
        $bayar = $this->db->query("SELECT 
            COALESCE(SUM(CASE WHEN pp.jenis = 'DP' THEN pp.jumlah ELSE 0 END), 0) as total_dp,
            COALESCE(SUM(CASE WHEN pp.jenis = 'Termin' THEN pp.jumlah ELSE 0 END), 0) as total_pelunasan
            FROM project_payments pp 
            JOIN projects p ON p.id = pp.project_id 
            WHERE 1=1 $where_workshop")->row();

        // 4. Total Nilai Proyek (Menggunakan kolom biaya_total)
        $proyek = $this->db->query("SELECT COALESCE(SUM(biaya_total), 0) as nilai FROM projects p WHERE 1=1 $where_workshop")->row();
        
        // Hitung total tagihan dari: Total Biaya - (Total DP + Total Termin)
        $total_pembayaran = $bayar->total_dp + $bayar->total_pelunasan;
        $total_tagihan = $proyek->nilai - $total_pembayaran;

        return [
            'total_klien'     => $total_klien ?: 0,
            'total_nilai'     => $proyek->nilai ?: 0,
            'total_tagihan'   => $total_tagihan > 0 ? $total_tagihan : 0,
            'total_dp'        => $bayar->total_dp ?: 0,
            'total_pelunasan' => $bayar->total_pelunasan ?: 0,
        ];
    }

    // 5. Persentase Lunas vs Belum Lunas (Pie Chart)
    public function get_status_project($workshop_id = null) 
    {
        $where = $workshop_id ? " WHERE workshop_id = " . $this->db->escape($workshop_id) : "";
        // Memanfaatkan enum status_pembayaran bawaan tabel projects
        return $this->db->query("SELECT 
            COALESCE(SUM(CASE WHEN status_pembayaran = 'Lunas' THEN 1 ELSE 0 END), 0) as lunas,
            COALESCE(SUM(CASE WHEN status_pembayaran = 'Belum Lunas' THEN 1 ELSE 0 END), 0) as belum_lunas
            FROM projects $where")->row();
    }

    // 6. Top 10 Klien dengan Tagihan Tertinggi
    public function get_top_10_tagihan($workshop_id = null) 
    {
        $where = $workshop_id ? " AND p.workshop_id = " . $this->db->escape($workshop_id) : "";
        
        // Menghitung tagihan dari biaya_total dikurangi semua jumlah pembayaran
        $sql = "SELECT c.nama, 
                   SUM(p.biaya_total - COALESCE((SELECT SUM(jumlah) FROM project_payments WHERE project_id = p.id), 0)) as tagihan
                FROM projects p 
                JOIN clients c ON c.id = p.client_id
                WHERE p.status_pembayaran = 'Belum Lunas' $where
                GROUP BY p.client_id 
                ORDER BY tagihan DESC 
                LIMIT 10";
                
        return $this->db->query($sql)->result();
    }

    // 7. Distribusi Pembayaran (Stacked Bar Chart)
    public function get_distribusi_pembayaran($workshop_id = null) 
    {
        $where = $workshop_id ? " AND p.workshop_id = " . $this->db->escape($workshop_id) : "";
        
        // Memanfaatkan kolom nama_pembayaran dan di-alias sebagai 'termin' agar kode JS di View tidak perlu diubah lagi
        return $this->db->query("SELECT pp.nama_pembayaran as termin, SUM(pp.jumlah) as total
            FROM project_payments pp 
            JOIN projects p ON p.id = pp.project_id
            WHERE 1=1 $where
            GROUP BY pp.nama_pembayaran 
            ORDER BY pp.nama_pembayaran ASC")->result();
    }
}