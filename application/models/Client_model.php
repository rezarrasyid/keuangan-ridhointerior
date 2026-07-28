<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Client_model extends CI_Model {

    private $table = 'clients';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Ambil semua klien berdasarkan workshop_id
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
     * Ambil klien dengan filter pencarian dan paginasi
     */
    public function get_list($workshop_id, $search = '', $limit = null, $offset = null)
    {
        $this->db->where('workshop_id', $workshop_id);
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('telepon', $search);
            $this->db->or_like('alamat', $search);
            $this->db->group_end();
        }
        
        $this->db->order_by('nama', 'ASC');
        
        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get($this->table)->result();
    }

    /**
     * Hitung total klien untuk paginasi
     */
    public function count_list($workshop_id, $search = '')
    {
        $this->db->where('workshop_id', $workshop_id);
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('nama', $search);
            $this->db->or_like('telepon', $search);
            $this->db->or_like('alamat', $search);
            $this->db->group_end();
        }
        
        return $this->db->count_all_results($this->table);
    }

    /**
     * Ambil klien berdasarkan ID (dengan verifikasi workshop)
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
     * Simpan klien baru
     */
    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update data klien
     */
    public function update($id, $workshop_id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->update($this->table, $data);
    }

    /**
     * Hapus klien
     */
    public function delete($id, $workshop_id)
    {
        return $this->db
            ->where('id', $id)
            ->where('workshop_id', $workshop_id)
            ->delete($this->table);
    }

    /**
     * Top Client berdasarkan total nilai proyek
     */
    public function get_top_clients($workshop_id, $limit = 5)
    {
        return $this->db
            ->select('c.id, c.nama, c.telepon, COUNT(p.id) as total_project, SUM(p.biaya_total) as total_nilai')
            ->from('clients c')
            ->join('projects p', 'p.client_id = c.id', 'left')
            ->where('c.workshop_id', $workshop_id)
            ->group_by('c.id')
            ->order_by('total_nilai', 'DESC')
            ->limit($limit)
            ->get()
            ->result();
    }
}
