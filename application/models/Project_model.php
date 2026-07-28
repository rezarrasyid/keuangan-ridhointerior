<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Project_model extends CI_Model {

    private $table = 'projects';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil semua proyek dengan nama klien, berdasarkan workshop_id
     */
    public function get_all($workshop_id)
    {
        return $this->db
            ->select('p.*, c.nama AS nama_client,
                      COALESCE(SUM(pp.jumlah), 0) AS total_terbayar,
                      p.biaya_total - COALESCE(SUM(pp.jumlah), 0) AS sisa_tagihan')
            ->from('projects p')
            ->join('clients c', 'c.id = p.client_id', 'left')
            ->join('project_payments pp', 'pp.project_id = p.id', 'left')
            ->where('p.workshop_id', $workshop_id)
            ->group_by('p.id')
            ->order_by('p.created_at', 'DESC')
            ->get()
            ->result();
    }

    /**
     * Ambil list proyek dengan pencarian, paginasi, dan filter tanggal/bulan
     */
    public function get_list($workshop_id, $search = '', $start_date = '', $end_date = '', $month = '', $limit = null, $offset = null)
    {
        $this->db
            ->select('p.*, c.nama AS nama_client,
                      COALESCE(SUM(pp.jumlah), 0) AS total_terbayar,
                      p.biaya_total - COALESCE(SUM(pp.jumlah), 0) AS sisa_tagihan')
            ->from('projects p')
            ->join('clients c', 'c.id = p.client_id', 'left')
            ->join('project_payments pp', 'pp.project_id = p.id', 'left')
            ->where('p.workshop_id', $workshop_id);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.nama_project', $search);
            $this->db->or_like('p.deskripsi', $search);
            $this->db->or_like('c.nama', $search);
            $this->db->group_end();
        }

        if (!empty($month)) {
            $this->db->where("DATE_FORMAT(p.tgl_mulai, '%Y-%m') =", $month);
        } elseif (!empty($start_date) && !empty($end_date)) {
            $this->db->where('p.tgl_mulai >=', $start_date);
            $this->db->where('p.tgl_mulai <=', $end_date);
        }

        $this->db->group_by('p.id');
        $this->db->order_by('p.created_at', 'DESC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }

    /**
     * Hitung total proyek untuk paginasi
     */
    public function count_projects($workshop_id, $search = '', $start_date = '', $end_date = '', $month = '')
    {
        $this->db
            ->from('projects p')
            ->join('clients c', 'c.id = p.client_id', 'left')
            ->where('p.workshop_id', $workshop_id);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('p.nama_project', $search);
            $this->db->or_like('p.deskripsi', $search);
            $this->db->or_like('c.nama', $search);
            $this->db->group_end();
        }

        if (!empty($month)) {
            $this->db->where("DATE_FORMAT(p.tgl_mulai, '%Y-%m') =", $month);
        } elseif (!empty($start_date) && !empty($end_date)) {
            $this->db->where('p.tgl_mulai >=', $start_date);
            $this->db->where('p.tgl_mulai <=', $end_date);
        }

        return $this->db->count_all_results();
    }

    /**
     * Ambil proyek berdasarkan ID dengan kalkulasi sisa tagihan
     */
    public function get_by_id($id, $workshop_id)
    {
        return $this->db
            ->select('p.*, c.nama AS nama_client, c.telepon AS telepon_client,
                      COALESCE(SUM(pp.jumlah), 0) AS total_terbayar,
                      p.biaya_total - COALESCE(SUM(pp.jumlah), 0) AS sisa_tagihan')
            ->from('projects p')
            ->join('clients c', 'c.id = p.client_id', 'left')
            ->join('project_payments pp', 'pp.project_id = p.id', 'left')
            ->where('p.id', $id)
            ->where('p.workshop_id', $workshop_id)
            ->group_by('p.id')
            ->get()
            ->row();
    }

    /**
     * Simpan proyek baru
     */
    public function insert($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update data proyek
     */
    public function update($id, $workshop_id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->update($this->table, $data);
    }

    /**
     * Hapus proyek
     */
    public function delete($id, $workshop_id)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->delete($this->table);
    }

    /**
     * Ambil semua pembayaran (termin) berdasarkan project_id
     */
    public function get_payments($project_id)
    {
        return $this->db
            ->where('project_id', $project_id)
            ->order_by('tgl', 'ASC')
            ->get('project_payments')
            ->result();
    }

    /**
     * Tambah pembayaran/termin
     */
    public function add_payment($data)
    {
        $this->db->insert('project_payments', $data);
        return $this->db->insert_id();
    }

    /**
     * Hapus pembayaran/termin
     */
    public function delete_payment($id)
    {
        return $this->db->where('id', $id)->delete('project_payments');
    }

    /**
     * Ambil data pembayaran/termin berdasarkan ID
     */
    public function get_payment_by_id($id)
    {
        return $this->db->where('id', $id)->get('project_payments')->row();
    }

    /**
     * Update data pembayaran/termin
     */
    public function update_payment($id, $data)
    {
        return $this->db->where('id', $id)->update('project_payments', $data);
    }

    /**
     * Hitung sisa tagihan proyek
     * Sisa = biaya_total - SUM(project_payments.jumlah)
     */
    public function get_sisa_tagihan($project_id)
    {
        $sql = "
            SELECT
                p.biaya_total,
                COALESCE(SUM(pp.jumlah), 0) AS total_terbayar,
                p.biaya_total - COALESCE(SUM(pp.jumlah), 0) AS sisa_tagihan
            FROM projects p
            LEFT JOIN project_payments pp ON pp.project_id = p.id
            WHERE p.id = ?
            GROUP BY p.id
        ";
        return $this->db->query($sql, [$project_id])->row();
    }

    /**
     * Update status pembayaran proyek otomatis
     */
    public function update_payment_status($project_id)
    {
        $sisa = $this->get_sisa_tagihan($project_id);
        if ($sisa && $sisa->sisa_tagihan <= 0) {
            $this->db->where('id', $project_id)->update($this->table, ['status_pembayaran' => 'Lunas']);
        } else {
            $this->db->where('id', $project_id)->update($this->table, ['status_pembayaran' => 'Belum Lunas']);
        }
    }

    /**
     * Ambil proyek aktif untuk dropdown
     */
    public function get_dropdown($workshop_id)
    {
        return $this->db
            ->select('id, nama_project')
            ->where('workshop_id', $workshop_id)
            ->where('status_project', 'Aktif')
            ->order_by('nama_project', 'ASC')
            ->get($this->table)
            ->result();
    }
}
