<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Total pemasukan (project_payments) bulan ini
     */
    public function get_total_pemasukan_month($workshop_id)
    {
        $sql = "
            SELECT COALESCE(SUM(pp.jumlah), 0) AS total
            FROM project_payments pp
            JOIN projects p ON p.id = pp.project_id
            WHERE p.workshop_id = ?
              AND MONTH(pp.tgl) = MONTH(CURDATE())
              AND YEAR(pp.tgl) = YEAR(CURDATE())
        ";
        $row = $this->db->query($sql, [$workshop_id])->row();
        return $row ? $row->total : 0;
    }

    /**
     * Total pengeluaran bulan ini
     */
    public function get_total_pengeluaran_month($workshop_id)
    {
        $sql = "
            SELECT COALESCE(SUM(jumlah), 0) AS total
            FROM expenses
            WHERE workshop_id = ?
              AND MONTH(tgl) = MONTH(CURDATE())
              AND YEAR(tgl) = YEAR(CURDATE())
        ";
        $row = $this->db->query($sql, [$workshop_id])->row();
        return $row ? $row->total : 0;
    }

    /**
     * Total saldo upah tukang yang belum diambil
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
     * Jumlah proyek aktif
     */
    public function get_total_proyek_aktif($workshop_id)
    {
        return $this->db
            ->where('workshop_id', $workshop_id)
            ->where('status_project', 'Aktif')
            ->count_all_results('projects');
    }

    /**
     * Data pemasukan per bulan untuk Chart
     */
    public function get_monthly_income($workshop_id, $year)
    {
        $sql = "
            SELECT MONTH(pp.tgl) AS bulan, SUM(pp.jumlah) AS total
            FROM project_payments pp
            JOIN projects p ON p.id = pp.project_id
            WHERE p.workshop_id = ? AND YEAR(pp.tgl) = ?
            GROUP BY MONTH(pp.tgl)
            ORDER BY MONTH(pp.tgl) ASC
        ";
        return $this->db->query($sql, [$workshop_id, $year])->result();
    }

    /**
     * Data pengeluaran per bulan untuk Chart
     */
    public function get_monthly_expense($workshop_id, $year)
    {
        $sql = "
            SELECT MONTH(tgl) AS bulan, SUM(jumlah) AS total
            FROM expenses
            WHERE workshop_id = ? AND YEAR(tgl) = ?
            GROUP BY MONTH(tgl)
            ORDER BY MONTH(tgl) ASC
        ";
        return $this->db->query($sql, [$workshop_id, $year])->result();
    }

    /**
     * Rekap bulanan kumulatif (Pemasukan vs Pengeluaran) untuk tahun terpilih
     */
    public function get_yearly_recap($workshop_id, $year)
    {
        $sql = "
            SELECT 
                m.bulan,
                COALESCE(pemasukan.total, 0) AS total_pemasukan,
                COALESCE(pengeluaran.total, 0) AS total_pengeluaran
            FROM (
                SELECT 1 AS bulan UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION 
                SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION 
                SELECT 9 UNION SELECT 10 UNION SELECT 11 UNION SELECT 12
            ) m
            LEFT JOIN (
                SELECT MONTH(pp.tgl) AS bulan, SUM(pp.jumlah) AS total
                FROM project_payments pp
                JOIN projects p ON p.id = pp.project_id
                WHERE p.workshop_id = ? AND YEAR(pp.tgl) = ?
                GROUP BY MONTH(pp.tgl)
            ) pemasukan ON pemasukan.bulan = m.bulan
            LEFT JOIN (
                SELECT MONTH(tgl) AS bulan, SUM(jumlah) AS total
                FROM expenses
                WHERE workshop_id = ? AND YEAR(tgl) = ?
                GROUP BY MONTH(tgl)
            ) pengeluaran ON pengeluaran.bulan = m.bulan
            ORDER BY m.bulan ASC
        ";
        return $this->db->query($sql, [$workshop_id, $year, $workshop_id, $year])->result();
    }
}
