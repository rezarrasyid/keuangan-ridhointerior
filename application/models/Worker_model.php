<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Worker_model extends CI_Model {

    private $table = 'workers';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil semua tukang berdasarkan workshop_id
     */
    public function get_all($workshop_id)
    {
        return $this->db
            ->where('workshop_id', $workshop_id)
            ->order_by('nama', 'ASC')
            ->get($this->table)
            ->result();
    }

    /**
     * Ambil tukang dengan kalkulasi saldo (Hak Upah - Tarik Tunai)
     */
    public function get_all_with_saldo($workshop_id)
    {
        $sql = "
            SELECT
                w.id,
                w.nama,
                w.telepon,
                w.kategori,
                COALESCE(SUM(CASE WHEN wl.jenis = 'Hak_Upah' THEN wl.jumlah ELSE 0 END), 0) AS total_hak_upah,
                COALESCE(SUM(CASE WHEN wl.jenis = 'Tarik_Tunai' THEN wl.jumlah ELSE 0 END), 0) AS total_tarik,
                COALESCE(SUM(CASE WHEN wl.jenis = 'Hak_Upah' THEN wl.jumlah ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN wl.jenis = 'Tarik_Tunai' THEN wl.jumlah ELSE 0 END), 0) AS sisa_saldo
            FROM workers w
            LEFT JOIN worker_ledgers wl ON wl.worker_id = w.id AND wl.workshop_id = ?
            WHERE w.workshop_id = ?
            GROUP BY w.id
            ORDER BY w.nama ASC
        ";
        return $this->db->query($sql, [$workshop_id, $workshop_id])->result();
    }

    /**
     * Ambil list tukang dengan saldo, pencarian, dan paginasi
     */
    public function get_list_with_saldo($workshop_id, $search = '', $limit = null, $offset = null)
    {
        $search_cond = "";
        $params = [$workshop_id, $workshop_id];
        
        if (!empty($search)) {
            $search_cond = " AND (w.nama LIKE ? OR w.telepon LIKE ? OR w.kategori LIKE ?) ";
            $q = '%' . $search . '%';
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
        }
        
        $limit_cond = "";
        if ($limit !== null) {
            $limit_cond = " LIMIT ? OFFSET ? ";
            $params[] = (int)$limit;
            $params[] = (int)$offset;
        }
        
        $sql = "
            SELECT
                w.id,
                w.nama,
                w.telepon,
                w.kategori,
                COALESCE(SUM(CASE WHEN wl.jenis = 'Hak_Upah' THEN wl.jumlah ELSE 0 END), 0) AS total_hak_upah,
                COALESCE(SUM(CASE WHEN wl.jenis = 'Tarik_Tunai' THEN wl.jumlah ELSE 0 END), 0) AS total_tarik,
                COALESCE(SUM(CASE WHEN wl.jenis = 'Hak_Upah' THEN wl.jumlah ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN wl.jenis = 'Tarik_Tunai' THEN wl.jumlah ELSE 0 END), 0) AS sisa_saldo
            FROM workers w
            LEFT JOIN worker_ledgers wl ON wl.worker_id = w.id AND wl.workshop_id = ?
            WHERE w.workshop_id = ? $search_cond
            GROUP BY w.id
            ORDER BY w.nama ASC
            $limit_cond
        ";
        return $this->db->query($sql, $params)->result();
    }

    /**
     * Hitung total tukang untuk paging
     */
    public function count_workers($workshop_id, $search = '')
    {
        $this->db->where('workshop_id', $workshop_id);
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('telepon', $search);
            $this->db->or_like('kategori', $search);
            $this->db->group_end();
        }
        return $this->db->count_all_results($this->table);
    }

    /**
     * Ambil tukang berdasarkan ID
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
     * Simpan tukang baru
     */
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update data tukang
     */
    public function update($id, $workshop_id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->update($this->table, $data);
    }

    /**
     * Hapus tukang
     */
    public function delete($id, $workshop_id)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->delete($this->table);
    }

    /**
     * Ambil detail saldo tukang tertentu
     */
    public function get_saldo($worker_id, $workshop_id)
    {
        $sql = "
            SELECT
                COALESCE(SUM(CASE WHEN jenis = 'Hak_Upah' THEN jumlah ELSE 0 END), 0) AS total_hak_upah,
                COALESCE(SUM(CASE WHEN jenis = 'Tarik_Tunai' THEN jumlah ELSE 0 END), 0) AS total_tarik,
                COALESCE(SUM(CASE WHEN jenis = 'Hak_Upah' THEN jumlah ELSE 0 END), 0) -
                COALESCE(SUM(CASE WHEN jenis = 'Tarik_Tunai' THEN jumlah ELSE 0 END), 0) AS sisa_saldo
            FROM worker_ledgers
            WHERE worker_id = ? AND workshop_id = ?
        ";
        return $this->db->query($sql, [$worker_id, $workshop_id])->row();
    }

    /**
     * Ambil riwayat ledger tukang
     */
    public function get_ledger($worker_id, $workshop_id)
    {
        return $this->db
            ->select('wl.*, p.nama_project')
            ->from('worker_ledgers wl')
            ->join('projects p', 'p.id = wl.project_id', 'left')
            ->where('wl.worker_id', $worker_id)
            ->where('wl.workshop_id', $workshop_id)
            ->order_by('wl.tgl', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Tambah entri ledger (upah/tarik)
     */
    public function add_ledger($data)
    {
        return $this->db->insert('worker_ledgers', $data);
    }

    /**
     * Ambil list ledger dengan search dan paginasi
     */
    public function get_ledger_list($worker_id, $workshop_id, $search = '', $limit = null, $offset = null)
    {
        $this->db
            ->select('wl.*, p.nama_project')
            ->from('worker_ledgers wl')
            ->join('projects p', 'p.id = wl.project_id', 'left')
            ->where('wl.worker_id', $worker_id)
            ->where('wl.workshop_id', $workshop_id);
            
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('wl.jenis', $search);
            $this->db->or_like('wl.keterangan', $search);
            $this->db->or_like('p.nama_project', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('wl.tgl', 'DESC');
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get()->result();
    }

    /**
     * Hitung total ledger untuk paginasi
     */
    public function count_ledger($worker_id, $workshop_id, $search = '')
    {
        $this->db
            ->from('worker_ledgers wl')
            ->join('projects p', 'p.id = wl.project_id', 'left')
            ->where('wl.worker_id', $worker_id)
            ->where('wl.workshop_id', $workshop_id);
            
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('wl.jenis', $search);
            $this->db->or_like('wl.keterangan', $search);
            $this->db->or_like('p.nama_project', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results();
    }

    /**
     * Ambil entri ledger berdasarkan ID
     */
    public function get_ledger_by_id($id, $workshop_id)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->get('worker_ledgers')
            ->row();
    }

    /**
     * Update entri ledger
     */
    public function update_ledger($id, $workshop_id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->update('worker_ledgers', $data);
    }

    /**
     * Hapus entri ledger
     */
    public function delete_ledger($id, $workshop_id)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->delete('worker_ledgers');
    }
}
