<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expense_model extends CI_Model {

    private $table = 'expenses';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil semua pengeluaran berdasarkan workshop_id
     */
    public function get_all($workshop_id)
    {
        return $this->db
            ->select('e.*, p.nama_project')
            ->from('expenses e')
            ->join('projects p', 'p.id = e.project_id', 'left')
            ->where('e.workshop_id', $workshop_id)
            ->order_by('e.tgl', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Ambil list pengeluaran dengan search dan paging
     */
    public function get_list($workshop_id, $search = '', $limit = null, $offset = null)
    {
        $this->db
            ->select('e.*, p.nama_project')
            ->from('expenses e')
            ->join('projects p', 'p.id = e.project_id', 'left')
            ->where('e.workshop_id', $workshop_id);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('e.kategori', $search);
            $this->db->or_like('e.keterangan', $search);
            $this->db->or_like('p.nama_project', $search);
            $this->db->group_end();
        }

        $this->db->order_by('e.tgl', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Hitung total pengeluaran untuk paging
     */
    public function count_expenses($workshop_id, $search = '')
    {
        $this->db
            ->from('expenses e')
            ->join('projects p', 'p.id = e.project_id', 'left')
            ->where('e.workshop_id', $workshop_id);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('e.kategori', $search);
            $this->db->or_like('e.keterangan', $search);
            $this->db->or_like('p.nama_project', $search);
            $this->db->group_end();
        }

        return $this->db->count_all_results();
    }

    /**
     * Ambil data pengeluaran berdasarkan ID
     */
    public function get_by_id($id, $workshop_id)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->get($this->table)
            ->row();
    }

    /**
     * Simpan pengeluaran baru
     */
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update data pengeluaran
     */
    public function update($id, $workshop_id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->update($this->table, $data);
    }

    /**
     * Hapus pengeluaran
     */
    public function delete($id, $workshop_id)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->delete($this->table);
    }

    /**
     * Total pengeluaran bulan ini
     */
    public function get_total_this_month($workshop_id)
    {
        $result = $this->db
            ->select_sum('jumlah')
            ->where('workshop_id', $workshop_id)
            ->where("MONTH(tgl)", date('m'))
            ->where("YEAR(tgl)", date('Y'))
            ->get($this->table)
            ->row();
        return $result ? $result->jumlah : 0;
    }

    /**
     * Data pengeluaran per bulan untuk Chart (12 bulan terakhir)
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
}
